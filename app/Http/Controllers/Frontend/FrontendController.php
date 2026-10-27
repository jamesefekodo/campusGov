<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Department;
use App\Models\AcademicConfig\Faculty;
use App\Models\AcademicResources\IndustryAffiliate;
use App\Models\AcademicResources\PastQuestion;
use App\Models\AcademicResources\RecommendedBook;
use App\Models\AcademicResources\RecommendedCourse;
use App\Models\Config\HomepageSlider;
use App\Models\Leadership\StudentRepresentative\AssociationAdvisor;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipRepresentative;
use App\Models\Leadership\StudentRepresentative\StudentLeadershipTenure;
use App\Models\Members\MemberAcademicInfo;
use App\Services\ThemeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    public function checkLogin()
    {
        if (Auth::check()) {
            $userType = getUserType();

            if (in_array($userType, ['root', 'admin'])) {
                return redirect()->route('adminDashboard');
            }
            if ($userType === 'member') {
                return redirect()->route('memberDashboard');
            }
        }

        return null; // No redirection, continue execution
    }

    // public function homepage()
    // {
    //     // Call the login check function
    //     $redirect = $this->checkLogin();
    //     if ($redirect) {
    //         return $redirect;
    //     }

    //     // return view('users.guests.temporaryHomepage');

    //     return view('users.guests.homepage');
    // }



    public function homepage()
    {
        $redirect = $this->checkLogin();

        if ($redirect) {
            return $redirect;
        }

        $tenant = tenant();

        $themeService = app(ThemeService::class);

        $theme = $themeService->activeTheme($tenant);
        $themeColors = $themeService->forTenant($tenant);

        // ############################
        $basicInfo = basicSystemInfo();
        $operationalLevel = $basicInfo->operational_level;
        $undergraduatesCount = MemberAcademicInfo::where('academic_status', 'student')
            ->whereHas('userProfile', function ($query) {
                $query->where('account_type', 2)->where('account_status', 2);
            })->count();
        $alumniCount = MemberAcademicInfo::query()
            ->where('academic_status', 'alumni')
            ->whereHas('userProfile', function ($query) {
                $query->where('account_type', 2)
                    ->where('account_status', 2);
            })
            ->count();
        // Format function: rounds to nearest 5 and appends '+'
        $formatCount = function ($count) {
            $rounded = round($count / 5) * 5;
            return $rounded . '+';
        };

        $undergraduates = $formatCount($undergraduatesCount);
        $alumni = $formatCount($alumniCount);

        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        $pastquestions = PastQuestion::where('approval_status', 'approved')->count();
        $recommendedCourses = RecommendedCourse::where('is_published', true)->count();
        $recommendedBooks = RecommendedBook::where('is_published', true)->count();
        $industryAffilates = IndustryAffiliate::where('is_published', true)->count();

        $totalAcademicResources = $pastquestions + $recommendedCourses + $recommendedBooks + $industryAffilates;

        $academicSession = currentAcademicYear();
        $thePresident = StudentLeadershipRepresentative::query()
            ->whereHas('tenure', function ($query) use ($academicSession) {
                $query->where('academic_session', $academicSession);
            })
            ->whereHas('position', function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['president']);
            })
            ->with(['position', 'tenure', 'theRep'])
            ->first();


        $data = [
            'sliders' => HomepageSlider::orderBy('order')->get(),
            'operationalLevel' => $operationalLevel,
            'undergraduates' => $undergraduates,
            'alumni' => $alumni,
            'faculties' => $faculties,
            'departments' => $departments,
            'totalAcademicResources' => $totalAcademicResources,
            'thePresident' => $thePresident,
            'basicInfo' => $basicInfo,
            'currentAcademicYear' => currentAcademicYear(),
        ];

        return view($themeService->homepageView($tenant), [
            'theme' => $theme,
            'themeColors' => $themeColors,
            'data' => $data,
        ]);
    }

    // public function guestAboutUs(){
    //     $redirect = $this->checkLogin();

    //     if ($redirect) {
    //         return $redirect;
    //     }

    //     $tenant = tenant();

    //     $themeService = app(ThemeService::class);

    //     $theme = $themeService->activeTheme($tenant);
    //     $themeColors = $themeService->forTenant($tenant);

    //     // ############################
    //     $basicInfo = basicSystemInfo();

    //     $data = [
    //         'basicInfo' => $basicInfo,
    //     ];

    //     return view($themeService->homepageView($tenant, 'about.aboutUs'), [
    //         'theme' => $theme,
    //         'themeColors' => $themeColors,
    //         'data' => $data,
    //     ]);
    // }
    public function guestAboutUs(Request $request)
    {
        $redirect = $this->checkLogin();

        if ($redirect) {
            return $redirect;
        }

        $tenant = tenant();

        $themeService = app(ThemeService::class);

        $theme = $themeService->activeTheme($tenant);
        $themeColors = $themeService->forTenant($tenant);

        $basicInfo = basicSystemInfo();
        $currentAcademicSession = currentAcademicYear();

        $thePresident = StudentLeadershipRepresentative::query()
            ->whereHas('tenure', function ($query) use ($currentAcademicSession) {
                $query->where('academic_session', $currentAcademicSession);
            })
            ->whereHas('position', function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['president']);
            })
            ->with(['position', 'tenure', 'theRep'])
            ->first();


              $academicSession = $request->academic_session ?? currentAcademicYear();

        // Fetch student leaders
        $studentLeaders = StudentLeadershipRepresentative::query()
            ->with([
                'position.arm',
                'position',
                'tenure',
                'theRep.memberPersonalProfile',
                'theRep.memberSocialMediaHandle.thePlatform',
            ])
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
            ->get();

        // Fetch the selected tenure
        $selectedTenure = StudentLeadershipTenure::where(
            'academic_session',
            $academicSession
        )->first();

        // Fetch advisors
        $advisors = AssociationAdvisor::query()
            ->when(
                $selectedTenure,
                function ($query) use ($selectedTenure) {
                    $tenureStart = $selectedTenure->start_date;
                    $tenureEnd = $selectedTenure->end_date ?? today();

                    $query
                        ->whereDate('start_date', '<=', $tenureEnd)
                        ->where(function ($query) use ($tenureStart) {
                            $query->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', $tenureStart);
                        });
                },
                function ($query) {
                    $query->whereRaw('1 = 0');
                }
            )
            ->orderByDesc('start_date')
            ->orderBy('name')
            ->get();

        $data = [
            'basicInfo' => $basicInfo,
            'thePresident' => $thePresident,
            'studentLeaders' => $studentLeaders,
            'selectedTenure' => $selectedTenure,
            'advisors' => $advisors,
            'academicSession' => $academicSession,
        ];


        return view($themeService->view($tenant, 'about.aboutUs'), [
            'theme' => $theme,
            'themeColors' => $themeColors,
            'data' => $data,
        ]);
    }

    // public function publications()
    // {
    //     $tenant = tenant();
    //     $themeService = app(ThemeService::class);

    //     $publications = Publication::latest('published_at')->paginate(12);

    //     return view($themeService->view($tenant, 'publications'), [
    //         'publications' => $publications,
    //         'theme' => $themeService->activeTheme($tenant),
    //         'themeColors' => $themeService->forTenant($tenant),
    //     ]);
    // }
}
