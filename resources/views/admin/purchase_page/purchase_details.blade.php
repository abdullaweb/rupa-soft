@php
    $total = $purchaseDetails->sum('total_amount');
@endphp
@extends('admin.admin_master')
@section('admin')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <!-- Begin Page Content -->
    <div class="page-content">
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="row">
                    <div class="col-12 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Purchase Details</h6>
                        <h6 class="m-0 font-weight-bold text-primary">
                            <a href="{{ url()->previous() }}">
                                <button class="btn btn-dark"> <i class="fas fa-arrow-right"></i>  Back</button>
                            </a>
                        </h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Sl</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Total</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($purchaseDetails as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="text-capitalize">
                                        {{ $item->product_name ?? 'N/A' }}
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $item->unit_price }}
                                    </td>
                                    <td>
                                        {{ $item->purchase->total_amount }}
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
@endsection
