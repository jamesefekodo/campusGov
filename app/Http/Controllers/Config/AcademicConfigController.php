<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicConfig\HeadOfFaculty;
use App\Models\Config\BasicSystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class AcademicConfigController extends Controller
{
    public function faculties(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $faculties = '';
        $totalFaculties = 0;
        $operationalLevel = BasicSystemSetting::value('operational_level') ?? 'departnemt';

        if (Route::currentRouteName() == 'facultiesSearch') {
            $searchKey = $request->input('searchKey');
            $faculties = Faculty::where('name', 'like', "%$searchKey%")
                ->orWhere('acronym', 'like', "%$searchKey%")
                ->orWhere('description', 'like', "%$searchKey%")
                ->orderBy('name')
                ->paginate(20)->withQueryString();


            $totalFaculties = $faculties->total();
            $requestType = 'search';
            $pageTitle = 'Search Results:: ' . $searchKey;
        } else {
            $faculties = Faculty::orderBy('name')->paginate(20);
            $totalFaculties = Faculty::count();
            $requestType = 'all';
            $pageTitle = 'Faculties';
        }
        $data = [
            'faculties' => $faculties,
            'totalFaculties' => $totalFaculties,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'operationalLevel' => $operationalLevel,
        ];


        return view('users.admin.academicConfig.faculties.faculties', compact('data'));
    }
    public function addFacultySubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.create'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'acronym' => 'nullable|string',
            'description' => 'required',
            'administrative_head_title' => 'nullable|string',
        ]);


        do {
            $facultySlug = 'faculty-' . now()->format('mY') . '-' . random_int(1000000000, 9999999999);
        } while (Faculty::where('slug', $facultySlug)->exists());


        $faculty = new Faculty();
        $faculty->name = trim($request->name);
        $faculty->slug = $facultySlug;
        $faculty->acronym = trim($request->acronym);
        $faculty->description = $request->description;
        $faculty->administrative_head_title = trim($request->administrative_head_title);
        $faculty->save();

        return redirect()->back()->with('success', 'Done!');
    }
    public function viewFaculty($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.read'),
            403
        );

        $faculty = Faculty::where('uuid', $uuid)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $data = [
            'faculty' => $faculty,
        ];

        return view('users.admin.academicConfig.faculties.viewFaculties', compact('data'));
    }
    public function updateFacultySubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.update'),
            403
        );

        $faculty = Faculty::where('uuid', $uuid)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $request->validate([
            'name' => 'required|string',
            'acronym' => 'nullable|string',
            'description' => 'required',
            'administrative_head_title' => 'nullable|string',
            'is_active' => 'required',
        ]);

        $faculty->update([
            'name' => $request->name,
            'acronym' => $request->acronym,
            'description' => $request->description,
            'administrative_head_title' => $request->administrative_head_title,
            'is_active' => $request->is_active,
        ]);

        return redirect()->back()->with('success', 'Done!');
    }
    public function addFacultyDeanSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.update'),
            403
        );
        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'nullable',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);
        $faculty = Faculty::where('uuid', $request->faculty)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }


        $head = new HeadOfFaculty();
        $head->faculty = $faculty->slug;
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->assumed_office_on = $request->assumed_office_on;
        $head->profile = $request->profile;
        if (!$faculty->facultyHead) {
            $head->is_active = true;

            // process the image
            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $logoExtension = $image->getClientOriginalExtension();
                $imageName = $faculty->slug . '.' . $logoExtension;
                $image->move(public_path('assets/uploads/faculty/head/photo'), $imageName);
                $head->photo = $imageName;
            }
        }
        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editFacultyDeanSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.update'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'nullable',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);

        $faculty = Faculty::where('uuid', $request->faculty)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $head = $faculty->facultyHead;
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->assumed_office_on = $request->assumed_office_on;
        $head->profile = $request->profile;

        // Process the image
        if ($request->hasFile('photo')) {
            $filePath = 'assets/uploads/faculty/head/photo/' . $head->photo;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $newImage = $request->file('photo');
            $newImageExtension = $newImage->getClientOriginalExtension();
            $newImageName = $faculty->slug . '.' . $newImageExtension;
            $newImage->move(public_path('assets/uploads/faculty/head/photo'), $newImageName);
            $head->photo = $newImageName;
        }
        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editEndFacultyDeanTenureSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.update'),
            403
        );

        $request->validate([
            'name' => 'required|string',
            'qualifications' => 'nullable|string',
            'assumed_office_on' => 'nullable',
            'tenure_end_on' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'profile' => 'nullable',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:300',
        ]);

        $faculty = Faculty::where('uuid', $request->faculty)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $head = $faculty->facultyHead;
        $head->name = trim($request->name);
        $head->qualifications = trim($request->qualifications);
        $head->phone = trim($request->phone);
        $head->email = trim($request->email);
        $head->assumed_office_on = $request->assumed_office_on;
        $head->tenure_end_on = $request->tenure_end_on;
        $head->profile = $request->profile;
        $head->is_active = false;

        // Process the image
        if ($request->hasFile('photo')) {
            $filePath = 'assets/uploads/faculty/head/photo/' . $head->photo;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $newImage = $request->file('photo');
            $newImageExtension = $newImage->getClientOriginalExtension();
            $newImageName = $faculty->slug . '.' . $newImageExtension;
            $newImage->move(public_path('assets/uploads/faculty/head/photo'), $newImageName);
            $head->photo = $newImageName;
        }
        $head->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function pastFacultyHeads($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('faculty.read'),
            403
        );

        $faculty = Faculty::where('uuid', $uuid)->first();
        if (!$faculty) {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $pastHeads = '';
        $totalPastHeads = 0;

        if (Route::currentRouteName() == 'pastFacultyHeadsSearch') {
            $searchKey = $request->input('searchKey');
            $pastHeads = HeadOfFaculty::where('faculty', $faculty->slug)->where('name', 'like', "%$searchKey%")
                ->orWhere('qualifications', 'like', "%$searchKey%")
                ->orWhere('phone', 'like', "%$searchKey%")
                ->orWhere('email', 'like', "%$searchKey%")
                ->orWhere('profile', 'like', "%$searchKey%")
                ->orderBy('name')
                ->paginate(20)->withQueryString();


            $totalPastHeads = $pastHeads->total();
            $requestType = 'search';
            $pageTitle = 'Search Results:: ' . $searchKey;
        } else {
            $pastHeads = HeadOfFaculty::where('faculty', $faculty->slug)
                ->orderBy('tenure_end_on', 'desc')
                ->paginate(20);
            $totalPastHeads = $faculty->pastFacultyHeads->count();
            $requestType = 'all';
            $pageTitle = 'Past Faculty Head';
        }
        $data = [
            'faculty' => $faculty,
            'pastHeads' => $pastHeads,
            'totalPastHeads' => $totalPastHeads,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.academicConfig.faculties.pastFacultyHeads', compact('data'));
    }
}
