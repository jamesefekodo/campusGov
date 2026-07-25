<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media\RecognizedSocialMediaPlatform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SocialMediaController extends Controller
{
    public function recognizedSocialMediaPlatforms(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_platform.read'),
            403
        );


        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $platforms = '';
        $totalPlatforms = 0;


        if (Route::currentRouteName() == 'recognizedSocialMediaPlatformsSearch') {
            $searchKey = $request->input('searchKey');
            $platforms = RecognizedSocialMediaPlatform::where('name', 'like', "%$searchKey%")
                ->orWhere('description', 'like', "%$searchKey%")
                ->orderBy('name')
                ->paginate(20)->withQueryString();


            $totalPlatforms = $platforms->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $platforms = RecognizedSocialMediaPlatform::orderBy('name')->paginate(20);
            $totalPlatforms = RecognizedSocialMediaPlatform::count();
            $requestType = 'all';
            $pageTitle = 'Recognized social media platforms';
        }

        $data = [
            'platforms' => $platforms,
            'totalPlatforms' => $totalPlatforms,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.media.recognizedPlatforms.recognizedPlatforms', compact('data'));
    }

    public function addRecognizedSocialMediaPlatform(Request $request)
    {
        request()->validate([
            'logo' => 'required|image|mimes:jpeg,jpg,png,webp|max:200',
            'name' => 'required|string',
            'description' => 'required|required',
        ]);


        do {
            $platformSlug = 'soc-med-plat-' . now()->format('Ym') . '-' . Str::lower(Str::random(10));
        } while (RecognizedSocialMediaPlatform::where('slug', $platformSlug)->exists());

        $platform = new RecognizedSocialMediaPlatform();
        $platform->slug = $platformSlug;
        $platform->name = trim($request->name);
        $platform->description = trim($request->description);

        if ($request->hasFile('logo')) {
            $passport = $request->file('logo');
            $passportExtension = $passport->getClientOriginalExtension();
            $logoName = $platformSlug . '.' . $passportExtension;
            $passport->move(public_path('assets/uploads/users/media/recognizedPlatforms/logo'), $logoName);
            $platform->logo = $logoName;
        }
        $platform->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editRecognizedSocialMediaPlatform($slug, Request $request)
    {
        request()->validate([
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:200',
            'name' => 'required|string',
            'description' => 'required|required',
            'is_active' => 'required',
        ]);

        $platform = RecognizedSocialMediaPlatform::where('slug', $slug)->first();
        if (!$platform) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $platform->name = trim($request->name);
        $platform->description = trim($request->description);
        $platform->is_active = $request->is_active;

        if ($request->hasFile('logo')) {
            if ($platform->logo) {
                $oldLogoPath = public_path('assets/uploads/users/media/recognizedPlatforms/logo/' . $platform->logo);
                if (file_exists($oldLogoPath)) {
                    @unlink($oldLogoPath);
                }
            }

            $logo = $request->file('logo');
            $logoExtension = $logo->getClientOriginalExtension();
            $logoName = $platform->slug . '.' . $logoExtension;
            $logo->move(public_path('assets/uploads/users/media/recognizedPlatforms/logo'), $logoName);
            $platform->logo = $logoName;
        }
        $platform->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function deletePlatform($slug)
    {
        $platform = RecognizedSocialMediaPlatform::where('slug', $slug)->first();
        if (!$platform) {
            return redirect()->back()->with('error', 'No records found!');
        }

        if ($platform->logo) {
            $oldLogoPath = public_path('assets/uploads/users/media/recognizedPlatforms/logo/' . $platform->logo);
            if (file_exists($oldLogoPath)) {
                @unlink($oldLogoPath);
            }
        }
        $platform->delete();
        return redirect()->back()->with('success', 'Done!');
    }
}
