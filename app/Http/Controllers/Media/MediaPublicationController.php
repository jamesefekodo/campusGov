<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media\MediaPublication;
use App\Models\Media\PublicationComment;
use App\Models\Media\PublicationCommentLike;
use App\Models\Media\PublicationLike;
use App\Models\Media\PublicationTag;
use App\Models\Media\TagsForPublication;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class MediaPublicationController extends Controller
{
    public function togglePublicationLike(string $uuid)
    {
        $publication = MediaPublication::where('uuid', $uuid)
            ->where('tenant_id', tenant()->id)
            ->firstOrFail();

        $username = Auth::user()->username;

        $like = PublicationLike::where('tenant_id', tenant()->id)
            ->where('media_publication_id', $publication->id)
            ->where('username', $username)
            ->first();

        if ($like) {
            $like->delete();

            $liked = false;
            $message = 'Publication unliked successfully.';
        } else {
            PublicationLike::create([
                'tenant_id' => tenant()->id,
                'media_publication_id' => $publication->id,
                'username' => $username,
            ]);

            $liked = true;
            $message = 'Publication liked successfully.';
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => PublicationLike::where(
                'media_publication_id',
                $publication->id
            )->count(),
            'message' => $message,
        ]);
    }

    public function storeComment(Request $request, string $publicationUuid)
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'min:2', 'max:3000'],
        ]);

        $publication = MediaPublication::where('uuid', $publicationUuid)
            ->where('tenant_id', tenant()->id)
            ->where('is_active', true)
            ->firstOrFail();

        $comment = PublicationComment::create([
            'tenant_id' => tenant()->id,
            'media_publication_id' => $publication->id,
            'parent_id' => null,
            'username' => Auth::user()->username,
            'comment' => trim($validated['comment']),
            'status' => 'approved',
            'is_active' => true,
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully.',
            'comment' => [
                'uuid' => $comment->uuid,
                'username' => $comment->username,
                'name' => $comment->user->name ?? $comment->username,
                'initial' => strtoupper(substr(
                    $comment->user->name ?? $comment->username,
                    0,
                    1
                )),
                'comment' => trim($comment->comment),
                'created_at' => $comment->created_at->diffForHumans(),
                'likes_count' => 0,
            ],
            'comments_count' => PublicationComment::where(
                'media_publication_id',
                $publication->id
            )
                ->where('status', 'approved')
                ->where('is_active', true)
                ->count(),
        ]);
    }

    public function storeReply(
        Request $request,
        string $publicationUuid,
        string $commentUuid
    ) {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'min:2', 'max:3000'],
        ]);

        $publication = MediaPublication::where('uuid', $publicationUuid)
            ->where('tenant_id', tenant()->id)
            ->where('is_active', true)
            ->firstOrFail();

        $parentComment = PublicationComment::where('uuid', $commentUuid)
            ->where('tenant_id', tenant()->id)
            ->where('media_publication_id', $publication->id)
            ->whereNull('parent_id')
            ->where('status', 'approved')
            ->where('is_active', true)
            ->firstOrFail();

        $reply = PublicationComment::create([
            'tenant_id' => tenant()->id,
            'media_publication_id' => $publication->id,
            'parent_id' => $parentComment->id,
            'username' => Auth::user()->username,
            'comment' => trim($validated['comment']),
            'status' => 'approved',
            'is_active' => true,
        ]);

        $reply->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Reply added successfully.',
            'reply' => [
                'uuid' => $reply->uuid,
                'username' => $reply->username,
                'name' => $reply->user->name ?? $reply->username,
                'initial' => strtoupper(substr(
                    $reply->user->name ?? $reply->username,
                    0,
                    1
                )),
                'comment' => trim($reply->comment),
                'created_at' => $reply->created_at->diffForHumans(),
                'likes_count' => 0,
            ],
        ]);
    }
    public function toggleCommentLike(string $commentUuid)
    {
        $comment = PublicationComment::where('uuid', $commentUuid)
            ->where('tenant_id', tenant()->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->firstOrFail();

        $username = Auth::user()->username;

        $like = PublicationCommentLike::where('tenant_id', tenant()->id)
            ->where('publication_comment_id', $comment->id)
            ->where('username', $username)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
            $message = 'Like removed successfully.';
        } else {
            PublicationCommentLike::create([
                'tenant_id' => tenant()->id,
                'publication_comment_id' => $comment->id,
                'username' => $username,
            ]);

            $liked = true;
            $message = 'Comment liked successfully.';
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => PublicationCommentLike::where(
                'publication_comment_id',
                $comment->id
            )->count(),
            'message' => $message,
        ]);
    }
    public function updateComment(
        Request $request,
        string $commentUuid
    ) {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'min:2', 'max:3000'],
        ]);

        $comment = PublicationComment::where('uuid', $commentUuid)
            ->where('tenant_id', tenant()->id)
            ->where('username', Auth::user()->username)
            ->firstOrFail();

        $comment->update([
            'comment' => $validated['comment'],
            'edited_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully.',
            'uuid' => $comment->uuid,
            'comment' => $comment->comment,
            'edited_label' => 'Edited',
        ]);
    }

    public function destroyComment(string $commentUuid)
    {
        $comment = PublicationComment::where('uuid', $commentUuid)
            ->where('tenant_id', tenant()->id)
            ->where('username', Auth::user()->username)
            ->firstOrFail();

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully.',
            'uuid' => $commentUuid,
        ]);
    }
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
        } elseif (Route::currentRouteName() == 'mediaPublicationWihTag') {
            $tag = TagsForPublication::where('slug', $request->slug)->first();
            if (!$tag) {
                return redirect()->back('error', 'Slug records not found!');
            }
            // Fetch publications belonging to this tag using the relationship
            $publications = $tag->publications()
                ->where('approval_status', 'approved') // Optional: only show approved ones if needed
                // ->where('is_active', true)
                ->orderByDesc('published_at')
                ->paginate(20)
                ->withQueryString();

            $totalPublications = $publications->total();
            $requestType = 'byTag';
            $pageTitle = "Media Publications with tag :: '" . $tag->name . "'";
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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_publication.create'),
            403
        );

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
        if ($request->status == 'submit_for_approval') {
            $publication->approval_status = 'pending';
        } else {
            $publication->approval_status = 'draft';
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $mime = $file->getMimeType();
            $ext  = strtolower($file->getClientOriginalExtension());

            $uploadPath = public_path('assets/tenant_' . tenant()->id . '/uploads/media/publications');

            File::ensureDirectoryExists($uploadPath);

            // Base filename
            $baseName = Str::slug($request->title, '_');

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

        $publication = MediaPublication::with([
            'likes',
            'mainComments.user',
            'mainComments.likes',
            'mainComments.replies.user',
            'mainComments.replies.likes',
        ])->withCount([
            'likes',

            'comments' => function ($query) {
                $query->where('status', 'approved')
                    ->where('is_active', true);
            },
        ])->where('uuid', $uuid)->first();

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

    public function updateMediaPublication($uuid, Request $request)
    {
        $publication = MediaPublication::where('uuid', $uuid)->first();

        if (!$publication) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_publication.read') || $publication->author == $user->username,
            403
        );

        $request->validate([
            'title' => 'required|string',
            'content' => 'required',
            'meta_description' => 'required|string',
            'meta_keywords' => 'required|string',
            'status' => 'nullable|string|in:submit_for_approval,draft',
            'tags' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2097152',
        ]);


        $publication->title = trim($request->title);
        $publication->content = $request->content;
        $publication->meta_description = trim($request->meta_description);
        $publication->meta_keywords = trim($request->meta_keywords);
        $publication->last_update = Carbon::now();
        $publication->last_update_by = Auth::user()->username;
        if ($request->status == 'submit_for_approval') {
            $publication->approval_status = 'pending';
        } elseif ($request->status == 'draft') {
            $publication->approval_status = 'draft';
        }
        if ($request->hasFile('image')) {
            // Delete the existing cover image if it exists
            if (!empty($publication->cover_image)) {
                $oldImagePath = public_path($publication->cover_image);
                if (is_file($oldImagePath)) {
                    unlink($oldImagePath);
                } else {
                    // Fallback: handle cases where only the filename was saved instead of the full path
                    $oldPathFallback = public_path('assets/tenant_' . tenant()->id . '/uploads/media/publications/' . basename($publication->cover_image));
                    if (is_file($oldPathFallback)) {
                        unlink($oldPathFallback);
                    }
                }
            }

            $file = $request->file('image');
            $mime = $file->getMimeType();
            $ext  = strtolower($file->getClientOriginalExtension());

            $uploadPath = public_path('assets/tenant_' . tenant()->id . '/uploads/media/publications');

            File::ensureDirectoryExists($uploadPath);

            // Base filename
            $baseName = Str::slug($request->title, '_');

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
    public function reviewMediaPublicationSubmit(Request $request, $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_publication.update'),
            403
        );

        $request->validate([
            'approval_status' => 'required',
        ]);

        $publication = MediaPublication::where('uuid', $uuid)->first();

        if (!$publication) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        $publication->update([
            'approval_status' => $request->approval_status,
            'approved_by' => Auth::user()->username,
            'last_approval_update' => Carbon::now(),
        ]);
        return redirect()->back()->with('success', 'Done!');
    }

    public function deleteMediaPublication($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('media_publication.update'),
            403
        );
        $publication = MediaPublication::where('uuid', $uuid)->first();

        if (!$publication) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        DB::beginTransaction();

        try {

            /*
        |--------------------------------------------------------------------------
        | Delete cover image
        |--------------------------------------------------------------------------
        */
            if (!empty($publication->cover_image)) {

                $coverPath = public_path(
                    'assets/tenant_' .
                        tenantId() .
                        '/uploads/media/publications/' .
                        $publication->cover_image
                );

                if (File::exists($coverPath)) {
                    File::delete($coverPath);
                }
            }


            /*
        |--------------------------------------------------------------------------
        | Get all comments belonging to this publication
        |--------------------------------------------------------------------------
        */
            $commentIds = PublicationComment::where(
                'media_publication_id',
                $publication->id
            )->pluck('id');


            /*
        |--------------------------------------------------------------------------
        | Delete likes belonging to comments
        |--------------------------------------------------------------------------
        */
            if ($commentIds->isNotEmpty()) {
                PublicationCommentLike::whereIn(
                    'publication_comment_id',
                    $commentIds
                )->delete();
            }


            /*
        |--------------------------------------------------------------------------
        | Delete all comments and replies
        |--------------------------------------------------------------------------
        */
            PublicationComment::where(
                'media_publication_id',
                $publication->id
            )->delete();


            /*
        |--------------------------------------------------------------------------
        | Delete publication likes
        |--------------------------------------------------------------------------
        */
            PublicationLike::where(
                'media_publication_id',
                $publication->id
            )->delete();


            /*
        |--------------------------------------------------------------------------
        | Delete publication tags / pivot records
        |--------------------------------------------------------------------------
        */
            PublicationTag::where(
                'media_publication_id',
                $publication->id
            )->delete();


            /*
        |--------------------------------------------------------------------------
        | Finally delete the publication
        |--------------------------------------------------------------------------
        */
            $publication->delete();


            DB::commit();

            return redirect()
                ->route('mediaPublications')
                ->with('success', 'Publication deleted successfully!');
        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unable to delete publication: ' . $e->getMessage()
                );
        }
    }
    // for members
    public function memberMediaPublications(Request $request)
    {
        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $publications = '';
        $totalPublications = 0;

        // sort by
        if ($request->sort_by == 'title') {
            $sortBy = 'title';
        } elseif ($request->sort_by == 'date_published') {
            $sortBy = 'published_at';
        } elseif ($request->sort_by == 'last_update') {
            $sortBy = 'last_update';
        } elseif ($request->sort_by == 'views') {
            $sortBy = 'views';
        } else {
            $sortBy = 'published_at';
        }

        // sorting order
        $sortOrder = ($request->sort_order === 'desc') ? 'desc' : 'asc';


        if (Route::currentRouteName() == 'memberMediaPublicationsSearch') {
            $searchKey = $request->input('searchKey');
            $publications = MediaPublication::query()
                ->where('approval_status', 'approved')
                ->where(function ($query) use ($searchKey) {
                    $query->where('title', 'like', "%{$searchKey}%")
                        ->orWhere('content', 'like', "%{$searchKey}%")
                        ->orWhere('meta_description', 'like', "%{$searchKey}%")
                        ->orWhere('meta_keywords', 'like', "%{$searchKey}%");
                })
                ->orderBy($sortBy, $sortOrder)
                ->paginate(20)
                ->withQueryString();

            $totalPublications = $publications->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } elseif (Route::currentRouteName() == 'memberMediaPublicationWihTag') {
            $tag = TagsForPublication::where('slug', $request->slug)->first();
            if (!$tag) {
                return redirect()->back('error', 'Slug records not found!');
            }
            // Fetch publications belonging to this tag using the relationship
            $publications = $tag->publications()
                ->where('approval_status', 'approved')->where('is_active', true)
                ->orderBy($sortBy, $sortOrder)
                ->paginate(20)
                ->withQueryString();

            $totalPublications = $publications->total();
            $requestType = 'byTag';
            $pageTitle = "Media Publications with tag :: '" . $tag->name . "'";
        } else {
            $publications = MediaPublication::where('approval_status', 'approved')->where('is_active', true)
                ->orderBy($sortBy, $sortOrder)
                ->paginate(20)->withQueryString();
            $totalPublications = MediaPublication::where('approval_status', 'approved')->where('is_active', true)
                ->orderBy($sortBy, $sortOrder)->count();
            $requestType = 'all';
            $pageTitle = 'Media Publications';
        }

        $data = [
            'publications' => $publications,
            'totalPublications' => $totalPublications,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ];
        return view('users.member.publications.publications', compact('data'));
    }
    public function memberViewMediaPublication($uuid)
    {
        $publication = MediaPublication::with([
            'likes',
            'mainComments.user',
            'mainComments.likes',
            'mainComments.replies.user',
            'mainComments.replies.likes',
        ])->withCount([
            'likes',

            'comments' => function ($query) {
                $query->where('status', 'approved')
                    ->where('is_active', true);
            },
        ])->where('approval_status', 'approved')->where('is_active', true)->where('uuid', $uuid)->first();

        if (!$publication) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $publication->increment('views');
        $data = [
            'publication' => $publication,
        ];
        return view('users.member.publications.viewPublications', compact('data'));
    }
}
