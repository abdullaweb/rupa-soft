<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Advanced;
use App\Models\Bonus;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\PaySalary;
use App\Models\PaySalaryDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function PaySalary()
    {
        $employees = Employee::latest()->get();
        return view('admin.salary.pay_salary.pay_salary', compact('employees'));
    }

    public function PaySalaryNow($id)
    {
        $employee = Employee::findOrFail($id);
        $voucher_no = $this->UniqueNumber();
        $advanced = Advanced::where('employee_id', $id)->get();
        if ($advanced->isEmpty()) {
            $advanced_amount = 0;
        } else {
            $advanced_amount = $advanced->sum('advance_amount');
        }

        $overtime = Overtime::where('employee_id', $employee->id)
            ->where('month', date('F', strtotime('-1 month')))
            ->where('year', date('Y'))
            ->first();
        $bonus = Bonus::where('employee_id', $employee->id)
            ->where('month', date('F', strtotime('-1 month')))
            ->where('year', date('Y'))
            ->first();

        $pay_salary = PaySalaryDetail::where('employee_id', $employee->id)
            ->where('paid_month', date('F', strtotime('-1 month')))
            ->where('paid_year', date('Y'))
            ->where('paid_type', 'Salary')
            ->get();

            // $currentMonth = date('n'); 
            // $currentYear = date('Y');
            // $monthlySalary = $employee->salary; 
            
            // $salaryPayments = PaySalaryDetail::select('paid_month', DB::raw('SUM(paid_amount) as total_paid'))
            //     ->where('employee_id', $employee->id)
            //     ->where('paid_year', $currentYear)
            //     ->where('paid_type', 'Salary')
            //     ->groupBy('paid_month')
            //     ->get()
            //     ->keyBy('paid_month');
            
            // $totalExpected = $monthlySalary * $currentMonth;
            // $totalPaid = $salaryPayments->sum('total_paid');
            // $totalDue = $totalExpected - $totalPaid;
            
            // $isCurrentMonthMissing = !$salaryPayments->has($currentMonth);
            
            // if ($totalDue > 0) {
            //     $due_salary = $totalDue - $monthlySalary;
            // } else {
            //     $due_salary = 0;
            // }

            // $unpaid_months = [];
            // for ($i = 1; $i <= $currentMonth; $i++) {
            //     if (!$salaryPayments->has($i)) {
            //         $unpaid_months[] = date('F', mktime(0, 0, 0, $i, 1));
            //     }
            // }
            // $unpaid_months = implode(', ', $unpaid_months);
            // $unpaid_months = $unpaid_months ? $unpaid_months : 'No Unpaid Month';
            // dd($unpaid_months);

        // check total salary
        if ($pay_salary->isEmpty()) {
            if ($employee != null && $overtime != null && $bonus != null) {
                $total_salary = $employee->salary + $overtime->ot_amount +  $bonus->bonus_amount;
            } elseif ($employee != null && $overtime != null && $bonus == null) {
                $total_salary = $employee->salary + $overtime->ot_amount;
            } elseif ($employee != null && $overtime == null && $bonus != null) {
                $total_salary = $employee->salary + $bonus->bonus_amount;
            } elseif ($employee != null && $overtime == null && $bonus == null) {
                $total_salary = $employee->salary;
            }
        } else {
            if ($employee != null && $overtime != null && $bonus != null) {
                $total_salary = $employee->salary + $overtime->ot_amount +  $bonus->bonus_amount - $pay_salary->sum('paid_amount');
            } elseif ($employee != null && $overtime != null && $bonus == null) {
                $total_salary = $employee->salary + $overtime->ot_amount - $pay_salary->sum('paid_amount');
            } elseif ($employee != null && $overtime == null && $bonus != null) {
                $total_salary = $employee->salary + $bonus->bonus_amount - $pay_salary->sum('paid_amount');
            } elseif ($employee != null && $overtime == null && $bonus == null) {
                $total_salary = $employee->salary - $pay_salary->sum('paid_amount');
            }
        }



        return view('admin.salary.pay_salary.pay_salary_add', compact('employee', 'total_salary', 'advanced_amount','voucher_no'));
    }

    public function StorePaySalary(Request $request)
    {
        $employee_id = $request->employee_id;
        $payment_type = $request->payment_type;
        $employee = Employee::findOrFail($employee_id);

        $pay_salary_table = PaySalary::where('employee_id', $employee_id)
            ->where('paid_month', date('F', strtotime('-1 month')))
            ->where('paid_year', date('Y'))
            ->get();


        if ($payment_type == 'advanced') {
            if ($request->paid_amount > $request->advanced_amount) {
                $notification = array(
                    'message' => 'Paid Amount must be less than or equal to Advance Salary',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            } else {
                $paid_salary_details = new PaySalaryDetail();
                $paid_salary_details->employee_id = $employee_id;
                $paid_salary_details->paid_amount = $request->paid_amount;
                $paid_salary_details->paid_month = date('F', strtotime('-1 month'));
                $paid_salary_details->paid_year = date('Y');
                $paid_salary_details->voucher_no = $request->voucher_no;
                $paid_salary_details->paid_date = $request->date;
                $paid_salary_details->paid_type = 'Advanced';
                $paid_salary_details->save();


                if ($pay_salary_table->isEmpty()) {
                    $paid_salary = new PaySalary();
                    $paid_salary->employee_id = $employee_id;
                    $paid_salary->paid_month = date('F', strtotime('-1 month'));
                    $paid_salary->paid_year = date('Y');
                    $paid_salary->save();
                }

                // update advacnced amount
                $new_advacned = Advanced::where('employee_id', $employee->id)->first()['advance_amount'] - $request->paid_amount;
                Advanced::findOrFail($employee->id)->update([
                    'advance_amount' => $new_advacned,
                ]);

                $notification = array(
                    'message' => 'Advanced Amount Added Successfully!',
                    'alert-type' => 'success'
                );
            }
        }


        if ($payment_type == 'salary') {
            if($due_salary > 0){
                $notification = array(
                    'message' => 'Salary Already Paid for this month',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }
            if ($request->paid_amount > $request->total_salary) {
                $notification = array(
                    'message' => 'Paid Amount must be less than to total Salary',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            } else {
                $paid_salary_details = new PaySalaryDetail();
                $paid_salary_details->employee_id = $employee_id;
                $paid_salary_details->paid_amount = $request->paid_amount;
                $paid_salary_details->paid_month = date('F', strtotime('-1 month'));
                $paid_salary_details->paid_year = date('Y');
                $paid_salary_details->voucher_no = $request->voucher_no;
                $paid_salary_details->paid_date = $request->date;
                $paid_salary_details->paid_type = 'Salary';
                $paid_salary_details->save();


                if ($pay_salary_table->isEmpty()) {
                    $paid_salary = new PaySalary();
                    $paid_salary->employee_id = $employee_id;
                    $paid_salary->paid_month = date('F', strtotime('-1 month'));
                    $paid_salary->paid_year = date('Y');
                    $paid_salary->save();
                }

                $notification = array(
                    'message' => 'Paid Amount Added Successfully!',
                    'alert-type' => 'success'
                );
            }
        }

        return redirect()->route('pay.salary')->with($notification);
    }


    // add salary by employee
    public function AddSalary()
    {
        $employees = Employee::OrderBy('name','asc')->get();
        $voucher_no = $this->UniqueNumber();
        return view('admin.salary.pay_salary.add_salary', compact('employees','voucher_no'));
    }

    // all overtime method
    public function AllOvertime()
    {
        $allOvertime = Overtime::all();
        return view('admin.salary.overtime.all_overtime', compact('allOvertime'));
    }

    public function AddOvertime()
    {
        $employees = Employee::orderBy('name', 'desc')->get();
        return view('admin.salary.overtime.add_overtime', compact('employees'));
    }
    public function StoreOvertime(Request $request)
    {
        // dd($request->all());
        $date = Carbon::createFromFormat('m/d/Y', date('m/d/Y', strtotime($request->month_date)));
        $monthName = $date->format('F');
        $year = $date->format('Y');
        $employeeSalary = Employee::where('id', $request->employee_id)->first()['salary'];

        $ot_hour_amount =  ($employeeSalary / 30) / 9;
        $ot_amount = round($ot_hour_amount * $request->ot_hour);
        $overtime = new Overtime();
        $overtime->employee_id = $request->employee_id;
        $overtime->ot_hour = $request->ot_hour;
        $overtime->ot_amount = $ot_amount;
        $overtime->month = $monthName;
        $overtime->year = $year;
        $overtime->created_at = Carbon::now();
        $overtime->save();


        $notification = array(
            'message' => 'Overtime Added Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.overtime')->with($notification);
    }

    public function UpdateOvertime(Request $request)
    {


        $overtime_id = $request->id;

        $date = Carbon::createFromFormat('m/d/Y', date('m/d/Y', strtotime($request->date)));
        $monthName = $date->format('F');
        $year = $date->format('Y');
        $employeeSalary = Employee::where('id', $request->employee_id)->first()['salary'];

        $ot_hour_amount =  ($employeeSalary / 30) / 9;
        $ot_amount = round($ot_hour_amount * $request->ot_hour);


        Overtime::findOrFail($overtime_id)->update([
            'employee_id' => $request->employee_id,
            'ot_hour' => $request->ot_hour,
            'ot_amount' => $ot_amount,
            'month' => $monthName,
            'year' => $year,
        ]);

        $notification = array(
            'message' => 'Overtime Updated Successfully',
            'alert-type' => 'success',
        );
        return redirect()->route('all.overtime')->with($notification);
    }
    public function EditOvertime($id)
    {
        $overtimeInfo = Overtime::findOrFail($id);
        $employees = Employee::orderBy('name', 'desc')->get();
        return view('admin.salary.overtime.edit_overtime', compact('overtimeInfo', 'employees'));
    }

    public function DeleteOvertime($id)
    {
        Overtime::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Overtime Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.overtime')->with($notification);
    }


    // all bonud method
    public function AllBonus()
    {
        $allBonus = Bonus::all();
        return view('admin.salary.bonus.all_bonus', compact('allBonus'));
    }

    public function AddBonus()
    {
        $employees = Employee::orderBy('name', 'desc')->get();
        return view('admin.salary.bonus.add_bonus', compact('employees'));
    }
    public function StoreBonus(Request $request)
    {
        $date = Carbon::createFromFormat('m/d/Y', date('m/d/Y', strtotime($request->date)));
        $monthName = $date->format('F');
        $year = $date->format('Y');

        $bonus = new Bonus();
        $bonus->employee_id = $request->employee_id;
        $bonus->bonus_amount = $request->bonus_amount;
        $bonus->month = $monthName;
        $bonus->year = $year;
        $bonus->created_at = Carbon::now();
        $bonus->save();


        $notification = array(
            'message' => 'Bonus Added Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.bonus')->with($notification);
    }

    public function UpdateBonus(Request $request)
    {
        $bonus_id = $request->id;

        $date = Carbon::createFromFormat('m/d/Y', date('m/d/Y', strtotime($request->date)));
        $monthName = $date->format('F');
        $year = $date->format('Y');
        $employeeSalary = Employee::where('id', $request->employee_id)->first()['salary'];



        Bonus::findOrFail($bonus_id)->update([
            'employee_id' => $request->employee_id,
            'bonus_amount' => $request->bonus_amount,
            'month' => $monthName,
            'year' => $year,
        ]);

        $notification = array(
            'message' => 'Bonus Updated Successfully',
            'alert-type' => 'success',
        );
        return redirect()->route('all.bonus')->with($notification);
    }
    public function EditBonus($id)
    {
        $bonusInfo = Bonus::findOrFail($id);
        $employees = Employee::orderBy('name', 'desc')->get();
        return view('admin.salary.bonus.edit_bonus', compact('bonusInfo', 'employees'));
    }

    public function DeleteBonus($id)
    {
        Bonus::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Bonus Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.bonus')->with($notification);
    }



    // payment details method
    public function EmployeePaymentDetails($id)
    {
        $employee = Employee::findOrFail($id);
        $payment_salary = PaySalary::where('employee_id', $id)
            ->get();
        return view('admin.salary.pay_salary.payment_details', compact('employee', 'payment_salary'));
    }
    
     // vourcher number
    public function UniqueNumber()
    {
        $vourcher = PaySalaryDetail::latest()->first();
        // dd($vourcher->voucher_no);
        if ($vourcher) {
            $name = $vourcher->voucher_no;
            $number = explode('_', $name);
            $vourcher_no = 'VO_' . str_pad((int)$number[1] + 1, 6, "0", STR_PAD_LEFT);
        } else {
            $vourcher_no = 'VO_000001';
        }
        return $vourcher_no;
    }
}
