@extends('layouts.userLayout')
@section('title', 'View Policy :: ' . $data['policy']->title)
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="col-md-12 m-auto">
                                <div class="my_form_container">
                                    <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                        View Policy ::
                                        {{ $data['policy']->title }}
                                    </h4>
                                    <div class="card-body pt-2 pb-5 px-4">
                                        <div class="col-md-12 row">
                                            <div class="col-md-6">
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        TItle:
                                                    </span>
                                                    <span class="text-black">
                                                        {{ $data['policy']->title }}
                                                    </span>
                                                </p>
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Members:
                                                    </span>
                                                    <span class="text-black">
                                                        @switch($data['policy']->applicable_to_member)
                                                            @case(true)
                                                                <span class="badge bg-success-subtle text-success">
                                                                    Applicable
                                                                </span>
                                                            @break

                                                            @default
                                                                <span class="badge bg-danger-subtle text-danger">
                                                                    Not-Applicable
                                                                </span>
                                                        @endswitch
                                                    </span>
                                                </p>
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Created at:
                                                    </span>
                                                    <span class="text-black">
                                                        {{ $data['policy']->created_at->format('l, j F, Y \a\t g:i A') }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Status:
                                                    </span>
                                                    <span class="text-black">
                                                        @switch($data['policy']->is_active)
                                                            @case(true)
                                                                <span class="badge bg-success-subtle text-success">True</span>
                                                            @break

                                                            @default
                                                                <span class="badge bg-danger-subtle text-danger">False</span>
                                                        @endswitch
                                                    </span>
                                                </p>
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">Admin:
                                                    </span>
                                                    <span class="text-black">
                                                        @switch($data['policy']->applicable_to_administrator)
                                                            @case(true)
                                                                <span class="badge bg-success-subtle text-success">
                                                                    Applicable
                                                                </span>
                                                            @break

                                                            @default
                                                                <span class="badge bg-danger-subtle text-danger">
                                                                    Not-Applicable
                                                                </span>
                                                        @endswitch
                                                    </span>
                                                </p>
                                                <p class="m-1 p-0">
                                                    <span class="text-mute">
                                                        Updated at:
                                                    </span>
                                                    <span class="text-black">
                                                        {{ $data['policy']->updated_at->format('l, j F, Y \a\t g:i A') }}
                                                    </span>
                                                </p>
                                            </div>
                                            <p class="m-1 p-0">
                                                <span class="text-black">
                                                    {!! $data['policy']->policy_statement !!}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="d-flex justify-content-end flex-wrap mt-3">
                                            @if (Auth::user()->hasPermission('policy.update'))
                                                <button class="btn btn-warning m-1 d-flex align-items-center"
                                                    data-toggle="modal" data-target="#editPolicyModal">
                                                    <i class="fas fa-edit me-2"></i>
                                                    <span>Update</span>
                                                </button>
                                            @endif

                                            @if (Auth::user()->hasPermission('policy.delete'))
                                                <button type="button"
                                                    class="btn btn-danger m-1 d-flex align-items-center delete-role"
                                                    data-url="{{ route('deletePolicy', ['slug' => $data['policy']->slug]) }}">
                                                    <i class="fas fa-trash me-2"></i>
                                                    <span>Delete</span>
                                                </button>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Back Home --}}
                            <div class="w-100 justify-content-center text-center my-4">
                                <a class="btn btn-primary" href="{{ route('systemPolicies') }}"> <i
                                        class="fas fa-chevron-left"></i>
                                    Back
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- modals start --}}
            @include('users.admin.config.systemPolicy.include.editPolicyModal')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.delete-role').forEach(button => {
                        button.addEventListener('click', function() {

                            const url = this.dataset.url;

                            Swal.fire({
                                title: 'Are you sure?',
                                text: "This policy will be permanently deleted.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Yes, delete it!',
                                cancelButtonText: 'Cancel',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = url;
                                }
                            });

                        });
                    });
                });
            </script>
            {{-- modals end --}}

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
