<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Donations\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    public function memberDonations(Request $request){
        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $donations = '';
        $totalDonations = 0;

        
         if (Route::currentRouteName() == 'memberDonationsSearch') {
            $searchKey = $request->input('searchKey');
            $donations = Donation::where('approval_status', 'approved')->when($searchKey, function ($query, $searchKey) {
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
            $donations = Donation::where('approval_status', 'approved')->orderBy('updated_at', 'desc')->paginate(20);
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
    }
}
