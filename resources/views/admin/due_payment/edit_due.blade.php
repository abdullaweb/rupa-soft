@extends('admin.admin_master')
@section('admin')
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" class="custom-validation" action="{{ route('update.due.payment') }}" novalidate="">
                                @csrf

                                <input type="hidden" name="id" value="{{ $due_payment_info->id }}">
                                <div class="mb-3 mt-3">
                                    <Label>Code</Label>
                                    <input type="text" class="form-control" name="code" id="code" readonly value="{{ $due_payment_info->code }}">
                                </div>
                                <div class="mb-3 mt-3">
                                    <label for="">Select Company</label>
                                    <select name="company_id" id="company_id" class="form-control select2" required="">
                                        <option value="" selected disabled>Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}" {{ $company->id == $due_payment_info->customer_id ? 'selected' : '' }}>{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 mt-3">
                                    <label for="">Total Due Amount </label>
                                    <input type="text" id="due_amount" name="due_amount" class="form-control" required="" placeholder="Total Due Amount" data-parsley-required-message="Company Total Bill" value="{{ $due_amount + $due_payment_info->paid_amount }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <div>
                                        <label for="">Due Payment</label>
                                        <input type="text" class="form-control" name="paid_amount" id="paid_amount" placeholder="Enter a due payment amount" required="" placeholder="" data-parsley-required-message="Pay due Amount is required" autocomplete="off" value="{{ $due_payment_info->paid_amount }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div>
                                        <label for="">Voucher </label>
                                        <input type="text" class="form-control" name="voucher" id="voucher" placeholder="Enter a voucher" value="{{ $due_payment_info->voucher }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div>
                                        <label for="">Date</label>
                                        <input type="text" class="form-control date_picker" name="date" id="date" placeholder="Enter Payment Date" autocomplete="off" value="{{ $due_payment_info->date }}">
                                    </div>
                                </div>

                                @if ($companyInfo->status == '1')
                                <div class="mb-3">
                                    <div>
                                        <label for="">Invoice</label>
                                        <select name="invoice[]" id="invoices" multiple class="form-select form-control select2">
                                            @foreach ($invoices as $invoice)
                                                <option value="{{ $invoice->id }}" selected>{{ $invoice->invoice_no_gen }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
                                

                                <div class="mb-3" id="paid_source_col">
                                    <label for="">Paid Status</label>
                                    <select class="form-control" name="paid_status" id="paid_status">
                                        <option value="" selected disabled>Select Payment Status</option>
                                        <option value="cash" {{ $due_payment_info->paid_status == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="check" {{ $due_payment_info->paid_status == 'check' ? 'selected' : '' }}>Check</option>
                                        <option value="online-banking" {{ $due_payment_info->paid_status == 'online-banking' ? 'selected' : '' }}>Online Banking</option>
                                    </select>
                                    <input type="text" placeholder="Check OR Online Banking Name" class="form-control" name="check_or_banking" id="check_or_banking" style="display:none;">
                                </div>

                                <div class="mb-0">
                                    <div>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light me-1">
                                            Update
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

    <script>
        $(document).ready(function() {
            $('#company_id').on('change', function() {
                let company_id = $(this).val();
                $.ajax({
                    url: "{{ route('get.due.amount') }}",
                    method: 'POST',
                    data: {
                        company_id: company_id,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        $('#due_amount').val(response.due_amount);

                        $('#invoice').empty();
                        $('#invoice').append('<option value="" selected disabled>Select Invoice</option>');
                        $.each(response.invoice, function(index, value) {
                            $('#invoice').append('<option value="' + value.id + '">' + value.invoice_no_gen + '</option>');
                        });
                    }
                });
            });
        });
    </script>
@endsection
