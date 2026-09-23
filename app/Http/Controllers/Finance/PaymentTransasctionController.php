<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Config\BasicSystemSetting;
use App\Models\Finance\Claims\FinancialClaim;
use App\Models\Finance\Dues\AssociationDue;
use App\Models\Finance\Settings\ElectronicPaymentMethod;
use App\Models\Finance\Settings\Tax;
use App\Models\Finance\Transactions\PaymentRegularization;
use App\Models\Finance\Transactions\PaymentTransaction;
use App\Models\Finance\Transactions\PaymentWaiver;
use Carbon\Carbon;
use Faker\Provider\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;

class PaymentTransasctionController extends Controller
{
    public function memberPayAssociationDue($uuid, Request $request)
    {
        $member = Auth::user();


        $due = AssociationDue::where('uuid', $uuid)->first();

        if (!$due) {
            return redirect()->back()->with('error', 'Association due not found.');
        }

        $academicInfo = $member->memberAcademicInfo;

        $memberDue = $academicInfo
            ->associationDues()
            ->firstWhere('uuid', $uuid);

        if (!$memberDue) {
            $memberDue = $academicInfo
                ->pastProgramsAssociationDues()
                ->flatten(1)
                ->firstWhere('uuid', $uuid);
        }

        if (!$memberDue) {
            return redirect()->back()->with(
                'error',
                'This due does not apply to your account.'
            );
        }

        $due = $memberDue;

        // check if it has been paid
        $alreadyPaid = PaymentTransaction::where('registered_payer', $member->username)->where('due_id', $due->fee_id)->where('approval_status', 'approved')->first();


        if ($alreadyPaid) {
            return redirect()->back()->with(
                'error',
                'This association due has already been paid.'
            );
        }

        // check if waiver exists
        $waiverExists = PaymentWaiver::where('username', $due->fee_id)->where('due_id', $due->fee_id)->where('approval_status', 'approved')->first();
        if ($alreadyPaid) {
            return redirect()->back()->with(
                'error',
                'You have been granted a payment waiver for this due by the association.'
            );
        }




        // check for pending transaction
        $pendingTransaction = PaymentTransaction::where('registered_payer', $member->username)
            ->where('due_id', $due->fee_id)
            ->where(function ($query) {
                $query->where('payment_status', 'pending')
                    ->orWhere('approval_status', 'pending');
            })
            ->first();

        if ($pendingTransaction) {
            return redirect()->back()->with(
                'error',
                'You already have a pending payment transaction. Please reconfirm it or wait for an administrator to process it before creating another payment.'
            );
        }

        do {
            $transactionId = 'pymt-' . now()->format('Ymds') . '-' . tenant()->id . random_int(100000000, 999999999);
        } while (PaymentTransaction::where('payment_transaction_id', $transactionId)->exists());



        // for manual payment
        if ($request->mode == 'manual') {
            $request->validate([
                'manual_payment_method' => 'required|in:cash,bank_transfer,cheque',
                'proof' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3145728',
            ]);


            $transaction = new PaymentTransaction();
            $transaction->payment_transaction_id = $transactionId;
            $transaction->payment_mode = 'manual';
            $transaction->registered_payer = $member->username;
            $transaction->payment_purpose = 'association_due';
            $transaction->due_id = $due->fee_id;
            $transaction->amount_payable = $due->payable_amount;
            $transaction->amount_paid = $due->payable_amount;
            $transaction->manual_payment_method = $request->manual_payment_method;
            $transaction->payment_status = 'pending';
            $transaction->approval_status = 'pending';
            $transaction->paid_at = Carbon::now();



            if ($request->hasFile('proof')) {

                $file = $request->file('proof');
                $mime = $file->getMimeType();
                $ext  = strtolower($file->getClientOriginalExtension());

                $uploadPath = public_path('assets/tenant_' . tenant()->id . '/uploads/paymentTransactions/proof');
                File::ensureDirectoryExists($uploadPath);

                // Base filename
                $baseName = strtolower($transactionId);

                $baseName = Str::of($baseName)
                    ->replaceMatches('/[^A-Za-z0-9]+/', '_')
                    ->trim('_')
                    ->toString();
                $fallbackName = $baseName . '.' . $ext;
                $counter = 1;

                // Delete existing file(s) with same base name regardless of extension
                foreach (glob($uploadPath . '/' . $baseName . '.*') as $existingFile) {
                    if (file_exists($existingFile)) {
                        unlink($existingFile);
                    }
                }

                // Avoid overwriting existing files
                while (file_exists($uploadPath . '/' . $fallbackName)) {
                    $fallbackName = $baseName . '-' . $counter . '.' . $ext;
                    $counter++;
                }

                $fallbackPath = $uploadPath . '/' . $fallbackName;

                /*
            |--------------------------------------------------------------------------
            | GD + WebP processing
            |--------------------------------------------------------------------------
            */
                $processed = false;

                if (function_exists('imagewebp') && function_exists('imagecreatetruecolor')) {

                    $tmp = $file->getPathname();

                    $src = match ($mime) {
                        'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($tmp) : null,
                        'image/png'  => function_exists('imagecreatefrompng')  ? @imagecreatefrompng($tmp)  : null,
                        'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmp) : null,
                        default => null,
                    };

                    if ($src) {
                        $width  = imagesx($src);
                        $height = imagesy($src);

                        // Resize if needed
                        $maxSize = 800;
                        $scale = min(1, $maxSize / max($width, $height));

                        if ($scale < 1) {
                            $newW = (int)($width * $scale);
                            $newH = (int)($height * $scale);
                            $dst = imagecreatetruecolor($newW, $newH);

                            // Preserve transparency
                            imagealphablending($dst, false);
                            imagesavealpha($dst, true);
                            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                            imagefill($dst, 0, 0, $transparent);

                            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
                            imagedestroy($src);
                            $src = $dst;
                        }

                        // WebP conversion with quality loop
                        $webpName = $baseName . '.webp';
                        $webpCounter = 1;
                        $webpPath = $uploadPath . '/' . $webpName;

                        while (file_exists($webpPath)) {
                            $webpName = $baseName . '-' . $webpCounter . '.webp';
                            $webpPath = $uploadPath . '/' . $webpName;
                            $webpCounter++;
                        }

                        $quality = 90;
                        do {
                            imagewebp($src, $webpPath, $quality);
                            clearstatcache();

                            if (filesize($webpPath) <= 1048576) {
                                $processed = true;
                                $transaction->photo = $webpName;
                                imagedestroy($src);
                                break;
                            }

                            $quality -= 5;
                        } while ($quality >= 40);

                        if (!$processed) {
                            @unlink($webpPath);
                        }
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Fallback: move original file
            |--------------------------------------------------------------------------
            */
                if (!$processed) {
                    $file->move($uploadPath, $fallbackName);
                    $transaction->photo = $fallbackName;
                }
            }

            $transaction->save();
        } elseif ($request->mode == 'electronic') {
            $request->validate([
                'electronic_payment_method' => 'required|exists:electronic_payment_methods,slug',
            ]);

            // Taxes
            $taxes = Tax::where('is_active', true)->orderBy('tax_name')->get();
            $totalTax = 0;
            $taxesPaid = '';
            if ($taxes) {
                $taxesPaid = '';
                foreach ($taxes as $tax) {
                    $taxAmount = 0; // Initialize tax amount for each tax.

                    // Concatenate tax details.
                    $taxesPaid .= '**' . $tax->tax_name . ' (' . $tax->tax_method . '-' . $tax->tax_rate . ')';

                    // Calculate tax amount based on the method.
                    if ($tax->tax_method === 'percentage') {
                        $taxAmount = ($tax->tax_rate / 100) * $due->payable_amount;
                    } elseif ($tax->tax_method === 'fixed_amount') {
                        $taxAmount = $tax->tax_rate;
                    }

                    $totalTax += $taxAmount; // Add to total tax.
                    $taxesPaid .= ': ₦' . number_format($taxAmount, 2) . "\n"; // Add tax amount to the concatenated string.
                }
            }

            $totalDueAndTax = $due->payable_amount + $totalTax;
            // get personal profile
            $personalProfile = $member->memberPersonalProfile;
            if (!$personalProfile) {
                return redirect()->back()->with('error', 'Personal profile not found!');
            }

            // pasyack processing start
            if ($request->electronic_payment_method == 'paystack') {
                $pastackSettings = ElectronicPaymentMethod::where('slug', $request->electronic_payment_method)->where('is_active', true)->first();
                if (!$pastackSettings) {
                    return redirect()->back()->with('error', 'Paystack method not found!');
                }
                $paystackApi = $pastackSettings->apiConfig;
                if (!$paystackApi) {
                    return redirect()->back()->with('error', 'Paystack method not found!');
                }

                $secretKey = $paystackApi->secret_key;
                $amount = $totalDueAndTax * 100; // Convert to kobo
                $email = $personalProfile->email1;

                $data = [
                    'transaction_id' => $transactionId,
                    'reciept_number' => $transactionId,
                    'email' => $email,
                    'amount' => $amount,
                    'callback_url' => route('paystackPaymentCallback', ['transaction_id' => $transactionId]),
                ];

                $response = Http::withToken($secretKey)
                    ->post('https://api.paystack.co/transaction/initialize', $data);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $transaction = new PaymentTransaction();
                    $transaction->payment_transaction_id = $transactionId; // Local reference
                    $transaction->payment_mode = 'electronic';
                    $transaction->payment_purpose = 'association_due';
                    $transaction->due_id = $due->fee_id;
                    $transaction->amount_payable = $due->payable_amount;
                    $transaction->registered_payer = $personalProfile->username;
                    $transaction->amount_paid = $totalDueAndTax;
                    $transaction->total_tax = $totalTax;
                    $transaction->taxes_paid = $taxesPaid;
                    $transaction->electronic_payment_method = $pastackSettings->slug;
                    $transaction->payment_status = 'pending';
                    $transaction->approval_status = 'pending';
                    $transaction->paid_at = Carbon::now();
                    $transaction->save();

                    return redirect()->away($responseData['data']['authorization_url']);
                }

                return redirect()->back()->with('error', 'Failed!');
            }
            // pasyack processing end
            elseif ($request->electronic_payment_method == 'flutterwave') {
                $flutterwaveSettings = ElectronicPaymentMethod::where('slug', $request->electronic_payment_method)->where('is_active', true)->first();
                if (!$flutterwaveSettings) {
                    return redirect()->back()->with('error', 'Flutterwave method not found!');
                }
                $flutterwaveApi = $flutterwaveSettings->apiConfig;
                if (!$flutterwaveApi) {
                    return redirect()->back()->with('error', 'Flutterwave config not found!');
                }

                $secretKey = $flutterwaveApi->secret_key;

                $data = [
                    'tx_ref' => $transactionId,
                    'amount' => $totalDueAndTax,
                    'currency' => 'NGN',
                    'redirect_url' => route('flutterwavePaymentCallback'),
                    'customer' => [
                        'email' => $personalProfile->email1,
                        'phone_number' => $personalProfile->phone1,
                        'name' => $personalProfile->first_name . ' ' . $personalProfile->middle_name . '' . $personalProfile->last_name,
                    ],
                    'customizations' => [
                        'title' => config('app.name'),
                        'description' => 'Association Due',
                        'logo' => associationLogo(),
                    ],
                ];

                // Make API request for payment
                $response = Http::withToken($secretKey)
                    ->post('https://api.flutterwave.com/v3/payments', $data);

                if ($response->successful()) {
                    $responseData = $response->json()['data'];
                    if (isset($responseData['link'])) {
                        $transaction = new PaymentTransaction();
                        $transaction->payment_transaction_id = $transactionId; // Local reference
                        $transaction->payment_mode = 'electronic';
                        $transaction->payment_purpose = 'association_due';
                        $transaction->due_id = $due->fee_id;
                        $transaction->amount_payable = $due->payable_amount;
                        $transaction->registered_payer = $personalProfile->username;
                        $transaction->amount_paid = $totalDueAndTax;
                        $transaction->total_tax = $totalTax;
                        $transaction->taxes_paid = $taxesPaid;
                        $transaction->electronic_payment_method = $flutterwaveSettings->slug;
                        $transaction->payment_status = 'pending';
                        $transaction->approval_status = 'pending';
                        $transaction->paid_at = Carbon::now();
                        $transaction->save();
                        return redirect()->away($responseData['link']);
                    } else {
                        logger()->error('Invalid Flutterwave response', $response->json());
                        return redirect()->back()->with('error', 'Unable to initiate payment. Please try again.');
                    }
                } else {
                    logger()->error('Flutterwave API Error', $response->json());
                    return redirect()->back()->with('error', 'Failed to initiate payment. Please try again.');
                }
            }
        }


        return redirect()->back()->with('success', 'Saved!');
    }
    public function paymentTransactions(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.read'),
            403
        );


        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $transactions = '';
        $totalTransactions = 0;


        if (Route::currentRouteName() == 'paymentTransactionsSearch') {
            $searchKey = $request->input('searchKey');
            // $transactions = PaymentTransaction::with([
            //     'due' => function ($query) use ($searchKey) {
            //         $query->where('title', 'like', "%$searchKey%")
            //             ->orWhere('fee_id', 'like', "%$searchKey%")
            //             ->orWhere('description', 'like', "%$searchKey%");
            //     },
            //     'payerPersonalProfile' => function ($query) use ($searchKey) {
            //         $query->where('phone1', 'like', "%$searchKey%")
            //             ->orWhere('email1', 'like', "%$searchKey%")
            //             ->orWhere('phone2', 'like', "%$searchKey%")
            //             ->orWhere('email2', 'like', "%$searchKey%")
            //             ->orWhere('first_name', 'like', "%$searchKey%")
            //             ->orWhere('middle_name', 'like', "%$searchKey%")
            //             ->orWhere('last_name', 'like', "%$searchKey%")
            //             ->orWhere('gender', 'like', "%$searchKey%")
            //             ->orWhere('nationality', 'like', "%$searchKey%")
            //             ->orWhere('state_of_origin', 'like', "%$searchKey%")
            //             ->orWhere('lga_of_origin', 'like', "%$searchKey%");
            //     },
            //     'registeredPayer',
            //     'payerAcademicInfo',
            //     'invoice',
            //     'donation',
            //     'election',
            // ])
            //     ->where(function ($query) use ($searchKey) {
            //         $query->where('payment_transaction_id', 'like', "%$searchKey%")
            //             ->orWhere('service_provider_id', 'like', "%$searchKey%")
            //             ->orWhere('paystack_reference', 'like', "%$searchKey%")
            //             ->orWhere('payment_mode', 'like', "%$searchKey%")
            //             ->orWhere('registered_payer', 'like', "%$searchKey%")
            //             ->orWhere('non_registered_payer', 'like', "%$searchKey%")
            //             ->orWhere('payment_purpose', 'like', "%$searchKey%")
            //             ->orWhere('electronic_payment_method', 'like', "%$searchKey%")
            //             ->orWhere('manual_payment_method', 'like', "%$searchKey%")
            //             ->orWhere('payment_status', 'like', "%$searchKey%")
            //             ->orWhere('approval_status', 'like', "%$searchKey%")
            //             ->orWhere('due_id', 'like', "%$searchKey%")
            //             ->orWhere('invoice_number', 'like', "%$searchKey%")
            //             ->orWhere('donation_id', 'like', "%$searchKey%")
            //             ->orWhere('election_id', 'like', "%$searchKey%");
            //     })
            //     ->orWhereHas('due', function ($query) use ($searchKey) {
            //         $query->where('title', 'like', "%$searchKey%")
            //             ->orWhere('fee_id', 'like', "%$searchKey%")
            //             ->orWhere('description', 'like', "%$searchKey%");
            //     })
            //     ->orWhereHas('payerPersonalProfile', function ($query) use ($searchKey) {
            //         $query->where('phone1', 'like', "%$searchKey%")
            //             ->orWhere('email1', 'like', "%$searchKey%")
            //             ->orWhere('phone2', 'like', "%$searchKey%")
            //             ->orWhere('email2', 'like', "%$searchKey%")
            //             ->orWhere('first_name', 'like', "%$searchKey%")
            //             ->orWhere('middle_name', 'like', "%$searchKey%")
            //             ->orWhere('last_name', 'like', "%$searchKey%")
            //             ->orWhere('gender', 'like', "%$searchKey%")
            //             ->orWhere('nationality', 'like', "%$searchKey%")
            //             ->orWhere('state_of_origin', 'like', "%$searchKey%")
            //             ->orWhere('lga_of_origin', 'like', "%$searchKey%");
            //     })
            //     ->orderBy('paid_at', 'desc')
            //     ->paginate(20)
            //     ->withQueryString();

            $transactions = PaymentTransaction::query()
                ->with([
                    // Always load these relationships fully
                    'due',
                    'invoice',
                    'donation',
                    'election',
                    'electionCandidateApplication.position',
                    'monetaryVote',
                    'registeredPayer.memberAcademicInfo.userProfile.memberPersonalProfile',
                    'payerPersonalProfile',
                ])
                ->where(function ($query) use ($searchKey) {
                    // Direct columns on payment_transactions
                    $query->where('payment_transaction_id', 'like', "%{$searchKey}%")
                        ->orWhere('service_provider_id', 'like', "%{$searchKey}%")
                        ->orWhere('paystack_reference', 'like', "%{$searchKey}%")
                        ->orWhere('payment_mode', 'like', "%{$searchKey}%")
                        ->orWhere('registered_payer', 'like', "%{$searchKey}%")
                        ->orWhere('non_registered_payer', 'like', "%{$searchKey}%")
                        ->orWhere('payment_purpose', 'like', "%{$searchKey}%")
                        ->orWhere('electronic_payment_method', 'like', "%{$searchKey}%")
                        ->orWhere('manual_payment_method', 'like', "%{$searchKey}%")
                        ->orWhere('payment_status', 'like', "%{$searchKey}%")
                        ->orWhere('approval_status', 'like', "%{$searchKey}%")
                        ->orWhere('due_id', 'like', "%{$searchKey}%")
                        ->orWhere('invoice_number', 'like', "%{$searchKey}%")
                        ->orWhere('donation_id', 'like', "%{$searchKey}%")
                        ->orWhere('election_id', 'like', "%{$searchKey}%")

                        // Search through related models
                        ->orWhereHas('due', function ($dueQuery) use ($searchKey) {
                            $dueQuery->where('title', 'like', "%{$searchKey}%")
                                ->orWhere('fee_id', 'like', "%{$searchKey}%")
                                ->orWhere('description', 'like', "%{$searchKey}%");
                        })

                        ->orWhereHas('payerPersonalProfile', function ($profileQuery) use ($searchKey) {
                            $profileQuery->where('phone1', 'like', "%{$searchKey}%")
                                ->orWhere('email1', 'like', "%{$searchKey}%")
                                ->orWhere('phone2', 'like', "%{$searchKey}%")
                                ->orWhere('email2', 'like', "%{$searchKey}%")
                                ->orWhere('first_name', 'like', "%{$searchKey}%")
                                ->orWhere('middle_name', 'like', "%{$searchKey}%")
                                ->orWhere('last_name', 'like', "%{$searchKey}%")
                                ->orWhere('gender', 'like', "%{$searchKey}%")
                                ->orWhere('nationality', 'like', "%{$searchKey}%")
                                ->orWhere('state_of_origin', 'like', "%{$searchKey}%")
                                ->orWhere('lga_of_origin', 'like', "%{$searchKey}%");
                        });
                })
                ->orderByDesc('paid_at', 'desc')
                ->paginate(20)
                ->withQueryString();

            $totalTransactions = $transactions->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $transactions = PaymentTransaction::orderBy('paid_at', 'desc')->paginate(20);
            $totalTransactions = PaymentTransaction::count();
            $requestType = 'all';
            $pageTitle = 'Payment Transactions';
        }

        $data = [
            'transactions' => $transactions,
            'totalTransactions' => $totalTransactions,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.finance.transactions.paymentTransactions', compact('data'));
    }
    public function viewPaymentTransaction($payment_transaction_id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.read'),
            403
        );

        $transaction = PaymentTransaction::where('payment_transaction_id', $payment_transaction_id)->first();
        if (!$transaction) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $data = [
            'transaction' => $transaction,
        ];
        return view('users.admin.finance.transactions.viewPaymentTransaction', compact('data'));
    }
    public function updatePaystackRefNumber($payment_transaction_id, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.update'),
            403
        );

