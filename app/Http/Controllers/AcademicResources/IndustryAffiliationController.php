<?php

namespace App\Http\Controllers\AcademicResources;

use App\Http\Controllers\Controller;
use App\Models\AcademicResources\IndustryAffiliate;
use App\Models\AcademicResources\TagsForIndustryAffiliate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class IndustryAffiliationController extends Controller
{
    public function industryAffiliates(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $industryAffiliates = '';
        $totalIndustryAffiliates = 0;
        $industryTags = TagsForIndustryAffiliate::orderBy('name')->get();


        if (Route::currentRouteName() == 'industryAffiliatesSearch') {
            $searchKey = $request->input('searchKey');
            $industryAffiliates = IndustryAffiliate::when($searchKey, function ($query) use ($searchKey) {
                $query->where(function ($q) use ($searchKey) {
                    $q->where('created_by', 'like', "%{$searchKey}%")
                        ->orWhere('title', 'like', "%{$searchKey}%")
                        ->orWhere('description', 'like', "%{$searchKey}%");
                });
            })
                ->orderByDesc('updated_at')->paginate(20)->withQueryString();

            $totalIndustryAffiliates = $industryAffiliates->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } elseif (Route::currentRouteName() == 'industryAffiliatesWihTag') {
            $tag = TagsForIndustryAffiliate::where(
                'slug',
                $request->slug
            )->first();

            if (!$tag) {
                return redirect()
                    ->back()
                    ->with('error', 'Tag not found!');
            }

            $industryAffiliates = $tag->industryAffiliates()
                ->paginate(20)
                ->withQueryString();

            $totalIndustryAffiliates = $industryAffiliates->total();
            $requestType = 'with_tag';
            $pageTitle = 'Affiliates with the tag :: ' . $tag->name;
        } else {
            $industryAffiliates = IndustryAffiliate::query()
                ->orderByDesc('updated_at')
                ->paginate(20);
            $totalIndustryAffiliates = IndustryAffiliate::count();
            $requestType = 'all';
            $pageTitle = 'Ecosystem & Industry Affiliate';
        }
        $data = [
            'industryAffiliates' => $industryAffiliates,
            'totalIndustryAffiliates' => $totalIndustryAffiliates,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'industryTags' => $industryTags,
        ];

        return view('users.admin.academicResources.industryAffiliates.industryAffiliates', compact('data'));
    }
    public function industryAffiliatesTags()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.manage_tags'),
            403
        );

        $industryTags = TagsForIndustryAffiliate::orderBy('name')->paginate(50);
        $data = [
            'industryTags' => $industryTags,
        ];

        return view('users.admin.academicResources.industryAffiliates.industryAffiliateTags', compact('data'));
    }
    public function addNewIndAffiliateSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.manage_tags'),
            403
        );

        request()->validate([
            'name' => 'required|string',
        ]);

        $tag = new TagsForIndustryAffiliate();
        $tag->name = trim($request->name);
        $tag->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editIndustryAffilateTagSubmit($slug, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.manage_tags'),
            403
        );

        request()->validate([
            'name' => 'required|string',
        ]);

        $tag = TagsForIndustryAffiliate::where('slug', $slug)->first();
        if (!$tag) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $tag->update([
            'name' => trim($request->name),
        ]);

        return redirect()->back()->with('success', 'Done!');
    }
    public function removeIndustryAffiliatesTag($slug)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.manage_tags'),
            403
        );

        $tag = TagsForIndustryAffiliate::where('slug', $slug)->first();
        if (!$tag) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $tag->delete();

        return redirect()->back()->with('success', 'Done!');
    }
    public function addNewIndustryAffiliateSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.create'),
            403
        );

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:300',
        ]);

        do {
            $affiliateSlug = 'indaf-' . now()->format('mY') . '-' . random_int(1000000000, 9999999999);
        } while (IndustryAffiliate::where('slug', $affiliateSlug)->exists());

        $affiliate = new IndustryAffiliate();
        $affiliate->slug = $affiliateSlug;
        $affiliate->created_by = Auth::user()->username;
        $affiliate->title = trim($request->title);
        $affiliate->description = $request->description;


        if ($request->hasFile('image')) {
            $filePath = 'assets/tenant_' . tenant()->id . '/uploads/academicResources/industryAffilate' . $affiliate->slug;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $newImage = $request->file('image');
            $newImageExtension = $newImage->getClientOriginalExtension();
            $newImageName = $affiliate->slug . '.' . $newImageExtension;
            $newImage->move(public_path('assets/tenant_' . tenant()->id . '/uploads/academicResources/industryAffilate'), $newImageName);
            $affiliate->cover_image = $newImageName;
        }
        $affiliate->save();

        // --------------------------------------------------------------------------
        // Process Tags
        // --------------------------------------------------------------------------
        if (!empty($request->tags)) {
            // Explode the comma-separated string, clean up whitespace, and remove empty values
            $tagNames = array_filter(array_map('trim', explode(',', $request->tags)));

            $tagIds = [];

            foreach ($tagNames as $tagName) {
                // Check if the tag exists for the current tenant or create it if missing
                $tag = TagsForIndustryAffiliate::firstOrCreate(
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

            // Attach/Sync each tag to the affiliate tag / affiliate relationship
            $affiliate->tags()->sync($tagIds);
        }
        return redirect()->back()->with('success', 'Done!');
    }
    public function viewIndustryAffiliates($slug)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.read'),
            403
        );

        $affiliate = IndustryAffiliate::where('slug', $slug)->first();
        if (!$affiliate) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $industryTags = TagsForIndustryAffiliate::orderBy('name')->get();


        $data = [
            'affiliate' => $affiliate,
            'industryTags' => $industryTags,
        ];

        return view('users.admin.academicResources.industryAffiliates.viewIndustryAffiliate', compact('data'));
    }
    public function updateIndustryAffiliateSubmit($slug, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.read'),
            403
        );
        $affiliate = IndustryAffiliate::where('slug', $slug)->first();
        if (!$affiliate) {
            return redirect()->back()->with('error', 'No records found!');
        }
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'is_published' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:300',
        ]);



        $affiliate->title = trim($request->title);
        $affiliate->description = $request->description;
        $affiliate->is_published = $request->is_published;

        // process the image
        if ($request->hasFile(key: 'image')) {
            $filePath = 'assets/tenant_' . tenant()->id . '/uploads/academicResources/industryAffilate' . $affiliate->slug;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $newImage = $request->file('image');
            $newImageExtension = $newImage->getClientOriginalExtension();
            $newImageName = $affiliate->slug . '.' . $newImageExtension;
            $newImage->move(public_path('assets/tenant_' . tenant()->id . '/uploads/academicResources/industryAffilate'), $newImageName);
            $affiliate->cover_image = $newImageName;
        }

        $affiliate->save();

        // --------------------------------------------------------------------------
        // Process Tags
        // --------------------------------------------------------------------------
        if (!empty($request->tags)) {
            // Explode the comma-separated string, clean up whitespace, and remove empty values
            $tagNames = array_filter(array_map('trim', explode(',', $request->tags)));

            $tagIds = [];

            foreach ($tagNames as $tagName) {
                // Check if the tag exists for the current tenant or create it if missing
                $tag = TagsForIndustryAffiliate::firstOrCreate(
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

            // Attach/Sync each tag to the tags for industry affiliate / affiliate relationship
            $affiliate->tags()->sync($tagIds);
        }
        return redirect()->back()->with('success', 'Done!');
    }
    public function deleteIndustryAffiliate($slug)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.delete'),
            403
        );
        $affiliate = IndustryAffiliate::where('slug', $slug)->first();
        if (!$affiliate) {
            return redirect()->back()->with('error', 'No records found!');
        }
        // Check if the course has a cover image attribute
        $coverImageName = $affiliate->cover_image ?? null;

        if ($coverImageName) {
            // Build the full public path to the image
            $coverPath = public_path(
                'assets/tenant_' .
                    tenant()->id .
                    '/uploads/academicResources/industryAffilate/' .
                    $coverImageName
            );

            // Delete the file from storage if it physically exists
            if (file_exists($coverPath)) {
                @unlink($coverPath);
            }
        }

        // Delete the course record from the database
        $affiliate->delete();
        return redirect()->route('industryAffiliates')->with('success', 'Done!');
    }


    // member
    public function memberIndustryAffiliates(Request $request)
    {
        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $industryAffiliates = '';
        $totalIndustryAffiliates = 0;
        $industryTags = TagsForIndustryAffiliate::orderBy('name')->get();


        if (Route::currentRouteName() == 'memberIndustryAffiliatesSearch') {
            $searchKey = $request->input('searchKey');
            $industryAffiliates = IndustryAffiliate::query()
                ->where('is_published', true)
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

            $totalIndustryAffiliates = $industryAffiliates->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } elseif (Route::currentRouteName() == 'memberIndustryAffiliatesFilter') {
            $tag = TagsForIndustryAffiliate::where(
                'slug',
                $request->slug
            )->first();

            if (!$tag) {
                return redirect()
                    ->back()
                    ->with('error', 'Tag not found!');
            }

            $industryAffiliates = $tag->industryAffiliates()
                ->where('is_published', true)
                ->paginate(20)
                ->withQueryString();

            $totalIndustryAffiliates = $industryAffiliates->total();
            $requestType = 'with_tag';
            $pageTitle = 'Affiliates with the tag :: ' . $tag->name;
        } else {
            $industryAffiliates = IndustryAffiliate::where('is_published', true)
                ->orderByDesc('updated_at')
                ->paginate(20);
            $totalIndustryAffiliates = IndustryAffiliate::where('is_published', true)->count();
            $requestType = 'all';
            $pageTitle = 'Ecosystem & Industry Affiliate';
        }
        $data = [
            'industryAffiliates' => $industryAffiliates,
            'totalIndustryAffiliates' => $totalIndustryAffiliates,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'industryTags' => $industryTags,
        ];

        return view('users.member.academicResources.industryAffiliates.industryAffiliates', compact('data'));
    }
    public function memberViewIndustryAffiliate($slug){
           $affiliate = IndustryAffiliate::where('is_published', true)->where('slug', $slug)->first();
        if (!$affiliate) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $data = [
            'affiliate' => $affiliate,
        ];
        return view('users.member.academicResources.industryAffiliates.viewIndustryAffiliate', compact('data'));
    }
}
