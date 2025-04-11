@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><strong>
                                                    <h5 class="text-muted">Supplier Name</h5>
                                                </strong></th>
                                            <th class="text-center"><strong>
                                                    <h5 class="text-muted">Supplier Mobile</h5>
                                                </strong></th>
                                            <th class="text-center"><strong>
                                                    <h5 class="text-muted">Supplier Address</h5>
                                                </strong></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">
                                                <strong>{{ $purchase->supplier->name }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ $purchase->supplier->phone }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ $purchase->supplier->address }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <form method="POST" class="custom-validation" action="{{ route('store.purchase.due.payment') }}"
                                novalidate="">
                                @csrf
                                <input type="hidden" name="id" value="{{ $purchase->id }}">
                                <div class="mb-3 mt-3">
                                    <label for="">Total Bill Amount </label>
                                    <input type="text" id="total_amount" name="total_amount" class="form-control"
                                        required="" placeholder="Total Amount"
                                        data-parsley-required-message="Company Total Bill"
                                        value="{{ $purchase->total_amount }}" readonly>
                                </div>
                                <div class="mb-3 mt-3">
                                    <label for="">Total Due Amount </label>
                                    <input type="text" id="due_amount" name="due_amount" class="form-control"
                                        required="" placeholder="Total Due Amount"
                                        data-parsley-required-message="Company Total Bill"
                                        value="{{ $supplier_payment->balance  }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <div>
                                        <label for="">Due Payment</label>
                                        <input type="text" class="form-control" name="paid_amount" id="paid_amount"
                                            placeholder="Enter a due payment amount" required="" placeholder=""
                                            data-parsley-required-message="Pay due Amount is required" autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div>
                                        <label for="">Date</label>
                                        <input type="text" class="form-control date_picker" name="date" id="date"
                                            placeholder="Enter Payment Date" autocomplete="off">
                                    </div>
                                </div>
                                <div class="mb-3" id="paid_source_col">
                                    <label for="">Paid Status</label>
                                    <select class="form-control" name="paid_status" id="paid_status">
                                        <option value="" selected disabled>Select Payment Status</option>
                                        <option value="cash">Cash</option>
                                        <option value="check">Check</option>
                                        <option value="online-banking">Online Banking</option>
                                    </select>
                                    <input type="text" placeholder="Check OR Online Banking Name" class="form-control"
                                        name="check_or_banking" id="check_or_banking" style="display:none;">
                                </div>

                                <div class="mb-0">
                                    <div>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light me-1">
                                            Due Payment
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            // paid source
            $('#paid_status').on('change', function() {
                let paidSource = $(this).val();
                console.log('paidSource', paidSource);
                if (paidSource == 'check' || paidSource == 'online-banking') {
                    $('#check_or_banking').show();
                } else {
                    $('#check_or_banking').hide();
                }
            });
        });
    </script>

@endsection
