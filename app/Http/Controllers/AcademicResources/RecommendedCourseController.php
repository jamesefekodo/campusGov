<?php

namespace App\Http\Controllers\AcademicResources;

use App\Http\Controllers\Controller;
use App\Models\AcademicResources\RecommendedCourse;
use App\Models\AcademicResources\TagsForRecommendedCourses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RecommendedCourseController extends Controller
{
    public function recommendedCourses(Request $request){
           /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_course.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $recommendedCourses = '';
        $totalRecommendedCourses = 0;
        $courseTags = TagsForRecommendedCourses::orderBy('name')->get();


        if (Route::currentRouteName() == 'recommendedCoursesSearch') {
            $searchKey = $request->input('searchKey');
            $recommendedCourses = RecommendedCourse::when($searchKey, function ($query) use ($searchKey) {
                $query->where(function ($q) use ($searchKey) {
                    $q->where('created_by', 'like', "%{$searchKey}%")
                        ->orWhere('title', 'like', "%{$searchKey}%")
                        ->orWhere('description', 'like', "%{$searchKey}%");
                });
            })
                ->orderByDesc('updated_at')->paginate(20)->withQueryString();

            $totalRecommendedCourses = $recommendedCourses->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } elseif (Route::currentRouteName() == 'recommendedCoursesWihTag') {
            $tag = TagsForRecommendedCourses::where(
                'slug',
                $request->slug
            )->first();

            if (!$tag) {
                return redirect()
                    ->back()
                    ->with('error', 'Tag not found!');
            }

            $recommendedCourses = $tag->recommendedCourses()
                ->paginate(20)
                ->withQueryString();

            $totalRecommendedCourses = $recommendedCourses->total();
            $requestType = 'with_tag';
            $pageTitle = 'Courses with the tag :: ' . $tag->name;
        } else {
            $recommendedCourses = RecommendedCourse::query()
                ->orderByDesc('updated_at')
                ->paginate(20);
            $totalRecommendedCourses = RecommendedCourse::count();
            $requestType = 'all';
            $pageTitle = 'Recommended Courses';
        }
        $data = [
            'recommendedCourses' => $recommendedCourses,
            'totalRecommendedCourses' => $totalRecommendedCourses,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'courseTags' => $courseTags,
        ];

        return view('users.admin.academicResources.recommendedCourses.recommendedCourses', compact('data'));
    }
    public function recommendedCourseTags(){
         /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_course.manage_tags'),
            403
        );

        $courseTags = TagsForRecommendedCourses::orderBy('name')->paginate(50);
        $data = [
            'courseTags' => $courseTags,
        ];

        return view('users.admin.academicResources.recommendedCourses.courseTags', compact('data'));
    }
    public function addNewCourseTagSubmit(Request $request){
           /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_course.manage_tags'),
            403
        );

         request()->validate([
            'name' => 'required|string',
        ]);

        $tag = new TagsForRecommendedCourses();
        $tag->name = trim($request->name);
        $tag->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editCourseTagSubmit($slug, Request $request){
      /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_course.manage_tags'),
            403
        );

         request()->validate([
            'name' => 'required|string',
        ]);

        $tag = TagsForRecommendedCourses::where('slug', $slug)->first();
        if (!$tag) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $tag->update([
            'name' => trim($request->name),
        ]);

        return redirect()->back()->with('success', 'Done!');
    }
}
