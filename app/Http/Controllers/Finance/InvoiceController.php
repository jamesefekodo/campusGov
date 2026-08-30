<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Claims\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
