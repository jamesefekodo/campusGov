<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media\MediaPublication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class MediaPublicationController extends Controller
{
    public function mediaPublications(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_publication.read'),
            403
        );


        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $publications = '';
        $totalPublications = 0;


        if (Route::currentRouteName() == 'mediaPublicationsSearch') {
            $searchKey = $request->input('searchKey');
            $publications = MediaPublication::query()
                ->where('approval_status', 'approved')
                ->where('is_active', true)
                ->whereNotNull('published_at')
                ->where(function ($query) use ($searchKey) {
                    $query->where('title', 'like', "%{$searchKey}%")
                        ->orWhere('content', 'like', "%{$searchKey}%")
                        ->orWhere('meta_description', 'like', "%{$searchKey}%")
                        ->orWhere('meta_keywords', 'like', "%{$searchKey}%");
                })
                ->orderByDesc('published_at')
                ->paginate(20)
                ->withQueryString();


            $totalPublications = $publications->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $publications = MediaPublication::where('approval_status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(20);
            $totalPublications = MediaPublication::where('approval_status', '!=', 'pending')->count();
            $requestType = 'all';
            $pageTitle = 'Media Publications';
        }

        $data = [
            'publications' => $publications,
            'totalPublications' => $totalPublications,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.admin.media.publications.mediaPublications', compact('data'));
    }
}
