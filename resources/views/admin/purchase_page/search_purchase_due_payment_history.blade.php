@extends('admin.admin_master')
@section('admin')
    <!-- Begin Page Content -->
    <div class="page-content">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 text-muted ">Filtering Purchase Due Payment Result</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">
                                <div class="d-print-none">
                                    <div class="float-end">
                                        <a href="javascript:window.print()"
                                            class="btn btn-success waves-effect waves-light"><i
                                                class="fa fa-print"></i> Print</a>
                                                <a href="{{ url()->previous() }}" class="btn btn-dark waves-effect waves-light">
                                                    <i class="fas fa-arrow-right"></i> Back</a>
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <!-- DataTales Example -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive" id="printContent">
                    <h4 class="text-muted text-center">Purchase Due Payment of
                        {{ date('d-m-Y', strtotime(Request::post('start_date'))) }} to
                        {{ date('d-m-Y', strtotime(Request::post('end_date'))) }}</h4>
                        {{-- @php
                            $total = $allPurchase->sum('total_amount');
                        @endphp --}}
                        {{-- <h5 class="text-center text-muted mb-3">Total Purchase: <strong>BDT {{ $total }}</strong> </h5> --}}
                    <table class="table table-bordered" id="" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>
                                    <h6 class="fw-bold">
                                        Sl. No
                                    </h6>
                                </th>
                                <th>
                                    <h6 class="fw-bold">Date</h6>
                                </th>
                                <th>
                                    <h6 class="fw-bold">Total Amount</h6>
                                </th>
                                <th>
                                    <h6 class="fw-bold">Due Amount</h6>
                                </th>
                                <th>
                                    <h6 class="fw-bold">Payment Amount</h6>
                                </th>

                                <th>
                                    <h6 class="fw-bold">Balance</h6>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($supplier_payment as $key => $details)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ date('d-m-Y', strtotime($details->date)) }}</td>
                                <td>{{ number_format($details->total_amount) }}/-</td>
                                <td>{{ number_format($details->due_amount) }}/-</td>
                                <td>{{ number_format($details->paid_amount) }}/-</td>
                                <td>{{ number_format($details->balance) }}/-
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
    <!-- End Page Content -->


    <script>
        function printDiv(printContent) {
            let printContents = document.getElementById(printContent).innerHTML;
            let originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
@endsection
