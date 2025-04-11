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
                                <th>Salary</th>
                                <th>Advance</th>
                                <th>Bonus</th>
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
                                <th>Salary</th>
                                <th>Advance</th>
                                <th>Bonus</th>
                                <th>OT Amount</th>
                                <th>Total Salary</th>
                                <th>Paid Salary</th>
                                <th>Due Salary</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>

                            @foreach ($employees as $key => $employee)
                                @php
                                    $total_sum = '0';
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <img src="{{ asset($employee->image) }}" class="rounded-circle" width="46"
                                            height="40" alt="" />
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $employee->name }}
                                    </td>
                                    <td class="text-capitalize">
                                        <span class="badge bg-info">
                                            {{ date('F', strtotime('-1 month')) }}
                                        </span>
                                    </td>
                                    <td class="text-capitalize">
                                        {{ $employee->salary }}
                                    </td>
                                    <td class="text-capitalize">
                                        @php
                                            $advanced = App\Models\Advanced::where('employee_id', $employee->id)->get();
                                        @endphp
                                        @if ($advanced->isEmpty())
                                            No Advanced
                                        @else
                                            @foreach ($advanced as $advance)
                                                {{ number_format($advance->advance_amount) }}
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="text-capitalize">
                                        @php
                                            $allBonus = App\Models\Bonus::where('employee_id', $employee->id)
                                                ->where('month', date('F', strtotime('-1 month')))
                                                ->where('year', date('Y'))
                                                ->get();
                                        @endphp
                                        @if ($allBonus->isEmpty())
                                            No Bonus
                                        @else
                                            @foreach ($allBonus as $bonus)
                                                {{ number_format($bonus->bonus_amount) }}
                                                @php
                                                    $total_sum += $bonus->bonus_amount;
                                                @endphp
                                            @endforeach
                                        @endif
                                    </td>
                                    @php
                                        $overtimes = App\Models\Overtime::where('employee_id', $employee->id)
                                            ->where('month', date('F', strtotime('-1 month')))
                                            ->where('year', date('Y'))
                                            ->get();
                                    @endphp
                                    <td>
                                        @if ($overtimes->isEmpty())
                                            No Overtime
                                        @else
                                            @foreach ($overtimes as $item)
                                                {{ number_format($item->ot_amount) }}
                                                @php
                                                    $total_sum += $item->ot_amount;
                                                @endphp
                                            @endforeach
                                        @endif
                                    </td>

                                    @php
                                        $pay_salary = App\Models\PaySalaryDetail::where('employee_id', $employee->id)
                                            ->where('paid_month', date('F', strtotime('-1 month')))
                                            ->where('paid_year', date('Y'))
                                            ->where('paid_type', 'Salary')
                                            ->get();
                                    @endphp
                                    @if ($pay_salary->isEmpty())
                                        <td> {{ number_format($total_sum + $employee->salary) }}</td>
                                        <td>0</td>
                                        <td> {{ number_format($total_sum + $employee->salary) }}</td>
                                    @else
                                        <td> {{ number_format($total_sum + $employee->salary) }}
                                        </td>
                                        <td>{{ $pay_salary->sum('paid_amount') }}</td>
                                        <td> {{ number_format($total_sum + $employee->salary - $pay_salary->sum('paid_amount')) }}
                                        </td>
                                    @endif
                                    <td style="display:flex">
                                        {{-- @php
                                            $due = $total_sum + $employee->salary - $pay_salary->sum('paid_amount');
                                        @endphp
                                        @if ($due == '0')
                                            <spanm class="badge badge-success">Full Paid</spanm>
                                        @else
                                            <a title="Pay Salary" href="{{ route('pay.salary.now', $employee->id) }}"
                                                class="btn btn-info text-light">
                                                Pay Now </a>
                                        @endif --}}
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