        $request->validate([
            'paystack_reference' => 'required|string',
        ]);

        $transaction = PaymentTransaction::where('payment_transaction_id', $payment_transaction_id)->where('payment_mode', 'electronic')->where('electronic_payment_method', 'paystack')->first();
        if (!$transaction) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $transaction->update([
            'paystack_reference' => $request->paystack_reference,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function processManualPaymentTransaction($payment_transaction_id, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.update'),
            403
        );

        $transaction = PaymentTransaction::where('payment_transaction_id', $payment_transaction_id)->first();
        if (!$transaction) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $request->validate([
            'approval_status' => 'required|in:approved,declined',
        ]);

        $user = Auth::user();

        $paymentStatus = $request->approval_status === 'approved'
            ? 'successful'
            : 'failed';

        $transaction->update([
            'amount_settled' => $request->approval_status === 'approved'
                ? $transaction->amount_paid
                : 0,
            'payment_status' => $paymentStatus,
            'approval_status' => $request->approval_status,
            'approved_by' => $user->username,
            'approved_at' => now(),
        ]);

        $settings = basicSystemInfo();

        $wasBalanceUpdated = $transaction->balance_update_status === 'done';
        $shouldBalanceBeUpdated = $request->approval_status === 'approved';


        if (!$wasBalanceUpdated && $shouldBalanceBeUpdated) {

            // Money has never been added, but should now be added.
            $settings->increment('account_balance', $transaction->amount_paid);

            $transaction->update([
                'amount_settled' => $transaction->amount_paid,
                'balance_update_status' => 'done',
            ]);
        } elseif ($wasBalanceUpdated && !$shouldBalanceBeUpdated) {

            // Money was previously added but should now be removed.
            $settings->decrement('account_balance', $transaction->amount_paid);

            $transaction->update([
                'amount_settled' => 0,
                'balance_update_status' => 'cancelled',
            ]);
        }

        return redirect()->back()->with('success', 'Done!');
    }
    public function paystackPaymentCallback(Request $request, $transaction_id)
    {
        $paymentTransaction = PaymentTransaction::where('payment_transaction_id', $transaction_id)->first();
        if (!$paymentTransaction) {
            return redirect()->back()->with('error', 'Payment transaction not found!');
        }

        // basic system settings manual definition start
        $settings = BasicSystemSetting::where('tenant_id', $paymentTransaction->tenant_id)->first();
        // basic system settings manual definition end


        $pastackSettings = ElectronicPaymentMethod::where('slug', $paymentTransaction->electronic_payment_method)->where('is_active', true)->first();
        if (!$pastackSettings) {
            return redirect()->back()->with('error', 'Paystack method not found!');
        }
        $paystackApi = $pastackSettings->apiConfig;
        if (!$paystackApi) {
            return redirect()->back()->with('error', 'Paystack setup not found!');
        }

        $secretKey = $paystackApi->secret_key;
        $reference = $request->query('reference');
        $response = Http::withToken($secretKey)
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        if ($response->successful()) {

            $responseData = $response->json()['data'];

            if ($responseData['status'] === 'success') {

                if ($paymentTransaction) {
                    $paymentTransaction->service_provider_id = $responseData['id'];
                    $paymentTransaction->paystack_reference = $responseData['reference'];
                    $paymentTransaction->amount_paid = round($responseData['amount'] / 100, 2);
                    $paymentTransaction->service_charge = round($responseData['fees'] / 100, 2);
                    $paymentTransaction->amount_settled = round($paymentTransaction->amount_paid - $paymentTransaction->service_charge, 2);
                    $paymentTransaction->payment_status = 'successful';
                    $paymentTransaction->approval_status = 'approved';
                    $paymentTransaction->approved_at = Carbon::now();
                    $paymentTransaction->approved_by = null;
                    $paymentTransaction->save();



                    $wasBalanceUpdated = $paymentTransaction->balance_update_status === 'done';
                    $shouldBalanceBeUpdated = $paymentTransaction->approval_status === 'approved';

                    if (!$wasBalanceUpdated && $shouldBalanceBeUpdated) {

                        // Money has never been added, but should now be added.
                        $settings->increment('account_balance', $paymentTransaction->amount_settled);

                        $paymentTransaction->update([
                            'balance_update_status' => 'done',
                        ]);
                    } elseif ($wasBalanceUpdated && !$shouldBalanceBeUpdated) {

                        // Money was previously added but should now be removed.
                        $settings->decrement('account_balance', $paymentTransaction->amount_settled);

                        $paymentTransaction->update([
                            'balance_update_status' => 'cancelled',
                        ]);
                    }

                    // submit election position application
                    if ($paymentTransaction->electionCandidateApplication) {
                        $paymentTransaction->electionCandidateApplication->update([
                            'status' => 'pending_approval',
                        ]);
                    }

                    // count election votes
                    if ($paymentTransaction->monetaryVote) {
                        $paymentTransaction->monetaryVote->update([
                            'votes_counted' => true,
                        ]);
                    }

                    // update invoice records
                    if ($paymentTransaction->invoice) {
                        $paymentTransaction->invoice->update([
                            'status' => 'paid',
                        ]);
                    }
                    return redirect()->back();
                }
            } else {
                $paymentTransaction->payment_status = $responseData['status'];
                $paymentTransaction->approval_status = 'declined';
                $paymentTransaction->approved_by = null;
                $paymentTransaction->approved_at = Carbon::now();
                $paymentTransaction->save();
                return redirect()->back();
            }
        } else {
            $paymentTransaction->payment_status = 'failed';
            $paymentTransaction->approval_status = 'declined';
            $paymentTransaction->approved_by = null;
            $paymentTransaction->approved_at = Carbon::now();
            $paymentTransaction->save();

            return redirect()->back()->with('error', 'Verification failed');
        }
        return redirect()->back();
    }
    public function flutterwavePaymentCallback(Request $request)
    {
        $transactionId = $request->tx_ref;
        $flutterwaveTransactionId = $request->transaction_id;

        $paymentTransaction = PaymentTransaction::where('payment_transaction_id', $transactionId)->first();
        if (!$paymentTransaction) {
            return redirect()->back()->with('error', 'Payment transaction not found!');
        }

        // basic system settings manual definition start
        $settings = BasicSystemSetting::where('tenant_id', $paymentTransaction->tenant_id)->first();
        // basic system settings manual definition end

        $flutterwaveSettings = ElectronicPaymentMethod::where('slug', 'flutterwave')->where('is_active', true)->first();
        if (!$flutterwaveSettings) {
            return redirect()->back()->with('error', 'Flutterwave method not found!');
        }
        $flutterwaveApi = $flutterwaveSettings->apiConfig;
        if (!$flutterwaveApi) {
            return redirect()->back()->with('error', 'Flutterwave config not found!');
        }

        // fallback to v3 start
        $secretKey = $flutterwaveApi->secret_key;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,  // Add authorization header
            'Content-Type' => 'application/json',  // Set content type
            'Accept' => 'application/json',  // Accept JSON response
        ])
            ->get("https://api.flutterwave.com/v3/transactions/{$flutterwaveTransactionId}/verify");

