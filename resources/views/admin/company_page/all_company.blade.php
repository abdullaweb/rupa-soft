@extends('admin.admin_master')
@section('admin')
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>

    <div class="page-content">
        <!--breadcrumb-->
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Company</h6>
            <h6 class="m-0 font-weight-bold text-primary">
                <a href="{{ route('add.company') }}">
                    <button class="btn btn-info">Add Company</button>
                </a>
            </h6>
        </div>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Company ID</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Due Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Company ID</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Due Amount</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach ($allData as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            {{ $item->name }}
                                        </td>
                                        <td>
                                            {{ $item->company_id }}
                                        </td>
                                        <td>
                                            {{ $item->email }}
                                        </td>
                                        <td>
                                            {{ $item->phone }}
                                        </td>
                                        <td>
                                            {{ $item->address }}
                                        </td>
                                        <td>
                                            @php
                                                $payment_due_amount = App\Models\Payment::where('company_id', $item->id)->sum('due_amount');

                                                $account_due_amount = App\Models\AccountDetail::where('company_id', $item->id)->latest()->first()->balance ?? 0;
                                                
                                            @endphp
                                            {{ number_format($account_due_amount) ?? $payment_due_amount }}
                                        </td>
                                        <td>
                                            @if (Auth::user()->can('company.edit'))
                                                <a style="margin-left: 5px;" href="{{ route('edit.company', $item->id) }}" class="btn btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            {{-- @if ($due_amount != 0)
                                                <a style="margin-left: 5px;" href="{{ route('corporate.due.payment', $item->id) }}" class="btn btn-secondary">
                                                    <i class="fas fa-edit"></i> Due Payment
                                                </a>
                                            @else
                                            @endif --}}

                                            @if (Auth::user()->can('corporate.bill.list'))
                                                <a style="margin-left: 5px;" href="{{ route('company.bill', $item->id) }}" class="btn btn-dark" title="Company Bill">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                    View Bill
                                                </a>
                                            @endif

                                            <a style="margin-left: 5px;" href="{{ route('corporate.bill.details', $item->id) }}" class="btn btn-secondary" title="Company Bill">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                Payment Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('backend/assets/js/code.js') }}"></script>
    @endsection
