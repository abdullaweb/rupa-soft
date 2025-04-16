<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll;

class PayrollManagerController extends Controller
{
    public function Payroll(){
        $payroll = Payroll::get();
        return view('admin.payroll.payroll_list', compact('payroll'));
    }
}
