@extends('admin.admin_master')
@section('admin')
    <style>
        .table>:not(caption)>*>* {
            padding: 5px !important;
        }
        .table td{
            font-size: 14px;
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Daily Invoice Report</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li>
                                    <a href="javascript:window.print()"
                                    class="btn btn-success waves-effect waves-light"><i
                                        class="fa fa-print"></i></a>
                                </li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 mt-4">
                                            <h3 class="text-muted text-center mb-2">Invoice Report from
                                                {{ date('d-m-Y', strtotime($sdate)) }} to
                                                {{ date('d-m-Y', strtotime($edate)) }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div>
                                        <div class="">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <td><strong>Sl.</strong></td>
                                                            <td class=""><strong>Customer Name</strong></td>
                                                            <td class=""><strong>Invoice No</strong></td>
                                                            <td class=""><strong>Date</strong></td>
                                                            <td class=""><strong>Paid Amount</strong></td>
                                                            <td class=""><strong>Due Amount</strong></td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                                        @php
                                                            $paid_amount = '0';
                                                            $due_amount = '0';
                                                        @endphp
                                                        @foreach ($allData as $key => $item)
                                                        @php
                                                            $account_details = App\Models\AccountDetail::where('invoice_id', $item->id)->get();
                                                        @endphp
                                                            <tr>
                                                                <td>{{ $key + 1 }}</td>
                                                                <td class="">
                                                                    {{ $item['payment']['company']['name'] ?? 'N/A' }}</td>
                                                                <td class="">#{{ $item->invoice_no_gen }}</td>
                                                                <td class="">
                                                                    {{ date('Y-m-d', strtotime($item->date)) }}</td>
                                                                <td class="">
                                                                    {{ number_format($account_details->sum('paid_amount')) }}/-
                                                                </td>
                                                                <td class="">
                                                                    {{ number_format($account_details->sum('due_amount')) }}/-
                                                                </td>
                                                            </tr>

                                                            @php
                                                                $paid_amount += $account_details->sum('paid_amount');

                                                                $due_amount += $account_details->sum('due_amount');
                                                            @endphp
                                                        @endforeach

                                                        <tr>
                                                            <td class="thick-line"></td>
                                                            <td class="thick-line"></td>
                                                            <td class="thick-line"></td>
                                                            <td class="thick-line text-center">
                                                                <strong>Grand Total</strong>
                                                            </td>
                                                            <td class="thick-line">
                                                                <h4 class="m-0">{{ number_format($paid_amount) }}/-</h4>
                                                            </td>
                                                            <td class="thick-line">
                                                                <h4 class="m-0">{{ number_format($due_amount) }}/-</h4>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    {{-- <h4 class="text-muted text-center mb-4">Total Amount:
                                                        {{ number_format($total_amount) }}/-</h4> --}}
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div> <!-- end row -->


                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->

        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->
@endsection
