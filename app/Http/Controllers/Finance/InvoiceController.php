<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Claims\Invoice;
use App\Models\Finance\Settings\ElectronicPaymentMethod;
use App\Models\Finance\Settings\Tax;
use App\Models\Finance\Transactions\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function invoices(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('invoice.read'),
            403
        );
        Invoice::where('status', 'pending')
            ->where('due_date', '<=', now())
            ->update([
                'status' => 'expired',
            ]);


        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $invoices = '';
        $totalInvoices = 0;


        if (Route::currentRouteName() == 'invoicesSearch') {
            $searchKey = $request->input('searchKey');
            $invoices = Invoice::when($searchKey, function ($query, $searchKey) {
                return $query->where(function ($q) use ($searchKey) {
                    $q->where('invoice_number', 'like', "%$searchKey%")
                        ->orWhere('registered_payer', 'like', "%$searchKey%")
                        ->orWhere('non_registered_payer', 'like', "%$searchKey%")
                        ->orWhere('description', 'like', "%$searchKey%")
                        ->orWhere('status', 'like', "%$searchKey%");
                });
            })
                ->orderBy('updated_at', 'desc')
                ->paginate(20)
                ->withQueryString();


            $totalInvoices = $invoices->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $invoices = Invoice::orderBy('updated_at', 'desc')->paginate(20);
            $totalInvoices = Invoice::count();
            $requestType = 'all';
            $pageTitle = 'Invoices';
        }

        $data = [
            'invoices' => $invoices,
            'totalInvoices' => $totalInvoices,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];
        return view('users.admin.finance.invoices.invoices', compact('data'));
    }
    public function addNewInvoiceSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('invoice.create'),
            403
        );

        $request->validate([
            'description' => ['required'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'is_registered_member' => ['required', 'in:yes,no'],

            'payer_name' => [
                'required',
                'string',
                Rule::when(
                    $request->is_registered_member === 'yes',
                    ['exists:users,username']
                ),
            ],

            'due_date' => [
                'required',
                'date',
                'after_or_equal:' . today()->addDay()->format('Y-m-d'),
            ],
        ]);

        do {
            $invoiceNumber = 'invoice-' . now()->format('Ymd') . '-' . random_int(1000000000, 9999999999);
        } while (Invoice::where('invoice_number', $invoiceNumber)->exists());


        $invoice = new Invoice();
        $invoice->invoice_number = $invoiceNumber;
        $invoice->amount = $request->amount;
        $invoice->description = trim($request->description);
        $invoice->issue_date = Carbon::today();
        $invoice->due_date = $request->due_date;
        $invoice->created_by = Auth::user()->username;
        if ($request->is_registered_member == 'yes') {
            $invoice->registered_payer = $request->payer_name;
        } else {
            $invoice->non_registered_payer = $request->payer_name;
        }
        $invoice->save();

        return redirect()->back()->with('success', 'Done!');
    }
    public function viewInvoice($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('invoice.read'),
            403
        );

        $invoice = Invoice::where('uuid', $uuid)->first();
        if (!$invoice) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $data = [
            'invoice' => $invoice,
        ];
        return view('users.admin.finance.invoices.viewInvoice', compact('data'));
    }
    public function myInvoices()
    {
        Invoice::where('status', 'pending')
            ->where('due_date', '<=', now())
            ->update([
                'status' => 'expired',
            ]);

        $myInvoices = Invoice::where('registered_payer', Auth::user()->username)->orderBy('created_at', 'desc')->paginate(20);

        $data = [
            'myInvoices' => $myInvoices,
        ];

        return view('users.member.finance.myInvoices', compact('data'));
    }
    public function viewMyInvoice($uuid)
    {
        $invoice = Invoice::where('uuid', $uuid)->where('registered_payer', Auth::user()->username)->first();
        if (!$invoice) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $data = [
            'invoice' => $invoice,
        ];
        return view('users.member.finance.viewMyInvoice', compact('data'));
    }
    public function memberPayInvoice($uuid, Request $request)
    {
        $request->validate([
            'electronic_payment_method' => 'required|exists:electronic_payment_methods,slug',
        ]);

        $invoice = Invoice::where('uuid', $uuid)->where('registered_payer', Auth::user()->username)->first();
        if (!$invoice) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        // expired invoice
        if ($invoice->status == 'expired') {
            return redirect()->back()->with('error', 'This invoice has expired. Kindly contact the administrators for a new invoice.');
        }

        $member = Auth::user();

        // check if it has been paid
        $alreadyPaid = PaymentTransaction::where('registered_payer', $member->username)->where('invoice_number', $invoice->invoice_number)->where('approval_status', 'approved')->first();


        if ($alreadyPaid) {
            return redirect()->back()->with(
                'error',
                'This invoice has already been paid.'
            );
        }
        // check for pending transaction
        $pendingTransaction = PaymentTransaction::where('registered_payer', $member->username)
            ->where('invoice_number', $invoice->invoice_number)
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
                    $taxAmount = ($tax->tax_rate / 100) * $invoice->amount;
                } elseif ($tax->tax_method === 'fixed_amount') {
                    $taxAmount = $tax->tax_rate;
                }

                $totalTax += $taxAmount; // Add to total tax.
                $taxesPaid .= ': ₦' . number_format($taxAmount, 2) . "\n"; // Add tax amount to the concatenated string.
            }
        }

        $totalDueAndTax = $invoice->amount + $totalTax;
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
                $transaction->payment_purpose = 'invoice';
                $transaction->invoice_number = $invoice->invoice_number;
                $transaction->amount_payable = $invoice->amount;
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
                    'description' => 'Pay Invoice',
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
                    $transaction->payment_purpose = 'invoice';
                    $transaction->invoice_number = $invoice->invoice_number;
                    $transaction->amount_payable = $invoice->amount;
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
}
