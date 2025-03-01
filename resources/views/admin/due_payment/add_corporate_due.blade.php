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
                            <form method="POST" class="custom-validation" action="{{ route('submit.due.payment') }}" novalidate="">
                                @csrf

                                <input type="hidden" name="status" value="corporate">
                                <div class="mb-3 mt-3">
                                    <select name="company_id" id="company_id" class="form-control select2" required="">
                                        <option value="" selected disabled>Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 mt-3">
                                    <label for="">Total Due Amount </label>
                                    <input type="text" id="due_amount" name="due_amount" class="form-control" required="" placeholder="Total Due Amount" data-parsley-required-message="Company Total Bill" value="0" readonly>
                                </div>

                                <div class="mb-3">
                                    <div>
                                        <label for="">Due Payment</label>
                                        <input type="text" class="form-control" name="paid_amount" id="paid_amount" placeholder="Enter a due payment amount" required="" placeholder="" data-parsley-required-message="Pay due Amount is required" autocomplete="off">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div>
                                        <label for="">Date</label>
                                        <input type="text" class="form-control date_picker" name="date" id="date" placeholder="Enter Payment Date" autocomplete="off">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div>
                                        <select class="form-select form-control" id="invoice" name="invoice[]" multiple>
                                            
                                        </select>
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
                                    <input type="text" placeholder="Check OR Online Banking Name" class="form-control" name="check_or_banking" id="check_or_banking" style="display:none;">
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
                        // $('#invoice').append('<option value="" selected disabled>Select Invoice</option>');
                        $.each(response.invoice, function(index, value) {
                            $('#invoice').append('<option value="' + value.id + '">' + value.invoice_no_gen + '</option>');
                        });
                    }
                });
            });
        });
    </script>
@endsection
