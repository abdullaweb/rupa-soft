
@extends('admin.admin_master')
@section('admin')
    <!-- Begin Page Content -->
    <div class="page-content">
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="row">
                    <div class="col-12 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">All Stock Deduction</h6>
                        <h6 class="m-0 font-weight-bold text-primary">
                            <a href="{{ route('add.stock.deduction') }}">
                                <button class="btn btn-info">Add Stock Deduction</button>
                            </a>
                        </h6>
                    </div>
                </div>
                {{-- <div class="row">
                    <form method="POST" action="{{ route('get.purchase') }}">
                        @csrf
                        <div class="errorMsgContainer"></div>
                        <div class="input-group mb-3">
                            <input type="date" class="form-control ml-2 date_picker" name="start_date" id="start_date">
                            <input type="date" class="form-control ml-2 date_picker" name="end_date" id="end_date">
                            <button class="btn btn-primary submit_btn ml-2" type="submit">Search</button>
                        </div>
                    </form>
                </div> --}}

            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Deduction No</th>
                                <th>Date</th>
                                <th>Total Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Sl</th>
                                <th>Deduction No</th>
                                <th>Date</th>
                                <th>Total Quantity</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($stock_deductions as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="text-capitalize">
                                        {{ $item->deduction_no ?? 'N/A' }}
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $item->date ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $item->total_qty ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('edit.stock.deduction', $item->id) }}" class="btn btn-info mr-5">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('purchase.details', $item->id) }}" class="btn btn-info mr-5">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('delete.stock.deduction', $item->id) }}" class="btn btn-danger" id="delete" title="Purchase Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
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
