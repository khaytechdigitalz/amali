@extends('layouts.sidebar')

@section('styles')
    <link rel="stylesheet" href="{{asset('assets/plugins/datatables/datatables.min.css')}}">
@endsection

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Sub Agent</h3>
                        
                    </div>
                     
                    </div>
                </div>
            </div>

            <ul class="breadcrumb">
                                    <li class=""><a href="{{url('dashboard')}}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Sub Agent</li>
                                </ul>


            <div id="filter_inputs" class="card filter-card">
                <div class="card-body pb-0">
                    <div class="row">
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form class="form" action="{{route('searchSubAgents')}}" id="filter_form" method="post">
                    @csrf
                    <div class="row">
                        <!-- search -->
                        <div class="col-md-3">
                            <label for="search"> Search </label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" value="" placeholder="search...">
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-primary " aria-haspopup="true">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-7"></div>

                        <div class="col-md-2 mt-4">

                            <a href="{{route('addSubAgent')}}" class="btn btn-primary" role="button"><i
                                class="fa fa-plus-circle"> </i> Add Sub Agent</a>
                    </div>
                </div>
            </form>
        </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-center table-hover datatable">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>Agent Code</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                        <th>Wallet Balance</th>
                                        <th>Status</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                {{$user->uuid."-".$user->id}}
                                            </td>
                                            <td>
                                                {{$user->firstname}}
                                            </td>
                                            <td>
                                                {{$user->lastname}}
                                            </td>
                                            <td>
                                                {{$user->email}}
                                            </td>
                                            <td>
                                                {{$user->phone}}
                                            </td>
                                            <td>
                                                {{$user->wallets[0]->balance}}
                                            </td>
                                            <td>
                                                @if($user->status == 1)
                                                    <span class="badge badge-success"> Active </span>
                                                @else
                                                    <span class="badge badge-danger"> De-activated </span>
                                                @endif
                                            </td>
                                            <td>
                                            <div class="btn-group">
                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Action
                                            </button>
                                            <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{route('agentTransactions',$user->id)}}">View Perfomance</a>
                                            <a class="dropdown-item" href="{{route('agentFloat',$user->id)}}">View Float</a> 
                                            </div>
                                            </div>
 
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
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
@endsection
