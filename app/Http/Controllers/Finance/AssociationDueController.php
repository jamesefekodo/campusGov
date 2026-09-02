<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\AcademicConfig\Program;
use App\Models\Finance\Dues\AssociationDue;
use App\Models\Finance\Dues\AssociationDueVariation;
use App\Models\Finance\Transactions\PaymentTransaction;
use App\Models\Finance\Transactions\PaymentWaiver;
use App\Models\Members\MemberAcademicInfo;
use App\Models\Members\MemberPersonalProfile;
use App\Models\Members\MemberReAdmissionApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;

class AssociationDueController extends Controller
{
    public function associationDues(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.read'),
            403
        );


        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $dues = '';
        $totalDues = 0;


        if (Route::currentRouteName() == 'associationDuesSearch') {
            $searchKey = $request->input('searchKey');
            $dues = AssociationDue::where('fee_id', 'like', "%$searchKey%")
                ->orWhere('title', 'like', "%$searchKey%")
                ->orWhere('description', 'like', "%$searchKey%")
                ->orWhere('amount', 'like', "%$searchKey%")
                ->orWhere('academic_year', 'like', "%$searchKey%")
                ->orderBy('academic_year', 'desc')
                ->paginate(20)->withQueryString();


            $totalDues = $dues->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $dues = AssociationDue::orderBy('academic_year', 'desc')->paginate(20);
            $totalDues = AssociationDue::count();
            $requestType = 'all';
            $pageTitle = 'Association Dues';
        }

