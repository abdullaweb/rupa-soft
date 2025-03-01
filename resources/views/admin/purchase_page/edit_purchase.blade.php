@extends('admin.admin_master')
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
@section('admin')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Edit Purchase </h4><br><br>
                            <div class="row mt-3">
                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Date</label>
                                        <input type="date" class="form-control"
                                            name="date" id="date" value="{{ $purchaseInfo->date }}">
                                            
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Supplier Name</label>
                                        <select name="supplier_id" id="supplier_id" class="form-control form-select supplier_id">
                                            <option value="" selected disabled>Select Supplier Name</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ $supplier->id == $purchaseInfo->supplier_id ? 'selected' : '' }}>
                                                    {{ $supplier->name  }} 
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Product Name</label>
                                        <input type="text" class="form-control" id="product_name" name="product_name"
                                            placeholder="Product Name" required=""
                                            data-parsley-required-message="Product Name is required">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Category Name</label>
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
                                            Add More</i>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card-body">
                            <form method="POST" action="{{ route('update.purchase') }}" novalidate=""
                                id="purchaseForm" autocomplete="off" >
                                @csrf
                                <table class="table table-sm table-bordered" width="100%" style="border-color: #ddd;">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Sub Category</th>
                                            <th>Description</th>
                                            <th width="7%">Quantity</th>
                                            <th width="10%">Unit Price</th>
                                            <th width="15%">Total Price</th>
                                            <th width="7%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="addRow" class="addRow">
                                        @foreach ($purchaseDetails as $details)
                                        <tr class="delete_add_more_item" id="delete_add_more_item">
                                            <input type="hidden" name="date" value="{{ $purchaseInfo->date }}">
                                            <input type="hidden" name="purchase_no" value="{{ $purchaseInfo->purchase_no }}">
                                            <input type="hidden" class="form-control"
                                            name="id" id="id" value="{{ $purchaseInfo->id }}">
                                            <td hidden>
                                                <input type="hidden" name="supplier_id"
                                                    value="{{ $purchaseInfo->supplier_id }}">
                                            </td>
                                            <td hidden>
                                                <input type="hidden" name="category_id[]"
                                                    value="{{ $details->category_id }}">
                                                <span class="cat_name">{{ $details->category->name ?? '' }}</span>
                                            </td>
                                            <td width="15%">
                                                <input type="hidden" name="product_name[]"
                                                    value="{{ $details->product_name }}">
                                                <span>{{ $details->product_name }}</span>
                                            </td>
                                            <td>
                                                <input type="hidden" name="sub_cat_id[]"
                                                    value="{{ $details->sub_cat_id }}">
                                                <span
                                                    class="sub_cat_name">{{ $details->subCategory->name ?? '' }}</span>

                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                    placeholder="Write Description"
                                                    value="{{ $details->description }}" name="description[]">
                                            </td>
                                            <td width="4%">
                                                <input type="digit" class="form-control selling_qty text-right"
                                                    required="" data-parsley-required-message="Qty is required"
                                                    name="selling_qty[]" value="{{ $details->quantity }}">
                                            </td>
                                            <td width="10%">
                                                <input type="digit" class="form-control unit_price text-right"
                                                    required=""
                                                    data-parsley-required-message="Uiit Price is required"
                                                    name="unit_price[]" value="{{ $details->unit_price }}">
                                            </td>

                                            <td>
                                                <input type="digit" class="form-control selling_price text-right"
                                                    required=""
                                                    data-parsley-required-message="Selling Price is required"
                                                    name="selling_price[]" value="{{ $details->unit_price * $details->quantity }}"
                                                    readonly>
                                            </td>
                                            <td>
                                                <i class="btn btn-danger btn-sm fas fa-window-close"
                                                    id="removeEventMore"></i>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tbody>
                                        <tr>
                                            <td colspan="5">Sub Total</td>
                                            <td>
                                                <input type="number" name="sub_total" id="sub_total"
                                                    class="form-control sub_total" placeholder="Sub Total" value="{{ $purchaseInfo->total_amount }}"
                                                    readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5">Discount Amount</td>
                                            <td>
                                                <input type="number" name="discount_amount" id="discount_amount"
                                                    class="form-control discount_amount" placeholder="Discount Amount" value="{{ $purchaseInfo->discount_amount ?? 0 }}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5">Grand Total</td>
                                            <td>
                                                <input type="text" name="estimated_amount" id="estimated_amount"
                                                    class="form-control estimated_amount" style="background:#ddd;" readonly
                                                    value="{{ $purchaseInfo->total_amount - $purchaseInfo->discount_amount }}">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <select class="form-control" name="paid_status" id="paid_status" required="">
                                            <option value="" selected disabled>Select Paid Status</option>
                                            <option value="full_paid" {{ $purchaseInfo->paid_status == 'full_paid' ? 'selected' : '' }}>Full Paid</option>
                                            <option value="full_due" {{ $purchaseInfo->paid_status == 'full_due' ? 'selected' : '' }}>Full Due</option>
                                            <option value="partial_paid" >Partial Paid</option>
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
                                    <button type="submit" class="btn btn-info" id="storeButton">Purchase Update</button>
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
            <td hidden>
                <input type="hidden" name="test" value="@{{ test }}">
                <span class="test">@{{ test }}</span>
            </td>
            <td hidden>
                <input type="hidden" name="supplier_id" value="@{{ supplier_id }}">
                @{{ supplier_id }}
            </td>
            <td hidden>
                <input type="hidden" name="category_id[]" value="@{{ category_id }}">
                <span class="cat_name">@{{ category_name }}</span>
            </td>
            <td width="15%">
                <input type="hidden" name="product_name[]" value="@{{ product_name }}">
                <span>@{{ product_name }}</span>
            </td>
            <td>
                <input type="hidden" name="sub_cat_id[]" value="@{{ sub_cat_id }}">
                     <span class="sub_cat_name">@{{ sub_cat_name }}</span>
            </td>
            <td>
                <input type="text" class="form-control" placeholder="Write Description"  name="description[]">
            </td>
            
            <td width="4%">
                <input type="digit" class="form-control selling_qty text-right" required="" data-parsley-required-message="Qty is required"   name="selling_qty[]"
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

                let test = $("#test").val();
                let date = $("#date").val();
                let purchase_no = $("#purchase_no").val();
                let product_name = $("#product_name").val();
                let supplier_id = $("#supplier_id").val();
                let category_id = $("#category_id").val();
                let category_name = $("#category_id").find('option:selected').text();
                let sub_cat_id = $("#sub_cat_id").val();
                let sub_cat_name = $("#sub_cat_id").find('option:selected').text();

                alert('supplier_id', supplier_id);

                // console.log('cat_id', category_id);

                if (date == '') {
                    $.notify("Date is required", {
                        globalPosition: 'top right',
                        className: 'error'
                    });
                    return false;
                }
                if (supplier_id == '') {
                    $.notify("Company is required", {
                        globalPosition: 'top right',
                        className: 'error'
                    });
                    return false;
                }
                if (product_name == '') {
                    $.notify("Product Name is required", {
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
                    test: test,
                    date: date,
                    purchase_no: purchase_no,
                    supplier_id: supplier_id,
                    category_id: category_id,
                    category_name: category_name,
                    sub_cat_id: sub_cat_id,
                    sub_cat_name: sub_cat_name,
                    product_name: product_name,
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


                if (cat_name == 'Printing' || cat_name == 'Die Cutting') {
                    let convert_price = unit_price / 1000;
                    let total = Math.round(convert_price * selling_qty);
                    $(this).closest("tr").find('input.selling_price').val(total);
                    $("#discount_amount").trigger('keyup');
                }

                if (cat_name == 'Lamination') {
                    let side = $(this).closest("tr").find('option:selected').val();
                    let total = Math.round(size_length * size_width * unit_price * selling_qty * side);
                    $(this).closest("tr").find('input.selling_price').val(total);
                    $("#discount_amount").trigger('keyup');
                }

                if (cat_name == 'Foyle Print' || cat_name == 'Paper' || cat_name == 'Pasting' || cat_name ==
                    'Printing Item' || cat_name == 'Carton' || cat_name == 'Paper Cutting'  || cat_name == 'Garments Printing Item' || cat_name == 'Dise') {
                    let total = Math.round(unit_price * selling_qty);
                    $(this).closest("tr").find('input.selling_price').val(total);
                    $("#discount_amount").trigger('keyup');
                }

                if (cat_name == 'Foyle Print' && sub_cat_name == 'Foyle Dise') {
                    let total = Math.round(size_length * size_width * unit_price * selling_qty);
                    $(this).closest("tr").find('input.selling_price').val(total);
                    $("#discount_amount").trigger('keyup');
                }
                
                if(selling_qty == 'MM'){
                    let total = Math.round(unit_price * 1);
                    $(this).closest("tr").find('input.selling_price').val(total);
                    $("#discount_amount").trigger('keyup');
                }

            });

            $(document).on('keyup', '#discount_amount', function() {
                totalAmountPrice();
            });

            //  calculate sum amount for purchase

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
                $("#sub_total").val(subTotal);


                let discount_amount = parseFloat($('#discount_amount').val());
                if (!isNaN(discount_amount) && discount_amount.length != 0) {
                    sum -= parseFloat(discount_amount);
                }

                $("#estimated_amount").val(sum);
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
                    url: '{{ route('get.sub.cat') }}?category_id=' + categoryId,
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
        $('#purchaseForm').parsley();
    </script>
@endsection
