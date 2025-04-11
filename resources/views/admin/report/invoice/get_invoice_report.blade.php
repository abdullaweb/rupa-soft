@extends('admin.admin_master')
@section('admin')
    <style>
        .table>:not(caption)>*>* {
            padding: 0 !important;
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Invoice Report</h4>
                        <div class="d-print-none">
                            <div class="float-end">
                                <a class="btn btn-info" href="{{ url()->previous() }}"> <i class="fa fa-arrow-left">
                                </i> Back</a>
                                <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light"><i class="fa fa-print"></i> Print</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="card">
                        {{-- <div class="card-header py-3 bg-white">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="card-title">Invoice Report from {{ $start_date }} to {{ $end_date }}</h4>
                                </div>
                            </div>
                        </div> --}}
                        <div class="card-body">
                            <div class="col-12 py-3">
                                <h4 class="text-center mb-5">Invoice Report from {{ $start_date }} to {{ $end_date }}</h4>
                                <div class="payment-details">
                                    <table class="table text-center text-dark" id="" width="100%">
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($invoiceAll) > 0)
                                                @php
                                                    $total_sum = '0';
                                                @endphp
                                                @foreach ($invoiceAll as $key => $invoice)
                                                @php
                                                    $payment = App\Models\Payment::where('invoice_id', $invoice->id)->first();
                                                @endphp
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ date('d-m-Y', strtotime($invoice->date)) }}</td>
                                                        <td>
                                                            #{{ $invoice->invoice_no_gen }}
                                                        </td>
                                                        <td>{{ number_format($payment->total_amount) }}/-</td>
                                                        <td>{{ number_format($payment->due_amount) }}/-</td>
                                                        <td>
                                                            {{ ($payment->paid_amount) }}/-
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
