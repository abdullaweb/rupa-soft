@extends('admin.admin_master')
@section('admin')
    <!-- Begin Page Content -->
    <div class="page-content">
        <!--breadcrumb-->
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div class="m-0 font-weight-bold text-primary">
                <h5 class="m-0 font-weight-bold text-primary">
                    Salary of {{ date('F', strtotime('-1 month')) }}

                </h5>
            </div>
            <h6 class="m-0 font-weight-bold text-primary">
                <p class="m-0 font-weight-bold text-primary">Current Month : {{ date(' F, Y') }}</p>
            </h6>
        </div>
        <!--end breadcrumb-->
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Basic Salary</th>
                                <th>Allowance</th>
                                <th>Advance</th>
                                <th>Bonus</th>
                                <th>Deduction</th>
                                <th>OT Amount</th>
                                <th>Total Salary</th>
                                <th>Paid Salary</th>
                                <th>Due Salary</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Sl</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Basic Salary</th>
                                <th>Allowance</th>
                                <th>Advance</th>
                                <th>Bonus</th>
                                <th>Deduction</th>
                                <th>OT Amount</th>
                                <th>Total Salary</th>
                                <th>Paid Salary</th>
                                <th>Due Salary</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>

                            @foreach ($payroll as $key => $pay)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <img src="{{ asset($pay->employee->image) }}" class="rounded-circle" width="46"
                                            height="40" alt="" />
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $pay->employee->name }}
                                    </td>
                                    <td class="text-capitalize">
                                        <span class="badge bg-info">
                                            {{ $pay->month }}
                                        </span>
                                    </td>
                                    <td class="text-capitalize">
                                        <span class="badge bg-info">
                                            {{ $pay->year }}
                                        </span>
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $pay->basic_salary }}
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $pay->allowance }}
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $pay->advance }}
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $pay->deduction }}
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $pay->bonus }}
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $pay->overtime }}
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $pay->net_salary }}
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $pay->due_salary }}
                                    </td>
                                    
                                    <td style="display:flex">
                                        <a title="Pay Salary" href="{{ route('pay.salary.now', $employee->id) }}"
                                            class="btn btn-info text-light">
                                            Pay Now </a>
                                        <a title="Payment Details" style="margin-left: 5px;"
                                            href="{{ route('employee.payment.details', $employee->id) }}"
                                            class="btn btn-dark">
                                            Payment Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    <!-- End Page Content -->
@endsection
