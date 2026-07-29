<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Dues\AssociationDue;
use App\Models\Finance\Transactions\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PaymentTransasctionController extends Controller
{
    public function memberPayAssociationDue($uuid, Request $request)
    {

        $request->validate([
            'manual_payment_method' => 'required|in:cash,bank_transfer,cheque',
            'proof' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3145728',
        ]);
        $member = Auth::user();


        $due = AssociationDue::where('uuid', $uuid)->first();

        if (!$due) {
            return redirect()->back()->with('error', 'Association due not found.');
        }

        $memberDue = $member->memberAcademicInfo
            ->associationDues()
            ->firstWhere('uuid', $uuid);

        if (!$memberDue) {
            return redirect()->back()->with('error', 'This due does not apply to your account.');
        }

        $due = $memberDue;

        // check if it has been paid
        $alreadyPaid = PaymentTransaction::where('registered_payer', $member->username)->where('due_id', $due->fee_id)->where('approval_status', 'approved')->first();


        if ($alreadyPaid) {
            return redirect()->back()->with(
                'error',
                'This association due has already been paid.'
            );
        }

        // check for pending transaction
        $pendingTransaction = PaymentTransaction::where('registered_payer', $member->username)
            ->where('due_id', $due->fee_id)
            ->where(function ($query) {
                $query->where('payment_status', 'pending')
                    ->orWhere('approval_status', 'pending');
            })
            ->first();

        if ($pendingTransaction) {
            return redirect()->back()->with(
                'error',
                'You already have a pending payment transaction. Please reconfirm it or wait for an administrator to process it before creating another payment.'
            );
        }

        do {
            $transactionId = 'pymt-' . now()->format('mYds') . '-' . random_int(100000000, 999999999);
        } while (PaymentTransaction::where('payment_transaction_id', $transactionId)->exists());

        $transaction = new PaymentTransaction();
        $transaction->payment_transaction_id = $transactionId;
        $transaction->payment_mode = 'manual';
        $transaction->registered_payer = $member->username;
        $transaction->payment_purpose = 'association_due';
        $transaction->due_id = $due->fee_id;
        $transaction->amount_payable = $due->payable_amount;
        $transaction->amount_paid = $due->payable_amount;
        $transaction->manual_payment_method = $request->manual_payment_method;
        $transaction->payment_status = 'pending';
        $transaction->approval_status = 'pending';
        $transaction->paid_at = Carbon::now();



        if ($request->hasFile('proof')) {

            $file = $request->file('proof');
            $mime = $file->getMimeType();
            $ext  = strtolower($file->getClientOriginalExtension());

            $uploadPath = public_path('assets/uploads/paymentTransactions/proof');
            File::ensureDirectoryExists($uploadPath);

            // Base filename
            $baseName = strtolower($transactionId);

            $baseName = Str::of($baseName)
                ->replaceMatches('/[^A-Za-z0-9]+/', '_')
                ->trim('_')
                ->toString();
            $fallbackName = $baseName . '.' . $ext;
            $counter = 1;

            // Delete existing file(s) with same base name regardless of extension
            foreach (glob($uploadPath . '/' . $baseName . '.*') as $existingFile) {
                if (file_exists($existingFile)) {
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
                            $transaction->photo = $webpName;
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
                $transaction->photo = $fallbackName;
            }
        }

        $transaction->save();
        return redirect()->back()->with('success', 'Saved!');
    }
}
