@extends('admin.admin_master')
@section('admin')
    <!-- Begin Page Content -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-10 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-muted">Pay Salary</h4>
                            <form class="custom-validation" action="{{ route('pay.salary.store') }}" method="POST"
                                novalidate="" id="form" autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mt-3">
                                        <div class="mb-2">
                                            <select name="employee_id" id="employee_id" class="form-control select2" required
                                                data-parsley-required-message="Employee ID  is required">
                                                <option value="" selected disabled>Select Employee </option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <div class="mb-2">
                                            <label for="" class="mb-2 mt-2"><b>Total Due Salary</b></label>
                                            <input type="text" name="total_salary" id="total_salary" class="form-control"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <div class="mb-2">
                                            <label for="" class="mb-2 mt-2"><b>Total Advanced</b></label>
                                            <input type="text" name="advanced_amount" id="advanced_amount"
                                                class="form-control" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="mb-2">
                                            <label for="" class="mb-2 mt-2"><b>Paid Amount</b></label>
                                            <input type="text" id="paid_amount" name="paid_amount" class="form-control"
                                                required="" data-parsley-trigger="keyup"
                                                data-parsley-validation-threshold="0" placeholder="Paid Amount"
                                                data-parsley-type="number"
                                                data-parsley-type-message="Input must be positive number"
                                                data-parsley-required-message="Paid Amount is required">
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="mb-2">
                                            <label for="payment_type" class="mb-2 mt-2">Payment Type</label>
                                            <input type="radio" name="payment_type" value="salary" id="payment_type">
                                            Salary
                                            <input type="radio" name="payment_type" value="advanced"
                                                style="margin-left: 20px;"> Advanced
                                        </div>
                                    </div>


                                    <div class="col-md-12 mt-3">
                                        <div class="mb-3">
                                            <label for="" class="mb-2 mt-2"><b>Voucher No</b></label>
                                            <input type="text" id="voucher_no" name="voucher_no" class="form-control"
                                                required data-parsley-required-message="Voucher No is required"
                                                placeholder="Voucher No" readonly value="{{ $voucher_no }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <div class="mb-3">
                                            <label for="" class="mb-2 mt-2"><b>Paid Date</b></label>
                                            <input type="text" id="date" name="date"
                                                class="form-control date_picker" required
                                                data-parsley-required-message="Paid Date is required"
                                                placeholder="Paid Date">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light me-1">
                                        Pay Now
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {
            $('#form').parsley();


            // course dropdown
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#employee_id').on('change', function() {
                let employeeId = this.value;
                console.log(employeeId);
                $.ajax({
                    url: '{{ route('get.employee.salary') }}?employee_id=' + employeeId,
                    type: 'get',
                    success: function(res) {
                        $("#total_salary").val(res);
                        console.log('res', res);
                    }
                });
            }); //end couse dropdown
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#form').parsley();


            // course dropdown
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#employee_id').on('change', function() {
                let employeeId = this.value;
                console.log(employeeId);
                $.ajax({
                    url: '{{ route('get.employee.advance') }}?employee_id=' + employeeId,
                    type: 'get',
                    success: function(res) {
                        $("#advanced_amount").val(res);
                        console.log('res', res);
                    }
                });
            }); //end couse dropdown
        });
    </script>
@endsection
