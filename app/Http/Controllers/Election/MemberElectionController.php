<?php

namespace App\Http\Controllers\Election;

use App\Http\Controllers\Controller;
use App\Models\Elections\Election;
use App\Models\Elections\ElectionCandidateApplication;
use App\Models\Elections\ElectionMonetaryVote;
use App\Models\Elections\ElectionPosition;
use App\Models\Elections\ElectionStandardVote;
use App\Models\Finance\Settings\ElectronicPaymentMethod;
use App\Models\Finance\Settings\Tax;
use App\Models\Finance\Transactions\PaymentTransaction;
use App\Models\Members\MemberAcademicInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Mpdf\Mpdf;

class MemberElectionController extends Controller
{
    public function memberNewElections(Request $request)
    {
        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $elections = '';
        $totalElections = 0;


        if (Route::currentRouteName() == 'memberNewElectionsSearch') {
            $searchKey = $request->input('searchKey');
            $elections = Election::where('approval_status', 'approved')
                ->whereIn('status', ['new', 'registration', 'screening', 'campaign'])
                // ->whereIn('status', [
                //     'new',
                //     'registration',
                //     'screening',
                //     'campaign',
                //     'voting',
                //     'result_pending',
                //     'completed',
                // ])
                ->when($searchKey, function ($query) use ($searchKey) {
                    // Group the search conditions to ensure they apply cleanly alongside other constraints
                    $query->where(function ($q) use ($searchKey) {
                        $q->where('election_id', 'like', "%{$searchKey}%")
                            ->orWhere('voting_method', 'like', "%{$searchKey}%")
                            ->orWhere('title', 'like', "%{$searchKey}%")
                            ->orWhere('description', 'like', "%{$searchKey}%")
                            ->orWhere('status', 'like', "%{$searchKey}%");
                    });
                })
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString();

            $totalElections = $elections->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $elections = Election::where('approval_status', 'approved')
                ->whereIn('status', ['new', 'registration', 'screening', 'campaign'])
                // ->whereIn('status', [
                //     'new',
                //     'registration',
                //     'screening',
                //     'campaign',
                //     'voting',
                //     'result_pending',
                //     'completed',
                // ])
                ->orderByDesc('created_at')
                ->paginate(20);

            $totalElections = $elections->total();
            $requestType = 'all';
            $pageTitle = 'New Elections';
        }
        $data = [
            'elections' => $elections,
            'totalElections' => $totalElections,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.member.elections.elections', compact('data'));
    }
    public function memberOngoingElections(Request $request)
    {
        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $elections = '';
        $totalElections = 0;


        if (Route::currentRouteName() == 'memberOngoingElectionsSearch') {
            $searchKey = $request->input('searchKey');
            $elections = Election::where('approval_status', 'approved')
                ->where('status', 'voting')

                ->when($searchKey, function ($query) use ($searchKey) {
                    // Group the search conditions to ensure they apply cleanly alongside other constraints
                    $query->where(function ($q) use ($searchKey) {
                        $q->where('election_id', 'like', "%{$searchKey}%")
                            ->orWhere('voting_method', 'like', "%{$searchKey}%")
                            ->orWhere('title', 'like', "%{$searchKey}%")
                            ->orWhere('description', 'like', "%{$searchKey}%")
                            ->orWhere('status', 'like', "%{$searchKey}%");
                    });
                })
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString();

            $totalElections = $elections->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $elections = Election::where('approval_status', 'approved')
                ->where('status', 'voting')
                ->orderByDesc('created_at')
                ->paginate(20);

            $totalElections = $elections->total();
            $requestType = 'all';
            $pageTitle = 'Ongoing Elections';
        }
        $data = [
            'elections' => $elections,
            'totalElections' => $totalElections,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.member.elections.elections', compact('data'));
    }

    public function memberCompletedElections(Request $request){
        $requestType = '';
        $searchKey = '';
        $pageTitle = '';
        $elections = '';
        $totalElections = 0;


        if (Route::currentRouteName() == 'searchMemberCompletedElections') {
            $searchKey = $request->input('searchKey');
            $elections = Election::where('approval_status', 'approved')
                ->where('status', 'completed')

                ->when($searchKey, function ($query) use ($searchKey) {
                    // Group the search conditions to ensure they apply cleanly alongside other constraints
                    $query->where(function ($q) use ($searchKey) {
                        $q->where('election_id', 'like', "%{$searchKey}%")
                            ->orWhere('voting_method', 'like', "%{$searchKey}%")
                            ->orWhere('title', 'like', "%{$searchKey}%")
                            ->orWhere('description', 'like', "%{$searchKey}%")
                            ->orWhere('status', 'like', "%{$searchKey}%");
                    });
                })
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString();

            $totalElections = $elections->total();
            $requestType = 'search';
            $pageTitle = 'Search Results :: ' . $searchKey;
        } else {
            $elections = Election::where('approval_status', 'approved')
                ->where('status', 'completed')
                ->orderByDesc('created_at')
                ->paginate(20);

            $totalElections = $elections->total();
            $requestType = 'all';
            $pageTitle = 'Ongoing Elections';
        }
        $data = [
            'elections' => $elections,
            'totalElections' => $totalElections,
            'requestType' => $requestType,
            'searchKey' => $searchKey,
            'pageTitle' => $pageTitle,
        ];

        return view('users.member.elections.elections', compact('data'));
    }

    public function memberViewElection($uuid)
    {
        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        if ($election->status == 'voting') {
            $election->update(
                [
                    'qualified_voters' => totalQualifiedVoters($election),
                ]
            );
        }

        $data = [
            'election' => $election,
        ];
        return view('users.member.elections.viewElection', compact('data'));
    }
    public function memberInitiateSelfApplyForPositionSubmit($uuid, Request $request)
    {
        $position = ElectionPosition::where('uuid', $uuid)->first();
        if (!$position) {
            return redirect()->back()->with('error', 'Records not found!');
        }


        $request->validate([
            'bio' => 'required|string',
            'manifesto' => 'required|string',
        ]);



        // already applied for this position
        $hasAlreadyApplied = ElectionCandidateApplication::where('election_id', $position->election_id)->where('position_id', $position->position_id)->where('candidate', Auth::user()->username)->first();
        if ($hasAlreadyApplied) {
            return redirect()->route('myElectionApplications', ['uuid' => $position->election->uuid])->with('error', 'You have a pending application or you have already applied for this position!');
        }

        // check if member has applied for a position before and if he can apply for multiple position
        if ($position->election->one_candidate_multiple_positions !== true) {
            $haveSomeApplications = ElectionCandidateApplication::where('election_id', $position->election_id)->where('candidate', Auth::user()->username)->first();
            if ($haveSomeApplications) {
                return redirect()->route('myElectionApplications', ['uuid' => $position->election->uuid])->with('error', 'You have a pending application or you have already applied for a position!');
            }
        }

        // check if he qualifies
        $qualification = canApplyForPosition(Auth::user(), $position);
        if (!$qualification['qualified']) {
            return redirect()->back()->with('error', $qualification['message']);
        }


        // academic info start
        $academicInfo = MemberAcademicInfo::where('username', Auth::user()->username)->where('academic_status', 'student')->first();
        if (!$academicInfo) {
            return redirect()->route('myElectionApplications', ['uuid' => $position->election->uuid])->with('error', 'Academic info not found!');
        }
        $currentLevel = null;
        if ($academicInfo?->academic_status == 'student') {
            $currentLevel = currentLevel($academicInfo?->theProgram, studentCurrentLevel($academicInfo));
        }
        // academic info end

        ElectionCandidateApplication::updateOrCreate(
            [
                'candidate'   => Auth::user()->username,
                'position_id' => $position->id,
                'election_id' => $position->election_id,
                'application_type' => 'self'
            ],
            [
                'bio'         => $request->input('bio'),
                'manifesto'   => $request->input('manifesto'),
                'current_matric_no' => $academicInfo->matric_number,
                'current_program' => $academicInfo->program,
                'current_program_type' => $academicInfo->program_type,
                'current_faculty' => $academicInfo->faculty,
                'current_department' => $academicInfo->department,
                'level_at_application' => $currentLevel,
                'initiated_by' => Auth::user()->username,
            ]
        );

        return redirect()->route('myElectionApplications', ['uuid' => $position->election->uuid])->with('success', 'Done!');
    }
    public function memberUpdateSelfApplyForPositionSubmit($uuid, Request $request)
    {
        $application = ElectionCandidateApplication::where('uuid', $uuid)->first();
        if (!$application) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $request->validate([
            'bio' => 'required|string',
            'manifesto' => 'required|string',
        ]);

        $application->update([
            'bio'         => $request->input('bio'),
            'manifesto'   => $request->input('manifesto'),
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function myElectionApplications($uuid)
    {
        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $applications = ElectionCandidateApplication::where('election_id', $election->election_id)->where('initiated_by', Auth::user()->username)->orderBy('updated_at', 'desc')->paginate(20);
        if (!$applications) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $data = [
            'election' => $election,
            'applications' => $applications,
        ];
        return view('users.member.elections.myElectionApplications', compact('data'));
    }
    public function viewMyElectionApplication($uuid)
    {
        $application = ElectionCandidateApplication::where('uuid', $uuid)->where('initiated_by', Auth::user()->username)->first();
        if (!$application) {
            return redirect()->back()->with('error', 'Records not found!');
        }

        $data = [
            'application' => $application,
            'election' => $application->election,
        ];
        return view('users.member.elections.viewMyElectionApplications', compact('data'));
    }
    public function memberPayForElectionCandidacy($uuid, Request $request)
    {
        $member = Auth::user();

        $application = ElectionCandidateApplication::where('uuid', $uuid)
            ->where('initiated_by', Auth::user()->username)
            ->where(function ($query) use ($member) {
                $query->where('candidate', $member->username)
                    ->orWhere('initiated_by', $member->username);
            })
            ->first();
        if (!$application) {
            return redirect()->back()->with('error', 'Records not found!');
        }
        // check if it is within accepted period
        $isWithinAcceptedApplicationPeriod =
            $application->election->status === 'registration' &&
            Carbon::now()->between(
                $application->election->form_sales_starts,
                $application->election->form_sales_ends
            );

        if (! $isWithinAcceptedApplicationPeriod) {
            return redirect()->back()->with(
                'error',
                'We are not receiving applications at the moment.'
            );
        }

        $alreadyPaid = $application->successfulPaymentTransaction()->exists();

        // already paid
        if ($alreadyPaid) {
            return redirect()->back()->with(
                'error',
                'This fee has already been paid.'
            );
        }
        // check for pending transaction

        $pendingTransaction = PaymentTransaction::where('election_candidate_application', $application->id)
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
            $transactionId = 'pymt-' . now()->format('Ymds') . '-' . tenant()->id . random_int(100000000, 999999999);
        } while (PaymentTransaction::where('payment_transaction_id', $transactionId)->exists());

        $request->validate([
            'electronic_payment_method' => 'required|exists:electronic_payment_methods,slug',
        ]);

        // Taxes
        $taxes = Tax::where('is_active', true)->orderBy('tax_name')->get();
        $totalTax = 0;
        $taxesPaid = '';
        if ($taxes) {
            $taxesPaid = '';
            foreach ($taxes as $tax) {
                $taxAmount = 0; // Initialize tax amount for each tax.

                // Concatenate tax details.
                $taxesPaid .= '**' . $tax->tax_name . ' (' . $tax->tax_method . '-' . $tax->tax_rate . ')';

                // Calculate tax amount based on the method.
                if ($tax->tax_method === 'percentage') {
                    $taxAmount = ($tax->tax_rate / 100) * $application->position->application_fee;
                } elseif ($tax->tax_method === 'fixed_amount') {
                    $taxAmount = $tax->tax_rate;
                }

                $totalTax += $taxAmount; // Add to total tax.
                $taxesPaid .= ': ₦' . number_format($taxAmount, 2) . "\n"; // Add tax amount to the concatenated string.
            }
        }

        $totalDueAndTax = $application->position->application_fee + $totalTax;
        // get personal profile
        $personalProfile = $member->memberPersonalProfile;
        if (!$personalProfile) {
            return redirect()->back()->with('error', 'Personal profile not found!');
        }
        // paystact
        if ($request->electronic_payment_method == 'paystack') {
            $pastackSettings = ElectronicPaymentMethod::where('slug', $request->electronic_payment_method)->where('is_active', true)->first();
            if (!$pastackSettings) {
                return redirect()->back()->with('error', 'Paystack method not found!');
            }
            $paystackApi = $pastackSettings->apiConfig;
            if (!$paystackApi) {
                return redirect()->back()->with('error', 'Paystack method not found!');
            }

            $secretKey = $paystackApi->secret_key;
            $amount = $totalDueAndTax * 100; // Convert to kobo
            $email = $personalProfile->email1;

            $data = [
                'transaction_id' => $transactionId,
                'reciept_number' => $transactionId,
                'email' => $email,
                'amount' => $amount,
                'callback_url' => route('paystackPaymentCallback', ['transaction_id' => $transactionId]),
            ];

            $response = Http::withToken($secretKey)
                ->post('https://api.paystack.co/transaction/initialize', $data);

            if ($response->successful()) {
                $responseData = $response->json();
                $transaction = new PaymentTransaction();
                $transaction->payment_transaction_id = $transactionId; // Local reference
                $transaction->payment_mode = 'electronic';
                $transaction->payment_purpose = 'election_form';

                $transaction->election_id = $application->election_id;
                $transaction->election_position = $application->position_id;
                $transaction->election_candidate_application = $application->id;

                $transaction->amount_payable = $application->position->application_fee;

                $transaction->registered_payer = $personalProfile->username;
                $transaction->amount_paid = $totalDueAndTax;
                $transaction->total_tax = $totalTax;
                $transaction->taxes_paid = $taxesPaid;
                $transaction->electronic_payment_method = $pastackSettings->slug;
                $transaction->payment_status = 'pending';
                $transaction->approval_status = 'pending';
                $transaction->paid_at = Carbon::now();
                $transaction->save();

                return redirect()->away($responseData['data']['authorization_url']);
            }

            return redirect()->back()->with('error', 'Failed!');
        }
        // flutterwave
        elseif ($request->electronic_payment_method == 'flutterwave') {
            $flutterwaveSettings = ElectronicPaymentMethod::where('slug', $request->electronic_payment_method)->where('is_active', true)->first();
            if (!$flutterwaveSettings) {
                return redirect()->back()->with('error', 'Flutterwave method not found!');
            }
            $flutterwaveApi = $flutterwaveSettings->apiConfig;
            if (!$flutterwaveApi) {
                return redirect()->back()->with('error', 'Flutterwave config not found!');
            }

            $secretKey = $flutterwaveApi->secret_key;

            $data = [
                'tx_ref' => $transactionId,
                'amount' => $totalDueAndTax,
                'currency' => 'NGN',
                'redirect_url' => route('flutterwavePaymentCallback'),
                'customer' => [
                    'email' => $personalProfile->email1,
                    'phone_number' => $personalProfile->phone1,
                    'name' => $personalProfile->first_name . ' ' . $personalProfile->middle_name . '' . $personalProfile->last_name,
                ],
                'customizations' => [
                    'title' => config('app.name'),
                    'description' => 'Application for the position of ' . $application->position->position,
                    'logo' => associationLogo(),
                ],
            ];

            // Make API request for payment
            $response = Http::withToken($secretKey)
                ->post('https://api.flutterwave.com/v3/payments', $data);

            if ($response->successful()) {
                $responseData = $response->json()['data'];
                if (isset($responseData['link'])) {
                    $transaction = new PaymentTransaction();
                    $transaction->payment_transaction_id = $transactionId; // Local reference
                    $transaction->payment_mode = 'electronic';
                    $transaction->payment_purpose = 'election_form';

                    $transaction->amount_payable = $application->position->application_fee;

                    $transaction->election_id = $application->election_id;
                    $transaction->election_position = $application->position_id;
                    $transaction->election_candidate_application = $application->id;

                    $transaction->registered_payer = $personalProfile->username;
                    $transaction->amount_paid = $totalDueAndTax;
                    $transaction->total_tax = $totalTax;
                    $transaction->taxes_paid = $taxesPaid;
                    $transaction->electronic_payment_method = $flutterwaveSettings->slug;
                    $transaction->payment_status = 'pending';
                    $transaction->approval_status = 'pending';
                    $transaction->paid_at = Carbon::now();
                    $transaction->save();
                    return redirect()->away($responseData['link']);
                } else {
                    logger()->error('Invalid Flutterwave response', $response->json());
                    return redirect()->back()->with('error', 'Unable to initiate payment. Please try again.');
                }
            } else {
                logger()->error('Flutterwave API Error', $response->json());
                return redirect()->back()->with('error', 'Failed to initiate payment. Please try again.');
            }
        }
    }
    public function printElectionApplicationEndorsementForm($uuid)
    {
        $application = ElectionCandidateApplication::where('uuid', $uuid)->first();
        if (!$application) {
            return redirect()->back()->with('error', 'Record not found!');
        }
        $election = $application->election;

        $electionSession = $election->academic_session . '/' . $election->academic_session + 1;


        ini_set('pcre.backtrack_limit', 10000000);
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');


        $mpdf = new Mpdf([
            'default_font_size'  => 10,
            'default_font'       => 'Arial',
            'margin_left'        => 5,
            'margin_right'       => 5,
            'margin_top'         => 10,
            'margin_bottom'      => 15,
            'format'             => 'A4',
            'autoLangToFont'     => true,
            'autoScriptToLang'   => true,
            'tempDir'            => storage_path('app/mpdf-temp'),
        ]);

        $mpdf->setFooter('Page {PAGENO} of {nb} >> Election - Application/Endorsement Form : ' . $election->title . ' ' . $electionSession);
        $mpdf->showImageErrors = true;

        $basicSystemInfo = basicSystemInfo();


        $data = [
            'election' => $election,
            'application' => $application,
            'basicSystemInfo' => $basicSystemInfo,
            'tenant' => tenant(),
        ];

        $html = view('printFiles.elections.endorsementForm', compact('data'))->render();

        $mpdf->WriteHTML($html); // AddPage() not needed — WriteHTML handles it

        $fileName = 'Election - Application/Endorsement Form :' . $election->title . '_' . $electionSession . '_' . date('Y-m-d_H-i-s') . '.pdf';

        while (ob_get_level()) ob_end_clean();

        $mpdf->Output($fileName, 'I'); // 'I' for inline, 'D' for download
        exit;
    }
    public function memberElectionCandidates($uuid)
    {
        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $data = [
            'election' => $election,
        ];
        return view('users.member.elections.electionCandidates', compact('data'));
    }
    public function memberViewElectionCandidatesForPosition($uuid)
    {
        $position = ElectionPosition::where('uuid', $uuid)->first();
        if (!$position) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $data = [
            'position' => $position,
            'election' => $position->election,
        ];

        return view('users.member.elections.viewElectionPositionCandidates', compact('data'));
    }
    public function memberElectionVoting($uuid)
    {
        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'No records found!');
        }
        if ($election->status == 'voting') {
            $election->update(
                [
                    'qualified_voters' => totalQualifiedVoters($election),
                ]
            );
        } else {
            return redirect()->route('memberViewElection', ['uuid' => $election->uuid])->with('error', 'Voting is currently inactive!');
        }

        $userStandardVotesForPosition = ElectionStandardVote::where('election_id', $election->election_id)
            ->where('voter', Auth::user()->username)
            ->get();

        $data = [
            'election' => $election,
            'userStandardVotesForPosition' => $userStandardVotesForPosition,
        ];

        return view('users.member.elections.voting.votingPageIndex', compact('data'));
    }
    public function memberViewElectionPositionCandidatesForVote($uuid)
    {
        $position = ElectionPosition::where('uuid', $uuid)->first();
        if (!$position) {
            return redirect()->back()->with('error', 'No records found!');
        }
        $hasAlreadyVoted = ElectionStandardVote::where('election_id', $position->election_id)->where('position', $position->id)->where('voter', Auth::user()->username)->first();

        $data = [
            'position' => $position,
            'election' => $position->election,
            'hasAlreadyVoted' => $hasAlreadyVoted,
        ];
        return view('users.member.elections.voting.votingPageIndex', compact('data'));
    }
    public function memberSubmitStandardVote($election, $position, $candidate)
    {
        $theElection = Election::where('uuid', $election)->where('voting_method', 'standard')->first();
        if (!$theElection) {
            return redirect()->back()->with('error', 'Election records not found!');
        }

        $thePosition = ElectionPosition::where('uuid', $position)->first();
        if (!$thePosition) {
            return redirect()->back()->with('error', 'Position records not found!');
        }

        $theCandidate = ElectionCandidateApplication::where('uuid', $candidate)->first();
        if (!$theCandidate) {
            return redirect()->back()->with('error', 'Candidate records not found!');
        }
        // check if he can vote
        if (!canUserVoteInElection($theElection, Auth::user())) {
            return redirect()->back()->with('error', 'You are not qualified to vote in this election!');
        }

        // check if he has voted for this position
        $hasAlreadyVoted = ElectionStandardVote::where('election_id', $theElection->election_id)->where('position', $thePosition->id)->where('voter', Auth::user()->username)->first();
        if ($hasAlreadyVoted) {
            return redirect()->back()->with('error', 'You can only vote once!');
        }


        // academic info start
        $academicInfo = MemberAcademicInfo::where('username', Auth::user()->username)->where('academic_status', 'student')->first();
        if (!$academicInfo) {
            return redirect()->route('myElectionApplications', ['uuid' => $position->election->uuid])->with('error', 'Academic info not found!');
        }
        $currentLevel = null;
        if ($academicInfo?->academic_status == 'student') {
            $currentLevel = currentLevel($academicInfo?->theProgram, studentCurrentLevel($academicInfo));
        }
        // academic info end



        ElectionStandardVote::create([
            'election_id' => $theElection->election_id,
            'position' => $thePosition->id,
            'candidate' => $theCandidate->application_id,
            'voter' => Auth::user()->username,

            'current_program' => $academicInfo->program,
            'current_program_type' => $academicInfo->program_type,
            'current_faculty' => $academicInfo->faculty,
            'current_department' => $academicInfo->department,
            'level_at_application' => $currentLevel,
        ]);
        return redirect()->back()->with('success', 'Done!');
    }
    public function memberSubmitMonetaryVote($election, $position, $candidate, Request $request)
    {
        $request->validate([
            'no_of_votes' => 'required|integer|min:1',
            'voters_name' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'electronic_payment_method' => 'required|exists:electronic_payment_methods,slug',
        ]);

        // get election info
        $theElection = Election::where('uuid', $election)->where('voting_method', 'monetary')->first();
        if (!$theElection) {
            return redirect()->back()->with('error', 'Election records not found!');
        }

        $thePosition = ElectionPosition::where('uuid', $position)->first();
        if (!$thePosition) {
            return redirect()->back()->with('error', 'Position records not found!');
        }

        $theCandidate = ElectionCandidateApplication::where('uuid', $candidate)->first();
        if (!$theCandidate) {
            return redirect()->back()->with('error', 'Candidate records not found!');
        }


        do {
            $transactionId = 'pymt-' . now()->format('Ymds') . '-' . tenant()->id . random_int(100000000, 999999999);
        } while (PaymentTransaction::where('payment_transaction_id', $transactionId)->exists());

        $basicAmount = $theElection->cost_per_vote * $request->no_of_votes;

        // Taxes
        $taxes = Tax::where('is_active', true)->orderBy('tax_name')->get();
        $totalTax = 0;
        $taxesPaid = '';
        if ($taxes) {
            $taxesPaid = '';
            foreach ($taxes as $tax) {
                $taxAmount = 0; // Initialize tax amount for each tax.

                // Concatenate tax details.
                $taxesPaid .= '**' . $tax->tax_name . ' (' . $tax->tax_method . '-' . $tax->tax_rate . ')';

                // Calculate tax amount based on the method.
                if ($tax->tax_method === 'percentage') {
                    $taxAmount = ($tax->tax_rate / 100) * $basicAmount;
                } elseif ($tax->tax_method === 'fixed_amount') {
                    $taxAmount = $tax->tax_rate;
                }

                $totalTax += $taxAmount; // Add to total tax.
                $taxesPaid .= ': ₦' . number_format($taxAmount, 2) . "\n"; // Add tax amount to the concatenated string.
            }
        }

        $totalDueAndTax = $basicAmount + $totalTax;
        // get personal profile
        $payer = Auth::user();

        if ($payer && $payer->memberPersonalProfile) {

            $payerName = $payer->memberPersonalProfile->first_name . ' ' .
                $payer->memberPersonalProfile->last_name;

            $payerEmail = $payer->email;
            $payerPhone = $payer->phone;
        } else {

            $payerName  = trim($request->voters_name);
            $payerEmail = trim($request->voter_email);
            $payerPhone = trim($request->voter_phone);
        }

        // log voting records
        $votingData = [
            'election_id' => $theElection->election_id,
            'position' => $thePosition->id,
            'candidate' => $theCandidate->application_id,
            'no_of_votes' => $request->no_of_votes,
            'payment_transaction_id' => $transactionId,
            'amount_paid' => $basicAmount,
            'cost_per_vote' => $theElection->cost_per_vote,
        ];

        if (Auth::check()) {
            $votingData['registered_voter'] = Auth::user()->username;
        } else {
            $votingData['unregistered_voter'] = $payerName;
            $votingData['voter_email'] = $payerEmail;
            $votingData['voter_phone'] = $payerPhone;
        }


        // proceed with payment       
        // paystact
        if ($request->electronic_payment_method == 'paystack') {
            $pastackSettings = ElectronicPaymentMethod::where('slug', $request->electronic_payment_method)->where('is_active', true)->first();
            if (!$pastackSettings) {
                return redirect()->back()->with('error', 'Paystack method not found!');
            }
            $paystackApi = $pastackSettings->apiConfig;
            if (!$paystackApi) {
                return redirect()->back()->with('error', 'Paystack method not found!');
            }

            $secretKey = $paystackApi->secret_key;
            $amount = $totalDueAndTax * 100; // Convert to kobo
            $email = $payerEmail;

            $data = [
                'transaction_id' => $transactionId,
                'reciept_number' => $transactionId,
                'email' => $payerEmail,
                'amount' => $payerPhone,
                'callback_url' => route('paystackPaymentCallback', ['transaction_id' => $transactionId]),
            ];

            $response = Http::withToken($secretKey)
                ->post('https://api.paystack.co/transaction/initialize', $data);

            if ($response->successful()) {
                $responseData = $response->json();
                $transaction = new PaymentTransaction();
                $transaction->payment_transaction_id = $transactionId; // Local reference
                $transaction->payment_mode = 'electronic';
                $transaction->payment_purpose = 'voting';

                $transaction->election_id = $theElection->election_id;
                $transaction->election_position = $thePosition->id;
                $transaction->election_candidate_voted = $theCandidate->application_id;

                $transaction->amount_payable = $basicAmount;
                if (Auth::user()) {
                    $transaction->registered_payer = Auth::user()->username;
                } else {
                    $transaction->non_registered_payer = $payerName;
                    $transaction->phone = $payerEmail;
                    $transaction->email = $payerPhone;
                }
                $transaction->amount_paid = $totalDueAndTax;
                $transaction->total_tax = $totalTax;
                $transaction->taxes_paid = $taxesPaid;
                $transaction->electronic_payment_method = $pastackSettings->slug;
                $transaction->payment_status = 'pending';
                $transaction->approval_status = 'pending';
                $transaction->paid_at = Carbon::now();
                $transaction->save();

                ElectionMonetaryVote::create($votingData);

                return redirect()->away($responseData['data']['authorization_url']);
            }

            return redirect()->back()->with('error', 'Failed!');
        }
        // // flutterwave
        elseif ($request->electronic_payment_method == 'flutterwave') {
            $flutterwaveSettings = ElectronicPaymentMethod::where('slug', $request->electronic_payment_method)->where('is_active', true)->first();
            if (!$flutterwaveSettings) {
                return redirect()->back()->with('error', 'Flutterwave method not found!');
            }
            $flutterwaveApi = $flutterwaveSettings->apiConfig;
            if (!$flutterwaveApi) {
                return redirect()->back()->with('error', 'Flutterwave config not found!');
            }

            $secretKey = $flutterwaveApi->secret_key;

            $data = [
                'tx_ref' => $transactionId,
                'amount' => $totalDueAndTax,
                'currency' => 'NGN',
                'redirect_url' => route('flutterwavePaymentCallback'),
                'customer' => [
                    'email' => $payerEmail,
                    'phone_number' => $payerPhone,
                    'name' => $payerName,
                ],
                'customizations' => [
                    'title' => config('app.name'),
                    'description' => 'Election Votes ' . $theElection->title,
                    'logo' => associationLogo(),
                ],
            ];

            // Make API request for payment
            $response = Http::withToken($secretKey)
                ->post('https://api.flutterwave.com/v3/payments', $data);

            if ($response->successful()) {
                $responseData = $response->json()['data'];
                if (isset($responseData['link'])) {
                    $transaction = new PaymentTransaction();
                    $transaction->payment_transaction_id = $transactionId; // Local reference
                    $transaction->payment_mode = 'electronic';
                    $transaction->payment_purpose = 'voting';

                    $transaction->election_id = $theElection->election_id;
                    $transaction->election_position = $thePosition->id;
                    $transaction->election_candidate_voted = $theCandidate->application_id;

                    $transaction->amount_payable = $basicAmount;
                    if (Auth::user()) {
                        $transaction->registered_payer = Auth::user()->username;
                    } else {
                        $transaction->non_registered_payer = $payerName;
                        $transaction->phone = $payerEmail;
                        $transaction->email = $payerPhone;
                    }

                    $transaction->amount_paid = $totalDueAndTax;
                    $transaction->total_tax = $totalTax;
                    $transaction->taxes_paid = $taxesPaid;
                    $transaction->electronic_payment_method = $flutterwaveSettings->slug;
                    $transaction->payment_status = 'pending';
                    $transaction->approval_status = 'pending';
                    $transaction->paid_at = Carbon::now();
                    $transaction->save();

                    ElectionMonetaryVote::create($votingData);


                    return redirect()->away($responseData['link']);
                } else {
                    logger()->error('Invalid Flutterwave response', $response->json());
                    return redirect()->back()->with('error', 'Unable to initiate payment. Please try again.');
                }
            } else {
                logger()->error('Flutterwave API Error', $response->json());
                return redirect()->back()->with('error', 'Failed to initiate payment. Please try again.');
            }
        }
    }
    public function memberElectionResults($uuid)
    {
        $election = Election::where('uuid', $uuid)->first();
        if (!$election) {
            return redirect()->back()->with('error', 'No records found!');
        }

        $position = null;
        $candidateResults = null;
        $highestVotes = null;
        $totalVotes = null;

        $data = [
            'election' => $election,
            'positions' => $election->positions,

            'position' => $position,
            'candidateResults' => $candidateResults,
            'highestVotes' => $highestVotes,
            'totalVotes' => $totalVotes,
        ];
        return view('users.member.elections.results.results', compact('data'));
    }
    public function memberViewElectionResultsForPosition($election, $position)
    {
        $theElection = Election::where('uuid', $election)->first();
        if (!$theElection) {
            return redirect()->back()->with('error', 'Election records not found!');
        }

        $thePosition = ElectionPosition::where('uuid', $position)->first();
        if (!$thePosition) {
            return redirect()->back()->with('error', 'Position records not found!');
        }

        if ($theElection->voting_method == 'monetary') {
            $candidates = ElectionCandidateApplication::with([
                'theCandidate.memberPersonalProfile',
                'election',
            ])
                ->where('election_id', $theElection->election_id)
                ->where('position_id', $thePosition->id)
                ->get();

            $candidateResults = $candidates->map(function ($candidate) {

                $candidate->voteCount = $candidate->totalMonetaryVote();

                return $candidate;
            })->sortByDesc('voteCount')->values();
        } else {
            $candidates = ElectionCandidateApplication::with('theCandidate.memberPersonalProfile')
                ->where('election_id', $theElection->election_id)
                ->where('position_id', $thePosition->id)
                ->get();

            $voteCounts = ElectionStandardVote::where('election_id', $theElection->election_id)
                ->where('position', $thePosition->id)
                ->selectRaw('candidate, count(*) as votes')
                ->groupBy('candidate')
                ->pluck('votes', 'candidate');

            $candidateResults = $candidates->map(function ($candidate) use ($voteCounts) {
                $candidate->voteCount = $voteCounts->get($candidate->application_id, 0);
                return $candidate;
            })->sortByDesc('voteCount')->values();
        }

        $highestVotes = $candidateResults->max('voteCount');
        $totalVotes = $candidateResults->sum('voteCount');

        $data = [
            'election' => $theElection,
            'position' => $thePosition,

            'candidateResults'  => $candidateResults,
            'highestVotes'      => $highestVotes,
            'totalVotes'        => $totalVotes,
        ];
        return view('users.member.elections.results.results', compact('data'));
    }
}
