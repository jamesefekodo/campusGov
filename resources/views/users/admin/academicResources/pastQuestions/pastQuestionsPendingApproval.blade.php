@extends('layouts.userLayout')
@section('title', 'Past Questions :: Approval')

@section('content')
    <div class="app-content pt-3 mb-4">
        <div class="container-fluid">
            <div class="navigation">
                @include('users.admin.academicResources.pastQuestions.include.inner_nav')
            </div>

            <div class="individual-review-card-container">
                <div class="col-md-12 individual-review-card">
                    <div class="table_head">
                        <div class="table_head_item col-md-12">
                            <div class="table_title">
                                Past Questions :: Approval
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
                                                        Course
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Session
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Department
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Program
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        Submited By
                                                    </th>
                                                    <th scope="col" class="table_header">
                                                        View
                                                    </th>
                                                </tr>
                                            </thead>

                                            @if ($data['pastQuestions']->isEmpty())
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <h3 class="p-1 m-1 text-red">
                                                            No Records Found!
                                                        </h3>
                                                    </td>
                                                </tr>
                                            @else
                                                <tbody>
                                                    @foreach ($data['pastQuestions'] as $question)
                                                        <tr class=" align-middle text-center">
                                                            <th>{{ $loop->iteration }}</th>
                                                            <td class="text-start">
                                                                <div class="d-flex align-items-center gap-3">
                                                                    <div class="flex-grow-1 min-width-0 text-center">
                                                                        <p class="fw-bold mb-0 text-truncate">
                                                                            {{ $question->course_title }}
                                                                        </p>
                                                                        <p class="text-muted mb-0 small text-truncate">
                                                                            {{ strtoupper($question->course_code) }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-start">
                                                                <div class="d-flex align-items-center gap-3">
                                                                    <div class="flex-grow-1 min-width-0 text-center">
                                                                        <p class="fw-bold mb-0 text-truncate">
                                                                            {{ $question->academic_year }}/{{ $question->academic_year + 1 }}
                                                                        </p>
                                                                        <p
                                                                            class="text-muted mb-0 small text-truncate text-capitalize">
                                                                            {{ $question->semester }} Semester @if ($question->level)
                                                                                - Year {{ $question->level }}
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-start">
                                                                <div class="d-flex align-items-center gap-3">
                                                                    <div class="flex-grow-1 min-width-0 text-center">
                                                                        <p class="fw-bold mb-0 text-truncate">
                                                                            {{ $question->departmentRelation->name }}
                                                                        </p>
                                                                        <p
                                                                            class="text-muted mb-0 small text-truncate text-capitalize">
                                                                            {{ $question->facultyRelation->name }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-start">
                                                                <div class="d-flex align-items-center gap-3">
                                                                    <div class="flex-grow-1 min-width-0 text-center">
                                                                        <p class="fw-bold mb-0 text-truncate">
                                                                            {{ $question->programRelation->name }}
                                                                        </p>
                                                                        <p
                                                                            class="text-muted mb-0 small text-truncate text-capitalize">
                                                                            {{ $question->programTypeRelation->type_name }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-start">
                                                                <div class="d-flex align-items-center gap-3">
                                                                    <div class="flex-grow-1 min-width-0 text-center">
                                                                        <p class="fw-bold mb-0 text-truncate">
                                                                            @if ($question?->creator?->memberPersonalProfile)
                                                                                {{ trim(
                                                                                    $question->creator->memberPersonalProfile->first_name . ' ' . $question->creator->memberPersonalProfile->last_name,
                                                                                ) }}
                                                                            @else
                                                                                {{ $question?->created_by ?? 'Unknown user' }}
                                                                            @endif
                                                                        </p>
                                                                        <p
                                                                            class="text-muted mb-0 small text-truncate text-capitalize">
                                                                            {{ \Carbon\Carbon::parse($question->created_at)->format('l, d F, Y h:i A') }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-primary btn-sm"
                                                                    href="{{ route('viewPastQuestion', ['uuid' => $question->uuid]) }}"><i
                                                                        class="fas fa-eye me-2"></i> View</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                        {{-- Pagination --}}
                                        @php
                                            $paginatedArray = $data['pastQuestions'];
                                        @endphp

                                        @include('layouts.include.pagination')
                                        {{-- Back Home --}}
                                        <div class="w-100 justify-content-center text-center mb-4">
                                            <a class="btn btn-success" href="{{ route('pastQuestionsHome') }}">
                                                <i class="fas fa-chevron-left"></i>
                                                Back
                                            </a>
                                        </div>
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
