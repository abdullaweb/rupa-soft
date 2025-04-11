@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Stock Deduction </h4><br><br>
                            <div class="row mt-3">
                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Product Name</label>
                                        <select class="form-control select2" name="product_name" id="product_name">
                                            <option value="" selected disabled>Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product }}">{{ $product }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" id="quantity" required="">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="md-3">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">Date</label>
                                        <input type="text" class="form-control date_picker" name="date" id="date" required="" autocomplete="off">
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
                            <form method="POST" action="{{ route('update.deduct.stock') }}" novalidate=""
                                id="invoiceForm" autocomplete="off" >
                                @csrf
                                <table class="table table-sm table-bordered" width="100%" style="border-color: #ddd;">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th width="7%">Quantity</th>
                                            <th width="7%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="addRow" class="addRow">

                                    </tbody>

                                    <tr>
                                        <td class="text-right
                                        " colspan="1">Total Quantity</td>
                                        <td>
                                            <input type="number" class="form-control" name="total_quantity" id="total_quantity"
                                                readonly>
                                        </td>
                                    </tr>
                                </table>
                                {{-- <br>
                                <div class="form-group mt-4">
                                    <label for="total_quantity" class="col-sm-12 col-form-label">Total Quantity</label>
                                    <input type="number" class="form-control" name="total_quantity" id="total_quantity" readonly>
                                </div> --}}
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
            <td width="15%">
                <input type="hidden" name="product_name[]" value="@{{ product_name }}">
                <span>@{{ product_name }}</span>
            </td>
            <td width="10%">
                <input type="number" class="form-control quantity text-right" required="" name="quantity[]" value="@{{ quantity }}" autocomplete="off">
            </td>
            <td>
                <i class="btn btn-danger btn-sm fas fa-window-close" id="removeEventMore"></i>
            </td>
        </tr>
    </script>

    <script>
        $(document).ready(function() {

            function calculateTotalQuantity() {
                let totalQuantity = 0;
                $('.quantity').each(function() {
                    let quantity = $(this).val();
                    if ($.isNumeric(quantity)) {
                        totalQuantity += parseFloat(quantity);
                    }
                });
                $('#total_quantity').val(totalQuantity);
            }

            $(document).on("click", "#addEventMore", function() {
                let product_name = $("#product_name").val();
                let quantity = $("#quantity").val();
                let date = $("#date").val();

                if(date == '') {
                    $.notify("Date is required", {
                        globalPosition: 'top right',
                        className: 'error'
                    });
                    return false;
                }

                if (product_name == null) {
                    $.notify("Product Name is required", {
                        globalPosition: 'top right',
                        className: 'error'
                    });
                    return false;
                }
                if (quantity == '') {
                    $.notify("Quantity is required", {
                        globalPosition: 'top right',
                        className: 'error'
                    });
                    return false;
                }

                let source = $("#document-template").html();
                let template = Handlebars.compile(source);

                let data = {
                    product_name: product_name,
                    quantity: quantity,
                };
                let html = template(data);
                $("#addRow").append(html);
                calculateTotalQuantity();
            });

            $(document).on("click", "#removeEventMore", function() {
                $(this).closest(".delete_add_more_item").remove();
                calculateTotalQuantity();
            });

            $(document).on("input", ".quantity", function() {
                calculateTotalQuantity();
            });
        });
    </script>
@endsection
