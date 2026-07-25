<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media\RecognizedSocialMediaPlatform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
}
