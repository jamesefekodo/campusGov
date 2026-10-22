<?php

namespace App\Http\Controllers\Leadership;

use App\Http\Controllers\Controller;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipRepresentative;
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
}
