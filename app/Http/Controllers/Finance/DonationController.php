<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Donations\Donation;
use App\Models\Finance\Settings\ElectronicPaymentMethod;
use App\Models\Finance\Settings\Tax;
use App\Models\Finance\Transactions\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;

class DonationController extends Controller
{
    public function donations(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('donation.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $donations = '';
        $totalDonations = 0;


        if (Route::currentRouteName() == 'donationsSearch') {
            $searchKey = $request->input('searchKey');
            $donations = Donation::when($searchKey, function ($query, $searchKey) {
                return $query->where(function ($q) use ($searchKey) {
                    $q->where('title', 'like', "%$searchKey%")
                        ->orWhere('description', 'like', "%$searchKey%");
                });
            })
                ->orderBy('updated_at', 'desc')
                ->paginate(20)
                ->withQueryString();


            $totalDonations = $donations->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $donations = Donation::orderBy('updated_at', 'desc')->paginate(20);
            $totalDonations = Donation::count();
            $requestType = 'all';
            $pageTitle = 'Donations';
        }

        $data = [
            'donations' => $donations,
            'totalDonations' => $totalDonations,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];
        return view('users.admin.finance.donations.donations', compact('data'));
    }
    public function addNewDonationSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('donation.create'),
            403
        );

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'target_amount' => 'nullable',
            'deadline' => 'nullable',
        ]);

        do {
            $donationId = 'donate-' . now()->format('Ymds') . '-' . tenant()->id . random_int(100000000, 999999999);
        } while (Donation::where('donation_id', $donationId)->exists());


        Donation::create([
            'donation_id' => $donationId,
            'title' => trim($request->input('title')),
            'description' => trim($request->input('description')),
            'target_amount' => $request->input('target_amount'),
            'deadline' => $request->input('deadline') ?? null,
            'created_by' => Auth::user()->username,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function viewDonation($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('donation.read'),
            403
        );
        $donation = Donation::where('uuid', $uuid)->first();
        if (!$donation) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $data = [
            'donation' => $donation,
        ];

        return view('users.admin.finance.donations.viewDonation', compact('data'));
    }
    public function updateDonationSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('donation.update'),
            403
        );

        $donation = Donation::where('uuid', $uuid)->first();
        if (!$donation) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'target_amount' => 'nullable',
            'deadline' => 'nullable',
        ]);

        $donation->update([
            'title' => trim($request->input('title')),
            'description' => trim($request->input('description')),
            'target_amount' => $request->input('target_amount'),
            'deadline' => $request->input('deadline') ?? null,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function processApproveDonationSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('donation.update'),
            403
        );

        $donation = Donation::where('uuid', $uuid)->first();
        if (!$donation) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $request->validate([
            'approval_status' => 'required|in:approved,declined',
        ]);

        if ($donation->created_by == Auth::user()->username) {
            return redirect()->back()->with('error', 'You cannot approve or decline a donation request you created.');
        }

        $donation->approval_status = $request->input('approval_status');
        if ($donation->approval_status == 'approved') {
            $donation->is_active = true;
        } else {
            $donation->is_active = false;
        }
        $donation->processed_by = Auth::user()->username;
        $donation->approved_at = now();
        $donation->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function memberDonations(Request $request)
    {
        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $donations = '';
        $totalDonations = 0;

        if (Route::currentRouteName() == 'memberDonationsSearch') {
            $searchKey = $request->input('searchKey');
            $donations = Donation::where('approval_status', 'approved')
                ->when($searchKey, function ($query, $searchKey) {
                    return $query->where(function ($q) use ($searchKey) {
                        $q->where('title', 'like', "%$searchKey%")
                            ->orWhere('description', 'like', "%$searchKey%");
                    });
                })
                ->orderByDesc('is_active')
                ->orderByDesc('updated_at')
                ->paginate(20)
                ->withQueryString();


            $totalDonations = $donations->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $donations = Donation::where('approval_status', 'approved')
                ->orderByDesc('is_active')
                ->orderByDesc('updated_at')
                ->paginate(20);
            $totalDonations = Donation::count();
            $requestType = 'all';
            $pageTitle = 'Donations';
        }

        $data = [
            'donations' => $donations,
            'totalDonations' => $totalDonations,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.member.finance.memberDonations', compact('data'));
    }
    public function memberViewDonation($uuid)
    {
        $donation = Donation::where('uuid', $uuid)->first();
        if (!$donation) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $myContribution = PaymentTransaction::where('donation_id', $donation->donation_id)
            ->where('registered_payer', Auth::user()->username)->where('payment_status', 'successful')->where('approval_status', 'approved')
            ->sum('amount_payable');
        $data = [
            'donation' => $donation,
            'myContribution' => $myContribution,
        ];

        return view('users.member.finance.memberViewDonations', compact('data'));
    }
    public function memberPayForDonation($uuid, Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'electronic_payment_method' => 'required|exists:electronic_payment_methods,slug',
        ]);

        $member = Auth::user();

        $donation = Donation::where('uuid', $uuid)->first();
        if (!$donation) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $amountToDonate = $request->input('amount');
        do {
            $transactionId = 'pymt-' . now()->format('Ymds') . '-' . tenant()->id . random_int(100000000, 999999999);
        } while (PaymentTransaction::where('payment_transaction_id', $transactionId)->exists());

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
                    $taxAmount = ($tax->tax_rate / 100) * $amountToDonate;
                } elseif ($tax->tax_method === 'fixed_amount') {
                    $taxAmount = $tax->tax_rate;
                }

                $totalTax += $taxAmount; // Add to total tax.
                $taxesPaid .= ': ₦' . number_format($taxAmount, 2) . "\n"; // Add tax amount to the concatenated string.
            }
        }

        $totalDueAndTax = $amountToDonate + $totalTax;
        // get personal profile
        $personalProfile = $member->memberPersonalProfile;
        if (!$personalProfile) {
            return redirect()->back()->with('error', 'Personal profile not found!');
        }

        // paystack processing start
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
                $transaction->payment_purpose = 'donation';
                $transaction->donation_id = $donation->donation_id;
                $transaction->amount_payable = $amountToDonate;
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
        // paystack processing end
        // flutterwave processing start
        if ($request->electronic_payment_method == 'flutterwave') {
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
                    'description' => 'Donation Payment for ' . $donation->title,
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
                    $transaction->payment_purpose = 'donation';
                    $transaction->donation_id = $donation->donation_id;
                    $transaction->amount_payable = $amountToDonate;
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
        // flutterwave processing end
    }
    public function viewDonationReport($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('donation.read'),
            403
        );
        $donation = Donation::where('uuid', $uuid)->first();
        if (!$donation) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $successfulPayments = $donation->successfulPayments()
            ->with('payerPersonalProfile')
            ->latest()
            ->paginate(20, ['*'], 'successful_page');

        $failedPayments = $donation->failedPayments()
            ->with('payerPersonalProfile')
            ->latest()
            ->paginate(20, ['*'], 'failed_page');

        $paymentsPendingApproval = $donation->paymentsPendingApproval()
            ->with('payerPersonalProfile')
            ->latest()
            ->paginate(20, ['*'], 'pending_page');


        $payments = $successfulPayments;
        if ($request->page_type == 'failed_page') {
            $pageType = 'failed_page';
            $payments = $failedPayments;
        } elseif ($request->page_type == 'pending_page') {
            $pageType = 'pending_page';
            $payments = $paymentsPendingApproval;
        } else {
            $pageType = 'successful_page';
            $payments = $successfulPayments;
        }
        $data = [
            'donation' => $donation,
            'pageType' => $pageType,
            'payments' => $payments,
        ];

        return view('users.admin.finance.donations.viewDonationReport', compact('data'));
    }
    public function downloadDonationReport($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('donation.read'),
            403
        );

        $donation = Donation::where('uuid', $uuid)->first();

        if (!$donation) {
            return redirect()
                ->back()
                ->with('error', 'Records not found.');
        }

        /*
    |--------------------------------------------------------------------------
    | Runtime configuration
    |--------------------------------------------------------------------------
    */
        ini_set('pcre.backtrack_limit', 10000000);
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');

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
    | mPDF setup
    |--------------------------------------------------------------------------
    */
        $mpdf = new Mpdf([
            'default_font_size' => 10,
            'default_font' => 'Arial',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 10,
            'margin_bottom' => 15,
            'format' => 'A4',
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
            'tempDir' => $tempDirectory,
        ]);

        $mpdf->setFooter(
            'Page {PAGENO} of {nb} >> Donation Report: '
                . $donation->title
        );

        $mpdf->showImageErrors = true;

        /*
    |--------------------------------------------------------------------------
    | Base payment queries
    |--------------------------------------------------------------------------
    |
    | Do NOT call paginate() here.
    | Pagination returns only one page and is for browser lists.
    |
    */
        $successfulPaymentsQuery = $donation->successfulPayments()
            ->with([
                'payerPersonalProfile',
            ])
            ->orderBy('id');

        $failedPaymentsQuery = $donation->failedPayments()
            ->with([
                'payerPersonalProfile',
            ])
            ->orderBy('id');

        $pendingPaymentsQuery = $donation->paymentsPendingApproval()
            ->with([
                'payerPersonalProfile',
            ])
            ->orderBy('id');

        /*
    |--------------------------------------------------------------------------
    | Donation summary values
    |--------------------------------------------------------------------------
    |
    | These are database aggregate queries.
    | They do not load all transactions into memory.
    |
    */
        $successfulPaymentCount = (clone $successfulPaymentsQuery)->count();

        $successfulAmount = (float) (
            (clone $successfulPaymentsQuery)->sum('amount_paid')
        );

        $failedPaymentCount = (clone $failedPaymentsQuery)->count();

        $failedAmount = (float) (
            (clone $failedPaymentsQuery)->sum('amount_paid')
        );

        $pendingPaymentCount = (clone $pendingPaymentsQuery)->count();

        $pendingAmount = (float) (
            (clone $pendingPaymentsQuery)->sum('amount_paid')
        );

        /*
    |--------------------------------------------------------------------------
    | Shared report data
    |--------------------------------------------------------------------------
    */
        $basicSystemInfo = basicSystemInfo();
        $tenant = tenant();

        $data = [
            'donation' => $donation,
            'tenant' => $tenant,
            'basicSystemInfo' => $basicSystemInfo,

            'successfulPaymentCount' => $successfulPaymentCount,
            'successfulAmount' => $successfulAmount,

            'failedPaymentCount' => $failedPaymentCount,
            'failedAmount' => $failedAmount,

            'pendingPaymentCount' => $pendingPaymentCount,
            'pendingAmount' => $pendingAmount,

            'generatedAt' => now('Africa/Lagos'),
        ];

        /*
    |--------------------------------------------------------------------------
    | Write donation report CSS once
    |--------------------------------------------------------------------------
    */
        $css = view(
            'printFiles.donations.reports.partials.donationReportStyles',
            compact('data')
        )->render();

        $mpdf->WriteHTML(
            $css,
            HTMLParserMode::HEADER_CSS
        );

        unset($css);

        /*
    |--------------------------------------------------------------------------
    | Write donation header and summary once
    |--------------------------------------------------------------------------
    */
        $headerHtml = view(
            'printFiles.donations.reports.partials.donationReportHeader',
            compact('data')
        )->render();

        $mpdf->WriteHTML(
            $headerHtml,
            HTMLParserMode::HTML_BODY
        );

        unset($headerHtml);

        /*
    |--------------------------------------------------------------------------
    | Successful donations section
    |--------------------------------------------------------------------------
    */
        $mpdf->WriteHTML(
            view(
                'printFiles.donations.reports.partials.donationSectionHeading',
                [
                    'data' => $data,
                    'title' => 'SUCCESSFUL DONATIONS',
                    'colour' => '#198754',
                ]
            )->render(),
            HTMLParserMode::HTML_BODY
        );

        if ($successfulPaymentCount === 0) {
            $mpdf->WriteHTML(
                '<div class="no-records">No successful donation payments found.</div>',
                HTMLParserMode::HTML_BODY
            );
        } else {
            $successfulSerialNumber = 0;

            $successfulPaymentsQuery->chunkById(
                50,
                function ($payments) use (
                    $data,
                    $mpdf,
                    &$successfulSerialNumber
                ) {
                    $html = view(
                        'printFiles.donations.reports.partials.donationPaymentChunk',
                        [
                            'data' => $data,
                            'payments' => $payments,
                            'serialStart' => $successfulSerialNumber,
                            'paymentCategory' => 'successful',
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
    | Pending approval section
    |--------------------------------------------------------------------------
    */
        $mpdf->WriteHTML(
            '<pagebreak />',
            HTMLParserMode::HTML_BODY
        );

        $mpdf->WriteHTML(
            view(
                'printFiles.donations.reports.partials.donationSectionHeading',
                [
                    'data' => $data,
                    'title' => 'PAYMENTS PENDING APPROVAL',
                    'colour' => '#d97706',
                ]
            )->render(),
            HTMLParserMode::HTML_BODY
        );

        if ($pendingPaymentCount === 0) {
            $mpdf->WriteHTML(
                '<div class="no-records">No donation payments are pending approval.</div>',
                HTMLParserMode::HTML_BODY
            );
        } else {
            $pendingSerialNumber = 0;

            $pendingPaymentsQuery->chunkById(
                50,
                function ($payments) use (
                    $data,
                    $mpdf,
                    &$pendingSerialNumber
                ) {
                    $html = view(
                        'printFiles.donations.reports.partials.donationPaymentChunk',
                        [
                            'data' => $data,
                            'payments' => $payments,
                            'serialStart' => $pendingSerialNumber,
                            'paymentCategory' => 'pending',
                        ]
                    )->render();

                    $mpdf->WriteHTML(
                        $html,
                        HTMLParserMode::HTML_BODY
                    );

                    $pendingSerialNumber += $payments->count();

                    unset($html, $payments);

                    gc_collect_cycles();
                },
                'id'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Failed payment section
    |--------------------------------------------------------------------------
    */
        $mpdf->WriteHTML(
            '<pagebreak />',
            HTMLParserMode::HTML_BODY
        );

        $mpdf->WriteHTML(
            view(
                'printFiles.donations.reports.partials.donationSectionHeading',
                [
                    'data' => $data,
                    'title' => 'FAILED DONATION PAYMENTS',
                    'colour' => '#dc2626',
                ]
            )->render(),
            HTMLParserMode::HTML_BODY
        );

        if ($failedPaymentCount === 0) {
            $mpdf->WriteHTML(
                '<div class="no-records">No failed donation payments found.</div>',
                HTMLParserMode::HTML_BODY
            );
        } else {
            $failedSerialNumber = 0;

            $failedPaymentsQuery->chunkById(
                50,
                function ($payments) use (
                    $data,
                    $mpdf,
                    &$failedSerialNumber
                ) {
                    $html = view(
                        'printFiles.donations.reports.partials.donationPaymentChunk',
                        [
                            'data' => $data,
                            'payments' => $payments,
                            'serialStart' => $failedSerialNumber,
                            'paymentCategory' => 'failed',
                        ]
                    )->render();

                    $mpdf->WriteHTML(
                        $html,
                        HTMLParserMode::HTML_BODY
                    );

                    $failedSerialNumber += $payments->count();

                    unset($html, $payments);

                    gc_collect_cycles();
                },
                'id'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Safe PDF filename
    |--------------------------------------------------------------------------
    */
        $fileName = 'donation-report-'
            . str($donation->title ?? 'donation')->slug()
            . '-'
            . now('Africa/Lagos')->format('Y-m-d_H-i-s')
            . '.pdf';

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $mpdf->Output($fileName, 'D');

        exit;
    }
}
