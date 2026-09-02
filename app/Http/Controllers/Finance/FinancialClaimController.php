<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Config\BasicSystemSetting;
use App\Models\Finance\Claims\FinancialClaim;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class FinancialClaimController extends Controller
{
    public function financialClaims(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('financial_claim.read'),
            403
        );


        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $financialClaims = '';
        $totalFinancialClaims = 0;


        if (Route::currentRouteName() == 'financialClaimsSearch') {
            $searchKey = $request->input('searchKey');
            $financialClaims = FinancialClaim::where('approval_status', '!=', 'pending')
                ->when($searchKey, function ($query, $searchKey) {
                    return $query->where(function ($q) use ($searchKey) {
                        $q->where('description', 'like', "%$searchKey%")
                            ->orWhere('remarks', 'like', "%$searchKey%");
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString();


            $totalFinancialClaims = $financialClaims->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $financialClaims = FinancialClaim::where('approval_status', '!=', 'pending')->orderBy('created_at', 'desc')->paginate(20);
            $totalFinancialClaims = FinancialClaim::count();
            $requestType = 'all';
            $pageTitle = 'Financial Claims';
        }

        $data = [
            'financialClaims' => $financialClaims,
            'totalFinancialClaim' => $totalFinancialClaims,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];
        return view('users.admin.finance.financialClaims.financialClaims', compact('data'));
    }
    public function addNewFinancialClaimSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('financial_claim.create'),
            403
        );

        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'claim_type' => 'required|string|in:credit,debit',
            'date' => 'required',
            'description' => 'required',
        ]);

        do {
            $claimId = 'finclaim-' . now()->format('mY') . '-' . random_int(1000000000, 9999999999);
        } while (FinancialClaim::where('claim_id', $claimId)->exists());

        FinancialClaim::create([
            'claim_id' => $claimId,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => trim($request->description),
            'initiated_by' => Auth::user()->username,
            'initiated_at' => Carbon::now(),
            'claim_type' => $request->claim_type,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function financialClaimsPendingApproval(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('financial_claim.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $financialClaims = '';
        $totalFinancialClaims = 0;


        if (Route::currentRouteName() == 'financialClaimsPendingApprovalSearch') {
            $searchKey = $request->input('searchKey');
            $financialClaims = FinancialClaim::where('approval_status', 'pending')
                ->when($searchKey, function ($query, $searchKey) {
                    return $query->where(function ($q) use ($searchKey) {
                        $q->where('description', 'like', "%$searchKey%")
                            ->orWhere('remarks', 'like', "%$searchKey%");
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString();


            $totalFinancialClaims = $financialClaims->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $financialClaims = FinancialClaim::where('approval_status', 'pending')->orderBy('created_at', 'desc')->paginate(20);
            $totalFinancialClaims = FinancialClaim::count();
            $requestType = 'all';
            $pageTitle = 'Approvals | Financial Claims';
        }

        $data = [
            'financialClaims' => $financialClaims,
            'totalFinancialClaim' => $totalFinancialClaims,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];
        return view('users.admin.finance.financialClaims.financialClaims', compact('data'));
    }
    public function viewFinancialClaim($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('financial_claim.read'),
            403
        );

        $claim = FinancialClaim::where('uuid', $uuid)->first();
        if (!$claim) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $data = [
            'claim' => $claim,
        ];
        return view('users.admin.finance.financialClaims.viewFinancialClaim', compact('data'));
    }
    public function processFinancialClaimSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('financial_claim.put'),
            403
        );

        $claim = FinancialClaim::where('uuid', $uuid)->first();
        if (!$claim) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $settings = BasicSystemSetting::where('tenant_id', $claim->tenant_id)->first();
        if (!$settings) {
            return redirect()->back()->with('error', 'Payment config not found!');
        }

        if ($claim->initiated_by == Auth::user()->username) {
            return redirect()->back()->with('error', 'You cannot approve or decline a financial claim you initiated.');
        }


        $request->validate([
            'description' => 'required',
            'remarks' => 'nullable',
            'approval_status' => 'required|in:approved,declined',
        ]);

        $claim->update([
            'description' => trim($request->description),
            'remarks' => trim($request->remarks),
            'approval_status' => trim($request->approval_status),
            'processed_by' => Auth::user()->username,
            'approved_at' => Carbon::now(),
        ]);

        if (
            $claim->approval_status === 'approved' &&
            $claim->balance_update_status !== 'updated'
        ) {

            if ($claim->claim_type === 'credit') {

                $settings->increment('account_balance', $claim->amount);
            } elseif ($claim->claim_type === 'debit') {

                $settings->decrement('account_balance', $claim->amount);
            }

            $claim->update([
                'balance_update_status' => 'updated',
            ]);
        }
        return redirect()->back()->with('success', 'Done!');
    }
}
