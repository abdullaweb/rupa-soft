@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <!--breadcrumb-->
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Invoice All</h6>
            <h6 class="m-0 font-weight-bold text-primary">
                <a href="{{ route('invoice.add.local') }}">
                    <button class="btn btn-info"><i class="fa fa-plus-circle" aria-hidden="true"> Add Invoice </i></button>
                </a>
            </h6>
        </div>
        <!--end breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="datatable2" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Customer Name</th>
                                        <th>Invoice No</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Customer Name</th>
                                        <th>Invoice No</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($allData as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                {{ $item['payment']['company']['name'] ?? '' }}
                                            </td>
                                            <td>
                                                #{{ $item->invoice_no_gen }}
                                            </td>
                                            <td>
                                                {{ date('d-m-Y', strtotime($item->date)) }}
                                            </td>
                                            <td>
                                                 {{ number_format($item['payment']['total_amount']) }}/-
                                            </td>
                                            <td>
                                                <a title="Edit Invoice" style="margin-left: 5px;"
                                                    href="{{ route('invoice.edit.local', $item->id) }}" class="btn btn-info">
                                                    <i class="fas fa-edit    "></i>
                                                </a>
                                                <a title="Print Invoice" style="margin-left: 5px;"
                                                    href="{{ route('invoice.print.local', $item->id) }}" class="btn btn-success">
                                                    <i class="fa fa-print" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $allData->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script src="{{ asset('backend/assets/js/code.js') }}"></script>
@endsection
