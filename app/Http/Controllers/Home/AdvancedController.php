<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Advanced;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdvancedController extends Controller
{
    public function AllAdvancedSalary()
    {
        $allAdvanced = Advanced::all();
        return view('admin.salary.advanced_salary.all_advanced', compact('allAdvanced'));
    }

    public function AddAdvancedSalary()
    {
        $employees = Employee::orderBy('name', 'desc')->get();
        return view('admin.salary.advanced_salary.add_advanced', compact('employees'));
    }

    public function EditAdvancedSalary($id)
    {
        $employees = Employee::all();
        $empInfo = Employee::findOrFail($id);
        $advancedSalary = Advanced::findOrFail($id);
        return view('admin.salary.advanced_salary.edit_advanced', compact('empInfo', 'advancedSalary', 'employees'));
    }

    public function StoreAdvancedSalary(Request $request)
    {
        // dd($request->all());

        $date = Carbon::createFromFormat('m/d/Y', date('m/d/Y', strtotime($request->date)));
        $monthName = $date->format('F');
        $year = $date->format('Y');

        $advanced = new Advanced();
        $advanced->advance_amount = $request->advanced_amount;
        $advanced->employee_id = $request->employee_id;
        $advanced->date = $request->date;
        $advanced->month = $monthName;
        $advanced->year = $year;
        $advanced->created_at = Carbon::now();
        $advanced->save();


        $notification = array(
            'message' => 'Advanced Salary Added Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.advanced.salary')->with($notification);
    }


    public function UpdateAdvancedSalary(Request $request)
    {



        $emp_id = $request->id;
        $advanced = Advanced::findOrFail($emp_id);


        $advanced_amount = $advanced->advance_amount + $request->advanced_amount;

        Advanced::findOrFail($emp_id)->update([
            'advance_amount' => $advanced_amount,
            'date' => $request->date,
        ]);

        $notification = array(
            'message' => 'Advanced Updated Successfully',
            'alert-type' => 'success',
        );
        return redirect()->route('all.advanced.salary')->with($notification);
    }

    public function DeleteAdvancedSalary($id)
    {
        Advanced::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Advanced Salary Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.advanced.salary')->with($notification);
    }
}
