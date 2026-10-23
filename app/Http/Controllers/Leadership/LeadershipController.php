<?php

namespace App\Http\Controllers\Leadership;

use App\Http\Controllers\Controller;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipRepresentative;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipTenure;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipArm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

        $studentLeaders = '';

        $academicSession = $request->academic_session ?? currentAcademicYear();

        $studentLeaders = StudentLeadershipRepresentative::query()
            ->whereHas('tenure', function ($query) use ($academicSession) {
                $query->where('academic_session', $academicSession);
            })
            ->leftJoin(
                'student_leadership_arms as arms',
                'student_leadership_representative.arm_slug',
                '=',
                'arms.slug'
            )
            ->leftJoin(
                'student_leadership_positions as positions',
                'student_leadership_representative.position_slug',
                '=',
                'positions.slug'
            )
            ->select('student_leadership_representative.*')
            ->orderBy('positions.sort_order', 'asc')
            ->orderBy('arms.sort_order', 'asc')
            ->orderByRaw("
        CASE
            WHEN arms.slug = 'executive' THEN 1
            WHEN arms.slug = 'legislative' THEN 2
            ELSE 3
        END ASC
    ")
            ->with(['arm', 'position', 'tenure'])
            ->paginate(20);



        $data = [
            'studentLeaders' => $studentLeaders,
            'academicSession' => $academicSession,
        ];
        return view('users.admin.leadership.studentReps.studentReps', compact('data'));
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
    public deleteStudentLeadershipArm($slug){
          $arm = StudentLeadershipArm::where('slug', $slug)->first();
        if (!$arm) {
            return redirect()->back()->with('error', 'Records not found!');
        }

          if ($arm->representatives()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete arms with representatives, please remove representatives first!');
        }
        $tenure->delete();
        return redirect()->back()->with('success', 'Done');
    }
}
