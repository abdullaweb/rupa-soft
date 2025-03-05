@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Dashboard</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Rupa</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-truncate font-size-14 mb-2">Total Sales</p>
                                    <h4 class="mb-2">{{ number_format(round($payment->sum('total_amount')), 2) }}/-</h4>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-light text-primary rounded-3">
                                        <i class="mdi mdi-currency-usd font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end cardbody -->
                    </div><!-- end card -->
                </div><!-- end col -->
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-truncate font-size-14 mb-2">Total Purchase</p>
                                    <h4 class="mb-2">{{ number_format(round($purchase->sum('total_amount')), 2) }}/-</h4>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-light text-success rounded-3">
                                        <i class="mdi mdi-currency-usd font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end cardbody -->
                    </div><!-- end card -->
                </div><!-- end col -->
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-truncate font-size-14 mb-2">Total Expense</p>
                                    <h4 class="mb-2">{{ number_format(round($expense->sum('amount')), 2) }}/-</h4>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-light text-primary rounded-3">
                                        <i class="mdi mdi-currency-usd font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end cardbody -->
                    </div><!-- end card -->
                </div><!-- end col -->
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-truncate font-size-14 mb-2">Total Due</p>
                                    <h4 class="mb-2">{{ number_format(round($dueAmount), 2) }}/-</h4>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-light text-success rounded-3">
                                        <i class="mdi mdi-currency-usd font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end cardbody -->
                    </div><!-- end card -->
                </div><!-- end col -->
            </div><!-- end row -->

            {{-- Due Payment Approve List --}}
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title text-center mb-3">Pending Due List</h4>
                            <div class="table-responsive">
                                <table id="" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Customer Name</th>
                                            <th>Date</th>
                                            <th>Paid Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pending_due_payment as $key => $item)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    {{ $item->company->name ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $item->date }}
                                                </td>
                                                <td>
                                                    {{ $item->paid_amount }}
                                                </td>

                                                <td>
                                                    {{-- @if (Auth::user()->can('due.payment.edit')) --}}
                                                    <a style="margin-left: 5px;" href="{{ route('due.payment.approval.now', $item->id) }}" class="btn btn-info" id="approve">
                                                        Approve
                                                    </a>
                                                    {{-- @endif --}}

                                                    {{-- @if (Auth::user()->can('due.payment.delete')) --}}
                                                    <a style="margin-left: 5px;" href="{{ route('delete.due.payment', $item->id) }}" class="btn btn-danger" title="Delete" id="decline">
                                                        Decline
                                                    </a>
                                                    {{-- @endif --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!-- end card -->
                    <!-- end row -->
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title text-center mb-3">Pending Supplier Due List</h4>
                            <div class="table-responsive">
                                <table id="" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Supplier Name</th>
                                            <th>Date</th>
                                            <th>Paid Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($supplier_pending_due_payment as $key => $due_payment)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    {{ $due_payment->supplier->name ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $due_payment->date }}
                                                </td>
                                                <td>
                                                    {{ $due_payment->paid_amount }}
                                                </td>

                                                <td>
                                                    {{-- @if (Auth::user()->can('due.payment.edit')) --}}
                                                    <a style="margin-left: 5px;" href="{{ route('supplier.due.payment.approval.now', $due_payment->id) }}" class="btn btn-info" id="approve">
                                                        Approve
                                                    </a>
                                                    {{-- @endif --}}

                                                    {{-- @if (Auth::user()->can('due.payment.delete')) --}}
                                                    <a style="margin-left: 5px;" href="{{ route('delete.supplier.due.payment', $due_payment->id) }}" class="btn btn-danger" title="Delete" id="decline">
                                                        Decline
                                                    </a>
                                                    {{-- @endif --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!-- end card -->
                    <!-- end row -->
                </div>

            </div>
            <!-- End Page-content -->
        @endsection
