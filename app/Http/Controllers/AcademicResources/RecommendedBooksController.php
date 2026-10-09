<?php

namespace App\Http\Controllers\AcademicResources;

use App\Http\Controllers\Controller;
use App\Models\AcademicResources\RecommendedBook;
use App\Models\AcademicResources\TagsForRecommendedBooks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RecommendedBooksController extends Controller
{
    public function recommendedBooks(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_book.read'),
            403
        );

        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $recommendedBooks = '';
        $totalRecommendedBooks = 0;
        $bookTags = TagsForRecommendedBooks::orderBy('name')->get();


        if (Route::currentRouteName() == 'recommendedBooksSearch') {
            $searchKey = $request->input('searchKey');
            $recommendedBooks = RecommendedBook::when($searchKey, function ($query) use ($searchKey) {
                $query->where(function ($q) use ($searchKey) {
                    $q->where('created_by', 'like', "%{$searchKey}%")
                        ->orWhere('title', 'like', "%{$searchKey}%")
                        ->orWhere('description', 'like', "%{$searchKey}%");
                });
            })
                ->orderByDesc('updated_at')->paginate(20)->withQueryString();

            $totalRecommendedBooks = $recommendedBooks->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } elseif (Route::currentRouteName() == 'recommendedBooksWihTag') {
            $tag = TagsForRecommendedBooks::where(
                'slug',
                $request->slug
            )->first();

            if (!$tag) {
                return redirect()
                    ->back()
                    ->with('error', 'Tag not found!');
            }

            $recommendedBooks = $tag->recommendedBooks()
                ->paginate(20)
                ->withQueryString();

            $totalRecommendedBooks = $recommendedBooks->total();
            $requestType = 'with_tag';
            $pageTitle = 'Books with the tag :: ' . $tag->name;
        } else {
            $recommendedBooks = RecommendedBook::query()
                ->orderByDesc('updated_at')
                ->paginate(20);
            $totalRecommendedBooks = RecommendedBook::count();
            $requestType = 'all';
            $pageTitle = 'Recommended Books';
        }
        $data = [
            'recommendedBooks' => $recommendedBooks,
            'totalRecommendedBooks' => $totalRecommendedBooks,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
            'bookTags' => $bookTags,
        ];

        return view('users.admin.academicResources.recommendedBooks.recommendedBooks', compact('data'));
    }
    public function recommendedBooksTags()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_book.manage_tags'),
            403
        );

        $bookTags = TagsForRecommendedBooks::orderBy('name')->paginate(50);
        $data = [
            'bookTags' => $bookTags,
        ];

        return view('users.admin.academicResources.recommendedBooks.bookTags', compact('data'));
    }
    public function addNewBookTagSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_book.manage_tags'),
            403
        );

        request()->validate([
            'name' => 'required|string',
        ]);

        $tag = new TagsForRecommendedBooks();
        $tag->name = trim($request->name);
        $tag->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function editBookTagSubmit($slug, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_book.manage_tags'),
            403
        );

        request()->validate([
            'name' => 'required|string',
        ]);

        $tag = TagsForRecommendedBooks::where('slug', $slug)->first();
        if (!$tag) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $tag->update([
            'name' => trim($request->name),
        ]);

        return redirect()->back()->with('success', 'Done!');
    }
    public function removeBookTag($slug)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_book.manage_tags'),
            403
        );

        $tag = TagsForRecommendedBooks::where('slug', $slug)->first();
        if (!$tag) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $tag->delete();

        return redirect()->back()->with('success', 'Done!');
    }
    public function addNewRecommendedBookSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_book.create'),
            403
        );

        $request->validate([
            'title' => 'required',
            'file_url' => 'nullable|url',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:300',
        ]);

        do {
            $bookSlug = 'rec-bk-' . now()->format('mY') . '-' . random_int(1000000000, 9999999999);
        } while (RecommendedBook::where('slug', $bookSlug)->exists());

        $book = new RecommendedBook();
        $book->slug = $bookSlug;
        $book->created_by = Auth::user()->username;
        $book->title = trim($request->title);
        $book->description = $request->description;
        $book->file_url = trim($request->file_url);


        if ($request->hasFile('image')) {
            $filePath = 'assets/tenant_' . tenant()->id . '/uploads/academicResources/recommendedBooks' . $book->slug;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $newImage = $request->file('image');
            $newImageExtension = $newImage->getClientOriginalExtension();
            $newImageName = $book->slug . '.' . $newImageExtension;
            $newImage->move(public_path('assets/tenant_' . tenant()->id . '/uploads/academicResources/recommendedBooks'), $newImageName);
            $book->cover_image = $newImageName;
        }
        $book->save();

        // --------------------------------------------------------------------------
        // Process Tags
        // --------------------------------------------------------------------------
        if (!empty($request->tags)) {
            // Explode the comma-separated string, clean up whitespace, and remove empty values
            $tagNames = array_filter(array_map('trim', explode(',', $request->tags)));

            $tagIds = [];

            foreach ($tagNames as $tagName) {
                // Check if the tag exists for the current tenant or create it if missing
                $tag = TagsForRecommendedBooks::firstOrCreate(
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
            $book->tags()->sync($tagIds);
        }
        return redirect()->back()->with('success', 'Done!');
    }
    public function viewRecommendedBook($slug)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_book.read'),
            403
        );

        $book = RecommendedBook::where('slug', $slug)->first();
        if (!$book) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $bookTags = TagsForRecommendedBooks::orderBy('name')->get();


        $data = [
            'book' => $book,
            'bookTags' => $bookTags,
        ];

        return view('users.admin.academicResources.recommendedBooks.viewRecommendedBook', compact('data'));
    }
    public function updateRecommendedBookSubmit($slug, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_book.update'),
            403
        );
        $book = RecommendedBook::where('slug', $slug)->first();
        if (!$book) {
            return redirect()->back()->with('error', 'No records found!');
        }
        $request->validate([
            'title' => 'required',
            'file_url' => 'nullable|url',
            'description' => 'required',
            'is_published' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:300',
        ]);

        do {
            $bookSlug = 'rec-bk-' . now()->format('mY') . '-' . random_int(1000000000, 9999999999);
        } while (RecommendedBook::where('slug', $bookSlug)->exists());

        $book->title = trim($request->title);
        $book->description = $request->description;
        $book->file_url = trim($request->file_url);
        $book->is_published = $request->is_published;

        // process the image
        if ($request->hasFile(key: 'image')) {
            $filePath = 'assets/tenant_' . tenant()->id . '/uploads/academicResources/recommendedBooks' . $book->slug;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $newImage = $request->file('image');
            $newImageExtension = $newImage->getClientOriginalExtension();
            $newImageName = $book->slug . '.' . $newImageExtension;
            $newImage->move(public_path('assets/tenant_' . tenant()->id . '/uploads/academicResources/recommendedBooks'), $newImageName);
            $book->cover_image = $newImageName;
        }

        $book->save();

        // --------------------------------------------------------------------------
        // Process Tags
        // --------------------------------------------------------------------------
        if (!empty($request->tags)) {
            // Explode the comma-separated string, clean up whitespace, and remove empty values
            $tagNames = array_filter(array_map('trim', explode(',', $request->tags)));

            $tagIds = [];

            foreach ($tagNames as $tagName) {
                // Check if the tag exists for the current tenant or create it if missing
                $tag = TagsForRecommendedBooks::firstOrCreate(
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
            $book->tags()->sync($tagIds);
        }
        return redirect()->back()->with('success', 'Done!');
    }
    public function deleteRecommendedBook($slug){
          /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('recommended_book.delete'),
            403
        );
        $book = RecommendedBook::where('slug', $slug)->first();
        if (!$book) {
            return redirect()->back()->with('error', 'No records found!');
        }
            // Check if the book has a cover image attribute
            $coverImageName = $book->cover_image ?? null;

            if ($coverImageName) {
                // Build the full public path to the image
                $coverPath = public_path(
                    'assets/tenant_' .
                        tenant()->id .
                        '/uploads/academicResources/recommendedBooks/' .
                        $coverImageName
                );

                // Delete the file from storage if it physically exists
                if (file_exists($coverPath)) {
                    @unlink($coverPath);
                }
            }

            // Delete the book record from the database
            $book->delete();
        return redirect()->route('recommendedBooks')->with('success', 'Done!');

    }
}
