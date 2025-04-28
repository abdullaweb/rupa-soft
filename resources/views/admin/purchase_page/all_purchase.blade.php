@php
    $purchaseAmount = App\Models\Purchase::all();
    $total = $purchaseAmount->sum('total_amount');
@endphp
@extends('admin.admin_master')
@section('admin')
    <!-- Begin Page Content -->
    <div class="page-content">
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="row">
                    <div class="col-12 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">All Purchase</h6>
                        <h6 class="m-0 font-weight-bold text-primary">
                            <a href="{{ route('add.purchase') }}">
                                <button class="btn btn-info">Add Purchase</button>
                            </a>
                        </h6>
                    </div>
                </div>
                <div class="row">
                    <form method="POST" action="{{ route('get.purchase') }}">
                        @csrf
                        <div class="errorMsgContainer"></div>
                        <div class="input-group mb-3">
                            <input type="date" class="form-control ml-2 date_picker" name="start_date" id="start_date">
                            <input type="date" class="form-control ml-2 date_picker" name="end_date" id="end_date">
                            <button class="btn btn-primary submit_btn ml-2" type="submit">Search</button>
                        </div>
                    </form>
                </div>

            </div>
            <div class="card-body">
                <h4 class="text-muted text-center">Total Purchase Amount {{ $total }}</h4>
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Purchase No</th>
                                <th>Supplier Name</th>
                                <th>Date</th>
                                <th>Paid Amount</th>
                                <th>Due Amount</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Sl</th>
                                <th>Purchase No</th>
                                <th>Supplier Name</th>
                                <th>Date</th>
                                <th>Paid Amount</th>
                                <th>Due Amount</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($allPurchase as $key => $item)
                                <tr>
                                    @php
                                        $paidAmount = App\Models\SupplierPaymentDetail::where('purchase_id', $item->id)->sum('paid_amount');

                                        $dueAmount = App\Models\SupplierPaymentDetail::where('purchase_id', $item->id)->latest()->first();


                                        $transaction = App\Models\Transaction::where('purchase_id', $item->id)->where('type', 'purchase')->latest()->first();
                                    @endphp
                                    <td>{{ $key + 1 }}</td>
                                    <td class="text-capitalize">
                                        {{ $item->purchase_no ?? 'N/A' }}
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $item->supplier->name ?? 'N/A' }}
                                    </td>
                                    <td class="text-capitalize">
                                        {{ date('Y-m-d', strtotime($item->date)) }}
                                    </td>
                                    <td>
                                        {{ $paidAmount ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $dueAmount->due_amount ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $item->total_amount ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('edit.purchase', $item->id) }}" class="btn btn-info mr-5">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('purchase.details', $item->id) }}" class="btn btn-info mr-5">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('delete.purchase', $item->id) }}" class="btn btn-danger" id="delete" title="Purchase Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>

                                        @if (Auth::user()->can('purchase.approval.status'))
                                        @if($transaction)
                                        @if ($transaction->approval_status == 'pending' && $transaction->type == 'purchase')
                                            <a href="{{ route('purchase.approve', $transaction->purchase_id) }}" class="btn btn-info" id="approve">
                                                Approve
                                            </a>

                                            <a href="{{ route('delete.purchase', $transaction->purchase_id) }}" class="btn btn-danger" id="decline">
                                                Decline
                                            </a>
                                        @endif
                                        @endif
                                        @endif

                                        

                                        {{-- @if (optional($dueAmount)->due_amount != 0)
                                            <a href="{{ route('purchase.due.payment', $item->id) }}" class="btn btn-info" title="Purchase Due Payment">
                                                Due Payment
                                            </a>
                                        @endif

                                        <a href="{{ route('purchase.due.payment.history', $item->id) }}" class="btn btn-info" title="Purchase Due  Payment History">
                                            Payment History
                                        </a> --}}
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
