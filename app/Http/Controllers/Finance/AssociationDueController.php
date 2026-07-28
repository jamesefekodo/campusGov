<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Dues\AssociationDue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AssociationDueController extends Controller
{
    public function associationDues(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.create'),
            403
        );


        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $dues = '';
        $totalDues = 0;


        if (Route::currentRouteName() == 'associationDuesSearch') {
            $searchKey = $request->input('searchKey');
            $dues = AssociationDue::where('fee_id', 'like', "%$searchKey%")
                ->orWhere('title', 'like', "%$searchKey%")
                ->orWhere('description', 'like', "%$searchKey%")
                ->orWhere('amount', 'like', "%$searchKey%")
                ->orWhere('academic_year', 'like', "%$searchKey%")
                ->orderBy('academic_year', 'desc')
                ->paginate(20)->withQueryString();


            $totalDues = $dues->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $dues = AssociationDue::orderBy('name')->paginate(20);
            $totalDues = AssociationDue::count();
            $requestType = 'all';
            $pageTitle = 'Association Dues';
        }

        $data = [
            'dues' => $dues,
            'totalDues' => $totalDues,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];
        return view('users.admin.finance.dues.dues', compact('data'));
    }
}
