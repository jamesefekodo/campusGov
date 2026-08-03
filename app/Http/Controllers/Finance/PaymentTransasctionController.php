<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Config\BasicSystemSetting;
use App\Models\Finance\Dues\AssociationDue;
use App\Models\Finance\Settings\ElectronicPaymentMethod;
use App\Models\Finance\Settings\Tax;
use App\Models\Finance\Transactions\PaymentTransaction;
use Carbon\Carbon;
use Faker\Provider\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class PaymentTransasctionController extends Controller
{
    public function memberPayAssociationDue($uuid, Request $request)
    {
        $member = Auth::user();


        $due = AssociationDue::where('uuid', $uuid)->first();

        if (!$due) {
            return redirect()->back()->with('error', 'Association due not found.');
        }

        $memberDue = $member->memberAcademicInfo
            ->associationDues()
            ->firstWhere('uuid', $uuid);

        if (!$memberDue) {
            return redirect()->back()->with('error', 'This due does not apply to your account.');
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
            $transactionId = 'pymt-' . now()->format('mYds') . '-' . tenant()->id . random_int(100000000, 999999999);
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

                $uploadPath = public_path('assets/uploads/paymentTransactions/proof');
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
                $paystackApi = $pastackSettings->defaultApi;
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
                $flutterwaveApi = $flutterwaveSettings->defaultApi;
                if (!$flutterwaveApi) {
                    return redirect()->back()->with('error', 'Flutterwave config not found!');
                }

                // flutterwave version v4 start
                if ($flutterwaveApi->version == 'v4') {


                    dd('fltterwave version 4');
                }
                // flutterwave version v4 end
                // fallback to v3 start
                else {
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
                // fallback to v3 end

            }


            // if()
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
            $transactions = PaymentTransaction::with([
                'due' => function ($query) use ($searchKey) {
                    $query->where('title', 'like', "%$searchKey%")
                        ->orWhere('description', 'like', "%$searchKey%");
                },
                'registeredPayer' => function ($query) use ($searchKey) {
                    $query->where('phone1', 'like', "%$searchKey%")
                        ->orWhere('email1', 'like', "%$searchKey%")
                        ->orWhere('phone2', 'like', "%$searchKey%")
                        ->orWhere('email2', 'like', "%$searchKey%")
                        ->orWhere('first_name', 'like', "%$searchKey%")
                        ->orWhere('middle_name', 'like', "%$searchKey%")
                        ->orWhere('last_name', 'like', "%$searchKey%")
                        ->orWhere('gender', 'like', "%$searchKey%")
                        ->orWhere('nationality', 'like', "%$searchKey%")
                        ->orWhere('state_of_origin', 'like', "%$searchKey%")
                        ->orWhere('lga_of_origin', 'like', "%$searchKey%");
                }
            ])
                ->where(function ($query) use ($searchKey) {
                    $query->where('payment_transaction_id', 'like', "%$searchKey%")
                        ->orWhere('service_provider_id', 'like', "%$searchKey%")
                        ->orWhere('paystack_reference', 'like', "%$searchKey%")
                        ->orWhere('payment_mode', 'like', "%$searchKey%")
                        ->orWhere('registered_payer', 'like', "%$searchKey%")
                        ->orWhere('non_registered_payer', 'like', "%$searchKey%")
                        ->orWhere('payment_purpose', 'like', "%$searchKey%")
                        ->orWhere('electronic_payment_method', 'like', "%$searchKey%")
                        ->orWhere('manual_payment_method', 'like', "%$searchKey%")
                        ->orWhere('payment_status', 'like', "%$searchKey%")
                        ->orWhere('approval_status', 'like', "%$searchKey%");
                })
                ->orWhereHas('due', function ($query) use ($searchKey) {
                    $query->where('title', 'like', "%$searchKey%")
                        ->orWhere('description', 'like', "%$searchKey%");
                })
                ->orWhereHas('registeredPayer', function ($query) use ($searchKey) {
                    $query->where('phone1', 'like', "%$searchKey%")
                        ->orWhere('email1', 'like', "%$searchKey%")
                        ->orWhere('phone2', 'like', "%$searchKey%")
                        ->orWhere('email2', 'like', "%$searchKey%")
                        ->orWhere('first_name', 'like', "%$searchKey%")
                        ->orWhere('middle_name', 'like', "%$searchKey%")
                        ->orWhere('last_name', 'like', "%$searchKey%")
                        ->orWhere('gender', 'like', "%$searchKey%")
                        ->orWhere('nationality', 'like', "%$searchKey%")
                        ->orWhere('state_of_origin', 'like', "%$searchKey%")
                        ->orWhere('lga_of_origin', 'like', "%$searchKey%");
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(20)
                ->withQueryString();

            $totalTransactions = $transactions->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $transactions = PaymentTransaction::orderBy('updated_at', 'desc')->paginate(20);
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
        $paystackApi = $pastackSettings->defaultApi;
        if (!$paystackApi) {
            return redirect()->back()->with('error', 'Paystack method not found!');
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
        $flutterwaveApi = $flutterwaveSettings->defaultApi;
        if (!$flutterwaveApi) {
            return redirect()->back()->with('error', 'Flutterwave config not found!');
        }

        // flutterwave version v4 start
        if ($flutterwaveApi->version == 'v4') {


            dd('fltterwave version 4');
        }
        // flutterwave version v4 end
        // fallback to v3 start
        else {
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
                # code...
            } else {
                return redirect()->back()->with('error', 'Verification failed.');
            }
        }
        return redirect()->back();

        // fallback to v4 enmd
    }
}
