@extends('layouts.userLayout')
@section('title', 'SEO Config')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">

                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="col-md-12 m-auto">
                                <div class="my_form_container pb-5">
                                    <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                        SEO Config
                                    </h4>

                                    <form class="form my_form" method="POST" 
                                    action="{{ route('updateSeoConfigSubmit') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="meta_title">Meta Title<span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="meta_title"
                                                    value="{{ $data['seoDetails']->meta_title }}" required>
                                                <p style="color: red">{{ $errors->first('meta_title') }}</p>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="meta_description">Meta Description<span
                                                        class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="meta_description"
                                                    value="{{ $data['seoDetails']->meta_description }}" required>
                                                <p style="color: red">{{ $errors->first('meta_description') }}</p>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="meta_keywords">Meta Keywords<span
                                                        class="text-red">*</span></label>
                                                <textarea name="meta_keywords" class="form-control" style="min-height: 100px;" required>{{ $data['seoDetails']->meta_keywords }}</textarea>
                                                <p style="color: red">{{ $errors->first('meta_keywords') }}</p>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="header_scripts">Header Scripts <span
                                                        class="text-small text-secondary">(before
                                                        &lt;/head&gt;)</span></label>
                                                <textarea name="header_scripts" class="form-control" style="min-height: 100px;">{{ $data['seoDetails']->header_scripts }}</textarea>
                                                <p style="color: red">{{ $errors->first('header_scripts ') }}</p>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="body_scripts">Header Scripts <span
                                                        class="text-small text-secondary">(before
                                                        &lt;/body&gt;)</span></label>
                                                <textarea name="body_scripts" class="form-control" style="min-height: 100px;">{{ $data['seoDetails']->body_scripts }}</textarea>
                                                <p style="color: red">{{ $errors->first('body_scripts ') }}</p>
                                            </div>
                                            <hr>
                                            <button type="submit" class="green-button submit-btn">
                                                <span class="btn-text">
                                                    Update
                                                </span>

                                                <span class="btn-loading" style="display:none;">
                                                    <i class="fas fa-spinner fa-spin"></i> Processing...
                                                </span>
                                            </button>
                                        </div>
                                    </form>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
