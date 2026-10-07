<?php

namespace App\Http\Controllers\Misconduct;

use App\Http\Controllers\Controller;
use App\Models\Misconduct\MemberMisconduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class MisconductController extends Controller
{
    public function activeMisconductCases(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('misconducts.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $activeMisconducts = '';
        $totalActiveMisconducts = 0;


        if (Route::currentRouteName() == 'activeMisconductCasesSearch') {
            $searchKey = $request->input('searchKey');
            $activeMisconducts = MemberMisconduct::where('status', '!=', 'resolved')->with('memberPersonalProfile')
                ->where(function ($query) use ($searchKey) {
                    $query->where('case_id', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('username', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('incident_date', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('report_date', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('reported_by', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('reporter_username', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('misconduct_type', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('description', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('witness_report', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('disciplinary_action', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('status', 'LIKE', '%' . $searchKey . '%');
                })
                // category
                ->orWhereHas('memberPersonalProfile', function ($query) use ($searchKey) {
                    $query->where('username', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('phone1', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('email1', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('phone2', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('email2', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('first_name', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('middle_name', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('gender', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('nationality', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('state_of_origin', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('lga_of_origin', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('address1', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('address2', 'LIKE', '%' . $searchKey . '%');
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(20)->withQueryString();

            $totalActiveMisconducts = $activeMisconducts->total();
            $requestType = 'search';
            $pageTitle = 'Search Results: ' . $searchKey;
        } else {
            $activeMisconducts = MemberMisconduct::where('status', '!=', 'resolved')
                ->orderBy('updated_at', 'desc')
                ->paginate(20);
            $totalActiveMisconducts = $activeMisconducts->total();
            $requestType = 'all';
            $pageTitle = 'Misconducts (Unresolved)';
        }

        $data = [
            'misconducts' => $activeMisconducts,
            'totalMisconducts' => $totalActiveMisconducts,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.misconducts.misconducts', compact('data'));
    }
    public function addNewMisconductSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('misconducts.create'),
            403
        );

        request()->validate([
            'username' => 'required|exists:users,username',
            'incident_date' => 'required|date',
            'report_date' => 'required|date',
            'reported_by' => 'required|string',
            'reporter_username' => 'nullable|exists:users,username',
            'misconduct_type' => 'required|string',
            'status' => 'required|string',
            'description' => 'required|string',
            'witness_report' => 'nullable|string',
            'disciplinary_action' => 'nullable|string',
            'attachment_1' => 'nullable|file|mimes:png,webp,jpg,jpeg|max:1024',
            'attachment_2' => 'nullable|file|mimes:png,webp,jpg,jpeg|max:1024',
        ]);

        do {
            $caseId = 'miscd-' . now()->format('Ym') . '-' . Str::lower(Str::random(10));
        } while (MemberMisconduct::where('case_id', $caseId)->exists());

        $case = new MemberMisconduct();
        $case->case_id = $caseId;
        $case->username = $request->username;
        $case->incident_date = $request->incident_date;
        $case->report_date = $request->report_date;
        $case->reported_by = $request->reported_by;
        $case->reporter_username = $request->reporter_username;
        $case->misconduct_type = $request->misconduct_type;
        $case->status = $request->status;
        $case->description = $request->description;
        $case->witness_report = $request->witness_report;
        $case->disciplinary_action = $request->disciplinary_action;

        // Define upload path
        $uploadPath = public_path('assets/uploads/misconductCases/attachments');

        // Process attachment_1
        if ($request->hasFile('attachment_1')) {
            $file1 = $request->file('attachment_1');
            $file1Extension = $file1->getClientOriginalExtension();
            $file1Name = $caseId . '_1.' . $file1Extension;
            $file1->move($uploadPath, $file1Name);
            $case->attachment_1 = $file1Name;
        }

        // Process attachment_2
        if ($request->hasFile('attachment_2')) {
            $file2 = $request->file('attachment_2');
            $file2Extension = $file2->getClientOriginalExtension();
            $file2Name = $caseId . '_2.' . $file2Extension;
            $file2->move($uploadPath, $file2Name);
            $case->attachment_2 = $file2Name;
        }

        $case->save();

        return redirect()->back()->with('success', 'Done!');
    }
    public function viewMisconductCase($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('misconducts.read'),
            403
        );

        $misconduct = MemberMisconduct::where('uuid', $uuid)->first();
        if (!$misconduct) {
            return redirect()->back()->with('error', 'No records found!');
        }
        $data = [
            'misconduct' => $misconduct,
        ];

        return view('users.admin.misconducts.viewMisconduct', compact('data'));
    }
    public function updateMisconductCaseSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('misconducts.update'),
            403
        );

        $misconduct = MemberMisconduct::where('uuid', $uuid)->first();
        if (!$misconduct) {
            return redirect()->back()->with('error', 'No records found!');
        }

        request()->validate([
            'username' => 'required|exists:users,username',
            'incident_date' => 'required|date',
            'report_date' => 'required|date',
            'reported_by' => 'required|string',
            'reporter_username' => 'nullable|exists:users,username',
            'misconduct_type' => 'required|string',
            'status' => 'required|string',
            'description' => 'required|string',
            'witness_report' => 'nullable|string',
            'disciplinary_action' => 'nullable|string',
            'attachment_1' => 'nullable|file|mimes:png,webp,jpg,jpeg|max:1024',
            'attachment_2' => 'nullable|file|mimes:png,webp,jpg,jpeg|max:1024',
        ]);
        $misconduct->username = $request->username;
        $misconduct->incident_date = $request->incident_date;
        $misconduct->report_date = $request->report_date;
        $misconduct->reported_by = $request->reported_by;
        $misconduct->reporter_username = $request->reporter_username;
        $misconduct->misconduct_type = $request->misconduct_type;
        $misconduct->status = $request->status;
        $misconduct->description = $request->description;
        $misconduct->witness_report = $request->witness_report;
        $misconduct->disciplinary_action = $request->disciplinary_action;

        $uploadPath = public_path('assets/uploads/misconductCases/attachments');

        // process the image
        if ($request->hasFile('attachment_1')) {
            // Delete old file if exists
            $filePath = public_path('assets/uploads/misconductCases/attachments/' . $misconduct->attachment_1);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            // Handle new image upload
            $newImage1 = $request->file('attachment_1');
            $newImageExtension1 = $newImage1->getClientOriginalExtension();
            $newImageName1 = $misconduct->case_id . '_1.' . $newImageExtension1;
            $newImage1->move(public_path('assets/uploads/misconductCases/attachments'), $newImageName1);

            // Update the attachment field
            $misconduct->attachment_1 = $newImageName1;
        }

        if ($request->hasFile('attachment_2')) {
            // Delete old file if exists
            $filePath = public_path('assets/uploads/misconductCases/attachments/' . $misconduct->attachment_2);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            // Handle new image upload
            $newImage2 = $request->file('attachment_2');
            $newImageExtension2 = $newImage2->getClientOriginalExtension();
            $newImageName2 = $misconduct->case_id . '_2.' . $newImageExtension2;
            $newImage2->move(public_path('assets/uploads/misconductCases/attachments'), $newImageName2);

            // Update the attachment field
            $misconduct->attachment_2 = $newImageName2;
        }

        $misconduct->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function resolvedMisconductCases(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('misconducts.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $resolvedMisconducts = '';
        $totalResolvedMisconducts = 0;


        if (Route::currentRouteName() == 'resolvedMisconductCasesSearch') {
            $searchKey = $request->input('searchKey');
            $resolvedMisconducts = MemberMisconduct::where('status', '=', 'resolved')->with('memberPersonalProfile')
                ->where(function ($query) use ($searchKey) {
                    $query->where('case_id', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('username', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('incident_date', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('report_date', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('reported_by', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('reporter_username', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('misconduct_type', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('description', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('witness_report', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('disciplinary_action', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('status', 'LIKE', '%' . $searchKey . '%');
                })
                // category
                ->orWhereHas('memberPersonalProfile', function ($query) use ($searchKey) {
                    $query->where('username', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('phone1', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('email1', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('phone2', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('email2', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('first_name', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('middle_name', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('gender', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('nationality', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('state_of_origin', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('lga_of_origin', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('address1', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('address2', 'LIKE', '%' . $searchKey . '%');
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(20)->withQueryString();

            $totalResolvedMisconducts = $resolvedMisconducts->total();
            $requestType = 'search';
            $pageTitle = 'Search Results: ' . $searchKey;
        } else {
            $resolvedMisconducts = MemberMisconduct::where('status', '=', 'resolved')
                ->orderBy('updated_at', 'desc')
                ->paginate(20);
            $totalResolvedMisconducts = $resolvedMisconducts->total();
            $requestType = 'all';
            $pageTitle = 'Misconducts (Resolved)';
        }

        $data = [
            'misconducts' => $resolvedMisconducts,
            'totalMisconducts' => $totalResolvedMisconducts,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.misconducts.misconducts', compact('data'));
    }
}
