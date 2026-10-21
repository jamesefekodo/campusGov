<?php

namespace App\Http\Controllers\Sharing;

use App\Http\Controllers\Controller;
use App\Models\AcademicResources\IndustryAffiliate;
use App\Models\AcademicResources\PastQuestion;
use App\Models\AcademicResources\RecommendedBook;
use App\Models\AcademicResources\RecommendedCourse;
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
    public function sharePastQuestion($uuid){
          $pastQuestion = PastQuestion::where('uuid', $uuid)
            ->where('is_active', true)
            ->first();

        if (!$pastQuestion) {
            return redirect()->route('homepage')->with('error', 'Records not found!');
        }
         switch (getUserType()) {
            case 'root':
            case 'admin':
                return redirect()->route('viewPastQuestion', [
                    'uuid' => $pastQuestion->uuid
                ]);

            case 'member':
                return redirect()->route('memberViewPastQuestion', [
                    'uuid' => $pastQuestion->uuid
                ]);

            default:
                return redirect()->route('/');
        }
    }

    public function sharedRecommendedBook($slug){
        $book = RecommendedBook::where('slug', $slug)
        ->where('is_published', true)
        ->first();

        if (!$book) {
            return redirect()->route('homepage')->with('error', 'Records not found!');
        }

         switch (getUserType()) {
            case 'root':
            case 'admin':
                return redirect()->route('viewRecommendedBook', [
                    'slug' => $book->slug
                ]);

            case 'member':
                return redirect()->route('memberViewRecommendedBook', [
                    'slug' => $book->slug
                ]);

            default:
                return redirect()->route('/');
        }
    }
    public function sharedRecommendedCourse($slug){
          $course = RecommendedCourse::where('slug', $slug)
        ->where('is_published', true)
        ->first();

        if (!$course) {
            return redirect()->route('homepage')->with('error', 'Records not found!');
        }

         switch (getUserType()) {
            case 'root':
            case 'admin':
                return redirect()->route('viewRecommendedCourse', [
                    'slug' => $course->slug
                ]);

            case 'member':
                return redirect()->route('memberViewRecommendedCourse', [
                    'slug' => $course->slug
                ]);

            default:
                return redirect()->route('/');
        }
    }
    public function sharedIndustryAffiliate($slug){
         $affiliate = IndustryAffiliate::where('slug', $slug)
        ->where('is_published', true)
        ->first();

        if (!$affiliate) {
            return redirect()->route('homepage')->with('error', 'Records not found!');
        }

         switch (getUserType()) {
            case 'root':
            case 'admin':
                return redirect()->route('viewIndustryAffiliates', [
                    'slug' => $affiliate->slug
                ]);

            case 'member':
                return redirect()->route('memberViewIndustryAffiliate', [
                    'slug' => $affiliate->slug
                ]);

            default:
                return redirect()->route('/');
        }
    }
}
