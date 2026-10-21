<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive\ArchiveFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ArchiveController extends Controller
{
    public function archive(Request $request)
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
        $archiveFiles = '';
        $totalArchiveFiles = 0;


        if (Route::currentRouteName() == 'archiveSearch') {
            $searchKey = $request->input('searchKey');
            $archiveFiles = ArchiveFile::where('title', 'like', "%$searchKey%")
                ->orderBy('description', 'desc')
                ->paginate(20)->withQueryString();


            $totalArchiveFiles = $archiveFiles->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $archiveFiles = ArchiveFile::orderBy('title')->paginate(20);
            $totalArchiveFiles = ArchiveFile::count();
            $requestType = 'all';
            $pageTitle = 'Archive';
        }

        $data = [
            'archiveFiles' => $archiveFiles,
            'totalArchiveFiles' => $totalArchiveFiles,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];
        return view('users.admin.archive.archiveFiles', compact('data'));
    }
    public function addNewArchiveFileSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.create'),
            403
        );

        request()->validate([
            'title' => 'required',
            'description' => 'nullable',
            'members_can_see' => 'required|boolean',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        $file = new ArchiveFile();
        $file->created_by = Auth::user()->username;
        $file->title = trim($request->title);
        $file->description = trim($request->description);
        $file->members_can_see = trim($request->members_can_see);
        $file->last_update_by = Auth::user()->username;

        if ($request->hasFile('file')) {
            $directory = 'assets/tenant_' . tenant()->id . '/uploads/archive/files/';

            // Delete the existing file
            $oldFilePath = public_path($directory . $file->file);

            if ($file->file && File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }

            $newFile = $request->file('file');
            $newFileExtension = $newFile->getClientOriginalExtension();

            // Sanitize the title for use as a filename
            $newFileName = Str::slug($file->title) . '.' . $newFileExtension;

            // Move the uploaded file
            $newFile->move(public_path($directory), $newFileName);

            $file->file = $newFileName;
        }
        $file->save();

        return redirect()->back()->with('success', 'Done!');
    }
    public function viewArchiveFile($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.read'),
            403
        );
        $file = ArchiveFile::where('uuid', $uuid)->first();
        if (!$file) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $data = [
            'file' => $file,
        ];

        return view('users.admin.archive.viewArchiveFile', compact('data'));
    }
    public function updateArchiveFileSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.update'),
            403
        );

        $file = ArchiveFile::where('uuid', $uuid)->first();
        if (!$file) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        request()->validate([
            'title' => 'required',
            'description' => 'nullable',
            'members_can_see' => 'required|boolean',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        $file->title = trim($request->title);
        $file->description = trim($request->description);
        $file->members_can_see = trim($request->members_can_see);
        $file->last_update_by = Auth::user()->username;
        if ($request->hasFile('file')) {
            $directory = 'assets/tenant_' . tenant()->id . '/uploads/archive/files/';

            $fileName = $file->file;
            $oldFilePath = public_path($directory . $fileName);

            // Delete the old file only if it exists
            if ($fileName && File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }

            $newFile = $request->file('file');
            $newFileExtension = $newFile->getClientOriginalExtension();

            // Generate a sanitized filename from the title
            $newFileName = Str::slug($file->title) . '.' . $newFileExtension;

            // Ensure the destination directory exists
            if (!File::exists(public_path($directory))) {
                File::makeDirectory(public_path($directory), 0755, true);
            }

            // Move the new file into the directory
            $newFile->move(public_path($directory), $newFileName);

            $file->file = $newFileName;
        }

        $file->save();
        return redirect()->back()->with('success', 'Done!');
    }
    public function deleteArchiveFile($uuid){
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('archive.delete'),
            403
        );

        $file = ArchiveFile::where('uuid', $uuid)->first();
        if (!$file) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $directory = 'assets/tenant_' . tenant()->id . '/uploads/archive/files/';
        $filePath = public_path($directory . $file->file);

        if ($file->file && File::exists($filePath)) {
            File::delete($filePath);
        }

        $file->delete();
        return redirect()->route('archive')->with('success', 'Done!');
    }
}
