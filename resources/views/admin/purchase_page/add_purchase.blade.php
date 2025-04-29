@extends('admin.admin_master')
<style>
    /* .side {
        display: none;
    } */
</style>
@section('admin')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Add Purchase </h4><br><br>
                            <div class="row mt-3">
                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Date</label>
                                        <input type="date" class="form-control date_picker"
                                            name="date" id="date" required="">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Voucher</label>
                                        <input type="text" class="form-control"
                                            name="voucher" id="voucher" placeholder="Voucher No">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Supplier Name</label>
                                        <select name="supplier_id" id="supplier_id" class="form-control form-select supplier_id">
                                            <option value="" selected disabled>Select Supplier Name</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">
                                                    {{ $supplier->name  }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Category
                                            Name</label>
                                        <select name="category_id" id="category_id"
                                            class="form-control form-select select2">
                                            <option value="">Select Category Name</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Sub Category
                                            Name</label>
                                        <select name="sub_cat_id" id="sub_cat_id" class="form-control form-select select2">
                                            <option selected value="">Select Sub Category</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="md-3" id="default_addBtn">
                                        <label for="example-text-input" class="col-sm-12 col-form-label mt-4"></label>
                                        <i class="btn btn-secondary btn-rounded wave-effect wave-light fas fa-plus-circle"
                                            id="addEventMore">
                                            Add
                                            More</i>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card-body">
                            <form method="POST" action="{{ route('store.purchase') }}" novalidate=""
                                id="invoiceForm" autocomplete="off" class="custom-validation">
                                @csrf
                                <table class="table table-sm table-bordered" width="100%" style="border-color: #ddd;">
                                    <thead>
                                        <tr>
                                            <th>Category Name</th>
                                            <th>Sub Category</th>
                                            <th>Description</th>
                                            <th width="7%">Quantity</th>
                                            <th width="10%">Unit Price</th>
                                            <th width="15%">Total Price</th>
                                            <th width="7%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="addRow" class="addRow">

                                    </tbody>
                                    <tbody>
                                        <tr>
                                            <td colspan="5">Sub Total</td>
                                            <td>
                                                <input type="number" name="sub_total" id="sub_total"
                                                    class="form-control sub_total" placeholder="Sub Total" value="0"
                                                    readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5">Discount Amount</td>
                                            <td>
                                                <input type="number" name="discount_amount" id="discount_amount"
                                                    class="form-control discount_amount" placeholder="Discount Amount">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5">Grand Total</td>
                                            <td>
                                                <input type="text" name="estimated_amount" id="estimated_amount"
                                                    class="form-control estimated_amount" style="background:#ddd;" readonly
                                                    value="0">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <select class="form-control" name="paid_status" id="paid_status">
                                            <option value="" selected disabled>Select Paid Status</option>
                                            <option value="full_paid">Full Paid</option>
                                            <option value="full_due">Full Due</option>
                                            <option value="partial_paid">Partial Paid</option>
                                        </select>
                                        <input type="text" placeholder="Enter Paid Amount" class="form-control"
                                            name="paid_amount" id="paid_amount" style="display:none;">
                                    </div>
                                    <div class="col-md-3" id="paid_source_col" style="display: none;">
                                        <select class="form-control" name="paid_source" id="paid_source">
                                            <option value="" selected disabled>Select Payment Status</option>
                                            <option value="cash">Cash</option>
                                            <option value="check">Check</option>
                                            <option value="online-banking">Online Banking</option>
                                        </select>
                                        <input type="text" placeholder="Check OR Online Banking Name"
                                            class="form-control" name="check_or_banking" id="check_or_banking"
                                            style="display:none;">
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-info" id="storeButton">Purchase Store</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div>

        </div>
    </div>

    <script id="document-template" type="text/x-handlebars-template">
        <tr class="delete_add_more_item" id="delete_add_more_item">
            <input type="hidden" name="date" value="@{{ date }}">
            <input type="hidden" name="voucher" value="@{{ voucher }}">
            <td hidden>
                <input type="hidden" name="supplier_id" value="@{{ supplier_id }}">
                <span class="supplier_id">@{{ supplier_id }}</span>
            </td>
            <td hidden>
                <input type="text" name="category_id[]" value="@{{ category_id }}">
                <span class="cat_name">@{{ category_name }}</span>
            </td>
            <td>
                <input type="hidden" name="category_name[]" value="@{{ category_name }}">
                     <span class="cat_name">@{{ category_name }}</span>
            </td>
            <td>
                <input type="hidden" name="sub_cat_id[]" value="@{{ sub_cat_id }}">
                     <span class="sub_cat_name">@{{ sub_cat_name }}</span>
            </td>
            <td>
                <input type="text" class="form-control" placeholder="Write Description"  name="description[]">
            </td>
            
            <td width="4%">
                <input type="digit" class="form-control selling_qty text-right" required="" data-parsley-required-message="Qty is required" name="selling_qty[]"
                    value="" autocomplete="off">
            </td>
            <td width="10%">
                <input type="digit" class="form-control unit_price text-right" required="" data-parsley-required-message="Uiit Price is required"  name="unit_price[]" value="" autocomplete="off">
            </td>
            <td>
                <input type="digit"  class="form-control selling_price text-right" required="" data-parsley-required-message="Selling Price is required" name="selling_price[]"
                    value="0" readonly>
            </td>
            <td>
                <i class="btn btn-danger btn-sm fas fa-window-close" id="removeEventMore"></i>
            </td>
        </tr>
    </script>


    <!--  add more purchase   -->
    <script>
        $(document).ready(function() {
            $(document).on("click", "#addEventMore", function() {

                let date = $("#date").val();
                let voucher = $("#voucher").val();
                let supplier_id = $("#supplier_id").val();
                let category_id = $("#category_id").val();
                let category_name = $("#category_id").find('option:selected').text();
                let sub_cat_id = $("#sub_cat_id").val();
                let sub_cat_name = $("#sub_cat_id").find('option:selected').text();

                // console.log('cat_id', category_id);

                if (date == '') {
                    $.notify("Date is required", {
                        globalPosition: 'top right',
                        className: 'error'
                    });
                    return false;
                }
                if (supplier_id == '') {
                    $.notify("Supplier is required", {
                        globalPosition: 'top right',
                        className: 'error'
                    });
                    return false;
                }

                if (category_id == '') {
                    $.notify("Category is required", {
                        globalPosition: 'top right',
                        className: 'error'
                    });
                    return false;
                }
                if (sub_cat_id == '') {
                    $.notify("Sub Category is required", {
                        globalPosition: 'top right',
                        className: 'error'
                    });
                    return false;
                }

                let source = $("#document-template").html();
                let template = Handlebars.compile(source);

                let data = {
                    date: date,
                    voucher: voucher,
                    supplier_id: supplier_id,
                    category_id: category_id,
                    category_name: category_name,
                    sub_cat_id: sub_cat_id,
                    sub_cat_name: sub_cat_name,
                };
                let html = template(data);
                $("#addRow").append(html);
            });


            $(document).on("click", "#removeEventMore", function() {
                $(this).closest(".delete_add_more_item").remove();
                totalAmountPrice();
            });


            $(document).on("keyup click", ".unit_price,.selling_qty", function() {
                let category_name_check = $("#category_id").find('option:selected').text();
                let subcat_name_check = $("#sub_cat_id").find('option:selected').text();
                let cat_name = category_name_check.trim();
                let sub_cat_name = subcat_name_check.trim();


                let unit_price = $(this).closest("tr").find('input.unit_price').val();
                let selling_qty = $(this).closest("tr").find('input.selling_qty').val();

                let total = unit_price * selling_qty;

                $(this).closest("tr").find('input.selling_price').val(total.toFixed(2));
                $("#discount_amount").trigger('keyup');

            });

            $(document).on('keyup', '#discount_amount', function() {
                totalAmountPrice();
            });

            //  calculate sum amount for invoice

            function totalAmountPrice() {
                let sum = 0;
                let subTotal = 0;
                $('.selling_price').each(function() {
                    let value = $(this).val();
                    if (!isNaN(value) && value.length != 0) {
                        sum += parseFloat(value);
                        subTotal += parseFloat(value);
                    }
                });
                $("#sub_total").val(subTotal.toFixed(2));


                let discount_amount = parseFloat($('#discount_amount').val());
                if (!isNaN(discount_amount) && discount_amount.length != 0) {
                    sum -= parseFloat(discount_amount);
                }

                $("#estimated_amount").val(sum.toFixed(2));
            }

        });
    </script>



    {{--  dropdown menu select  --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#paid_status').on('change', function() {
                let paidStatus = $(this).val();
                console.log('paidSource', paidStatus);
                if (paidStatus) {
                    $('#paid_source_col').show();
                }

                if (paidStatus == 'partial_paid') {
                    $('#paid_amount').show();
                } else {
                    $('#paid_amount').hide();
                }
                
                if (paidStatus == 'full_due') {
                    $('#paid_source_col').hide();
                }
            });

            // paid source
            $('#paid_source').on('change', function() {
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

    {{-- New Company --}}

    <script>
        $(document).ready(function() {
            $('#company_id').on('change', function() {
                let company_id = $(this).val();
                if (company_id == 'new_company') {
                    $('#new_company').show();
                } else {
                    $('#new_company').hide();
                }
            });
        });
    </script>


    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $('#category_id').on('change', function() {
                let categoryId = $(this).val();
                let catName = $("#category_id").find('option:selected').text();
                let checkCat = catName.trim();

                console.log('cat', checkCat);

                if (checkCat == 'Printing') {
                    // $("#side").prop("disabled", true);
                    // document.getElementById('side').disabled = true;
                }

                $.ajax({
                    url: '{{ route('get.purchase.sub.cat') }}?category_id=' + categoryId,
                    type: 'GET',
                    success: function(data) {
                        let html = '<option value="">Select Sub Category </option>';
                        $.each(data, function(key, value) {
                            html += '<option value=" ' + value.id + ' "> ' +
                                value.name + '</option>';
                        });
                        $("#sub_cat_id").html(html);
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('backend/assets/libs/parsleyjs/parsley.min.js') }}"></script>

    <script>
        $('#invoiceForm').parsley();
    </script>
@endsection
