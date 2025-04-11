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
                        <h4 class="mb-sm-0">Stock Report</h4>

                        <div class="d-print-none">
                            <div class="float-end">
                                <a class="btn btn-info" href="{{ url()->previous() }}"> <i class="fa fa-arrow-left">
                                    </i> Back</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12 py-3">
                                {{-- <h4 class="text-center mb-3">Account details from {{ $start_date }} to {{ $end_date }}</h4> --}}
                                <div class="payment-details">
                                    <table class="table text-center text-dark" id="datatable" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Product Name</th>
                                                <th>Purchased Qty</th>
                                                <th>Purchased Amount</th>
                                                <th>Deducted Qty</th>
                                                <th>Deducted Amount</th>
                                                <th>Current Stock</th>
                                                <th>Current Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sub_categories as $key => $product)
                                                <tr>
                                                    @php
                                                        $purchaseMeta = App\Models\PurchaseMeta::where('sub_cat_id', $product->id)->get();

                                                        $purchaseAmount = App\Models\PurchaseSummery::where('purchase_id', '!=', NULL)->where('purchase_sub_cat_id', $product->id)->sum('amount');

                                                        $deductedAmount = App\Models\PurchaseSummery::where('deduction_id', '!=', NULL)->where('purchase_sub_cat_id', $product->id)->sum('amount');
                                                    @endphp
                                                    <td> {{ $key = $key + 1 }} </td>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $purchaseMeta->sum('quantity') }}</td>
                                                    <td>{{ $purchaseAmount }}</td>
                                                    <td>{{ $purchaseMeta->sum('quantity') - $purchaseMeta->sum('current_qty')  }}</td>
                                                    <td>{{ $deductedAmount }}</td>
                                                    <td>{{ $purchaseMeta->sum('current_qty') }}</td>
                                                    <td>{{ $purchaseAmount - $deductedAmount }}</td>
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
