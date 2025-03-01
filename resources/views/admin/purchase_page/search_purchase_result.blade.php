@extends('admin.admin_master')
@section('admin')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <!-- Begin Page Content -->
    <div class="page-content">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 text-muted ">Filtering Purchase Result</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">
                                <div class="d-print-none">
                                    <div class="float-end">
                                        <a href="javascript:window.print()"
                                            class="btn btn-success waves-effect waves-light"><i
                                                class="fa fa-print"></i> Print</a>
                                        <a href="{{ url()->previous() }}" class="btn btn-dark waves-effect waves-light">
                                            <i class="fas fa-arrow-right"></i> Back</a>
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <!-- DataTales Example -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive" id="printContent">
                    <h4 class="text-muted text-center">Purchase of
                        {{ date('d-m-Y', strtotime(Request::post('start_date'))) }} to
                        {{ date('d-m-Y', strtotime(Request::post('end_date'))) }}</h4>
                        @php
                            $total = $allPurchase->sum('total_amount');
                        @endphp
                        <h5 class="text-center text-muted mb-3">Total Purchase: <strong>BDT {{ $total }}</strong> </h5>
                    <table class="table table-bordered" id="" width="100%" cellspacing="0">
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
                        <tbody>
                            @foreach ($allPurchase as $key => $item)
                            <tr>
                                @php
                                    $paidAmount = App\Models\SupplierPaymentDetail::where('purchase_id', $item->id)->sum('paid_amount');

                                    $dueAmount = App\Models\SupplierPaymentDetail::where('purchase_id', $item->id)->latest()->first();
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

                                    @if ($dueAmount->due_amount != 0)
                                        <a href="{{ route('purchase.due.payment', $item->id) }}" class="btn btn-info" title="Purchase Due Payment">
                                            Due Payment
                                        </a>
                                    @endif

                                    <a href="{{ route('purchase.due.payment.history', $item->id) }}" class="btn btn-info" title="Purchase Due  Payment History">
                                        Payment History
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


    <script>
        function printDiv(printContent) {
            let printContents = document.getElementById(printContent).innerHTML;
            let originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
@endsection
