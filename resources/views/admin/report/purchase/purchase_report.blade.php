@extends('admin.admin_master')
@section('admin')
    {{-- <style>
        .table>:not(caption)>*>* {
            padding: 0 !important;
        }
    </style> --}}
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Purchase Report</h4>

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
                        <div class="card-header py-3 bg-white">
                            <div class="row">
                                {{-- <div class="col-6">
                                    <h4 class="card-title">Invoice Report</h4>
                                </div> --}}
                                <div class="col-12 mx-auto">
                                    <form action="{{ route('get.invoice.report') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-5">
                                                <input type="date" name="start_date" class="form-control date_picker" required>
                                            </div>
                                            <div class="col-5">
                                                <input type="date" id="date" name="end_date" class="form-control date_picker" required>
                                            </div>
                                            <div class="col-2">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-12 py-3">
                                {{-- <h4 class="text-center mb-3">Account details from {{ $start_date }} to {{ $end_date }}</h4> --}}
                                <div class="payment-details">
                                    <table class="table text-center text-dark" id="datatable" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Date</th>
                                                <th>Purchase No</th>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Amount</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($summaries as $key => $summary)
                                                <tr>
                                                    <td> {{ $key = $key + 1 }} </td>
                                                    <td>{{ $summary->purchase->date ?? $summary->deduction->date }}</td>
                                                    <td>{{ $summary->purchase->purchase_no ?? $summary->deduction->deduction_no }}</td>
                                                    <td>{{ $summary->subCategory->name }}</td>
                                                    <td>{{ $summary->quantity }}</td>
                                                    <td>{{ $summary->unit_price }}</td>
                                                    <td>{{ $summary->amount }}</td>
                                                    <td>{{ $summary->stock }}</td>
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
    </div>
@endsection
