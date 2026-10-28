@extends('layouts.userLayout')
@section('title', 'Contacts')
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="">
                <div class="navigation">
                    @include('users.admin.config.basic.include.inner_nav')
                </div>
                <div class="row">
                    <div class="individual-review-card-container">
                        <div class="col-md-12 individual-review-card">
                            <div class="col-md-12 m-auto">
                                <div class="my_form_container pb-5">
                                    <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                        Contacts
                                    </h4>
                                    <div class="col-md-12 row px-4 py-1 g-2">
                                        <div class="col-md-12 p-1">
                                            <div class="card stretch-full shadow-sm border-0">
                                                <form class="form my_form" method="POST" 
                                                action="{{ route('updateContactDetailsSubmit') }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="email">Email : </label>
                                                            <input type="email" class="form-control" name="email"
                                                                value="{{ $data['basicSystemSetting']->email }}">
                                                            <p style="color: red">{{ $errors->first('email') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                           <label for="phone">Phone :</label>
                                                           <input type="text" class="form-control" name="phone"
                                                               value="{{ $data['basicSystemSetting']->phone }}">
                                                           <p style="color: red">{{ $errors->first('phone') }}</p>
                                                       </div>
                                                       <div class="form-group col-md-12">
                                                           <label for="address">Address :</label>
                                                           <input type="text" class="form-control" name="address"
                                                               value="{{ $data['basicSystemSetting']->address }}">
                                                           <p style="color: red">{{ $errors->first('address') }}</p>
                                                       </div>
                                                       <div class="form-group col-md-6">
                                                           <label for="facebook_link">Facebook :</label>
                                                           <input type="url"
                                                               class="form-control"
                                                               name="facebook_link" id="facebook_link"
                                                               placeholder="https://facebook.com/your-page"
                                                               value="{{ old('facebook_link', $data['basicSystemSetting']->facebook_link ?? '') }}">
                                                           <p style="color: red">{{ $errors->first('facebook_link') }}</p>
                                                       </div>
                                                       <div class="form-group col-md-6">
                                                           <label for="instagram_link">Instagram :</label>
                                                           <input type="url"
                                                               class="form-control"
                                                               name="instagram_link" id="instagram_link"
                                                               placeholder="https://instagram.com/your-page"
                                                               value="{{ old('instagram_link', $data['basicSystemSetting']->instagram_link ?? '') }}">
                                                           <p style="color: red">{{ $errors->first('instagram_link') }}</p>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="twitter_link">X (formerly Twitter) :</label>
                                                            <input type="url"
                                                                class="form-control"
                                                                name="twitter_link" id="twitter_link"
                                                                placeholder="https://x.com/your-page"
                                                                value="{{ old('twitter_link', $data['basicSystemSetting']->twitter_link ?? '') }}">
                                                            <p style="color: red">{{ $errors->first('twitter_link') }}</p>
                                                        </div>
                                                        
                                                        <div class="form-group col-md-6">
                                                            <label for="linkedin_link">LinkedIn:</label>
                                                            <input type="url"
                                                                class="form-control"
                                                                name="linkedin_link" id="linkedin_link"
                                                                placeholder="https://linkedin.com/your-page"
                                                                value="{{ old('linkedin_link', $data['basicSystemSetting']->linkedin_link ?? '') }}">
                                                            <p style="color: red">{{ $errors->first('linkedin_link') }}</p>
                                                        </div>

                                                    </div>
                                                    <hr>

                                                    <button type="submit" class="green-button submit-btn">
                                                        <span class="btn-text">
                                                            Submit
                                                        </span>

                                                        <span class="btn-loading" style="display:none;">
                                                            <i class="fas fa-spinner fa-spin"></i> Processing...
                                                        </span>
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





            {{-- program/type/fac/dept end --}}

        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
