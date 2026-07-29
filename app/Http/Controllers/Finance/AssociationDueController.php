<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Program;
use App\Models\Finance\Dues\AssociationDue;
use App\Models\Finance\Dues\AssociationDueVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class AssociationDueController extends Controller
{
    public function associationDues(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.read'),
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
            $dues = AssociationDue::orderBy('academic_year', 'desc')->paginate(20);
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
    public function addNewAssociationDueSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.create'),
            403
        );
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'amount' => 'required',
            'academic_year' => 'required',
        ]);

        do {
            $feeId = 'due-' . now()->format('mY') . '-' . random_int(1000000000, 9999999999);
        } while (AssociationDue::where('fee_id', $feeId)->exists());

        AssociationDue::create(
            [
                'fee_id' => $feeId,
                'title' => trim($request->title),
                'description' => trim($request->description),
                'amount' => trim($request->amount),
                'academic_year' => trim($request->academic_year),
            ]
        );
        return redirect()->back()->with('success', 'Done!');
    }
    public function viewDue($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.read'),
            403
        );

        $due = AssociationDue::where('uuid', $uuid)->first();
        if (!$due) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $activePrograms = Program::where('is_active', true)->orderBy('name')->get();

        $data = [
            'due' => $due,
            'activePrograms' => $activePrograms,
        ];
        return view('users.admin.finance.dues.viewDues', compact('data'));
    }

    public function editAssociationDueSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.update'),
            403
        );

        $due = AssociationDue::where('uuid', $uuid)->first();
        if (!$due) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'amount' => 'required',
            'academic_year' => 'required',
            'is_active' => 'required',
        ]);

        $due->update(
            [
                'title' => trim($request->title),
                'description' => trim($request->description),
                'amount' => trim($request->amount),
                'academic_year' => trim($request->academic_year),
                'is_active' => $request->is_active,
            ]
        );
        return redirect()->back()->with('success', 'Done!');
    }
    public function createAssociationDueVariation($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.create'),
            403
        );

        $due = AssociationDue::where('uuid', $uuid)->first();
        if (!$due) {
            return redirect()->back()->with('error', 'Records not found.');
        }
        $validator = Validator::make($request->all(), [
            'admission_session' => [
                'nullable',
                'integer',
                'min:' . $due->academic_year,
                'max:' . ($due->academic_year + 10),
                'required_without_all:program_slug,program_type_slug',
            ],

            'program_slug' => [
                'nullable',
                'exists:programs,slug',
                'required_without_all:admission_session,program_type_slug',
            ],

            'program_type_slug' => [
                'nullable',
                'exists:program_types,slug',
                'required_without_all:admission_session,program_slug',
            ],

            'amount' => 'required|numeric',
        ], [
            'admission_session.required_without_all' => 'Please specify at least one of Admission Session, Program, or Program Type.',
            'program_slug.required_without_all' => 'Please specify at least one of Admission Session, Program, or Program Type.',
            'program_type_slug.required_without_all' => 'Please specify at least one of Admission Session, Program, or Program Type.',
            'admission_session.min' => 'The selected admission session is too old. It cannot be more than 10 academic sessions before the due academic session.',
            'admission_session.max' => 'The admission session cannot be later than the due academic session. The latest eligible admission session is the session immediately preceding the due academic session.',
        ]);

        $validator->validate();

        AssociationDueVariation::create([
            'due_id' => $due->fee_id,
            'admission_session' => $request->admission_session,
            'program_slug' => $request->program_slug,
            'program_type_slug' => $request->program_type_slug,
            'amount' => $request->amount,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function editAssociationDueVariation($id, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.update'),
            403
        );

        $variation = AssociationDueVariation::where('id', $id)->first();
        if (!$variation) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $validator = Validator::make($request->all(), [
            'admission_session' => [
                'nullable',
                'integer',
                'min:' . $variation->due->academic_year,
                'max:' . ($variation->due->academic_year + 10),
                'required_without_all:program_slug,program_type_slug',
            ],

            'program_slug' => [
                'nullable',
                'exists:programs,slug',
                'required_without_all:admission_session,program_type_slug',
            ],

            'program_type_slug' => [
                'nullable',
                'exists:program_types,slug',
                'required_without_all:admission_session,program_slug',
            ],

            'amount' => 'required|numeric',
        ], [
            'admission_session.required_without_all' => 'Please specify at least one of Admission Session, Program, or Program Type.',
            'program_slug.required_without_all' => 'Please specify at least one of Admission Session, Program, or Program Type.',
            'program_type_slug.required_without_all' => 'Please specify at least one of Admission Session, Program, or Program Type.',
            'admission_session.min' => 'The selected admission session is too old. It cannot be more than 10 academic sessions before the due academic session.',
            'admission_session.max' => 'The admission session cannot be later than the due academic session. The latest eligible admission session is the session immediately preceding the due academic session.',
        ]);

        $variation->update([
            'admission_session' => $request->admission_session,
            'program_slug' => $request->program_slug,
            'program_type_slug' => $request->program_type_slug,
            'amount' => $request->amount,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function removeAssociationVariation($id)
    {
        $variation = AssociationDueVariation::where('id', $id)->first();
        if (!$variation) {
            return redirect()->back()->with('error', 'Records not found.');
        }
        $variation->delete();
        return redirect()->back()->with('success', 'Done!');
    }
}
