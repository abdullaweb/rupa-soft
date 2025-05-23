@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="company-details mt-5">
                                    <h5><strong>Supplier Name : {{ $supplierInfo->name }}</strong></h5>
                                    <p class="mb-0">Address : {{ $supplierInfo->address }}</p>
                                    <p class="mb-0">Phone : {{ $supplierInfo->phone }}</p>
                                    <p class="mb-0">E-mail : {{ $supplierInfo->email }}</p>
                                </div>
                            </div>

                            <div class="col-12">
                                <h4 class="text-center">Account Details</h4>
                                <div class="payment-details">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
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
                                                    <h6 class="fw-bold">Purchase No</h6>
                                                </th>
                                                <th>
                                                    <h6 class="fw-bold">Voucher No</h6>
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
                                            @php
                                                $total_sum = '0';
                                            @endphp
                                            @foreach ($billDetails as $key => $details)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ date('d-m-Y', strtotime($details->date)) }}</td>

                                                    <td>

                                                        {!! $details->supplier_account_details ? '<a href="' . route('purchase.details', $details->purchase_id) . '" target="_blank">' . e($details->supplier_account_details->purchase_no) . '</a>' : ($details->status == 'opening' ? 'Opening Balance' : 'N/A') !!}
                                                    </td>

                                                    <td>
                                                        {{ $details->voucher ?? 'N/A' }}
                                                    </td>

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
                </div>
            </div>
        </div>
    </div>
@endsection
