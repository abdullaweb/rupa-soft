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
                            <h4 class="card-title">Edit Stock Deduction </h4><br><br>
                            <div class="row mt-3">
                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Date</label>
                                        <input type="date" class="form-control date_picker"
                                            name="date" id="date" required="" value="{{ $stock_deduction->date }}">
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
                                        <select name="sub_cat_id" id="sub_cat_id" class="form-control form-select">
                                            <option selected value="">Select Sub Category</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2" hidden>
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Current Quantity</label>
                                        <input type="text" class="form-control" name="stock_quantity" id="stock_quantity" required="" readonly value="0">
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
                            <form method="POST" action="{{ route('update.stock.deduction') }}" novalidate=""
                                id="invoiceForm" autocomplete="off" >
                                @csrf
                                <table class="table table-sm table-bordered" width="100%" style="border-color: #ddd;">
                                    <thead>
                                        <tr>
                                            <th>Category Name</th>
                                            <th>Sub Category</th>
                                            <th>Description</th>
                                            <th width="10%">Stock Qty</th>
                                            <th width="10%">Quantity</th>
                                            <th width="7%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="addRow" class="addRow">

                                        @foreach ($stock_deduction_details as $item)
                                            <tr class="delete_add" id="delete_add">
                                                <input type="hidden" name="id[]" value="{{ $item->id }}">
                                                <td hidden>
                                                    <input type="text" name="category_id[]" value="{{ $item->category_id }}">
                                                    <span class="cat_name">{{ $item->category->name }}</span>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="category_name[]"
                                                        value="{{ $item->category->name }}">
                                                    <span class="cat_name">{{ $item->category->name }}</span>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="sub_cat_id[]"
                                                        value="{{ $item->sub_cat_id }}">
                                                    <span class="sub_cat_name">{{ $item->sub_category->name }}</span>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" placeholder="Write Description"
                                                        name="description[]" value="{{ $item->description }}">
                                                </td>

                                                @php
                                                    $stock_qty = App\Models\PurchaseSummery::where('purchase_sub_cat_id', $item->sub_cat_id)->latest('id')->first();
                                                @endphp

                                                <td width="4%">
                                                    <input type="digit" class="form-control stock_qty text-right" required=""
                                                        data-parsley-required-message="Qty is required" name="stock_qty[]"
                                                        value="{{ $stock_qty->stock + $item->total_quantity }}" autocomplete="off" readonly>
                                                </td>

                                                <td width="4%">
                                                    <input type="digit" class="form-control selling_qty text-right" required=""
                                                        data-parsley-required-message="Qty is required" name="selling_qty[]"
                                                        value="{{ $item->total_quantity }}" autocomplete="off">
                                                </td>
                                                <td>
                                                    <i class="btn btn-danger btn-sm fas fa-window-close" id="removeEventMore"></i>
                                                </td>
                                            </tr>
                                 </tr>
                            @endforeach

                                    </tbody>
                                    <tbody>
                                        <tr>
                                            <td colspan="4">Total Qty</td>
                                            <td>
                                                <input type="text" name="estimated_qty" id="estimated_qty"
                                                    class="form-control estimated_qty" style="background:#ddd;" readonly
                                                    value="{{ $stock_deduction->total_qty }}">
                                            </td>
                                            <input type="text" name="stock_deduction_id" value="{{ $stock_deduction->id }}"
                                                hidden>
                                            <input type="text" name="deduction_no" value="{{ $stock_deduction->deduction_no }}"
                                                hidden>

                                            <input type="date" class="form-control"
                                                name="date" id="form_date" required="" value="{{ $stock_deduction->date }}" hidden>
                                            
                                        </tr>
                                    </tbody>
                                </table>
                                <br>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-info" id="storeButton">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div>

        </div>
    </div>

    <script>
        $('#sub_cat_id').on('change', function() {
            let sub_category = $(this).val();

            // alert(sub_category);
            $.ajax({
                url: "{{ route('get.stock.quantity') }}",
                type: "GET",
                data: {
                    sub_category: sub_category
                },
                success: function(data) {
                    $('#stock_quantity').val(data);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#date').on('change', function() {
                let date = $(this).val();
                $('#form_date').val(date);
            });
        });
    </script>

    <script id="document-template" type="text/x-handlebars-template">
        <tr class="delete_add_more_item" id="delete_add_more_item">
            <input type="hidden" name="date" value="@{{ date }}">
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
                <input type="digit" class="form-control stock_qty text-right" required="" data-parsley-required-message="Qty is required" name="stock_qty[]"
                    value="@{{ stock_qty }}" autocomplete="off" readonly>
            </td>
            
            <td width="4%">
                <input type="digit" class="form-control selling_qty text-right" required="" data-parsley-required-message="Qty is required" name="selling_qty[]"
                    value="" autocomplete="off">
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
                let category_id = $("#category_id").val();
                let category_name = $("#category_id").find('option:selected').text();
                let sub_cat_id = $("#sub_cat_id").val();
                let sub_cat_name = $("#sub_cat_id").find('option:selected').text();
                let stock_qty = $("#stock_quantity").val();

                if (date == '') {
                    $.notify("Date is required", {
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
                    category_id: category_id,
                    category_name: category_name,
                    sub_cat_id: sub_cat_id,
                    sub_cat_name: sub_cat_name,
                    stock_qty: stock_qty

                };
                let html = template(data);
                $("#addRow").append(html);
            });

            $(document).on("click", "#removeEventMore", function() {
                $(this).closest(".delete_add_more_item").remove();
                totalQuantity();
            });

        });
    </script>

    <script>
        $(document).on("keyup click", ".selling_qty", function () {
               totalQuantity();
         });

        function totalQuantity() {
            let totalQty = 0;
            $('.selling_qty').each(function () {
                let qty = $(this).val();
                if (!isNaN(qty) && qty.length != 0) {
                    totalQty += parseFloat(qty);
                }
            });
            $("#estimated_qty").val(totalQty);
        }

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
