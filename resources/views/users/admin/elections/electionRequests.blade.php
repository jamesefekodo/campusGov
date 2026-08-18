@extends('layouts.userLayout')
@section('title', 'Election Related Requests Pending Approval')

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.elections.include.inner_nav')
            </div>
            <div class="individual-review-card-container">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-12">
                            <div class="table_title">
                                Election Related Requests Pending Approval
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 m-auto">
                        <div class="my_form_container">
                            <div class=" pb-4">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered p-3">
                                            <thead class="justify-content-center text-center align-middle">
                                                <tr>
                                                    <th scope="col" style="width: 10px" class="table_header">S/N
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Election
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Request Type
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['electionApprovalRequests']->isEmpty())
                                                There is no request pending approval.
                                            @endif
                                            @if ($data['electionApprovalRequests']->isNotEmpty())
                                                <tbody>
                                                    @foreach ($data['electionApprovalRequests'] as $election)
                                                        <tr class=" align-middle text-center">
                                                            <td>
                                                                *
                                                            </td>
                                                            <td>
                                                                {{ $election->title }}
                                                                {{ $election->academic_session . '/' . $election->academic_session + 1 }}
                                                            </td>
                                                            <td>
                                                                Election Approval
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewElection', ['uuid' => $election->uuid]) }}">View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>

                                    </div>


                                </div>

                            </div>
                        </div>



                    </div>
                </div>
            </div>





        </div>
    </div><!--//app-content-->
@endsection
