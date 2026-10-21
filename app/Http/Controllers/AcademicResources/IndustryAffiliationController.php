<?php

namespace App\Http\Controllers\AcademicResources;

use App\Http\Controllers\Controller;
use App\Models\AcademicResources\IndustryAffiliate;
use App\Models\AcademicResources\TagsForIndustryAffiliate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
            $tag = IndustryAffiliate::where(
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
            $pageTitle = 'Courses with the tag :: ' . $tag->name;
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
    public function industryAffiliatesTags(){
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
    public function addNewIndAffiliateSubmit(Request $request){
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
    public function editIndustryAffilateTagSubmit($slug, Request $request){
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
    public function removeIndustryAffiliatesTag($slug){
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
}