        $data = [
            'dues' => $dues,
            'totalDues' => $totalDues,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];
        return view('users.admin.finance.dues.dues', compact('data'));
    }
    public function addNewAssociationDueSubmit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.create'),
            403
        );
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'amount' => 'required',
            'academic_year' => 'required',
        ]);

        do {
            $feeId = 'due-' . now()->format('mY') . '-' . random_int(1000000000, 9999999999);
        } while (AssociationDue::where('fee_id', $feeId)->exists());

        AssociationDue::create(
            [
                'fee_id' => $feeId,
                'title' => trim($request->title),
                'description' => trim($request->description),
                'amount' => trim($request->amount),
                'academic_year' => trim($request->academic_year),
            ]
        );
        return redirect()->back()->with('success', 'Done!');
    }
    public function viewDue($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.read'),
            403
        );

        $due = AssociationDue::where('uuid', $uuid)->first();
        if (!$due) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $activePrograms = Program::where('is_active', true)->orderBy('name')->get();

        $data = [
            'due' => $due,
            'activePrograms' => $activePrograms,
        ];
        return view('users.admin.finance.dues.viewDues', compact('data'));
    }

    public function editAssociationDueSubmit($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.update'),
            403
        );

        $due = AssociationDue::where('uuid', $uuid)->first();
        if (!$due) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'amount' => 'required',
            'academic_year' => 'required',
            'is_active' => 'required',
        ]);

        $due->update(
            [
                'title' => trim($request->title),
                'description' => trim($request->description),
                'amount' => trim($request->amount),
                'academic_year' => trim($request->academic_year),
                'is_active' => $request->is_active,
            ]
        );
        return redirect()->back()->with('success', 'Done!');
    }
    public function createAssociationDueVariation($uuid, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.create'),
            403
        );

        $due = AssociationDue::where('uuid', $uuid)->first();
        if (!$due) {
            return redirect()->back()->with('error', 'Records not found.');
        }
        $validator = Validator::make($request->all(), [
            'admission_session' => [
                'nullable',
                'integer',
                'min:' . $due->academic_year,
                'max:' . ($due->academic_year + 10),
                'required_without_all:program_slug,program_type_slug',
            ],

            'program_slug' => [
                'nullable',
                'exists:programs,slug',
                'required_without_all:admission_session,program_type_slug',
            ],

            'program_type_slug' => [
                'nullable',
                'exists:program_types,slug',
                'required_without_all:admission_session,program_slug',
            ],

            'amount' => 'required|numeric',
        ], [
            'admission_session.required_without_all' => 'Please specify at least one of Admission Session, Program, or Program Type.',
            'program_slug.required_without_all' => 'Please specify at least one of Admission Session, Program, or Program Type.',
            'program_type_slug.required_without_all' => 'Please specify at least one of Admission Session, Program, or Program Type.',
            'admission_session.min' => 'The selected admission session is too old. It cannot be more than 10 academic sessions before the due academic session.',
            'admission_session.max' => 'The admission session cannot be later than the due academic session. The latest eligible admission session is the session immediately preceding the due academic session.',
        ]);

        $validator->validate();

        AssociationDueVariation::create([
            'due_id' => $due->fee_id,
            'admission_session' => $request->admission_session,
            'program_slug' => $request->program_slug,
            'program_type_slug' => $request->program_type_slug,
            'amount' => $request->amount,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function editAssociationDueVariation($id, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.update'),
            403
        );

        $variation = AssociationDueVariation::where('id', $id)->first();
        if (!$variation) {
            return redirect()->back()->with('error', 'Records not found.');
        }

        $validator = Validator::make($request->all(), [
            'admission_session' => [
                'nullable',
                'integer',
                'min:' . $variation->due->academic_year,
                'max:' . ($variation->due->academic_year + 10),
                'required_without_all:program_slug,program_type_slug',
            ],

            'program_slug' => [
                'nullable',
                'exists:programs,slug',
                'required_without_all:admission_session,program_type_slug',
            ],

            'program_type_slug' => [
                'nullable',
                'exists:program_types,slug',
                'required_without_all:admission_session,program_slug',
            ],

            'amount' => 'required|numeric',
        ], [
            'admission_session.required_without_all' => 'Please specify at least one of Admission Session, Program, or Program Type.',
            'program_slug.required_without_all' => 'Please specify at least one of Admission Session, Program, or Program Type.',
            'program_type_slug.required_without_all' => 'Please specify at least one of Admission Session, Program, or Program Type.',
            'admission_session.min' => 'The selected admission session is too old. It cannot be more than 10 academic sessions before the due academic session.',
            'admission_session.max' => 'The admission session cannot be later than the due academic session. The latest eligible admission session is the session immediately preceding the due academic session.',
        ]);

        $variation->update([
            'admission_session' => $request->admission_session,
            'program_slug' => $request->program_slug,
            'program_type_slug' => $request->program_type_slug,
            'amount' => $request->amount,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function removeAssociationVariation($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.delete'),
            403
        );
        $variation = AssociationDueVariation::where('id', $id)->first();
        if (!$variation) {
            return redirect()->back()->with('error', 'Records not found.');
        }
        $variation->delete();
        return redirect()->back()->with('success', 'Done!');
    }
    // public function printAssociationDueWaiverAndPayment($uuid)
    // {
    //     /** @var \App\Models\User $user */
    //     $user = Auth::user();

    //     abort_unless(
    //         $user->hasPermission('dues.read'),
    //         403
    //     );


    //     $due = AssociationDue::where('uuid', $uuid)->first();
    //     if (!$due) {
    //         return redirect()->back()->with('error', 'Records not found.');
    //     }

    //     $successfulPayment = PaymentTransaction::with([
    //         'payerPersonalProfile',
    //         'payerAcademicInfo.theProgram',
    //         'payerAcademicInfo.programType',
    //         'payerAcademicInfo.theFaculty',
    //         'payerAcademicInfo.theDepartment',
    //         'payerAcademicInfo.approvedReAdmissions.previousProgram',
    //         'payerAcademicInfo.approvedReAdmissions.previousProgramType',
    //         'payerAcademicInfo.approvedReAdmissions.previousFaculty',
    //         'payerAcademicInfo.approvedReAdmissions.previousDepartment',

    //         'registeredPayer.memberAcademicInfo.theProgram',
    //         'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousProgram',
    //         'due',
    //     ])
    //         ->where('due_id', $due->fee_id)
    //         ->where('payment_status', 'successful')
    //         ->where('approval_status', 'approved')
    //         ->get()
    //         ->map(function ($payment) use ($due) {
    //             $payment->academicContext = resolveAcademicContextForDue(
    //                 $payment->payerAcademicInfo,
    //                 $due
    //             );
    //             return $payment;
    //         })
    //         ->sortBy(fn($p) => $p->payerPersonalProfile?->first_name ?: $p->registered_payer)
    //         ->values();

    //     $waiversGranted = PaymentWaiver::with([
    //         'personalProfile',
    //         'memberAcademicInfo.theProgram',
    //         'memberAcademicInfo.programType',
    //         'memberAcademicInfo.theFaculty',
    //         'memberAcademicInfo.theDepartment',
    //         'memberAcademicInfo.approvedReAdmissions.previousProgram',
    //         'memberAcademicInfo.approvedReAdmissions.previousProgramType',
    //         'memberAcademicInfo.approvedReAdmissions.previousFaculty',
    //         'memberAcademicInfo.approvedReAdmissions.previousDepartment',
    //     ])
    //         ->where('due_id', $due->fee_id)
    //         ->where('approval_status', 'approved')
    //         ->get()
    //         ->map(function ($waiver) use ($due) {
    //             $waiver->academicContext = resolveAcademicContextForDue(
    //                 $waiver->memberAcademicInfo,
    //                 $due
    //             );
    //             return $waiver;
    //         })
    //         ->sortBy(fn($w) => $w->personalProfile?->first_name ?: $w->username)
    //         ->values();

    //     $dueSession = $due->academic_year . '/' . $due->academic_year + 1;


    //     ini_set('pcre.backtrack_limit', 10000000);
    //     ini_set('max_execution_time', 600);
    //     ini_set('memory_limit', '1024M');


    //     $mpdf = new Mpdf([
    //         'default_font_size'  => 10,
    //         'default_font'       => 'Arial',
    //         'margin_left'        => 5,
    //         'margin_right'       => 5,
    //         'margin_top'         => 10,
    //         'margin_bottom'      => 15,
    //         'format'             => 'A4',
    //         'autoLangToFont'     => true,
    //         'autoScriptToLang'   => true,
    //         'tempDir'            => storage_path('app/mpdf-temp'),
    //     ]);

    //     $mpdf->setFooter('Page {PAGENO} of {nb} >> Waivers and Successful Payments for : ' . $due->title . ' ' . $dueSession);
    //     $mpdf->showImageErrors = true;

    //     $basicSystemInfo = basicSystemInfo();
    //     $tenant = tenant();

    //     $data = [
    //         'due' => $due,
    //         'successfulPayment' => $successfulPayment,
    //         'waiversGranted' => $waiversGranted,
    //         'tenant' => $tenant,
    //         'basicSystemInfo' => $basicSystemInfo,
    //     ];

    //     $html = view('printFiles.dues.successfulDuePaymentAndWaivers', compact('data'))->render();

    //     $mpdf->WriteHTML($html); // AddPage() not needed — WriteHTML handles it

    //     $fileName = 'Waivers and Successful Payments for :' . $due->title . '_' . $dueSession . '_' . date('Y-m-d_H-i-s') . '.pdf';

    //     while (ob_get_level()) ob_end_clean();

    //     $mpdf->Output($fileName, 'I'); // 'I' for inline, 'D' for download
    //     exit;
    // }

    public function printAssociationDueWaiverAndPayment($uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasPermission('dues.read'),
            403
        );

        $due = AssociationDue::where('uuid', $uuid)->first();

        if (!$due) {
            return redirect()
                ->back()
                ->with('error', 'Records not found.');
        }

        /*
    |--------------------------------------------------------------------------
    | Academic session
    |--------------------------------------------------------------------------
    */
        $dueSession = $due->academic_year . '/' . (
            (int) $due->academic_year + 1
        );

        /*
    |--------------------------------------------------------------------------
    | Runtime configuration
    |--------------------------------------------------------------------------
    */
        ini_set('pcre.backtrack_limit', 10000000);
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');

        /*
    |--------------------------------------------------------------------------
    | mPDF temporary directory
    |--------------------------------------------------------------------------
    */
        $tempDirectory = storage_path('app/mpdf-temp');

        if (!is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        /*
    |--------------------------------------------------------------------------
    | Initialise mPDF once
    |--------------------------------------------------------------------------
    */
        $mpdf = new Mpdf([
            'default_font_size' => 10,
            'default_font' => 'Arial',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 10,
            'margin_bottom' => 15,
            'format' => 'A4',
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
            'tempDir' => $tempDirectory,
        ]);

        $mpdf->setFooter(
            'Page {PAGENO} of {nb} >> Waivers and Successful Payments for: '
                . $due->title
                . ' '
                . $dueSession
        );

        $mpdf->showImageErrors = true;

        /*
    |--------------------------------------------------------------------------
    | Shared data only
    |--------------------------------------------------------------------------
    |
    | Do not put all payments/waivers here.
    | This data remains small regardless of report size.
    |
    */
        $basicSystemInfo = basicSystemInfo();
        $tenant = tenant();

        $expectedPayers = $due->expectedPayerCount();
        $approvedWaivers = $due->approvedWaiverCount();
        $successfulPaymentsCount = $due->successfulPaymentCount();
        $membersPaidOrWaivedCount = $due->membersPaidOrWaived()->count();

        $data = [
            'due' => $due,
            'tenant' => $tenant,
            'basicSystemInfo' => $basicSystemInfo,
            'dueSession' => $dueSession,

            /*
         * One-time summary values for the report heading.
         */
            'expectedPayers' => $expectedPayers,
            'approvedWaivers' => $approvedWaivers,
            'variationsCount' => $due->variations()->count(),
            'successfulPaymentsCount' => $successfulPaymentsCount,
            'outstandingCount' => max(
                0,
                $expectedPayers - $membersPaidOrWaivedCount
            ),
        ];

        /*
    |--------------------------------------------------------------------------
    | 1. Write CSS once
    |--------------------------------------------------------------------------
    */
        $css = view(
            'printFiles.dues.paid.partials.waiverPaymentStyles',
            compact('data')
        )->render();

        $mpdf->WriteHTML(
            $css,
            HTMLParserMode::HEADER_CSS
        );

        unset($css);

        /*
    |--------------------------------------------------------------------------
    | 2. Write report header and due information once
    |--------------------------------------------------------------------------
    */
        $headerHtml = view(
            'printFiles.dues.paid.partials.waiverPaymentHeader',
            compact('data')
        )->render();

        $mpdf->WriteHTML(
            $headerHtml,
            HTMLParserMode::HTML_BODY
        );

        unset($headerHtml);

        /*
    |--------------------------------------------------------------------------
    | Waivers query
    |--------------------------------------------------------------------------
    */
        $waiverQuery = PaymentWaiver::query()
            ->with([
                /*
             * Required because your old Blade accesses:
             * $waiver->payer->photo
             *
             * Remove/rename this if your actual relationship
             * is not called `payer`.
             */
                'payer',

                'personalProfile',

                'memberAcademicInfo.theProgram',
                'memberAcademicInfo.programType',
                'memberAcademicInfo.theFaculty',
                'memberAcademicInfo.theDepartment',

                'memberAcademicInfo.approvedReAdmissions.previousProgram',
                'memberAcademicInfo.approvedReAdmissions.previousProgramType',
                'memberAcademicInfo.approvedReAdmissions.previousFaculty',
                'memberAcademicInfo.approvedReAdmissions.previousDepartment',
            ])
            ->where('due_id', $due->fee_id)
            ->where('approval_status', 'approved');

        /*
    |--------------------------------------------------------------------------
    | 3. Waivers heading
    |--------------------------------------------------------------------------
    */
        $waiverHeadingHtml = view(
            'printFiles.dues.paid.partials.waiverSectionHeading',
            compact('data')
        )->render();

        $mpdf->WriteHTML(
            $waiverHeadingHtml,
            HTMLParserMode::HTML_BODY
        );

        unset($waiverHeadingHtml);

        /*
    |--------------------------------------------------------------------------
    | 4. Waivers in chunks
    |--------------------------------------------------------------------------
    |
    | getKeyName() lets this work even if your primary key is
    | `waiver_id` instead of `id`.
    |
    */
        $waiverSerialNumber = 0;
        $waiverPrimaryKey = (new PaymentWaiver())->getKeyName();

        $hasWaivers = (clone $waiverQuery)->exists();

        if ($hasWaivers) {
            $waiverQuery->chunkById(
                50,
                function ($waivers) use (
                    $due,
                    $data,
                    $mpdf,
                    &$waiverSerialNumber
                ) {
                    $waivers->each(function ($waiver) use ($due) {
                        $waiver->academicContext = resolveAcademicContextForDue(
                            $waiver->memberAcademicInfo,
                            $due
                        );
                    });

                    $html = view(
                        'printFiles.dues.paid.partials.waiverChunk',
                        [
                            'data' => $data,
                            'waivers' => $waivers,
                            'serialStart' => $waiverSerialNumber,
                        ]
                    )->render();

                    $mpdf->WriteHTML(
                        $html,
                        HTMLParserMode::HTML_BODY
                    );

                    $waiverSerialNumber += $waivers->count();

                    unset($html, $waivers);

                    gc_collect_cycles();
                },
                $waiverPrimaryKey
            );
        } else {
            $mpdf->WriteHTML(
                '<div class="no-records">No approved waiver records found.</div>',
                HTMLParserMode::HTML_BODY
            );
        }

        /*
    |--------------------------------------------------------------------------
    | New page before payments
    |--------------------------------------------------------------------------
    */
        $mpdf->WriteHTML(
            '<pagebreak />',
            HTMLParserMode::HTML_BODY
        );

        /*
    |--------------------------------------------------------------------------
    | Payments query
    |--------------------------------------------------------------------------
    */
        $paymentQuery = PaymentTransaction::query()
            ->with([
                'payerPersonalProfile',

                'payerAcademicInfo.theProgram',
                'payerAcademicInfo.programType',
                'payerAcademicInfo.theFaculty',
                'payerAcademicInfo.theDepartment',

                'payerAcademicInfo.approvedReAdmissions.previousProgram',
                'payerAcademicInfo.approvedReAdmissions.previousProgramType',
                'payerAcademicInfo.approvedReAdmissions.previousFaculty',
                'payerAcademicInfo.approvedReAdmissions.previousDepartment',

                /*
             * Used for the photo in your old Blade:
             * $payment->registeredPayer->photo
             */
                'registeredPayer.memberAcademicInfo.theProgram',
                'registeredPayer.memberAcademicInfo.programType',
                'registeredPayer.memberAcademicInfo.theFaculty',
                'registeredPayer.memberAcademicInfo.theDepartment',

                'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousProgram',
                'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousProgramType',
                'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousFaculty',
                'registeredPayer.memberAcademicInfo.approvedReAdmissions.previousDepartment',

                'due',
            ])
            ->where('due_id', $due->fee_id)
            ->where('payment_status', 'successful')
            ->where('approval_status', 'approved');

        /*
    |--------------------------------------------------------------------------
    | 5. Payments heading
    |--------------------------------------------------------------------------
    */
        $paymentHeadingHtml = view(
            'printFiles.dues.paid.partials.paymentSectionHeading',
            compact('data')
        )->render();

        $mpdf->WriteHTML(
            $paymentHeadingHtml,
            HTMLParserMode::HTML_BODY
        );

        unset($paymentHeadingHtml);

        /*
    |--------------------------------------------------------------------------
    | 6. Payments in chunks
    |--------------------------------------------------------------------------
    */
        $paymentSerialNumber = 0;
        $paymentPrimaryKey = (new PaymentTransaction())->getKeyName();

        $hasPayments = (clone $paymentQuery)->exists();

        if ($hasPayments) {
            $paymentQuery->chunkById(
                50,
                function ($payments) use (
                    $due,
                    $data,
                    $mpdf,
                    &$paymentSerialNumber
                ) {
                    $payments->each(function ($payment) use ($due) {
                        $payment->academicContext = resolveAcademicContextForDue(
                            $payment->payerAcademicInfo,
                            $due
                        );
                    });

                    $html = view(
                        'printFiles.dues.paid.partials.paymentChunk',
                        [
                            'data' => $data,
                            'payments' => $payments,
                            'serialStart' => $paymentSerialNumber,
                        ]
                    )->render();

                    $mpdf->WriteHTML(
                        $html,
                        HTMLParserMode::HTML_BODY
                    );

                    $paymentSerialNumber += $payments->count();

                    unset($html, $payments);

                    gc_collect_cycles();
                },
                $paymentPrimaryKey
            );
        } else {
            $mpdf->WriteHTML(
                '<div class="no-records">No successful payment records found.</div>',
                HTMLParserMode::HTML_BODY
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Safe filename
    |--------------------------------------------------------------------------
    |
    | Avoid colon (:), slash (/), and other invalid filename
    | characters. $dueSession contains a slash, so replace it.
    |
    */
        $fileName = 'waivers-successful-payments-'
            . str($due->title)->slug()
            . '-'
            . str($dueSession)->replace('/', '-')
            . '-'
            . now('Africa/Lagos')->format('Y-m-d_H-i-s')
            . '.pdf';

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $mpdf->Output($fileName, 'I');

        exit;
    }

   public function printAssociationDueDebtors($uuid)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    abort_unless(
        $user->hasPermission('dues.read'),
        403
    );

    $due = AssociationDue::where('uuid', $uuid)->first();

    if (!$due) {
        return redirect()
            ->back()
            ->with('error', 'Records not found.');
    }

    /*
    |--------------------------------------------------------------------------
    | Academic session
    |--------------------------------------------------------------------------
    */
    $dueSession = $due->academic_year . '/' . (
        (int) $due->academic_year + 1
    );

    ini_set('pcre.backtrack_limit', 10000000);
    ini_set('max_execution_time', 600);
    ini_set('memory_limit', '1024M');

    /*
    |--------------------------------------------------------------------------
    | mPDF temporary directory
    |--------------------------------------------------------------------------
    */
    $tempDirectory = storage_path('app/mpdf-temp');

    if (!is_dir($tempDirectory)) {
        mkdir($tempDirectory, 0755, true);
    }

    $mpdf = new Mpdf([
        'default_font_size' => 10,
        'default_font' => 'Arial',
        'margin_left' => 5,
        'margin_right' => 5,
        'margin_top' => 10,
        'margin_bottom' => 15,
        'format' => 'A4',
        'autoLangToFont' => true,
        'autoScriptToLang' => true,
        'tempDir' => $tempDirectory,
    ]);

    $mpdf->setFooter(
        'Page {PAGENO} of {nb} >> Outstanding Debtors for: '
        . $due->title
        . ' '
        . $dueSession
    );

    $mpdf->showImageErrors = true;

    /*
    |--------------------------------------------------------------------------
    | Get debtors using your existing AssociationDue method
    |--------------------------------------------------------------------------
    |
    | This returns Illuminate\Support\Collection.
    | Do not call ->with(), ->load(), ->orderBy(), or ->chunkById()
    | on this result.
    |
    */
    $initialDebtors = $due->membersUnpaidAndUnwaived();

    /*
    |--------------------------------------------------------------------------
    | Extract usernames from the returned Support Collection
    |--------------------------------------------------------------------------
    */
    $debtorUsernames = $initialDebtors
        ->pluck('username')
        ->filter()
        ->unique()
        ->values();

    unset($initialDebtors);

    /*
    |--------------------------------------------------------------------------
    | Re-query as Eloquent models with all PDF relationships
    |--------------------------------------------------------------------------
    |
    | The original method returns MemberAcademicInfo records, but the
    | returned support collection cannot eager-load anything. Therefore,
    | re-fetch the same academic records with required relationships.
    |
    */
    $debtors = MemberAcademicInfo::query()
        ->with([
            'personalProfile',

            'theProgram',
            'programType',
            'theFaculty',
            'theDepartment',

            'approvedReAdmissions.previousProgram',
            'approvedReAdmissions.previousProgramType',
            'approvedReAdmissions.previousFaculty',
            'approvedReAdmissions.previousDepartment',
        ])
        ->whereIn('username', $debtorUsernames)
        ->get()
        ->sortBy(function ($member) {
            return strtolower(
                $member->personalProfile?->first_name
                ?? $member->username
                ?? ''
            );
        })
        ->values();

    unset($debtorUsernames);

    /*
    |--------------------------------------------------------------------------
    | Small shared report data
    |--------------------------------------------------------------------------
    */
    $basicSystemInfo = basicSystemInfo();
    $tenant = tenant();

    $expectedPayers = $due->expectedPayerCount();
    $successfulPaymentsCount = $due->successfulPaymentCount();
    $approvedWaiversCount = $due->approvedWaiverCount();

    $debtorCount = $debtors->count();

    $data = [
        'due' => $due,
        'tenant' => $tenant,
        'basicSystemInfo' => $basicSystemInfo,
        'dueSession' => $dueSession,

        'expectedPayers' => $expectedPayers,
        'successfulPaymentsCount' => $successfulPaymentsCount,
        'approvedWaiversCount' => $approvedWaiversCount,
        'debtorCount' => $debtorCount,
    ];

    /*
    |--------------------------------------------------------------------------
    | PDF CSS once
    |--------------------------------------------------------------------------
    */
    $css = view(
        'printFiles.dues.paid.partials.waiverPaymentStyles',
        compact('data')
    )->render();

    $mpdf->WriteHTML(
        $css,
        HTMLParserMode::HEADER_CSS
    );

    unset($css);

    /*
    |--------------------------------------------------------------------------
    | Debtors report header once
    |--------------------------------------------------------------------------
    */
    $headerHtml = view(
        'printFiles.dues.outstanding.partials.debtorsHeader',
        compact('data')
    )->render();

    $mpdf->WriteHTML(
        $headerHtml,
        HTMLParserMode::HTML_BODY
    );

    unset($headerHtml);

    /*
    |--------------------------------------------------------------------------
    | No debtors
    |--------------------------------------------------------------------------
    */
    if ($debtors->isEmpty()) {
        $mpdf->WriteHTML(
            '<div class="no-records">'
            . 'No outstanding debtors were found for this due.'
            . '</div>',
            HTMLParserMode::HTML_BODY
        );
    } else {
        /*
        |--------------------------------------------------------------------------
        | Collection chunks for mPDF HTML rendering
        |--------------------------------------------------------------------------
        |
        | This is PDF rendering chunking, not query/database chunking.
        |
        */
        $debtorSerialNumber = 0;

        $debtors->chunk(50)->each(
            function ($debtorChunk) use (
                $due,
                $data,
                $mpdf,
                &$debtorSerialNumber
            ) {
                /*
                |--------------------------------------------------------------------------
                | Calculate academic context for current PDF batch
                |--------------------------------------------------------------------------
                |
                | $debtor is already MemberAcademicInfo.
                | Do not use $debtor->memberAcademicInfo.
                |
                */
                $debtorChunk->each(function ($debtor) use ($due) {
                    $debtor->academicContext = resolveAcademicContextForDue(
                        $debtor,
                        $due
                    );
                });

                $html = view(
                    'printFiles.dues.outstanding.partials.debtorChunk',
                    [
                        'data' => $data,
                        'debtors' => $debtorChunk,
                        'serialStart' => $debtorSerialNumber,
                    ]
                )->render();

                $mpdf->WriteHTML(
                    $html,
                    HTMLParserMode::HTML_BODY
                );

                $debtorSerialNumber += $debtorChunk->count();

                unset($html, $debtorChunk);

                gc_collect_cycles();
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Safe filename
    |--------------------------------------------------------------------------
    */
    $fileName = 'outstanding-debtors-'
        . str($due->title)->slug()
        . '-'
        . str($dueSession)->replace('/', '-')
        . '-'
        . now('Africa/Lagos')->format('Y-m-d_H-i-s')
        . '.pdf';

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    $mpdf->Output($fileName, 'I');

    exit;
}
}
