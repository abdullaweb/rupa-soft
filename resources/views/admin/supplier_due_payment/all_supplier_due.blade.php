@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <!--breadcrumb-->
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Due</h6>
            <h6 class="m-0 font-weight-bold text-primary">
                <a href="{{ route('add.supplier.due.payment') }}">
                    <button class="btn btn-info">Add Supplier Due</button>
                </a>
            </h6>
        </div>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Supplier Name</th>
                                    <th>Date</th>
                                    <th>Paid Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Sl</th>
                                    <th>Supplier Name</th>
                                    <th>Date</th>
                                    <th>Paid Amount</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach ($dueAll as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            {{ $item->supplier->name }}
                                        </td>
                                        <td>
                                            {{ $item->date }}
                                        </td>
                                        <td>
                                            {{ $item->paid_amount }}
                                        </td>

                                        <td>
                                            @if (Auth::user()->can('edit.supplier.due'))
                                                <a style="margin-left: 5px;" href="{{ route('edit.supplier.due.payment', $item->id) }}" class="btn btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                    @if (Auth::user()->can('delete.supplier.due'))
                                                <a style="margin-left: 5px;" href="{{ route('delete.supplier.due.payment', $item->id) }}" class="btn btn-danger" title="Delete" id="delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            @endif

                    @if (Auth::user()->can('approval.supplier.due'))
                                            @if($item->status == 'pending')
                                                <a style="margin-left: 5px;" href="{{ route('supplier.due.payment.approval.now', $item->id) }}" class="btn btn-success" id="approve" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            @endif
                    @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('backend/assets/js/code.js') }}"></script>
    @endsection
