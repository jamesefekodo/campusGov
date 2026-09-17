<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    public function checkLogin()
    {
        if (Auth::check()) {
            $userType = getUserType();

            if (in_array($userType, ['root', 'admin'])) {
                return redirect()->route('adminDashboard');
            }
            if ($userType === 'member') {
                return redirect()->route('memberDashboard');
            }
        }

        return null; // No redirection, continue execution
    }

    public function homepage()
    {
        // Call the login check function
        $redirect = $this->checkLogin();
        if ($redirect) {
            return $redirect;
        }

        return redirect()->back()->with('error', 'Not aavailable!');

        // return view('frontend2.homepage', compact('data'));
        // return 'Homepage is under construction. Please check back later.';
    }

   public function showUploadForm()
{
    return '
        <form action="' . route('processTemporaryUpload') . '" method="POST" enctype="multipart/form-data">
            ' . csrf_field() . '
            <input type="file" name="csv_file" accept=".csv" required>
            <button type="submit">Upload CSV</button>
        </form>
    ';
}

public function processTemporaryUpload(Request $request)
{
    // Increase resource limits
    ini_set('upload_max_filesize', '100M');
    ini_set('post_max_size', '100M');
    ini_set('max_execution_time', 600);
    ini_set('memory_limit', '512M');
    set_time_limit(600);

    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt|max:102400',
    ]);

    $file = $request->file('csv_file');
    if (!$file || !$file->isValid()) {
        return response()->json(['error' => 'Invalid or missing CSV file'], 400);
    }

    $path = $file->getRealPath();
    $handle = fopen($path, 'r');
    if ($handle === false) {
        return response()->json(['error' => 'Unable to open uploaded CSV'], 500);
    }

    $header = fgetcsv($handle);
    if ($header === false) {
        fclose($handle);
        return response()->json(['error' => 'Empty CSV'], 400);
    }

    $header = array_map(fn($h) => strtolower(trim($h)), $header);

    // Due ID mapping groups
    $dueIdMapGroup1 = [
        'oblfee-2024-3985420209',
        'oblfee-2024-4641384815',
        'oblfee-2024-9145679087',
        'oblfee-2024-1055031457',
        'oblfee-2024-2966638762',
        'oblfee-2024-6119787905',
        'oblfee-2024-4088644346',
        'oblfee-2024-1276205626',
    ];

    $dueIdMapGroup2 = [
        'oblfee-2024-7533562301',
        'oblfee-2024-3250351918',
        'oblfee-2024-4186432628',
        'oblfee-2024-6759015907',
        'oblfee-2024-1423441851',
        'oblfee-2024-5129256330',
        'oblfee-2024-5327201211',
        'oblfee-2024-6324363311',
    ];

    $dueIdMapGroup3 = [
        'oblfee-2025-6894098096',
        'oblfee-2025-5882553273',
        'oblfee-2025-1678527266',
        'oblfee-2025-5370191650',
        'oblfee-2025-9867986623',
        'oblfee-2025-5414173032',
        'oblfee-2025-5221389352',
        'oblfee-2025-1708632618',
    ];

    $inserted = 0;
    $skipped  = 0;
    $errors   = [];

    // Disable foreign key checks for the whole import
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    try {
        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row)) === 0) {
                continue;
            }

            $data = array_combine($header, $row);
            if ($data === false) {
                $skipped++;
                continue;
            }

            try {
                // Helper: empty string → null
                $nullIfEmpty = function ($value) {
                    $value = is_string($value) ? trim($value) : $value;
                    return ($value === '' || $value === null) ? null : $value;
                };

                // ---------- payment_mode ----------
                $oldPaymentType = strtolower(trim($data['payment_type'] ?? ''));
                $paymentMode = in_array($oldPaymentType, ['electronic', 'manual'])
                    ? $oldPaymentType
                    : 'electronic';

                // ---------- payment_type ----------
                $paymentType = 'direct';
                if (stripos($oldPaymentType, 'regularization') !== false) {
                    $paymentType = 'regularization';
                }

                // ---------- due_id mapping ----------
                $oldDueId = null;
                foreach (['undergraduate_due_id', 'backlog_due_id', 'alumni_due_id'] as $col) {
                    $val = trim($data[$col] ?? '');
                    if ($val !== '') {
                        $oldDueId = $val;
                        break;
                    }
                }

                $dueId = null;
                if ($oldDueId !== null) {
                    if (in_array($oldDueId, $dueIdMapGroup1, true)) {
                        $dueId = 'due-082026-6290059709';
                    } elseif (in_array($oldDueId, $dueIdMapGroup2, true)) {
                        $dueId = 'due-082026-1540019841';
                    } elseif (in_array($oldDueId, $dueIdMapGroup3, true)) {
                        $dueId = 'due-082026-7918687141';
                    }
                }

                // ---------- amount_payable ----------
                $amountPayable = null;
                foreach (['invoice_amount', 'donated_amount', 'election_amount', 'due_amount'] as $col) {
                    $val = trim($data[$col] ?? '');
                    if ($val !== '' && is_numeric($val)) {
                        $amountPayable = (float) $val;
                        break;
                    }
                }

                // ---------- Build record ----------
                $record = [
                    'tenant_id'                      => null,
                    'payment_transaction_id'         => $nullIfEmpty($data['payment_transaction_id'] ?? null),
                    'service_provider_id'            => $nullIfEmpty($data['service_provider_id'] ?? null),
                    'paystack_reference'             => $nullIfEmpty($data['paystack_reference'] ?? null),
                    'payment_mode'                   => $paymentMode,
                    'registered_payer'               => $nullIfEmpty($data['registered_payer'] ?? null),
                    'payment_type'                   => $paymentType,
                    'non_registered_payer'           => $nullIfEmpty($data['non_registered_payer'] ?? null),
                    'phone'                          => null,
                    'email'                          => null,
                    'payment_purpose'                => $data['payment_purpose'] ?? null,
                    'due_id'                         => $dueId,
                    'invoice_number'                 => $nullIfEmpty($data['invoice_id'] ?? null),
                    'donation_id'                    => null,
                    'election_id'                    => null,
                    'election_position'              => null,
                    'election_candidate_application' => null,
                    'election_candidate_voted'       => null,
                    'amount_payable'                 => $amountPayable,
                    'amount_paid'                    => (float) ($data['amount_paid'] ?? 0),
                    'amount_settled'                 => isset($data['amount_settled']) && $data['amount_settled'] !== ''
                                                        ? (float) $data['amount_settled'] : null,
                    'service_charge'                 => isset($data['service_charge']) && $data['service_charge'] !== ''
                                                        ? (float) $data['service_charge'] : null,
                    'total_tax'                      => isset($data['total_tax']) && $data['total_tax'] !== ''
                                                        ? (float) $data['total_tax'] : null,
                    'taxes_paid'                     => $nullIfEmpty($data['taxes_paid'] ?? null),
                    'electronic_payment_method'      => $nullIfEmpty($data['online_payment_method'] ?? null),
                    'manual_payment_method'          => null,
                    'photo'                          => null,
                    'payment_status'                 => $data['payment_status'] ?? 'waiting',
                    'paid_at'                        => $data['paid_at'] ?? now(),
                    'approval_status'                => $data['approval_status'] ?? 'pending',
                    'balance_update_status'          => $data['balance_update_status'] ?? 'pending',
                    'approved_by'                    => null, // always null
                    'approved_at'                    => $nullIfEmpty($data['updated_at'] ?? null),
                    'created_at'                     => $data['created_at'] ?? now(),
                    'updated_at'                     => $data['updated_at'] ?? now(),
                ];

                if (empty($record['payment_transaction_id'])) {
                    $skipped++;
                    continue;
                }

                \App\Models\Finance\Transactions\PaymentTransaction::updateOrCreate(
                    ['payment_transaction_id' => $record['payment_transaction_id']],
                    $record
                );

                $inserted++;
            } catch (\Throwable $e) {
                $errors[] = [
                    'payment_transaction_id' => $data['payment_transaction_id'] ?? 'unknown',
                    'error'                  => $e->getMessage(),
                ];
                $skipped++;
            }
        }
    } finally {
        // Always re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        fclose($handle);
    }

    return response()->json([
        'message'  => 'CSV processing completed',
        'inserted' => $inserted,
        'skipped'  => $skipped,
        'errors'   => $errors,
    ]);
}
}