        // check response status
        if ($response->successful()) {
            $responseData = $response->json();  // Decode the response
            if (isset($responseData['data']) && $responseData['data']['status'] === 'successful') {

                $paymentTransaction->service_provider_id = $responseData['data']['id'];
                $paymentTransaction->amount_paid = $responseData['data']['amount'];
                $paymentTransaction->amount_settled = $responseData['data']['amount_settled'];
                $paymentTransaction->service_charge = $responseData['data']['app_fee'];
                // $paymentTransaction->payment_status = $responseData['data']['status'];
                $paymentTransaction->payment_status = 'successful';
                $paymentTransaction->approval_status = 'approved';
                $paymentTransaction->approved_by = null;
                $paymentTransaction->approved_at = Carbon::now();
                $paymentTransaction->save();



                $wasBalanceUpdated = $paymentTransaction->balance_update_status === 'done';
                $shouldBalanceBeUpdated = $paymentTransaction->approval_status === 'approved';

                if (!$wasBalanceUpdated && $shouldBalanceBeUpdated) {

                    // Money has never been added, but should now be added.
                    $settings->increment('account_balance', $paymentTransaction->amount_settled);

                    $paymentTransaction->update([
                        'balance_update_status' => 'done',
                    ]);
                } elseif ($wasBalanceUpdated && !$shouldBalanceBeUpdated) {

                    // Money was previously added but should now be removed.
                    $settings->decrement('account_balance', $paymentTransaction->amount_settled);

                    $paymentTransaction->update([
                        'balance_update_status' => 'cancelled',
                    ]);
                }

                // submit election position application
                if ($paymentTransaction->electionCandidateApplication) {
                    $paymentTransaction->electionCandidateApplication->update([
                        'status' => 'pending_approval',
                    ]);
                }

                // count election votes
                if ($paymentTransaction->monetaryVote) {
                    $paymentTransaction->monetaryVote->update([
                        'votes_counted' => true,
                    ]);
                }

                // update invoice records
                if ($paymentTransaction->invoice) {
                    $paymentTransaction->invoice->update([
                        'status' => 'paid',
                    ]);
                }
                return redirect()->back();
            } else {
                if ($responseData && $responseData['data'] && $responseData['data']['id']) {
                    $paymentTransaction->service_provider_id = $responseData['data']['id'];
                }
                $paymentTransaction->payment_status = 'failed';
                $paymentTransaction->approval_status = 'declined';
                $paymentTransaction->approved_by = null;
                $paymentTransaction->approved_at = Carbon::now();
                $paymentTransaction->save();
                return redirect()->back();
            }
            return redirect()->back();
        } else {
            return redirect()->back()->with('error', 'Verification failed.');
        }
        return redirect()->back();
    }
    public function paymentRegularizationRequests(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $regularizations = '';
        $totalRegularizations = 0;


        if (Route::currentRouteName() == 'paymentRegularizationRequestsSearch') {
            $searchKey = $request->input('searchKey');
            $regularizations = PaymentRegularization::with([
                'due',
                'personalProfile',
            ])
                ->when($searchKey, function ($query) use ($searchKey) {

                    $keyword = "%{$searchKey}%";

                    $query->where(function ($q) use ($keyword) {

                        // PaymentRegularization fields
                        $q->where('description', 'like', $keyword)
                            ->orWhere('remarks', 'like', $keyword)

                            // Due
                            ->orWhereHas('due', function ($dueQuery) use ($keyword) {
                                $dueQuery->where('title', 'like', $keyword)
                                    ->orWhere('description', 'like', $keyword);
                            })

                            // Personal profile
                            ->orWhereHas('personalProfile', function ($profileQuery) use ($keyword) {
                                $profileQuery->where('phone1', 'like', $keyword)
                                    ->orWhere('email1', 'like', $keyword)
                                    ->orWhere('phone2', 'like', $keyword)
                                    ->orWhere('email2', 'like', $keyword)
                                    ->orWhere('first_name', 'like', $keyword)
                                    ->orWhere('middle_name', 'like', $keyword)
                                    ->orWhere('last_name', 'like', $keyword)
                                    ->orWhere('gender', 'like', $keyword)
                                    ->orWhere('nationality', 'like', $keyword)
                                    ->orWhere('state_of_origin', 'like', $keyword)
                                    ->orWhere('lga_of_origin', 'like', $keyword);
                            });
                    });
                })
                ->orderByRaw("CASE WHEN approval_status = 'pending' THEN 0 ELSE 1 END")
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString();

            $totalRegularizations = $regularizations->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $regularizations = PaymentRegularization::query()
                ->orderByRaw("CASE WHEN approval_status = 'pending' THEN 0 ELSE 1 END")
                ->orderByDesc('created_at')
                ->paginate(20);
            $totalRegularizations = PaymentRegularization::count();
            $requestType = 'all';
            $pageTitle = 'Payment Regularization';
        }
        $data = [
            'regularizations' => $regularizations,
            'totalRegularizations' => $totalRegularizations,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.finance.regularizations.regularizations', compact('data'));
    }
    public function viewPaymentRegularization($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.read'),
            403
        );

        $regularization = PaymentRegularization::where('uuid', $uuid)->first();
        if (!$regularization) {
            return redirect()->back()->with('error', 'Regularization not found.');
        }

        $data = [
            'regularization' => $regularization,
        ];
        return view('users.admin.finance.regularizations.viewRegularization', compact('data'));
    }
    public function processPaymentRegularizationSubmit($uuid, Request $request)
    {
        $paymentTransaction = null;
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.update'),
            403
        );

        $regularization = PaymentRegularization::where('uuid', $uuid)->first();
        if (!$regularization) {
            return redirect()->back()->with('error', 'Regularization not found.');
        }

        $request->validate([
            'approval_status' => 'required|string|in:approved,declined',
            'update_balance' => 'required|required',
            'remarks' => 'nullable',
        ]);

        $currentUser = Auth::user();

        if ($regularization->initiated_by === $currentUser->username) {
            return redirect()->back()->with(
                'error',
                'You cannot approve a payment regularization request that you initiated. Kindly contact another executive to approve this request.'
            );
        }

        $regularization->update([
            'remarks' => $request->remarks,
            'processed_by' => Auth::user()->username,
            'approval_status' => $request->approval_status,
            'approved_at' => Carbon::now(),
            'update_balance' => $request->update_balance,
        ]);

        // only if approved
        if ($regularization->approval_status == 'approved') {
            $paymentTransaction = PaymentTransaction::updateOrCreate(
                [
                    // Unique attributes used to search for the existing record
                    'payment_transaction_id' => $regularization->regularization_id,
                    'tenant_id' => tenant()->id,
                ],
                [
                    'amount' => $regularization->amount,
                    'status' => 'pending',
                    'payment_transaction_id' => $regularization->regularization_id,
                    'payment_mode' => $regularization->payment_mode,
                    'payment_type' => 'regularization',
                    'registered_payer' => $regularization->username,
                    'payment_purpose' => 'association_due',
                    'due_id' => $regularization->due_id,
                    'amount_payable' => $regularization->amount_paid,
                    'amount_paid' => $regularization->amount_paid,
                    'electronic_payment_method' => $regularization->electronic_payment_method,
                    'manual_payment_method' => $regularization->manual_payment_method,
                    'payment_status' => 'successful',
                    'paid_at' => $regularization->payment_date,
                    'approval_status' => 'approved',
                    'photo' => $regularization->proof_of_payment,
                    'approved_by' => $currentUser->username,
                    'approved_at' => Carbon::now(),
                ]
            );
        }


        $transactionExists = PaymentTransaction::where('payment_transaction_id', $regularization->regularization_id)->where('tenant_id', tenant()->id)->first();
        if ($transactionExists && $paymentTransaction->approval_status !== 'approved') {
            $transactionExists->update(
                [
                    'payment_status' => 'failed',
                    'approval_status' => 'declined',
                ]
            );
        }



        $settings = basicSystemInfo();

        // update balance
        if (!$paymentTransaction) {
            $paymentTransaction = $transactionExists;
        }

        if ($paymentTransaction) {
            $wasBalanceUpdated = $paymentTransaction->balance_update_status === 'done';
            $shouldBalanceBeUpdated = $regularization->approval_status === 'approved' && $regularization->update_balance == true;


            if (!$wasBalanceUpdated && $shouldBalanceBeUpdated) {

                // Money has never been added, but should now be added.
                $settings->increment('account_balance', $paymentTransaction->amount_paid);

                $paymentTransaction->update([
                    'amount_settled' => $paymentTransaction->amount_paid,
                    'balance_update_status' => 'done',
                ]);
            } elseif ($wasBalanceUpdated && !$shouldBalanceBeUpdated) {

                // Money was previously added but should now be removed.
                $settings->decrement('account_balance', $paymentTransaction->amount_paid);

                $paymentTransaction->update([
                    'amount_settled' => 0,
                    'balance_update_status' => 'cancelled',
                ]);
            }
        }

        return redirect()->back()->with('success', 'done!');
    }
    public function paymentWaiverRequests(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $paymentWaivers = '';
        $totalPaymentWaivers = 0;


        if (Route::currentRouteName() == 'paymentWaiverRequestsSearch') {
            $searchKey = $request->input('searchKey');
            $paymentWaivers = PaymentWaiver::with([
                'due',
                'personalProfile',
            ])
                ->when($searchKey, function ($query) use ($searchKey) {

                    $keyword = "%{$searchKey}%";

                    $query->where(function ($q) use ($keyword) {

                        // PaymentRegularization fields
                        $q->where('reason', 'like', $keyword)
                            ->orWhere('due_id', 'like', $keyword)
                            ->orWhere('approval_status', 'like', $keyword)

                            // Due
                            ->orWhereHas('due', function ($dueQuery) use ($keyword) {
                                $dueQuery->where('title', 'like', $keyword)
                                    ->orWhere('fee_id', 'like', $keyword)
                                    ->orWhere('description', 'like', $keyword);
                            })

                            // Personal profile
                            ->orWhereHas('personalProfile', function ($profileQuery) use ($keyword) {
                                $profileQuery->where('phone1', 'like', $keyword)
                                    ->orWhere('email1', 'like', $keyword)
                                    ->orWhere('phone2', 'like', $keyword)
                                    ->orWhere('email2', 'like', $keyword)
                                    ->orWhere('first_name', 'like', $keyword)
                                    ->orWhere('middle_name', 'like', $keyword)
                                    ->orWhere('last_name', 'like', $keyword)
                                    ->orWhere('gender', 'like', $keyword)
                                    ->orWhere('nationality', 'like', $keyword)
                                    ->orWhere('state_of_origin', 'like', $keyword)
                                    ->orWhere('lga_of_origin', 'like', $keyword);
                            });
                    });
                })
                ->orderByRaw("CASE WHEN approval_status = 'pending' THEN 0 ELSE 1 END")
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString();

            $totalPaymentWaivers = $paymentWaivers->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $paymentWaivers = PaymentWaiver::query()
                ->orderByRaw("CASE WHEN approval_status = 'pending' THEN 0 ELSE 1 END")
                ->orderByDesc('created_at')
                ->paginate(20);
            $totalPaymentWaivers = PaymentWaiver::count();
            $requestType = 'all';
            $pageTitle = 'Payment Regularization';
        }
        $data = [
            'paymentWaivers' => $paymentWaivers,
            'totalPaymentWaivers' => $totalPaymentWaivers,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.finance.paymentWaivers.paymentWaivers', compact('data'));
    }
    public function viewPaymentWaiver($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.read'),
            403
        );

        $waiver = PaymentWaiver::where('uuid', $uuid)->first();
        if (!$waiver) {
            return redirect()->back()->with('error', 'Waiver not found.');
        }

        $data = [
            'waiver' => $waiver,
        ];
        return view('users.admin.finance.paymentWaivers.viewPaymentWaiver', compact('data'));
    }
    public function processPaymentWaiverSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.update'),
            403
        );

        $waiver = PaymentWaiver::where('uuid', $uuid)->first();
        if (!$waiver) {
            return redirect()->back()->with('error', 'Waiver not found.');
        }
        $request->validate([
            'approval_status' => 'required|string|in:approved,declined',
        ]);

        if ($waiver->initiated_by === $user->username) {
            return redirect()->back()->with(
                'error',
                'You cannot approve a payment regularization request that you initiated. Kindly contact another executive to approve this request.'
            );
        }

        $waiver->update([
            'approval_status' => $request->approval_status,
            'processed_by' => Auth::user()->username,
            'approved_at' => Carbon::now(),

        ]);
        return redirect()->back()->with('success', 'done!');
    }
    public function verifyAssociationDuePaymentWaiver($uuid)
    {
        $waiver = PaymentWaiver::with([
            'memberAcademicInfo.theProgram',
            'memberAcademicInfo.programType',
            'memberAcademicInfo.theFaculty',
            'memberAcademicInfo.theDepartment',
            'memberAcademicInfo.approvedReAdmissions.previousProgram',
            'memberAcademicInfo.approvedReAdmissions.previousProgramType',
            'memberAcademicInfo.approvedReAdmissions.previousFaculty',
            'memberAcademicInfo.approvedReAdmissions.previousDepartment',
        ])->where('uuid', $uuid)->first();
        if (!$waiver) {
            return redirect()->back()->with('error', 'Record not found!');
        }

        $memberDue = $waiver->memberAcademicInfo
            ->associationDues()
            ->firstWhere('uuid', $waiver->due->uuid);

        if (!$memberDue) {
            $memberDue = $waiver->memberAcademicInfo
                ->pastProgramsAssociationDues()
                ->flatten(1)
                ->firstWhere('uuid', $waiver->due->uuid);
        }

        if (!$memberDue) {
            return redirect()->back()->with(
                'error',
                'This due does not apply to this member.'
            );
        }

        $due = $memberDue;
        $due->payable_amount;
        $data = [
            'waiver' => $waiver,
            'due' => $due,
        ];

        return view('miscellaneous.paymentVerifications.verifyWaiver', compact('data'));
    }
    public function downloadPaymentReceipt($payment_transaction_id)
    {
        $payment = PaymentTransaction::query()
            ->with([
                'payerPersonalProfile',

                'payerAcademicInfo.theProgram',
                'payerAcademicInfo.programType',
                'payerAcademicInfo.theFaculty',
                'payerAcademicInfo.theDepartment',

                'payerAcademicInfo.approvedReAdmissions.previousProgram',
                'payerAcademicInfo.approvedReAdmissions.previousProgramType',
                'payerAcademicInfo.approvedReAdmissions.previousFaculty',
                'payerAcademicInfo.approvedReAdmissions.previousDepartment',

                /*
             * Used for the photo in your old Blade:
             * $payment->registeredPayer->photo
             */
                'registeredPayer.memberAcademicInfo.theProgram',
                'registeredPayer.memberAcademicInfo.programType',
                'registeredPayer.memberAcademicInfo.theFaculty',
                'registeredPayer.memberAcademicInfo.theDepartment',

                'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousProgram',
                'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousProgramType',
                'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousFaculty',
                'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousDepartment',

                'due',
            ])
            ->where('payment_status', 'successful')
            ->where('approval_status', 'approved')->where('payment_transaction_id', $payment_transaction_id)->first();


        if (!$payment) {
            return redirect()->back()->with('error', 'Record not found!');
        }

        // if it has a due
        $due = null;
        if ($payment->due) {
            $memberDue = $payment->payerAcademicInfo
                ->associationDues()
                ->firstWhere('uuid', $payment->due->uuid);

            if (!$memberDue) {
                $memberDue = $payment->payerAcademicInfo
                    ->pastProgramsAssociationDues()
                    ->flatten(1)
                    ->firstWhere('uuid', $payment->due->uuid);
            }

            if (!$memberDue) {
                return redirect()->back()->with(
                    'error',
                    'This due does not apply to this member.'
                );
            }
            $due = $memberDue;

            $due->payable_amount;
        }

        // if it is a donation
        $amountHeContributed = null;
        $numberOfContributions = 0;
        if ($payment->donation) {
            if ($payment->donation->registeredPayer) {
                $amountHeContributed = PaymentTransaction::where('donation_id', $payment->donation->donation_id)
                    ->where('registered_payer', $payment->registered_payer)->where('payment_status', 'successful')->where('approval_status', 'approved')
                    ->sum('amount_payable');
                $numberOfContributions = PaymentTransaction::where('donation_id', $payment->donation->donation_id)
                    ->where('registered_payer', $payment->registered_payer)->where('payment_status', 'successful')->where('approval_status', 'approved')
                    ->count();
            } else {
                $amountHeContributed = PaymentTransaction::where('donation_id', $payment->donation->donation_id)
                    ->where('non_registered_payer', $payment->non_registered_payer)->where('payment_status', 'successful')->where('approval_status', 'approved')
                    ->sum('amount_payable');
                $numberOfContributions = PaymentTransaction::where('donation_id', $payment->donation->donation_id)
                    ->where('non_registered_payer', $payment->non_registered_payer)->where('payment_status', 'successful')->where('approval_status', 'approved')
                    ->count();
            }
        }



        ini_set('pcre.backtrack_limit', 10000000);
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');


        $mpdf = new Mpdf([
            'default_font_size'  => 10,
            'default_font'       => 'Arial',
            'margin_left'        => 5,
            'margin_right'       => 5,
            'margin_top'         => 10,
            'margin_bottom'      => 15,
            'format'             => 'A4',
            'autoLangToFont'     => true,
            'autoScriptToLang'   => true,
            'tempDir'            => storage_path('app/mpdf-temp'),
        ]);

        $mpdf->setFooter('Page {PAGENO} of {nb} >> Payment Transaction :: ' . $payment->payment_transaction_id);
        $mpdf->showImageErrors = true;

        $basicSystemInfo = basicSystemInfo();

        $data = [
            'due' => $due,
            'basicSystemInfo' => $basicSystemInfo,
            'tenant' => tenant(),
            'payment' => $payment,
            'amountHeContributed' => $amountHeContributed,
            'numberOfContributions' => $numberOfContributions,
        ];


        $html = view('printFiles.paymentTransactions.paymentReceipt', compact('data'))->render();

        $mpdf->WriteHTML($html); // AddPage() not needed — WriteHTML handles it

        $fileName = 'Payment Transaction :: ' . $payment->payment_transaction_id . '_' . date('Y-m-d_H-i-s') . '.pdf';

        while (ob_get_level()) ob_end_clean();

        $mpdf->Output($fileName, 'D'); // 'I' for inline, 'D' for download
        exit;
    }
    public function financialReports(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.read'),
            403
        );

        $request->validate([
            'start_date' => [
                'nullable',
                'date',
                'before:end_date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('end_date')) {
                        $start = Carbon::parse($value);
                        $end   = Carbon::parse($request->end_date);

                        // Allow max gap of 1 year and 3 months (15 months)
                        if ($start->copy()->addMonths(15)->lt($end)) {
                            $fail('The date gap must not be more than 1 year and 3 months.');
                        }
                    }
                },
            ],
            'end_date' => [
                'nullable',
                'date',
                'after:start_date',
            ],
        ]);



        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : now();

        // ── Base queries ──────────────────────────────────────────────────────

        $paymentQuery = PaymentTransaction::where('tenant_id', tenant()->id)
            ->whereBetween('paid_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ]);

        // Successful + approved payments only — used for financial totals
        $successfulPaymentQuery = (clone $paymentQuery)
            ->where('payment_status', 'successful')
            ->where('approval_status', 'approved');

        // Pending/failed payments — for display only, NOT included in totals
        $pendingFailedPaymentQuery = (clone $paymentQuery)
            ->where(function ($q) {
                $q->where('payment_status', '!=', 'successful')
                    ->orWhere('approval_status', '!=', 'approved');
            });

        $financialClaimQuery = FinancialClaim::where('tenant_id', tenant()->id)
            ->whereBetween('date', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ]);

        // Approved claims only — used for financial totals
        $approvedClaimQuery = (clone $financialClaimQuery)
            ->where('approval_status', 'approved');

        // Unapproved claims — for display only, NOT included in totals
        $unapprovedClaimQuery = (clone $financialClaimQuery)
            ->where('approval_status', '!=', 'approved');

        /*
    |--------------------------------------------------------------------------
    | Payment Transaction Totals (successful + approved only)
    |--------------------------------------------------------------------------
    */
        $totalAmountPaid          = (clone $successfulPaymentQuery)->sum('amount_paid');
        $totalPaymentTransactions = (clone $successfulPaymentQuery)->count();
        $totalTax                 = (clone $successfulPaymentQuery)->sum('total_tax');
        $totalTaxTransactions     = (clone $successfulPaymentQuery)->where('total_tax', '>', 0)->count();
        $totalServiceCharge       = (clone $successfulPaymentQuery)->sum('service_charge');
        $totalAmountSettled       = (clone $successfulPaymentQuery)->sum('amount_settled');

        /*
    |--------------------------------------------------------------------------
    | Direct Payments (successful + approved only)
    |--------------------------------------------------------------------------
    */
        $totalDirectPaymentCount  = (clone $successfulPaymentQuery)->where('payment_type', 'direct')->count();
        $totalDirectPaymentAmount = (clone $successfulPaymentQuery)->where('payment_type', 'direct')->sum('amount_settled');

        /*
    |--------------------------------------------------------------------------
    | Regularized Payments (successful + approved only)
    |--------------------------------------------------------------------------
    */
        $totalRegularizedPaymentCount  = (clone $successfulPaymentQuery)->where('payment_type', 'regularization')->count();
        $totalRegularizedPaymentAmount = (clone $successfulPaymentQuery)->where('payment_type', 'regularization')->sum('amount_settled');

        /*
    |--------------------------------------------------------------------------
    | Pending / Failed Payments (display only — NOT in totals)
    |--------------------------------------------------------------------------
    */
        $totalPendingFailedCount  = (clone $pendingFailedPaymentQuery)->count();
        $totalPendingFailedAmount = (clone $pendingFailedPaymentQuery)->sum('amount_paid');

        $totalPendingCount = (clone $paymentQuery)
            ->where('payment_status', 'pending')
            ->count();

        $totalFailedCount = (clone $paymentQuery)
            ->whereNotIn('payment_status', ['successful', 'pending'])
            ->count();

        /*
    |--------------------------------------------------------------------------
    | Financial Claims (approved only — used in totals)
    |--------------------------------------------------------------------------
    */
        $totalCreditFinClaim      = (clone $approvedClaimQuery)->where('claim_type', 'credit')->sum('amount');
        $totalCreditFinClaimCount = (clone $approvedClaimQuery)->where('claim_type', 'credit')->count();
        $totalDebitFinClaim       = (clone $approvedClaimQuery)->where('claim_type', 'debit')->sum('amount');
        $totalDebitFinClaimCount  = (clone $approvedClaimQuery)->where('claim_type', 'debit')->count();

        /*
    |--------------------------------------------------------------------------
    | Unapproved Claims (display only — NOT in totals)
    |--------------------------------------------------------------------------
    */
        $totalUnapprovedClaimCount  = (clone $unapprovedClaimQuery)->count();
        $totalUnapprovedClaimAmount = (clone $unapprovedClaimQuery)->sum('amount');

        $totalPendingClaimCount = (clone $financialClaimQuery)
            ->where('approval_status', 'pending')
            ->count();

        $totalRejectedClaimCount = (clone $financialClaimQuery)
            ->where('approval_status', 'declined')
            ->count();

        /*
    |--------------------------------------------------------------------------
    | Paginated Records
    |--------------------------------------------------------------------------
    */
        // $paymentTransactions = (clone $successfulPaymentQuery)
        //     ->orderByDesc('paid_at')
        //     ->paginate(20)
        //     ->withQueryString();

        $paymentTransactions = PaymentTransaction::whereBetween('paid_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay(),
        ])
            ->orderByDesc('paid_at')
            ->paginate(20)
            ->withQueryString();

        $pendingFailedTransactions = (clone $pendingFailedPaymentQuery)
            ->orderByDesc('paid_at')
            ->paginate(20, ['*'], 'pf_page')
            ->withQueryString();

        $pendingFailedTransactions = (clone $pendingFailedPaymentQuery)
            ->orderByDesc('paid_at')
            ->paginate(20, ['*'], 'pf_page')
            ->withQueryString();

        // $financialClaim = (clone $approvedClaimQuery)
        //     ->orderByDesc('initiated_at')
        //     ->paginate(20)
        //     ->withQueryString();

        $financialClaims = FinancialClaim::whereBetween('date', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay(),
        ])
            ->orderByDesc('initiated_at')
            ->paginate(20)
            ->withQueryString();

        $unapprovedClaims = (clone $unapprovedClaimQuery)
            ->orderByDesc('initiated_at')
            ->paginate(20, ['*'], 'uc_page')
            ->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | Chart Grouping
    |--------------------------------------------------------------------------
    */
        $days = $startDate->diffInDays($endDate);

        if ($days <= 31) {
            $chartGrouping = 'daily';
        } elseif ($days <= 90) {
            $chartGrouping = 'weekly';
        } else {
            $chartGrouping = 'monthly';
        }

        /*
    |--------------------------------------------------------------------------
    | Chart Data Queries (successful + approved only for accuracy)
    |--------------------------------------------------------------------------
    */
        $paymentIncome = (clone $successfulPaymentQuery)
            ->selectRaw("DATE(paid_at) as period, SUM(amount_settled) as amount")
            ->groupBy('period')
            ->pluck('amount', 'period');

        $paymentExpenses = (clone $successfulPaymentQuery)
            ->selectRaw("DATE(paid_at) as period, SUM(service_charge) as amount")
            ->groupBy('period')
            ->pluck('amount', 'period');

        $creditClaims = (clone $approvedClaimQuery)
            ->where('claim_type', 'credit')
            ->selectRaw("DATE(initiated_at) as period, SUM(amount) as amount")
            ->groupBy('period')
            ->pluck('amount', 'period');

        $debitClaims = (clone $approvedClaimQuery)
            ->where('claim_type', 'debit')
            ->selectRaw("DATE(initiated_at) as period, SUM(amount) as amount")
            ->groupBy('period')
            ->pluck('amount', 'period');

        /*
    |--------------------------------------------------------------------------
    | Build Chart Data
    |--------------------------------------------------------------------------
    */
        $chartData = collect();

        if ($chartGrouping === 'daily') {
            $period = $startDate->copy()->startOfDay();
            while ($period->lte($endDate)) {
                $key = $period->format('Y-m-d');
                $chartData->push([
                    'label'    => $period->format('d M'),
                    'income'   => (float) ($paymentIncome->get($key, 0) + $creditClaims->get($key, 0)),
                    'expenses' => (float) ($paymentExpenses->get($key, 0) + $debitClaims->get($key, 0)),
                ]);
                $period->addDay();
            }
        } elseif ($chartGrouping === 'weekly') {
            $period = $startDate->copy()->startOfWeek();
            while ($period->lte($endDate)) {
                $weekStart = $period->copy()->startOfWeek();
                $weekEnd   = $period->copy()->endOfWeek();
                $income = $expenses = 0;

                foreach ($paymentIncome  as $date => $amount) {
                    if (Carbon::parse($date)->between($weekStart, $weekEnd)) $income   += (float) $amount;
                }
                foreach ($creditClaims   as $date => $amount) {
                    if (Carbon::parse($date)->between($weekStart, $weekEnd)) $income   += (float) $amount;
                }
                foreach ($paymentExpenses as $date => $amount) {
                    if (Carbon::parse($date)->between($weekStart, $weekEnd)) $expenses += (float) $amount;
                }
                foreach ($debitClaims    as $date => $amount) {
                    if (Carbon::parse($date)->between($weekStart, $weekEnd)) $expenses += (float) $amount;
                }

                $chartData->push([
                    'label'    => $weekStart->format('d M') . ' - ' . $weekEnd->format('d M'),
                    'income'   => $income,
                    'expenses' => $expenses,
                ]);
                $period->addWeek();
            }
        } else {
            $period = $startDate->copy()->startOfMonth();
            $end    = $endDate->copy()->startOfMonth();

            while ($period->lte($end)) {
                $monthStart = $period->copy()->startOfMonth();
                $monthEnd   = $period->copy()->endOfMonth();
                $income = $expenses = 0;

                foreach ($paymentIncome   as $date => $amount) {
                    if (Carbon::parse($date)->between($monthStart, $monthEnd)) $income   += (float) $amount;
                }
                foreach ($creditClaims    as $date => $amount) {
                    if (Carbon::parse($date)->between($monthStart, $monthEnd)) $income   += (float) $amount;
                }
                foreach ($paymentExpenses as $date => $amount) {
                    if (Carbon::parse($date)->between($monthStart, $monthEnd)) $expenses += (float) $amount;
                }
                foreach ($debitClaims     as $date => $amount) {
                    if (Carbon::parse($date)->between($monthStart, $monthEnd)) $expenses += (float) $amount;
                }

                $chartData->push([
                    'label'    => $period->format('M Y'),
                    'income'   => $income,
                    'expenses' => $expenses,
                ]);
                $period->addMonth();
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Data
    |--------------------------------------------------------------------------
    */
        //
        //

        if ($request->page_type == 'claims_page') {
            $pageType = 'claims_page';
        } else {
            $pageType = 'transactions_page';
        }

        $data = [
            // ── Successful payment totals ──────────────────────────────────
            'totalAmountPaid'              => $totalAmountPaid,
            'totalPaymentTransactions'     => $totalPaymentTransactions,
            'totalTax'                     => $totalTax,
            'totalTaxTransactions'         => $totalTaxTransactions,
            'totalServiceCharge'           => $totalServiceCharge,
            'totalAmountSettled'           => $totalAmountSettled,
            'totalDirectPaymentCount'      => $totalDirectPaymentCount,
            'totalDirectPaymentAmount'     => $totalDirectPaymentAmount,
            'totalRegularizedPaymentCount' => $totalRegularizedPaymentCount,
            'totalRegularizedPaymentAmount' => $totalRegularizedPaymentAmount,
            'pageType' => $pageType,

            // ── Pending / failed payments (display only) ───────────────────
            'totalPendingFailedCount'      => $totalPendingFailedCount,
            'totalPendingFailedAmount'     => $totalPendingFailedAmount,
            'totalPendingCount'            => $totalPendingCount,
            'totalFailedCount'             => $totalFailedCount,

            // ── Approved financial claim totals ────────────────────────────
            'totalCreditFinClaim'          => $totalCreditFinClaim,
            'totalCreditFinClaimCount'     => $totalCreditFinClaimCount,
            'totalDebitFinClaim'           => $totalDebitFinClaim,
            'totalDebitFinClaimCount'      => $totalDebitFinClaimCount,

            // ── Unapproved claims (display only) ──────────────────────────
            'totalUnapprovedClaimCount'    => $totalUnapprovedClaimCount,
            'totalUnapprovedClaimAmount'   => $totalUnapprovedClaimAmount,
            'totalPendingClaimCount'       => $totalPendingClaimCount,
            'totalRejectedClaimCount'      => $totalRejectedClaimCount,

            // ── Paginated records ──────────────────────────────────────────
            'paymentTransactions'          => $paymentTransactions,
            'pendingFailedTransactions'    => $pendingFailedTransactions,
            'financialClaims'               => $financialClaims,
            'unapprovedClaims'             => $unapprovedClaims,

            // ── Date range ────────────────────────────────────────────────
            'startDate'                    => $startDate,
            'endDate'                      => $endDate,

            // ── Chart ─────────────────────────────────────────────────────
            'chartData'                    => $chartData,
            'chartGrouping'                => $chartGrouping,
        ];

        return view('users.admin.finance.reports.financialReports', compact('data'));
    }
    public function downloadFinancialReports(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('transactions.read'),
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Date validation
    |--------------------------------------------------------------------------
    */
        $request->validate([
            'start_date' => [
                'nullable',
                'date',
                'before_or_equal:end_date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('end_date')) {
                        $startDate = Carbon::parse($value);
                        $endDate = Carbon::parse($request->end_date);

                        if ($startDate->copy()->addYear()->lt($endDate)) {
                            $fail('The date gap must not be more than one year.');
                        }
                    }
                },
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Date range
    |--------------------------------------------------------------------------
    */
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfDay();

        $tenantId = tenant()->id;

        /*
    |--------------------------------------------------------------------------
    | Runtime configuration
    |--------------------------------------------------------------------------
    */
        ini_set('pcre.backtrack_limit', 10000000);
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        /*
    |--------------------------------------------------------------------------
    | mPDF temporary directory
    |--------------------------------------------------------------------------
    */
        $tempDirectory = storage_path('app/mpdf-temp');

        if (!is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        /*
    |--------------------------------------------------------------------------
    | Base payment query
    |--------------------------------------------------------------------------
    */
        $paymentQuery = PaymentTransaction::query()
            ->where('tenant_id', $tenantId)
            ->whereBetween('paid_at', [
                $startDate,
                $endDate,
            ]);

        $successfulPaymentQuery = (clone $paymentQuery)
            ->where('payment_status', 'successful')
            ->where('approval_status', 'approved');

        $pendingFailedPaymentQuery = (clone $paymentQuery)
            ->where(function ($query) {
                $query->where('payment_status', '!=', 'successful')
                    ->orWhere('approval_status', '!=', 'approved');
            });

        /*
    |--------------------------------------------------------------------------
    | Base financial claims query
    |--------------------------------------------------------------------------
    */
        $financialClaimQuery = FinancialClaim::query()
            ->where('tenant_id', $tenantId)
            ->whereBetween('date', [
                $startDate,
                $endDate,
            ]);

        $approvedClaimQuery = (clone $financialClaimQuery)
            ->where('approval_status', 'approved');

        $unapprovedClaimQuery = (clone $financialClaimQuery)
            ->where('approval_status', '!=', 'approved');

        /*
    |--------------------------------------------------------------------------
    | Totals
    |--------------------------------------------------------------------------
    |
    | All values below come from SQL aggregate queries.
    | No transaction records are loaded into PHP memory here.
    |
    */
        $totalAmountPaid = (float) (
            (clone $successfulPaymentQuery)->sum('amount_paid')
        );

        $totalPaymentTransactions = (clone $successfulPaymentQuery)->count();

        $totalTax = (float) (
            (clone $successfulPaymentQuery)->sum('total_tax')
        );

        $totalTaxTransactions = (clone $successfulPaymentQuery)
            ->where('total_tax', '>', 0)
            ->count();

        $totalServiceCharge = (float) (
            (clone $successfulPaymentQuery)->sum('service_charge')
        );

        $totalAmountSettled = (float) (
            (clone $successfulPaymentQuery)->sum('amount_settled')
        );

        $totalDirectPaymentCount = (clone $successfulPaymentQuery)
            ->where('payment_type', 'direct')
            ->count();

        $totalDirectPaymentAmount = (float) (
            (clone $successfulPaymentQuery)
            ->where('payment_type', 'direct')
            ->sum('amount_settled')
        );

        $totalRegularizedPaymentCount = (clone $successfulPaymentQuery)
            ->where('payment_type', 'regularization')
            ->count();

        $totalRegularizedPaymentAmount = (float) (
            (clone $successfulPaymentQuery)
            ->where('payment_type', 'regularization')
            ->sum('amount_settled')
        );

        $totalPendingFailedCount = (clone $pendingFailedPaymentQuery)->count();

        $totalPendingFailedAmount = (float) (
            (clone $pendingFailedPaymentQuery)->sum('amount_paid')
        );

        $totalPendingCount = (clone $paymentQuery)
            ->where('payment_status', 'pending')
            ->count();

        $totalFailedCount = (clone $paymentQuery)
            ->whereNotIn('payment_status', ['successful', 'pending'])
            ->count();

        $totalCreditFinClaim = (float) (
            (clone $approvedClaimQuery)
            ->where('claim_type', 'credit')
            ->sum('amount')
        );

        $totalCreditFinClaimCount = (clone $approvedClaimQuery)
            ->where('claim_type', 'credit')
            ->count();

        $totalDebitFinClaim = (float) (
            (clone $approvedClaimQuery)
            ->where('claim_type', 'debit')
            ->sum('amount')
        );

        $totalDebitFinClaimCount = (clone $approvedClaimQuery)
            ->where('claim_type', 'debit')
            ->count();

        $totalUnapprovedClaimCount = (clone $unapprovedClaimQuery)->count();

        $totalUnapprovedClaimAmount = (float) (
            (clone $unapprovedClaimQuery)->sum('amount')
        );

        $totalPendingClaimCount = (clone $financialClaimQuery)
            ->where('approval_status', 'pending')
            ->count();

        $totalRejectedClaimCount = (clone $financialClaimQuery)
            ->where('approval_status', 'declined')
            ->count();

        /*
    |--------------------------------------------------------------------------
    | mPDF setup
    |--------------------------------------------------------------------------
    */
        $mpdf = new Mpdf([
            'default_font_size' => 8,
            'default_font' => 'Arial',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 10,
            'margin_bottom' => 15,
            'format' => 'A4-L',
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
            'tempDir' => $tempDirectory,
        ]);

        $mpdf->setFooter(
            'Page {PAGENO} of {nb} >> Financial Report: '
                . $startDate->format('d M Y')
                . ' - '
                . $endDate->format('d M Y')
        );

        $mpdf->showImageErrors = true;

        /*
    |--------------------------------------------------------------------------
    | Shared data only
    |--------------------------------------------------------------------------
    |
    | No payment or claim Collections should be stored here.
    |
    */
        $currentAccountBalance = basicSystemInfo()->account_balance;
        $data = [
            'tenant' => tenant(),
            'basicSystemInfo' => basicSystemInfo(),

            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now('Africa/Lagos'),

            'totalAmountPaid' => $totalAmountPaid,
            'totalPaymentTransactions' => $totalPaymentTransactions,
            'totalTax' => $totalTax,
            'totalTaxTransactions' => $totalTaxTransactions,
            'totalServiceCharge' => $totalServiceCharge,
            'totalAmountSettled' => $totalAmountSettled,

            'totalDirectPaymentCount' => $totalDirectPaymentCount,
            'totalDirectPaymentAmount' => $totalDirectPaymentAmount,

            'totalRegularizedPaymentCount' => $totalRegularizedPaymentCount,
            'totalRegularizedPaymentAmount' => $totalRegularizedPaymentAmount,

            'totalPendingFailedCount' => $totalPendingFailedCount,
            'totalPendingFailedAmount' => $totalPendingFailedAmount,
            'totalPendingCount' => $totalPendingCount,
            'totalFailedCount' => $totalFailedCount,

            'totalCreditFinClaim' => $totalCreditFinClaim,
            'totalCreditFinClaimCount' => $totalCreditFinClaimCount,
            'totalDebitFinClaim' => $totalDebitFinClaim,
            'totalDebitFinClaimCount' => $totalDebitFinClaimCount,

            'totalUnapprovedClaimCount' => $totalUnapprovedClaimCount,
            'totalUnapprovedClaimAmount' => $totalUnapprovedClaimAmount,
            'totalPendingClaimCount' => $totalPendingClaimCount,
            'totalRejectedClaimCount' => $totalRejectedClaimCount,

            'currentAccountBalance' => $currentAccountBalance,
        ];

        /*
    |--------------------------------------------------------------------------
    | Write CSS once
    |--------------------------------------------------------------------------
    */
        $css = view(
            'printFiles.paymentTransactions.financialReportPartials.financialReportStyles',
            compact('data')
        )->render();

        $mpdf->WriteHTML(
            $css,
            HTMLParserMode::HEADER_CSS
        );

        unset($css);

        /*
    |--------------------------------------------------------------------------
    | Write report header / totals once
    |--------------------------------------------------------------------------
    */
        $headerHtml = view(
            'printFiles.paymentTransactions.financialReportPartials.financialReportHeader',
            compact('data')
        )->render();

        $mpdf->WriteHTML(
            $headerHtml,
            HTMLParserMode::HTML_BODY
        );

        unset($headerHtml);

        /*
    |--------------------------------------------------------------------------
    | Section 1: Successful approved transactions
    |--------------------------------------------------------------------------
    */
        $mpdf->WriteHTML(
            view(
                'printFiles.paymentTransactions.financialReportPartials.financialSectionHeading',
                [
                    'title' => 'SUCCESSFUL AND APPROVED PAYMENT TRANSACTIONS',
                    'colour' => '#198754',
                ]
            )->render(),
            HTMLParserMode::HTML_BODY
        );

        $successfulSerialNumber = 0;
        $successfulCount = $totalPaymentTransactions;

        if ($successfulCount === 0) {
            $mpdf->WriteHTML(
                '<div class="no-records">No successful and approved payment transactions were found.</div>',
                HTMLParserMode::HTML_BODY
            );
        } else {
            /*
         * Use the actual numeric primary key of payment_transactions.
         * Change 'id' to 'payment_id' if your table uses payment_id.
         */
            $successfulPaymentQuery
                ->with([
                    'payerPersonalProfile',
                ])
                ->orderBy('id')
                ->chunkById(
                    250,
                    function ($payments) use (
                        $data,
                        $mpdf,
                        &$successfulSerialNumber
                    ) {
                        $html = view(
                            'printFiles.paymentTransactions.financialReportPartials.successfulPaymentChunk',
                            [
                                'data' => $data,
                                'payments' => $payments,
                                'serialStart' => $successfulSerialNumber,
                            ]
                        )->render();

                        $mpdf->WriteHTML(
                            $html,
                            HTMLParserMode::HTML_BODY
                        );

                        $successfulSerialNumber += $payments->count();

                        unset($html, $payments);

                        gc_collect_cycles();
                    },
                    'id'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Section 2: Pending / failed transactions
    |--------------------------------------------------------------------------
    */
        $mpdf->WriteHTML(
            '<pagebreak />',
            HTMLParserMode::HTML_BODY
        );

        $mpdf->WriteHTML(
            view(
                'printFiles.paymentTransactions.financialReportPartials.financialSectionHeading',
                [
                    'title' => 'PENDING OR FAILED PAYMENT TRANSACTIONS',
                    'colour' => '#dc2626',
                ]
            )->render(),
            HTMLParserMode::HTML_BODY
        );

        $pendingFailedSerialNumber = 0;

        if ($totalPendingFailedCount === 0) {
            $mpdf->WriteHTML(
                '<div class="no-records">No pending or failed payment transactions were found.</div>',
                HTMLParserMode::HTML_BODY
            );
        } else {
            $pendingFailedPaymentQuery
                ->with([
                    'payerPersonalProfile',
                ])
                ->orderBy('id')
                ->chunkById(
                    250,
                    function ($payments) use (
                        $data,
                        $mpdf,
                        &$pendingFailedSerialNumber
                    ) {
                        $html = view(
                            'printFiles.paymentTransactions.financialReportPartials.pendingFailedPaymentChunk',
                            [
                                'data' => $data,
                                'payments' => $payments,
                                'serialStart' => $pendingFailedSerialNumber,
                            ]
                        )->render();

                        $mpdf->WriteHTML(
                            $html,
                            HTMLParserMode::HTML_BODY
                        );

                        $pendingFailedSerialNumber += $payments->count();

                        unset($html, $payments);

                        gc_collect_cycles();
                    },
                    'id'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Section 3: Approved financial claims
    |--------------------------------------------------------------------------
    */
        $mpdf->WriteHTML(
            '<pagebreak />',
            HTMLParserMode::HTML_BODY
        );

        $mpdf->WriteHTML(
            view(
                'printFiles.paymentTransactions.financialReportPartials.financialSectionHeading',
                [
                    'title' => 'APPROVED FINANCIAL CLAIMS',
                    'colour' => '#2563eb',
                ]
            )->render(),
            HTMLParserMode::HTML_BODY
        );

        $approvedClaimSerialNumber = 0;
        $approvedClaimCount = (clone $approvedClaimQuery)->count();

        if ($approvedClaimCount === 0) {
            $mpdf->WriteHTML(
                '<div class="no-records">No approved financial claims were found.</div>',
                HTMLParserMode::HTML_BODY
            );
        } else {
            /*
         * Change 'id' if FinancialClaim uses another numeric primary key.
         */
            $approvedClaimQuery
                ->orderBy('id')
                ->chunkById(
                    250,
                    function ($claims) use (
                        $data,
                        $mpdf,
                        &$approvedClaimSerialNumber
                    ) {
                        $html = view(
                            'printFiles.paymentTransactions.financialReportPartials.approvedClaimChunk',
                            [
                                'data' => $data,
                                'claims' => $claims,
                                'serialStart' => $approvedClaimSerialNumber,
                            ]
                        )->render();

                        $mpdf->WriteHTML(
                            $html,
                            HTMLParserMode::HTML_BODY
                        );

                        $approvedClaimSerialNumber += $claims->count();

                        unset($html, $claims);

                        gc_collect_cycles();
                    },
                    'id'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Section 4: Unapproved financial claims
    |--------------------------------------------------------------------------
    */
        $mpdf->WriteHTML(
            '<pagebreak />',
            HTMLParserMode::HTML_BODY
        );

        $mpdf->WriteHTML(
            view(
                'printFiles.paymentTransactions.financialReportPartials.financialSectionHeading',
                [
                    'title' => 'PENDING OR DECLINED FINANCIAL CLAIMS',
                    'colour' => '#d97706',
                ]
            )->render(),
            HTMLParserMode::HTML_BODY
        );

        $unapprovedClaimSerialNumber = 0;

        if ($totalUnapprovedClaimCount === 0) {
            $mpdf->WriteHTML(
                '<div class="no-records">No unapproved financial claims were found.</div>',
                HTMLParserMode::HTML_BODY
            );
        } else {
            $unapprovedClaimQuery
                ->orderBy('id')
                ->chunkById(
                    250,
                    function ($claims) use (
                        $data,
                        $mpdf,
                        &$unapprovedClaimSerialNumber
                    ) {
                        $html = view(
                            'printFiles.paymentTransactions.financialReportPartials.unapprovedClaimChunk',
                            [
                                'data' => $data,
                                'claims' => $claims,
                                'serialStart' => $unapprovedClaimSerialNumber,
                            ]
                        )->render();

                        $mpdf->WriteHTML(
                            $html,
                            HTMLParserMode::HTML_BODY
                        );

                        $unapprovedClaimSerialNumber += $claims->count();

                        unset($html, $claims);

                        gc_collect_cycles();
                    },
                    'id'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | PDF filename
    |--------------------------------------------------------------------------
    */
        $fileName = 'financial-report-'
            . $startDate->format('Y-m-d')
            . '-to-'
            . $endDate->format('Y-m-d')
            . '-'
            . now('Africa/Lagos')->format('H-i-s')
            . '.pdf';

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        /*
    |--------------------------------------------------------------------------
    | Inline PDF view
    |--------------------------------------------------------------------------
    */
        $mpdf->Output($fileName, 'D');

        exit;
    }
    public function verifyPaymentReceipt($payment_transaction_id)
    {
        $payment = PaymentTransaction::query()
            ->with([
                'payerPersonalProfile',

                'payerAcademicInfo.theProgram',
                'payerAcademicInfo.programType',
                'payerAcademicInfo.theFaculty',
                'payerAcademicInfo.theDepartment',

                'payerAcademicInfo.approvedReAdmissions.previousProgram',
                'payerAcademicInfo.approvedReAdmissions.previousProgramType',
                'payerAcademicInfo.approvedReAdmissions.previousFaculty',
                'payerAcademicInfo.approvedReAdmissions.previousDepartment',

                /*
             * Used for the photo in your old Blade:
             * $payment->registeredPayer->photo
             */
                'registeredPayer.memberAcademicInfo.theProgram',
                'registeredPayer.memberAcademicInfo.programType',
                'registeredPayer.memberAcademicInfo.theFaculty',
                'registeredPayer.memberAcademicInfo.theDepartment',

                'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousProgram',
                'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousProgramType',
                'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousFaculty',
                'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousDepartment',

                'due',
            ])
            ->where('payment_status', 'successful')
            ->where('approval_status', 'approved')->where('payment_transaction_id', $payment_transaction_id)->first();


        if (!$payment) {
            return redirect()->back()->with('error', 'Record not found!');
        }


        // if it is a donation
        $amountHeContributed = null;
        $numberOfContributions = 0;
        if ($payment->donation) {
            if ($payment->donation->registeredPayer) {
                $amountHeContributed = PaymentTransaction::where('donation_id', $payment->donation->donation_id)
                    ->where('registered_payer', $payment->registered_payer)->where('payment_status', 'successful')->where('approval_status', 'approved')
                    ->sum('amount_payable');
                $numberOfContributions = PaymentTransaction::where('donation_id', $payment->donation->donation_id)
                    ->where('registered_payer', $payment->registered_payer)->where('payment_status', 'successful')->where('approval_status', 'approved')
                    ->count();
            } else {
                $amountHeContributed = PaymentTransaction::where('donation_id', $payment->donation->donation_id)
                    ->where('non_registered_payer', $payment->non_registered_payer)->where('payment_status', 'successful')->where('approval_status', 'approved')
                    ->sum('amount_payable');
                $numberOfContributions = PaymentTransaction::where('donation_id', $payment->donation->donation_id)
                    ->where('non_registered_payer', $payment->non_registered_payer)->where('payment_status', 'successful')->where('approval_status', 'approved')
                    ->count();
            }
        }


        $data = [
            'payment' => $payment,
            'amountHeContributed' => $amountHeContributed,
            'numberOfContributions' => $numberOfContributions,
        ];

        return view('miscellaneous.paymentVerifications.verifyReceipt', compact('data'));
    }
}
