@extends('layouts.sidebar')

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="row justify-content-lg-center">
                <div class="col-lg-12">

                    <div class="page-header">
                        <div class="row">
                            <div class="col">
                                <h3 class="page-title">Data TopUp</h3>
                                 
                            </div>
                        </div>
                    </div>

                    <ul class="breadcrumb">
                                    <li class=""><a href="{{url('dashboard')}}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Data Topup</li>
                                </ul>
                    <div class="card">
                        <div class="card-body">
                            <div class="box w3-card-4">
                                <span class="text-muted mt-3 mb-4 text-center" style="font-size: x-small">Complete your payment information</span>

                                <x-jet-validation-errors class="mb-4 alert-danger alert-dismissible alert"/>

                                @if (session('danger'))
                                    <div class="mb-4 font-medium text-sm text-red-500">
                                        {{ session('danger') }}
                                    </div>
                                @endif

                                <form action="{{route('bills.dataplans')}}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div id="AirtimeNote" class="alert alert-danger"
                                                 style="text-transform: uppercase;font-weight: bold;font-size: 23px;display: none;"></div>
                                            <div id="AirtimePanel">
                                                <div id="div_id_network" class="form-group mt-4">
                                                    <label for="network" class=" requiredField">
                                                        Network<span class="asteriskField">*</span>
                                                    </label>

                                                    <div class="mb-3">
                                                        <select name="id" class="text-success form-control" required="">
                                                            @foreach($providers as $provider)
                                                                <option value="{{$provider->service_type}}">{{$provider->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
{{--                                                    <button id="network" type="button" onclick="showUser()"  class="btn"--}}
{{--                                                            style="color: white;background-color: #048047" > Get Plan </button>--}}

                                                </div>

                                                <div id="div_id_airtimetype" class="form-group">
                                                    <label for="airtimetype_a" class=" requiredField">
                                                        Amount<span class="asteriskField">*</span>
                                                    </label>
                                                    <div class="form-group">
                                                        <input name="airtimetype" max="4000" min="100"
                                                               class="text-success form-control" placeholder="Amount"
                                                               id="amount" readonly>
                                                    </div>
                                                </div>
                                           

                                                <div id="div_id_network" class="form-group">
                                                    <label for="network" class=" requiredField">
                                                        Phone Number<span class="asteriskField">*</span>
                                                    </label>
                                                    <div class="">
                                                        <input type="number" class="form-control"
                                                               placeholder="Phone number" required>
                                                    </div>
                                                </div>
                                                         
                                                
                                                <div id="div_id_network" class="form-group">
                                                    <label for="network" class=" requiredField">
                                                        Payment Method<span class="asteriskField">*</span>
                                                    </label>
                                                    <div class="">
                                                        <select name="wallet" class="form-control" required>
                                                        <option name="wallet" >Wallet</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn"
                                                        style="color: white;background-color: #048047" id="submite">
                                                    Get Plan
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 ">
                                            <br>
                                            <h6 class="text-center">Codes for Data Balance: </h6>
                                            <ul class="list-group">
                                                <li class="list-group-item list-group-item-warning">MTN Airtime VTU
                                                    <span id="RightT"> *556#  </span></li>
                                                <li class="list-group-item list-group-item-secondary"> 9mobile Airtime VTU
                                                    *232#
                                                </li>
                                                <li class="list-group-item list-group-item-danger"> Airtel Airtime VTU
                                                    *123#
                                                </li>
                                                <li class="list-group-item list-group-item-success"> Glo Airtime VTU
                                                    #124#.
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </form>
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
