<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media\MediaPublication;
use App\Models\Media\TagsForPublication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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
        $mediaTags = TagsForPublication::orderBy('name')->get();


        if (Route::currentRouteName() == 'mediaPublicationsSearch') {
            $searchKey = $request->input('searchKey');
            $publications = MediaPublication::query()
                ->whereNotIn('approval_status', ['pending', 'draft'])
                ->where('is_active', true)
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
            $publications = MediaPublication::whereNotIn('approval_status', ['pending', 'draft'])
                ->orderBy('updated_at', 'desc')
                ->paginate(20);
            $totalPublications = MediaPublication::whereNotIn('approval_status', ['pending', 'draft'])->count();
            $requestType = 'all';
            $pageTitle = 'Media Publications';
        }

        $data = [
            'publications' => $publications,
            'totalPublications' => $totalPublications,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'mediaTags' => $mediaTags,
        ];

        return view('users.admin.media.publications.mediaPublications', compact('data'));
    }

    public function mediaPublicationTags()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_publication.manage_tags'),
            403
        );

        $mediaTags = TagsForPublication::orderBy('name')->paginate(50);
        $data = [
            'mediaTags' => $mediaTags,
        ];

        return view('users.admin.media.publications.mediaTags', compact('data'));
    }
    public function addNewPublicationTagSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_publication.manage_tags'),
            403
        );

        request()->validate([
            'name' => 'required|string',
        ]);


        // do {
        //     $tagSlug = 'publ-tag-' . now()->format('Ymd') . '-' . Str::lower(Str::random(10));
        // } while (TagsForPublication::where('slug', $tagSlug)->exists());

        $tag = new TagsForPublication();
        // $tag->slug = $tagSlug;
        $tag->name = trim($request->name);
        $tag->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editPublicationTagSubmit($slug, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_publication.manage_tags'),
            403
        );

        request()->validate([
            'name' => 'required|string',
        ]);

        $tag = TagsForPublication::where('slug', $slug)->first();
        if (!$tag) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $tag->update([
            'name' => trim($request->name),
        ]);

        return redirect()->back()->with('success', 'Done!');
    }
    public function removePublicationTag($slug)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_publication.manage_tags'),
            403
        );

        $tag = TagsForPublication::where('slug', $slug)->first();
        if (!$tag) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $tag->delete();

        return redirect()->back()->with('success', 'Done!');
    }
    public function addNewMediaPublicationSubmit(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required',
            'meta_description' => 'required|string',
            'meta_keywords' => 'required|string',
            'status' => 'required|string|in:submit_for_approval,draft',
            'tags' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2097152',
        ]);


        $publication = new MediaPublication();
        $publication->author = Auth::user()->username;
        $publication->title = trim($request->title);
        $publication->content = $request->content;
        $publication->meta_description = trim($request->meta_description);
        $publication->meta_keywords = trim($request->meta_keywords);
        $publication->last_update = Carbon::now();
        $publication->last_update_by = Auth::user()->username;
        if ($request->status == '') {
            $publication->approval_status = 'draft';
        } else {
            $publication->approval_status = 'pending';
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $mime = $file->getMimeType();
            $ext  = strtolower($file->getClientOriginalExtension());

            $uploadPath = public_path('assets/tenant_' . tenant()->id . '/uploads/media/publications');

            File::ensureDirectoryExists($uploadPath);

            // Base filename
            $baseName = Str::slug($request->header, '_');

            $baseName = Str::of($baseName)
                ->replaceMatches('/[^A-Za-z0-9]+/', '_')
                ->trim('_')
                ->toString();

            // Guard: never allow an empty base name to reach glob()/unlink()
            if (empty($baseName)) {
                $baseName = 'publication_' . Str::random(8);
            }

            $fallbackName = $baseName . '.' . $ext;
            $counter = 1;

            // Delete existing file(s) with same base name regardless of extension
            foreach (glob($uploadPath . '/' . $baseName . '.*') as $existingFile) {
                // Defensive: never unlink directories, even if glob somehow returns one
                if (is_file($existingFile)) {
                    unlink($existingFile);
                }
            }

            // Avoid overwriting existing files
            while (file_exists($uploadPath . '/' . $fallbackName)) {
                $fallbackName = $baseName . '-' . $counter . '.' . $ext;
                $counter++;
            }

            $fallbackPath = $uploadPath . '/' . $fallbackName;

            /*
            |--------------------------------------------------------------------------
            | GD + WebP processing
            |--------------------------------------------------------------------------
            */
            $processed = false;

            if (function_exists('imagewebp') && function_exists('imagecreatetruecolor')) {

                $tmp = $file->getPathname();

                $src = match ($mime) {
                    'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($tmp) : null,
                    'image/png'  => function_exists('imagecreatefrompng')  ? @imagecreatefrompng($tmp)  : null,
                    'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmp) : null,
                    default => null,
                };

                if ($src) {
                    $width  = imagesx($src);
                    $height = imagesy($src);

                    // Resize if needed
                    $maxSize = 800;
                    $scale = min(1, $maxSize / max($width, $height));

                    if ($scale < 1) {
                        $newW = (int)($width * $scale);
                        $newH = (int)($height * $scale);
                        $dst = imagecreatetruecolor($newW, $newH);

                        // Preserve transparency
                        imagealphablending($dst, false);
                        imagesavealpha($dst, true);
                        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                        imagefill($dst, 0, 0, $transparent);

                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
                        imagedestroy($src);
                        $src = $dst;
                    }

                    // WebP conversion with quality loop
                    $webpName = $baseName . '.webp';
                    $webpCounter = 1;
                    $webpPath = $uploadPath . '/' . $webpName;

                    while (file_exists($webpPath)) {
                        $webpName = $baseName . '-' . $webpCounter . '.webp';
                        $webpPath = $uploadPath . '/' . $webpName;
                        $webpCounter++;
                    }

                    $quality = 90;
                    do {
                        imagewebp($src, $webpPath, $quality);
                        clearstatcache();

                        if (filesize($webpPath) <= 1048576) {
                            $processed = true;
                            $publication->cover_image = $webpName;
                            imagedestroy($src);
                            break;
                        }

                        $quality -= 5;
                    } while ($quality >= 40);

                    if (!$processed) {
                        @unlink($webpPath);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Fallback: move original file
            |--------------------------------------------------------------------------
            */
            if (!$processed) {
                $file->move($uploadPath, $fallbackName);
                $publication->cover_image = $fallbackName;
            }
        }
        $publication->save();

        // --------------------------------------------------------------------------
        // Process Tags
        // --------------------------------------------------------------------------
        if (!empty($request->tags)) {
            // Explode the comma-separated string, clean up whitespace, and remove empty values
            $tagNames = array_filter(array_map('trim', explode(',', $request->tags)));

            $tagIds = [];

            foreach ($tagNames as $tagName) {
                // Check if the tag exists for the current tenant or create it if missing
                $tag = TagsForPublication::firstOrCreate(
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

            // Attach/Sync each tag to the PublicationTag / publication relationship
            $publication->tags()->sync($tagIds);
        }
        return redirect()->back()->with('success', 'Done!');
    }
    public function mediaPublicationsPendingApproval(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_publication.update'),
            403
        );


        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $publications = '';
        $totalPublications = 0;
        $mediaTags = TagsForPublication::orderBy('name')->get();


        if (Route::currentRouteName() == 'mediaPublicationsPendingApprovalSearch') {
            $searchKey = $request->input('searchKey');
            $publications = MediaPublication::query()
                ->where('approval_status', 'pending')
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
            $publications = MediaPublication::where('approval_status', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(20);
            $totalPublications = MediaPublication::where('approval_status', 'pending')->count();
            $requestType = 'all';
            $pageTitle = 'Media Publications :: Review/Approval';
        }

        $data = [
            'publications' => $publications,
            'totalPublications' => $totalPublications,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'mediaTags' => $mediaTags,
        ];

        return view('users.admin.media.publications.mediaPublications', compact('data'));
    }

    public function mediaPublicationsDraft(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_publication.update'),
            403
        );


        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $publications = '';
        $totalPublications = 0;
        $mediaTags = TagsForPublication::orderBy('name')->get();


        if (Route::currentRouteName() == 'mediaPublicationsDraftSearch') {
            $searchKey = $request->input('searchKey');
            $publications = MediaPublication::query()
                ->where('approval_status', 'draft')
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
            $publications = MediaPublication::where('approval_status', 'draft')
                ->orderBy('updated_at', 'desc')
                ->paginate(20);
            $totalPublications = MediaPublication::where('approval_status', 'draft')->count();
            $requestType = 'all';
            $pageTitle = 'Media Publications :: Drafts';
        }

        $data = [
            'publications' => $publications,
            'totalPublications' => $totalPublications,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'mediaTags' => $mediaTags,
        ];

        return view('users.admin.media.publications.mediaPublications', compact('data'));
    }

    public function viewMediaPublication($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_publication.read'),
            403
        );

        $publication = MediaPublication::where('uuid', $uuid)->first();

        if (!$publication) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $mediaTags = TagsForPublication::orderBy('name')->get();

        $data = [
            'publication' => $publication,
            'mediaTags' => $mediaTags,
        ];
        return view('users.admin.media.publications.viewMediaPublications', compact('data'));
    }
}
