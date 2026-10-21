<?php

namespace App\Http\Controllers\AcademicResources;

use App\Http\Controllers\Controller;
use App\Models\AcademicResources\RecommendedCourse;
use App\Models\AcademicResources\TagsForRecommendedCourses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RecommendedCourseController extends Controller
{
    public function recommendedCourses(Request $request)
    {
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
    public function recommendedCourseTags()
    {
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
    public function addNewCourseTagSubmit(Request $request)
    {
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
    public function editCourseTagSubmit($slug, Request $request)
    {
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
    public function removeCourseTag($slug)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_course.manage_tags'),
            403
        );

        $tag = TagsForRecommendedCourses::where('slug', $slug)->first();
        if (!$tag) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $tag->delete();

        return redirect()->back()->with('success', 'Done!');
    }
    public function addNewRecommendedCourseSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_course.create'),
            403
        );

        $request->validate([
            'title' => 'required',
            'file_url' => 'nullable|url',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:300',
        ]);

        do {
            $courseSlug = 'rec-cs-' . now()->format('mY') . '-' . random_int(1000000000, 9999999999);
        } while (RecommendedCourse::where('slug', $courseSlug)->exists());

        $course = new RecommendedCourse();
        $course->slug = $courseSlug;
        $course->created_by = Auth::user()->username;
        $course->title = trim($request->title);
        $course->description = $request->description;
        $course->file_url = trim($request->file_url);


        if ($request->hasFile('image')) {
            $filePath = 'assets/tenant_' . tenant()->id . '/uploads/academicResources/recommendedCourses' . $course->slug;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $newImage = $request->file('image');
            $newImageExtension = $newImage->getClientOriginalExtension();
            $newImageName = $course->slug . '.' . $newImageExtension;
            $newImage->move(public_path('assets/tenant_' . tenant()->id . '/uploads/academicResources/recommendedCourses'), $newImageName);
            $course->cover_image = $newImageName;
        }
        $course->save();

        // --------------------------------------------------------------------------
        // Process Tags
        // --------------------------------------------------------------------------
        if (!empty($request->tags)) {
            // Explode the comma-separated string, clean up whitespace, and remove empty values
            $tagNames = array_filter(array_map('trim', explode(',', $request->tags)));

            $tagIds = [];

            foreach ($tagNames as $tagName) {
                // Check if the tag exists for the current tenant or create it if missing
                $tag = TagsForRecommendedCourses::firstOrCreate(
                    [
                        'tenant_id' => tenant()->id,
                        'name'      => $tagName,
                    ],
                    [
                        'slug'      => Str::slug($tagName),
                    ]
                );

                $tagIds[] = $tag->id;
            }

            // Attach/Sync each tag to the course tag / course relationship
            $course->tags()->sync($tagIds);
        }
        return redirect()->back()->with('success', 'Done!');
    }
    public function viewRecommendedCourse($slug)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_course.read'),
            403
        );

        $course = RecommendedCourse::where('slug', $slug)->first();
        if (!$course) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $courseTags = TagsForRecommendedCourses::orderBy('name')->get();


        $data = [
            'course' => $course,
            'courseTags' => $courseTags,
        ];

        return view('users.admin.academicResources.recommendedCourses.viewRecommendedCourses', compact('data'));
    }
    public function updateRecommendedCoursesubmit($slug, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_course.update'),
            403
        );
        $course = RecommendedCourse::where('slug', $slug)->first();
        if (!$course) {
            return redirect()->back()->with('error', 'No records found!');
        }
        $request->validate([
            'title' => 'required',
            'file_url' => 'nullable|url',
            'description' => 'required',
            'is_published' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:300',
        ]);



        $course->title = trim($request->title);
        $course->description = $request->description;
        $course->file_url = trim($request->file_url);
        $course->is_published = $request->is_published;

        // process the image
        if ($request->hasFile(key: 'image')) {
            $filePath = 'assets/tenant_' . tenant()->id . '/uploads/academicResources/recommendedCourses' . $course->slug;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $newImage = $request->file('image');
            $newImageExtension = $newImage->getClientOriginalExtension();
            $newImageName = $course->slug . '.' . $newImageExtension;
            $newImage->move(public_path('assets/tenant_' . tenant()->id . '/uploads/academicResources/recommendedCourses'), $newImageName);
            $course->cover_image = $newImageName;
        }

        $course->save();

        // --------------------------------------------------------------------------
        // Process Tags
        // --------------------------------------------------------------------------
        if (!empty($request->tags)) {
            // Explode the comma-separated string, clean up whitespace, and remove empty values
            $tagNames = array_filter(array_map('trim', explode(',', $request->tags)));

            $tagIds = [];

            foreach ($tagNames as $tagName) {
                // Check if the tag exists for the current tenant or create it if missing
                $tag = TagsForRecommendedCourses::firstOrCreate(
                    [
                        'tenant_id' => tenant()->id,
                        'name'      => $tagName,
                    ],
                    [
                        'slug'      => Str::slug($tagName),
                    ]
                );

                $tagIds[] = $tag->id;
            }

            // Attach/Sync each tag to the TagsForRecommendedBooks / book relationship
            $course->tags()->sync($tagIds);
        }
        return redirect()->back()->with('success', 'Done!');
    }
    public function deleteRecommendedCourse($slug)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_course.delete'),
            403
        );
        $course = RecommendedCourse::where('slug', $slug)->first();
        if (!$course) {
            return redirect()->back()->with('error', 'No records found!');
        }
        // Check if the course has a cover image attribute
        $coverImageName = $course->cover_image ?? null;

        if ($coverImageName) {
            // Build the full public path to the image
            $coverPath = public_path(
                'assets/tenant_' .
                    tenant()->id .
                    '/uploads/academicResources/recommendedCourses/' .
                    $coverImageName
            );

            // Delete the file from storage if it physically exists
            if (file_exists($coverPath)) {
                @unlink($coverPath);
            }
        }

        // Delete the course record from the database
        $course->delete();
        return redirect()->route('recommendedCourses')->with('success', 'Done!');
    }



    // member view
    public function memberRecommendedCourses(Request $request)
    {
        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $recommendedCourses = '';
        $totalRecommendedCourses = 0;
        $courseTags = TagsForRecommendedCourses::orderBy('name')->get();


        if (Route::currentRouteName() == 'memberRecommendedCoursesSearch') {
            $searchKey = $request->input('searchKey');
            $recommendedCourses = RecommendedCourse::where('is_published', true)
                ->when($searchKey, function ($query) use ($searchKey) {
                    $query->where(function ($q) use ($searchKey) {
                        $q->where('created_by', 'like', "%{$searchKey}%")
                            ->orWhere('title', 'like', "%{$searchKey}%")
                            ->orWhere('description', 'like', "%{$searchKey}%");
                    });
                })
                ->orderByDesc('updated_at')
                ->paginate(20)
                ->withQueryString();

            $totalRecommendedCourses = $recommendedCourses->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } elseif (Route::currentRouteName() == 'memberRecommendedCoursesFilter') {
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
                ->where('is_published', true)
                ->paginate(20)
                ->withQueryString();

            $totalRecommendedCourses = $recommendedCourses->total();
            $requestType = 'with_tag';
            $pageTitle = 'Courses with the tag :: ' . $tag->name;
        } else {
            $recommendedCourses = RecommendedCourse::where('is_published', true)
                ->orderByDesc('updated_at')
                ->paginate(20);
            $totalRecommendedCourses = RecommendedCourse::where('is_published', true)->count();
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

        return view('users.member.academicResources.recommendedCourses.recommendedCourses', compact('data'));
    }
    public function memberViewRecommendedCourse($slug)
    {
        $recommendedCourse = RecommendedCourse::where('is_published', true)->where('slug', $slug)->first();
        if (!$recommendedCourse) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $data = [
            'course' => $recommendedCourse,
        ];
        return view('users.member.academicResources.recommendedCourses.viewRecommendedCourses', compact('data'));
    }
}
