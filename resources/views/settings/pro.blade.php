@extends('layouts.sidebar')

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="row justify-content-lg-center">
                <div class="col-lg-10">

                    <div class="page-header">
                        <div class="row">
                            <div class="col">
                                <h3 class="page-title">Profile</h3>
                                <ul class="breadcrumb">
                                    {{--                                <li class=""><a href="{{url('dashboard')}}">Dashboard</a></li>--}}
                                    {{--                                <li class="breadcrumb-item active">Profile</li>--}}
                            </ul>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-9 col-md-8">
                            @include('error_success_message')
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Basic information</h5>
                                </div>
                                <div class="card-body">

                                    <form method="POST" action="{{route('update-profile')}}">
                                        @csrf
                                        {{--                                    <div class="row form-group">--}}
                                        {{--                                        <label for="name" class="col-sm-3 col-form-label input-label">Name</label>--}}
                                        {{--                                        <div class="col-sm-9">--}}
                                        {{--                                            <div class="d-flex align-items-center">--}}
                                        {{--                                                <label class="avatar avatar-xxl profile-cover-avatar m-0" for="edit_img">--}}
                                        {{--                                                    <img id="avatarImg" class="avatar-img" src="{{asset('assets/img/profiles/avatar-02.jpg')}}" alt="Profile Image">--}}
                                        {{--                                                    <input type="file" id="edit_img">--}}
                                        {{--                                                    <span class="avatar-edit">--}}
                                        {{--<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>--}}
                                        {{--</span>--}}
                                        {{--                                                </label>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        </div>--}}
                                        {{--                                    </div>--}}
                                        <div class="row form-group">
                                            <label for="name" class="col-sm-3 col-form-label input-label">Name</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" readonly name="lastname"
                                                       placeholder="Last Name"
                                                       value="{{\Illuminate\Support\Facades\Auth::user()->lastname}}">
                                            </div>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" readonly name="firstname"
                                                       placeholder="First Name"
                                                       value="{{\Illuminate\Support\Facades\Auth::user()->firstname}}">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <label for="email" class="col-sm-3 col-form-label input-label">Email</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" readonly name="email" id="email"
                                                       placeholder="Email"
                                                       value="{{\Illuminate\Support\Facades\Auth::user()->email}}">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <label for="phone" class="col-sm-3 col-form-label input-label">Phone</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="phone"
                                                       placeholder="xxxx-xxx-xxxx"
                                                       value="{{\Illuminate\Support\Facades\Auth::user()->phone}}"
                                                       readonly>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <label for="addressline1" class="col-sm-3 col-form-label input-label">Business
                                                Name</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="addressline1"
                                                       placeholder="Your address"
                                                       value="{{\Illuminate\Support\Facades\Auth::user()->business->name ?? \App\Models\User::where([['uuid', \Illuminate\Support\Facades\Auth::user()->uuid], ['sub_agent', NULL]])->first()->business->name}}"
                                                       readonly>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <label for="addressline1" class="col-sm-3 col-form-label input-label">Business
                                                Address</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="addressline1"
                                                       placeholder="Your address"
                                                       value="{{\Illuminate\Support\Facades\Auth::user()->business->address ?? \App\Models\User::where([['uuid', \Illuminate\Support\Facades\Auth::user()->uuid], ['sub_agent', NULL]])->first()->business->name}}"
                                                       readonly>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>

<script src="{{asset('assets/plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-datetimepicker.min.js')}}"></script>

<script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatables/datatables.min.js')}}"></script>

<script src="{{asset('assets/js/script.js')}}"></script>
<script src="{{asset('assets/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>

