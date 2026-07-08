@extends('layouts.systemSettings.setup.systemSetupLayout')
@section('title', 'Start')
@section('content')


    <div class="container setup_container">
        <div class="login_wrapper">
            @include('layouts.systemSettings.setup.include.setupSideBar')
            <hr>
            <div class="setup_form_container">
                <div class="setup_header">System Installation</div>
                <form  class="login_form">
                    @csrf
                    <div class="container col-md-12">
                        <span class="section_title">System Details</span>
                        <table class="product_to_setup">
                            <tr>
                                <td class="w-25"><span class="title">Product Name:</span></td>
                                <td class="w-80 px-1 text-white">{{ $data['systemInfo']['product_name'] }}</td>
                            </tr>
                            <tr>
                                <td class="w-20"><span class="title">Description:</span></td>
                                <td class="w-75 px-1 text-white">{{ $data['systemInfo']['description'] }}</td>
                            </tr>
                            <tr>
                                <td class="w-20"><span class="title">Sub Product Name:</span></td>
                                <td class="w-80 px-1 text-white">{{ $data['systemInfo']['sub_product_name'] }}</td>
                            </tr>
                            <tr>
                                <td class="w-20"><span class="title">System Type:</span></td>
                                <td class="w-80 px-1 text-white">{{ $data['systemInfo']['system_type'] }}</td>
                            </tr>
                            <tr>
                                <td class="w-20"><span class="title">Version:</span></td>
                                <td class="w-80 px-1 text-white">{{ $data['systemInfo']['version_number'] }}</td>
                            </tr>
                        </table>
                    </div>
                    <hr>
                    <div class="needed_to_proceed">
                        <p class="title">You will need to know the following items before proceeding</p>
                        <div class="col-md-12 text-start">
                            <div class="row">
                                <div class="col-md-6">
                                    <i class="fa-solid fa-check check_icon"></i> Product ID
                                </div>
                                <div class="col-md-6">
                                    <i class="fa-solid fa-check check_icon"></i> License Key
                                </div>
                                <div class="col-md-6">
                                    <i class="fa-solid fa-check check_icon"></i> License Key
                                </div>
                                <div class="col-md-6">
                                    <i class="fa-solid fa-check check_icon"></i> Database Name
                                </div>
                                <div class="col-md-6">
                                    <i class="fa-solid fa-check check_icon"></i> Database Username
                                </div>
                                <div class="col-md-6">
                                    <i class="fa-solid fa-check check_icon"></i> Database Password
                                </div>
                                <div class="col-md-6">
                                    <i class="fa-solid fa-check check_icon"></i> Database Hostname
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <button type="button" onclick="gotoRouteLink('{{ route('installSystemBasicRequirementCheck') }}')" class="login_botton">Start Installation Process</button>
                    </div>
                    <br>
                </form>
            </div>
        </div>

    </div>

@endsection


{{-- Link --}}
<script>
    function gotoRouteLink(url) {
        window.location.href = url;
    }
</script>
