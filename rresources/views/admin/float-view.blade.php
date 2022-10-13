@extends("admin.layout.sidebar")

@section('styles')
    <link rel="stylesheet" href="{{asset('assets/plugins/datatables/datatables.min.css')}}">
@endsection

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

             
<div class="content-wrapper">

<section class="content-header">
<div class="container-fluid">
<div class="row mb-2">



<div class="col-sm-6">
<h3>View Loan</h3>
</div>
 
</div>
</div>
</section>

<section class="content">
<div class="container-fluid">
<div class="row">

@if (session('status'))
                                    <div class="card-body">
                                        <div class="mb-4 font-medium text-sm text-green-600 alert-dismissible alert">
                                            {{ session('status') }}
                                        </div>
                                    </div>
                                    @endif

                                    @if (session('error'))
                                    <div class="card-body">
                                        <div class="mb-4 font-medium text-sm alert-danger alert-dismissible alert">
                                            {{ session('error') }}
                                        </div>
                                    </div>
                                    @endif

                                    @if (session('success'))
                                    <div class="card-body">
                                        <div class="mb-4 font-medium text-sm alert-success alert-dismissible alert">
                                            {{ session('success') }}
                                        </div>
                                    </div>
                                    @endif
                                    <br>
<div class="col-md-5">

<div class="card card-primary card-outline">
<div class="card-body box-profile">

<h3 class="profile-username text-center">{{$agent->firstname ?? "N/A"}} {{$agent->lastname ?? "N/A"}}</h3>
<p class="text-muted text-center">{{$agent->email ?? "N/A"}}</p>
<ul class="list-group list-group-unbordered mb-3">
<li class="list-group-item">
<b>Sub Agents</b> <a class="float-right">{{$subagent}}</a>
</li> 
<li class="list-group-item">
<b>Transaction Count</b> <a class="float-right">{{App\Models\Transaction::whereUuid($agent->uuid)->count()}}</a>
</li> 
<li class="list-group-item">
<b>Total Transaction</b> <a class="float-right">{{App\Models\Transaction::whereUuid($agent->uuid)->count()}}</a>
</li> 
<li class="list-group-item">
<b>Running Loan</b> <a class="float-right">{{App\Models\Loan::whereStatus(1)->whereUserId($agent->id)->count()}}</a>
</li> 

<li class="list-group-item">
<b>Due Loan </b> <a class="float-right">{{App\Models\Loan::where('expire','>',$now)->whereUserId($agent->id)->count()}}</a>
</li> 

<li class="list-group-item">
<b>Pending Loan</b> <a class="float-right">{{App\Models\Loan::whereUserId($agent->id)->whereStatus(0)->count()}}</a>
</li> 
</ul>
<a href="{{url('admin/floatapprove')}}/{{$loan->id}}" class="btn btn-primary btn-block"><b>Approve Loan</b></a>

<a href="{{url('admin/floatreject')}}/{{$loan->id}}" class="btn btn-danger btn-block"><b>Reject Loan</b></a>

</div>

</div>




</div>

<div class="col-md-7">
<div class="card"> 
<div class="card-body">
<div class="tab-content">
<div class="active tab-pane" id="activity">
<h4>Transactions Log
</h4>
                            <div class="table-responsive">
                                <table class="table table-center table-hover datatable">
                                    <thead class="thead-light">
                                    <tr>
                                        <th></th>  
                                        <th>Transaction Reference</th>  
                                        <th>Amount</th>
                                        <th>Interest</th>
                                         <th>Status</th>
                                        <th>Date Requested</th>
                                     </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($trx as $data)
                                        <tr>
                                            <td>
                                                {{$loop->iteration}}
                                            </td>  
                                            <td>
                                                {{$data->reference}}
                                            </td>  
                                            <td>
                                            ₦{{$data->amount}}
                                            </td>
                                            <td>
                                                @if($data->status == 0)
                                                    <span class="badge badge-warning"> Pending </span>
                                                @elseif($data->status == 1)
                                                    <span class="badge badge-danger"> Running </span>
                                                @elseif($data->status == 2)
                                                    <span class="badge badge-success"> Closed </span>
                                                @else
                                                    <span class="badge badge-danger"> Failed</span>
                                                @endif
                                            </td> 
                                            <td>
                                            {!! date(' D, d M, Y', strtotime($data->created_at)) !!}<br>
                                           <b> {{ Carbon\Carbon::parse($data->created_at)->diffForHumans() }}</b>
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
</section>

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
