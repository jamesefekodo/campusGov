@extends('layouts.userLayout')

@section('title', $data['pageTitle'])

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">

            <div class="individual-review-card-container row">
                <div class="col-md-12 individual-review-card">

                    <div class="table_head">

                        <div class="table_head_item col-md-6">
                            <div class="table_title">
                                {{ $data['pageTitle'] }} <span class="ms-5 text-white-60 fs-7"><i class="fa fa-filter"
                                        data-toggle="modal" data-target="#pastQuestionFilterModal"></i></span>

                                <small class="d-block text-muted fs-6 mt-1">
                                    {{ $data['totalPastQuestions'] }}
                                    {{ Str::plural('past question', $data['totalPastQuestions']) }}
                                    found
                                </small>
                            </div>
                        </div>

                        <div class="table_head_item col-md-6">
                            <div class="custom_search">

                                <form method="GET" action="{{ route('memberPastQuestions') }}">

                                    <div class="input-group-append">
                                        <input type="search" class="table_search my-2 my-sm-0" name="searchKey"
                                            value="{{ $data['searchKey'] }}" placeholder="Course code, title or keyword">

                                        <button type="submit" class="table_search_button btn btn-primary">

                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>

                    <div class="col-md-12 m-auto">
                        <div class="my_form_container">
                            <div class="pb-4">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered p-3">
                                            <thead class="justify-content-center text-center align-middle">
                                                <tr>
                                                    <th scope="col" style="width: 70px" class="table_header">

                                                        S/N
                                                    </th>

                                                    <th scope="col" class="table_header">
                                                        Course
                                                    </th>

                                                    <th scope="col" class="table_header">
                                                        Program / Type
                                                    </th>

                                                    <th scope="col" class="table_header">
                                                        Faculty / Department
                                                    </th>

                                                    <th scope="col" class="table_header">
                                                        Semester
                                                    </th>

                                                    <th scope="col" class="table_header">
                                                        Academic Year
                                                    </th>

                                                    <th scope="col" class="table_header">
                                                        Question Type
                                                    </th>

                                                    <th scope="col" class="table_header">
                                                        Status
                                                    </th>

                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($data['pastQuestions'] as $pastQuestion)
                                                    <tr class="align-middle text-center">

                                                        <th>
                                                            {{ $data['pastQuestions']->firstItem() + $loop->index }}
                                                        </th>

                                                        <td class="text-start">
                                                            <strong>
                                                                {{ $pastQuestion->course_code }}
                                                            </strong>

                                                            <br>

                                                            <small class="text-muted">
                                                                {{ $pastQuestion->course_title }}
                                                            </small>
                                                        </td>

                                                        <td class="text-start">
                                                            <div>
                                                                {{ $pastQuestion->programRelation?->name ?? 'N/A' }}
                                                            </div>

                                                            <small class="text-muted">
                                                                {{ $pastQuestion->programTypeRelation?->type_name ?? 'N/A' }}
                                                            </small>
                                                        </td>

                                                        <td class="text-start">
                                                            <div>
                                                                {{ $pastQuestion->facultyRelation?->name ?? 'N/A' }}
                                                            </div>

                                                            <small class="text-muted">
                                                                {{ $pastQuestion->departmentRelation?->name ?? 'N/A' }}
                                                            </small>
                                                        </td>

                                                        <td>
                                                            {{ ucfirst($pastQuestion->semester) }}
                                                        </td>

                                                        <td>
                                                            {{ $pastQuestion->academic_year }}
                                                        </td>

                                                        <td>
                                                            @if ($pastQuestion->is_exam)
                                                                <span class="badge bg-danger text-white">
                                                                    Exam
                                                                </span>
                                                            @else
                                                                <span class="badge bg-info text-dark">
                                                                    Others
                                                                </span>
                                                            @endif
                                                        </td>

                                                        <td>
                                                            @switch($pastQuestion->approval_status)
                                                                @case('draft')
                                                                    <span class="badge bg-secondary-subtle text-secondary">
                                                                        Draft
                                                                    </span>
                                                                @break

                                                                @case('pending')
                                                                    <span class="badge bg-warning-subtle text-warning">
                                                                        Awaiting Approval
                                                                    </span>
                                                                @break

                                                                @case('approved')
                                                                    <span class="badge bg-success-subtle text-success">
                                                                        Approved
                                                                    </span>
                                                                @break

                                                                @case('declined')
                                                                    <span class="badge bg-danger-subtle text-danger">
                                                                        Declined
                                                                    </span>
                                                                @break

                                                                @default
                                                                    <span class="badge bg-danger-subtle text-danger">
                                                                        {{ ucfirst($pastQuestion->approval_status) }}
                                                                    </span>
                                                            @endswitch
                                                        </td>

                                                        <td>
                                                            <a class="btn btn-primary btn-sm"
                                                                href="{{ route('memberViewPastQuestion', ['uuid' => $pastQuestion->uuid]) }}">

                                                                View
                                                            </a>
                                                        </td>

                                                    </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="9" class="text-center">
                                                                <h3 class="p-1 m-1 text-red">
                                                                    No Past Questions Found!
                                                                </h3>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>

                                            @php
                                                $paginatedArray = $data['pastQuestions'];
                                            @endphp

                                            @include('layouts.include.pagination')

                                            @if ($data['requestType'] === 'filter')
                                                <div class="w-100 justify-content-center text-center mb-4">
                                                    <a class="btn btn-success" href="{{ route('memberPastQuestions') }}">

                                                        <i class="fas fa-chevron-left"></i>
                                                        Back to All Past Questions
                                                    </a>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        @include('users.admin.academicResources.pastQuestions.include.pastQuestionFilterModal')
    @endsection
