@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body py-5">
                        <form action="{{ route('update.supplier.opening.balance') }}" method="POST" class="custom-validation" novalidate="" autocomplete="off">
                            @csrf
                            <div class="row">
                                <input type="hidden" value="{{ $accountInfo->id }}" name="id">
                                <div class="col-12 mt-3 mb-2">
                                    <div class="d-flex">
                                        <h5 class="text-muted">Choose your opening balance type</h5>
                                        <div style="margin-left: 50px;">

                                            @if ($accountInfo->purchase_id != null && $accountInfo->status == '1')
                                                <input type="radio" id="purchasewise_balance" checked name="opening_type"
                                                    value="purchasewise_balance">
                                                <label for="no">Purchase Wise Balance</label>
                                                <input type="radio" id="opening_balance" name="opening_type"
                                                    value="opening_balance" required
                                                    data-parsley-required-message="Balance type is required" />
                                                <label for="yes">Opening Balance</label>
                                            @else
                                                <input type="radio" checked id="opening_balance" name="opening_type"
                                                    value="opening_balance" required
                                                    data-parsley-required-message="Balance type is required" />
                                                <label for="yes">Opening Balance</label>

                                                <input type="radio" id="purchasewise_balance" name="opening_type"
                                                    value="purchasewise_balance">
                                                <label for="no">Purchase Wise Balance</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12" id="purchase_no_field" style="display: none;">
                                    <div class="mb-2">
                                        <input type="text" name="purchase_no" id="purchase_no" placeholder="Enter Your Purchase No"
                                            class="form-control" value="{{ $accountInfo->purchase_id }}" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="mb-2">
                                        <select name="supplier_id" id="supplier_id" class="form-control" required
                                            data-parsley-required-message="Supplier Id is required" autocomplete="off">
                                            <option disabled selected>Select Supplier</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    {{ $supplier->id == $accountInfo->supplier_id ? 'selected' : '' }}>
                                                    {{ $supplier->name }} -
                                                    {{ $supplier->phone }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="mb-2">
                                        <input type="digit" id="total_amount" name="total_amount" class="form-control"
                                            required="" data-parsley-trigger="keyup"
                                            data-parsley-validation-threshold="0" placeholder="Total Amount"
                                            data-parsley-type="number"
                                            data-parsley-type-message="Input must be positive number"
                                            data-parsley-required-message="Total Amount is required" autocomplete="off"
                                            value="{{ $accountInfo->total_amount }}">
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="mb-2">
                                        <input type="digit" id="paid_amount" name="paid_amount" class="form-control"
                                            required="" data-parsley-trigger="keyup"
                                            data-parsley-validation-threshold="0" placeholder="Paid Amount"
                                            data-parsley-type="number"
                                            data-parsley-type-message="Input must be positive number"
                                            data-parsley-required-message="Paid Amount is required"
                                            value="{{ $accountInfo->paid_amount }}" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="mb-3">
                                        <input type="text" autocomplete="off" id="date" name="date" value="{{ $accountInfo->date }}"
                                            class="form-control date_picker" required
                                            data-parsley-required-message="Date is required" placeholder="Enter Your Date">
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <button type="submit" class="btn btn-info waves-effect waves-light me-1">
                                        Update Balance
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- js --}}
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    {{--  add more purchase   --}}
    <script>
        $(document).ready(function() {

            $(document).on("keyup", ".product_price,.product_qty", function() {
                let product_qty = $('input.product_qty').val();
                let product_price = $('input.product_price').val();
                let total = product_price * product_qty;
                $('input.total_amount').val(total);
            });


            $(window).on("load", function() {
                $typeStatus = $('input[type=radio][name="opening_type"]').val();
                // alert($typeStatus);
                if ($typeStatus === 'purchasewise_balance') {
                    $('#purchase_no_field').show();
                }
            });

            //opening balance
            $('input[type=radio][name="opening_type"]').change(function() {
                let status = $(this).val();
                // alert(status);
                if (status == 'purchasewise_balance') {
                    $('#purchase_no_field').show();
                } else {
                    $('#purchase_no_field').hide();
                }
            });
        });
    </script>
@endsection
