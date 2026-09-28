<?php

namespace App\Http\Controllers\Sharing;

use App\Http\Controllers\Controller;
use App\Models\Media\MediaPublication;
use Illuminate\Http\Request;

class SharingController extends Controller
{
    public function sharedMediaPublication($uuid)
    {
        $publication = MediaPublication::where('uuid', $uuid)
            ->where('is_active', true)
            ->first();

        if (!$publication) {
            return redirect()->route('homepage')->with('error', 'Records not found!');
        }

        $publication->increment('shares');

        switch (getUserType()) {
            case 'root':
            case 'admin':
                return redirect()->route('viewMediaPublication', [
                    'uuid' => $publication->uuid
                ]);

            case 'member':
                return redirect()->route('memberViewMediaPublication', [
                    'uuid' => $publication->uuid
                ]);

            default:
                return redirect()->route('guestViewMediaPublication', [
                    'uuid' => $publication->uuid
                ]);
        }
    }

}
