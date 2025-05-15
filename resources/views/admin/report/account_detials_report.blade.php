@extends('admin.admin_master')
@section('admin')
    <style>
        .table>:not(caption)>*>* {
            padding: 0 !important;
        }
    </style>
    <style>
        @media print {

            .btn,
            .d-print-none {
                display: none !important;
            }

            .border {
                border: 1px solid #000 !important;
            }

            .bg-light {
                background-color: #f8f9fa !important;
            }
        }
    </style>

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Account Report</h4>

                        <div class="d-print-none">
                            <div class="float-end">
                                <a class="btn btn-info" href="{{ url()->previous() }}">Go Back</a>
                                <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light"><i class="fa fa-print"></i> Print</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12 py-3">
                                <h4 class="text-center mb-3">Account details from {{ $start_date }} to {{ $end_date }}</h4>
                                <div class="row text-center mb-4">
                                    <div class="row text-center mb-3">
                                        <div class="col-md-4">
                                            <div class="border rounded bg-light py-2 px-2">
                                                <small class="text-muted">Total Amount</small>
                                                <div class="fw-bold text-primary fs-5">BDT {{ number_format($billDetails->sum('total_amount')) ?? 0.00 }}/-</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="border rounded bg-light py-2 px-2">
                                                <small class="text-muted">Total Paid</small>
                                                <div class="fw-bold text-success fs-5">BDT {{ number_format($billDetails->sum('paid_amount')) ?? 0.00 }}/-</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="border rounded bg-light py-2 px-2">
                                                <small class="text-muted">Total Due</small>
                                                <div class="fw-bold text-danger fs-5">BDT {{ number_format($billDetails->sum('total_amount') - $billDetails->sum('paid_amount')) ?? 0.00 }}/-</div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="payment-details">
                                        <table class="table table-bordered border-dark text-center text-dark" width="100%">
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
                                                        <h6 class="fw-bold">Invoice</h6>
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
                                                @if (count($billDetails) > 0)
                                                    @php
                                                        $total_sum = '0';
                                                    @endphp
                                                    @foreach ($billDetails as $key => $details)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ date('d-m-Y', strtotime($details->created_at)) }}</td>
                                                            <td>
                                                                @if ($details->invoice_id != null && $details->status == '0')
                                                                    <a href="{{ route('invoice.print.local', $details->invoice_id) }}">
                                                                        #{{ $details['account_details']['invoice_no_gen'] }}
                                                                    </a>
                                                                @elseif ($details->invoice_id == null && $details->status == '1')
                                                                    Opening Balance
                                                                @elseif ($details->invoice_id == null && $details->status == '0')
                                                                    Cash Payment
                                                                @elseif ($details->invoice_id != null && $details->status == '1')
                                                                    #{{ $details->invoice_id }}
                                                                @endif
                                                            </td>
                                                            <td>{{ number_format($details->total_amount) }}/-</td>
                                                            <td>{{ number_format($details->due_amount) }}/-</td>
                                                            <td>{{ number_format($details->paid_amount) }}/-</td>
                                                            <td>{{ number_format($details->balance) }}/-
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="7">
                                                            <h4 class="m-0">No data found</h4>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
