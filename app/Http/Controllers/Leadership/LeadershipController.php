<?php

namespace App\Http\Controllers\Leadership;

use App\Http\Controllers\Controller;
use App\Models\Leadership\StudentRepresentative\AssociationAdvisor;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipArm;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipPosition;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipRepresentative;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipTenure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class LeadershipController extends Controller
{
    public function studentLeadership(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('leadership.manage'),
            403
        );

        $arms = StudentLeadershipArm::orderBy('name')->get();
        $position = StudentLeadershipPosition::orderBy('name')->get();
        $tenure = StudentLeadershipTenure::orderBy('academic_session', 'desc')->get();

        $studentLeaders = '';

        $academicSession = $request->academic_session ?? currentAcademicYear();

        $studentLeaders = StudentLeadershipRepresentative::query()
            ->whereHas('tenure', function ($query) use ($academicSession) {
                $query->where('academic_session', $academicSession);
            })
            ->leftJoin(
                'student_leadership_positions as positions',
                'student_leadership_representative.position_slug',
                '=',
                'positions.slug'
            )
            ->leftJoin(
                'student_leadership_arms as arms',
                'positions.arm_slug',
                '=',
                'arms.slug'
            )
            ->select('student_leadership_representative.*')
            ->orderBy('arms.sort_order', 'asc')
            ->orderBy('positions.sort_order', 'asc')
            ->orderByRaw("
        CASE
            WHEN arms.slug = 'executive' THEN 1
            WHEN arms.slug = 'legislative' THEN 2
            ELSE 3
        END ASC
    ")
            ->with(['position.arm', 'position', 'tenure'])
            ->paginate(20);



        $data = [
            'studentLeaders' => $studentLeaders,
            'academicSession' => $academicSession,
            'arms' => $arms,
            'position' => $position,
            'tenure' => $tenure,
        ];
        return view('users.admin.leadership.studentReps.studentReps', compact('data'));
    }
    public function addNewStudentLeadershipRepresentativeSubmit(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:users,username',
            'position_slug' => 'required|exists:student_leadership_positions,slug',
            'tenure_slug' => 'required|exists:student_leadership_tenures,slug',
            'remarks' => 'nullable',
        ]);

        StudentLeadershipRepresentative::create([
            'username' => $request->username,
            'position_slug' => $request->position_slug,
            'tenure_slug' => $request->tenure_slug,
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Done');
    }
    public function editStudentLeadershipRepSubmit($uuid, Request $request)
    {
        $rep = StudentLeadershipRepresentative::where('uuid', $uuid)->first();
        if (!$rep) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $request->validate([
            'username' => 'required|exists:users,username',
            'position_slug' => 'required|exists:student_leadership_positions,slug',
            'tenure_slug' => 'required|exists:student_leadership_tenures,slug',
            'remarks' => 'nullable',
            'is_active' => 'required',
        ]);

        $rep->update([
            'username' => $request->username,
            'position_slug' => $request->position_slug,
            'tenure_slug' => $request->tenure_slug,
            'remarks' => $request->remarks,
            'is_active' => $request->is_active,
        ]);
        return redirect()->back()->with('success', 'Done');
    }
    public function deleteStudentLeadershipRep($uuid)
    {
        $rep = StudentLeadershipRepresentative::where('uuid', $uuid)->first();
        if (!$rep) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $rep->delete();
        return redirect()->back()->with('success', 'Done');
    }
    public function studentLeadershipTenures()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('leadership.manage'),
            403
        );

        $tenures = StudentLeadershipTenure::orderBy('academic_session', 'desc')->paginate(20);

        $data = [
            'tenures' => $tenures,
        ];
        return view('users.admin.leadership.tenures.tenures', compact('data'));
    }
    public function addNewStudentLeadershipTenureSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'academic_session' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        StudentLeadershipTenure::create([
            'name' => trim($request->name),
            'academic_session' => trim($request->academic_session),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date ?? null,
        ]);

        return redirect()->back()->with('success', 'Done');
    }
    public function editStudentLeadershipTenureSubmit($slug, Request $request)
    {
        $tenure = StudentLeadershipTenure::where('slug', $slug)->first();
        if (!$tenure) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $request->validate([
            'name' => 'required',
            'academic_session' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $tenure->update([
            'name' => trim($request->name),
            'academic_session' => trim($request->academic_session),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date ?? null,
        ]);
        return redirect()->back()->with('success', 'Done');
    }
    public function deleteStudentLeadershipTenure($slug)
    {
        $tenure = StudentLeadershipTenure::where('slug', $slug)->first();
        if (!$tenure) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        if ($tenure->representatives()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete tenure with representatives, please remove representatives first!');
        }
        $tenure->delete();
        return redirect()->back()->with('success', 'Done');
    }
    public function studentLeadershipBodies()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('leadership.manage'),
            403
        );

        $arms = StudentLeadershipArm::orderBy('sort_order')->paginate(20);

        $data = [
            'arms' => $arms,
        ];
        return view('users.admin.leadership.arms.arms', compact('data'));
    }
    public function addNewStudentLeadershipArmSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'sort_order' => 'required',
        ]);
        StudentLeadershipArm::create([
            'name' => trim($request->name),
            'description' => trim($request->description),
            'sort_order' => trim($request->sort_order),
        ]);
        return redirect()->back()->with('success', 'Done');
    }
    public function editStudentLeadershipArmSubmit($slug, Request $request)
    {
        $arm = StudentLeadershipArm::where('slug', $slug)->first();
        if (!$arm) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'sort_order' => 'required',
            'is_active' => 'required',
        ]);

        $arm->update([
            'name' => trim($request->name),
            'description' => trim($request->description),
            'sort_order' => trim($request->sort_order),
            'is_active' => trim($request->is_active),
        ]);
        return redirect()->back()->with('success', 'Done');
    }
    public function deleteStudentLeadershipArm($slug)
    {
        $arm = StudentLeadershipArm::where('slug', $slug)->first();
        if (!$arm) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        if ($arm->representatives()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete arms with representatives, please remove representatives first!');
        }
        $arm->delete();
        return redirect()->back()->with('success', 'Done');
    }
    public function studentLeadershipPositions()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('leadership.manage'),
            403
        );
        $arms = StudentLeadershipArm::orderBy('sort_order')->get();

        $positions = StudentLeadershipPosition::orderBy('name')->paginate(20);

        $data = [
            'positions' => $positions,
            'arms' => $arms,
        ];
        return view('users.admin.leadership.positions.positions', compact('data'));
    }
    public function addNewStudentLeadershipPositionSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'arm_slug' => 'required',
            'sort_order' => 'required',
        ]);

        StudentLeadershipPosition::create([
            'name' => trim($request->name),
            'description' => trim($request->description),
            'arm_slug' => trim($request->arm_slug),
            'sort_order' => trim($request->sort_order),
        ]);
        return redirect()->back()->with('success', 'Done');
    }
    public function editStudentLeadershipPositionSubmit($slug, Request $request)
    {
        $position = StudentLeadershipPosition::where('slug', $slug)->first();
        if (!$position) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'arm_slug' => 'required',
            'sort_order' => 'required',
            'is_active' => 'required',
        ]);

        $position->update([
            'name' => trim($request->name),
            'description' => trim($request->description),
            'arm_slug' => trim($request->arm_slug),
            'sort_order' => trim($request->sort_order),
            'is_active' => trim($request->is_active),

        ]);
        return redirect()->back()->with('success', 'Done');
    }
    public function deleteStudentLeadershipPosition($slug)
    {
        $position = StudentLeadershipPosition::where('slug', $slug)->first();
        if (!$position) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        if ($position->representatives()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete position with representatives, please remove representatives first!');
        }
        $position->delete();
        return redirect()->back()->with('success', 'Done');
    }
    public function advisors(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('leadership.manage'),
            403
        );
        $tenure = StudentLeadershipTenure::orderByDesc('academic_session')->get();

        $academicSession = $request->academic_session ?? currentAcademicYear();

        $selectedTenure = StudentLeadershipTenure::where(
            'academic_session',
            $academicSession
        )->first();

        $advisors = AssociationAdvisor::query()
            ->when(
                $selectedTenure,
                function ($query) use ($selectedTenure) {
                    // Use today if the selected tenure has no end date
                    $tenureStart = $selectedTenure->start_date;
                    $tenureEnd = $selectedTenure->end_date ?? today();

                    $query
                        // Advisor must have started by the tenure's effective end
                        ->whereDate('start_date', '<=', $tenureEnd)

                        // Advisor must not have ended before the tenure started
                        ->where(function ($query) use ($tenureStart) {
                            $query->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', $tenureStart);
                        });
                },
                function ($query) {
                    // No matching tenure means no results
                    $query->whereRaw('1 = 0');
                }
            )
            ->orderByDesc('start_date')
            ->orderBy('name')
            ->paginate(20);

        $data = [
            'advisors' => $advisors,
            'tenure' => $tenure,
            'academicSession' => $academicSession,
        ];

        return view('users.admin.leadership.advisors.advisors', compact('data'));
    }
    public function addNewStudentLeadershipAdvisorSubmit(Request $request)
    {
        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'name' => 'required',
            'position' => 'required',
            'remarks' => 'nullable',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after:start_date',
        ]);
        $advisor = new AssociationAdvisor();

        $advisor->name = trim($request->name);
        $advisor->position = trim($request->position);
        // $advisor->tenure_slug = trim($request->tenure_slug);
        $advisor->start_date = $request->start_date;
        $advisor->end_date = $request->end_date ?? null;
        $advisor->remarks = trim($request->remarks);

        if ($request->hasFile('photo')) {
            $tenantId = tenant()->id;

            $directory = public_path("assets/tenant_{$tenantId}/uploads/leadership/advisor");

            // Create the folder if it does not exist
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Delete the old photo if this is an update
            if (!empty($advisor->photo)) {
                $oldPhotoPath = $directory . '/' . $advisor->photo;

                if (File::exists($oldPhotoPath)) {
                    File::delete($oldPhotoPath);
                }
            }

            // Clean the advisor name for use as a filename
            $safeName = Str::slug(pathinfo($advisor->name, PATHINFO_FILENAME));

            $extension = strtolower($request->file('photo')->getClientOriginalExtension());

            // Unique name prevents one advisor overwriting another with the same name
            $newImageName = $safeName . '-' . uniqid() . '.' . $extension;

            $request->file('photo')->move($directory, $newImageName);

            $advisor->photo = $newImageName;
        }

        $advisor->save();

        return redirect()->back()->with('success', 'Done');
    }
    public function editStudentLeadershipAdvisorSubmit($uuid, Request $request){
         $advisor = AssociationAdvisor::where('uuid', $uuid)->first();
        if (!$advisor) {
            return redirect()->back()->with('error', 'Records not found!');
        }

         $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'name' => 'required',
            'position' => 'required',
            'remarks' => 'nullable',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after:start_date',
        ]);

         $advisor->name = trim($request->name);
        $advisor->position = trim($request->position);
        // $advisor->tenure_slug = trim($request->tenure_slug);
        $advisor->start_date = $request->start_date;
        $advisor->end_date = $request->end_date ?? null;
        $advisor->remarks = trim($request->remarks);

        if ($request->hasFile('photo')) {
            $tenantId = tenant()->id;

            $directory = public_path("assets/tenant_{$tenantId}/uploads/leadership/advisor");

            // Create the folder if it does not exist
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Delete the old photo if this is an update
            if (!empty($advisor->photo)) {
                $oldPhotoPath = $directory . '/' . $advisor->photo;

                if (File::exists($oldPhotoPath)) {
                    File::delete($oldPhotoPath);
                }
            }

            // Clean the advisor name for use as a filename
            $safeName = Str::slug(pathinfo($advisor->name, PATHINFO_FILENAME));

            $extension = strtolower($request->file('photo')->getClientOriginalExtension());

            // Unique name prevents one advisor overwriting another with the same name
            $newImageName = $safeName . '-' . uniqid() . '.' . $extension;

            $request->file('photo')->move($directory, $newImageName);

            $advisor->photo = $newImageName;
        }

        $advisor->save();

        return redirect()->back()->with('success', 'Done');
    }
}
