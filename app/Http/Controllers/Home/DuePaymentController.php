<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\DuePayment;
use App\Models\Company;
use App\Models\Payment;
use App\Models\AccountDetail;
use App\Models\Invoice;
use App\Models\DuePaymentDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



class DuePaymentController extends Controller
{
    public function AllDuePayment()
    {
        $dueAll = DuePayment::whereHas('company', function ($query) {
            $query->where('status', '0');
        })->latest()->get();
        return view('admin.due_payment.all_due', compact('dueAll'));
    }

    public function AllCorporateDuePayment()
    {
        $dueAll = DuePayment::whereHas('company', function ($query) {
            $query->where('status', '1');
        })->latest()->get();
        return view('admin.due_payment.all_corporate_due', compact('dueAll'));
    }


    public function AddDuePayment()
    {
        $companies = Company::where('status', '0')->get();
        $code = $this->UniqueNumberForDuePayment();
        return view('admin.due_payment.add_due', compact('companies', 'code'));
    }

    public function AddCorporateDuePayment()
    {
        $companies = Company::where('status', '1')->get();
        $code = $this->UniqueNumberForDuePayment();
        return view('admin.due_payment.add_corporate_due', compact('companies', 'code'));
    }

    public function StoreDuePayment(Request $request)
    {
        // dd($request->all());
        $company_id = $request->company_id;
        $companyInfo = Company::where('id', $company_id)->first();

        if ($request->paid_amount > $request->due_amount) {
            return redirect()->back()->with([
                'message' => 'Sorry, Paid amount is greater than the due amount!',
                'alert-type' => 'error',
            ]);
        }

        // Save payment request without modifying any balances
        $due_payment = new DuePayment();
        $due_payment->customer_id = $company_id;
        $due_payment->code = $request->code;
        $due_payment->voucher = $request->voucher;
        $due_payment->paid_amount = $request->paid_amount;
        $due_payment->date = $request->date;
        $due_payment->paid_status = $request->paid_status;
        $due_payment->status = 'pending'; // Mark as pending
        $due_payment->approved_at = null; // Not approved yet
        $due_payment->updated_at = null;
        $due_payment->save();

        // Store invoices but don't update amounts yet
        $selectedInvoice = $request->input('invoice', []); 
        if (is_array($selectedInvoice)) {
            foreach ($selectedInvoice as $invoice) {
                $due_payment_details = new DuePaymentDetail();
                $due_payment_details->due_payment_id = $due_payment->id;
                $due_payment_details->invoice_id = $invoice;
                $due_payment_details->save();
            }
        }

        if($companyInfo->status == '1') {

            $notification = array(
                'message' => 'Due Payment Approval in Progress!',
                'alert_type' => 'success',
            );
            return redirect()->route('all.corporate.due.payment')->with($notification);
        } elseif($companyInfo->status == '0') {

        $notification = array(
            'message' => 'Due Payment Approval in Progress!',
            'alert_type' => 'success',
        );
        return redirect()->route('all.due.payment')->with($notification);
    }
    }

    public function EditDuePayment($id)
    {
        $due_payment_info = DuePayment::findOrFail($id);
        $companies = Company::get();
        $invoices = Invoice::whereIn('id', DuePaymentDetail::where('due_payment_id', $due_payment_info->id)->pluck('invoice_id'))->get();
        $payment_due_amount = Payment::where('company_id', $due_payment_info->customer_id)->sum('due_amount');

        $account_details = AccountDetail::where('company_id', $due_payment_info->customer_id)
            ->latest('id')
            ->first();
        
        $due_amount = $account_details->balance ?? $payment_due_amount;
        $companyInfo = Company::where('id', $due_payment_info->customer_id)->first();

        return view('admin.due_payment.edit_due', compact('due_payment_info', 'companies', 'invoices', 'due_amount', 'companyInfo'));
    }

    private function resetDuePayment($due_payment)
    {        
        $accountDetail = AccountDetail::where('company_id', $due_payment->customer_id)
            ->where('due_payment_id', $due_payment->id)
            ->first();
        
        if ($accountDetail) {
            $nextAccountDetails = AccountDetail::where('company_id', $due_payment->customer_id)
                ->where('id', '>', $accountDetail->id)
                ->orderBy('id')
                ->get();

            $previous_balance = $accountDetail->balance;
        
            foreach ($nextAccountDetails as $next) {
                if($next->total_amount > 0){
                    $next->balance = $next->total_amount - $next->paid_amount + $previous_balance;
                    $next->due_amount = $next->balance;
                }elseif($next->total_amount == 0){
                    $next->balance = $previous_balance - $next->paid_amount;
                    $next->due_amount = $next->balance;
                }
                $next->save();
                $previous_balance = $next->balance;
            }

        }
        
    }

    public function UpdateDuePayment(Request $request)
    {
        DB::beginTransaction();
        try {
                $id = $request->id;
                $due_payment = DuePayment::findOrFail($id);
                $company_id = $due_payment->customer_id;
                $companyInfo = Company::where('id', $company_id)->first();

                $company_id = $request->company_id;
                $companyInfo = Company::where('id', $company_id)->first();
        
                // Save payment request without modifying any balances
                // $due_payment = DuePayment();
                $due_payment->customer_id = $company_id;
                $due_payment->code = $request->code;
                $due_payment->voucher = $request->voucher;
                $due_payment->paid_amount = $request->paid_amount;
                $due_payment->date = $request->date;
                $due_payment->paid_status = $request->paid_status;
                $due_payment->status = 'pending'; // Mark as pending
                $due_payment->approved_at = null; // Not approved yet
                $due_payment->save();

                // Store invoices but don't update amounts yet
                $selectedInvoice = $request->input('invoice', []); 
                if (is_array($selectedInvoice)) {
                    foreach ($selectedInvoice as $invoice) {
                        $due_payment_details = new DuePaymentDetail();
                        $due_payment_details->due_payment_id = $due_payment->id;
                        $due_payment_details->invoice_id = $invoice;
                        $due_payment_details->save();
                    }
                }

                DB::commit();

                if($companyInfo->status == '1') {

                    $notification = array(
                        'message' => 'Due Payment Successfully Updated!',
                        'alert_type' => 'success',
                    );
                    return redirect()->route('all.corporate.due.payment')->with($notification);
                } elseif($companyInfo->status == '0') {

                $notification = array(
                    'message' => 'Due Payment Successfully Updated!',
                    'alert_type' => 'success',
                );
                return redirect()->route('all.due.payment')->with($notification);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating due payment: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while updating the due payment.']);
        }
    }


    public function DeleteDuePayment($id)
    {
        $due_payment = DuePayment::findOrFail($id);
        $companyInfo = Company::where('id', $due_payment->customer_id)->first();
        
        $due_payment_details = DuePaymentDetail::where('due_payment_id', $due_payment->id)->get();

        foreach ($due_payment_details as $detail) {
            $payment = Payment::where('invoice_id', $detail->invoice_id)->first();
            if ($payment) {
                $payment->paid_amount -= $due_payment->paid_amount;
                $payment->due_amount += $due_payment->paid_amount;
                $payment->save();
            }
            $detail->delete();
        }

        $accountDetail = AccountDetail::where('company_id', $due_payment->customer_id)
            ->where('paid_amount', $due_payment->paid_amount)
            ->where('date', $due_payment->date)
            ->first();
        
        if ($accountDetail) {
            $nextAccountDetail = AccountDetail::where('company_id', $due_payment->customer_id)
                ->where('id', '>', $accountDetail->id)
                ->get();
        
            foreach ($nextAccountDetail as $next) {
                $next->balance = $next->balance + $due_payment->paid_amount;
                $next->save();
            }

            $accountDetail->delete();
        }

        $due_payment->delete();

        $transaction = Transaction::where('customer_due_id', $due_payment->id)->first();
        if ($transaction) {
            $transaction->delete();
        }

        if ($companyInfo->status == '1') {
            $notification = array(
                'message' => 'Due Payment Successfully Deleted!',
                'alert_type' => 'success',
            );

            return redirect()->route('all.corporate.due.payment')->with($notification);
        } elseif ($companyInfo->status == '0') {   

            $notification = array(
                'message' => 'Due Payment Successfully Deleted!',
                'alert_type' => 'success',
            );

        return redirect()->route('all.due.payment')->with($notification);
      }
    }


    public function GetDuePayment(Request $request)
    {
        $companyInfo = Company::where('id', $request->company_id)->first();
        if($companyInfo->status == '1') {
            $accountBill = AccountDetail::where('company_id', $companyInfo->id)->where('approval_status', 'approved')->latest('id')->first();
            $payment_due_amount = Payment::where('company_id', $request->company_id)->sum('due_amount');

            $due_amount = $accountBill->balance ?? $payment_due_amount;

            $invoiceAll = Invoice::where('company_id', $request->company_id)->whereHas('payment', function ($query) {
                $query->where('due_amount', '!=', 0);
            })->get();
        } elseif($companyInfo->status == '0') {
            $accountBill = AccountDetail::where('company_id', $companyInfo->id)->where('approval_status', 'approved')->latest('id')->first();
            $due_amount = $accountBill->balance ?? 0;

            $invoiceAll = NULL;
           
        }

        return response()->json(
            [
                'due_amount' => $due_amount,
                'invoice' => $invoiceAll
            ]
        );
    }


    public function DuePaymentApproval(){
        $dueAll = DuePayment::where('status', 'pending')->get();
        return view('admin.due_payment.due_payment_approval', compact('dueAll'));
    }

    private function updateNextAccountBalance($due_payment) {
        $company_id = $due_payment->customer_id;
        $account_details = AccountDetail::where('company_id', $company_id)
        ->where('approval_status', 'approved')
            ->where('due_payment_id', $due_payment->id)
            ->first();

        $previous_balance = AccountDetail::where('id', '<', $account_details->id)
            ->where('approval_status', 'approved')
            ->where('company_id', $company_id)
            ->latest('id')
            ->first()->balance ?? 0;

        if ($account_details) {
            $account_details->balance = $previous_balance - $due_payment->paid_amount;
            $account_details->save();
        }

            if ($account_details) {
                $nextAccountDetails = AccountDetail::where('company_id', $due_payment->customer_id)
                    ->where('approval_status', 'approved')
                    ->where('id', '>', $account_details->id)
                    ->orderBy('id')
                    ->get();

                $previous_balance = $account_details->balance;
            
                foreach ($nextAccountDetails as $next) {

                    $next->balance = $previous_balance - $next->paid_amount;
                    $next->save();
                    $previous_balance = $next->balance;
                }
    
            }
    }


    public function DuePaymentApprovalNow($id)
    {
        $due_payment = DuePayment::findOrFail($id);

        $company_id = $due_payment->customer_id;
        $companyInfo = Company::where('id', $company_id)->first();

        $due_payment_details = DuePaymentDetail::where('due_payment_id', $due_payment->id)->get();

        if ($due_payment->approved_at) {
            return redirect()->route('due.payment.approval')->with([
                'message' => 'This Due Payment is already approved!',
                'alert-type' => 'warning',
            ]);
        }

        $company_id = $due_payment->customer_id;
        $total_paid_amount = $due_payment->paid_amount;

        // Get account details
        $account_details = AccountDetail::where('company_id', $company_id)
            ->where('approval_status', 'approved')
            ->latest('id')
            ->first();

            // dd($account_details);

        $due_amount = Payment::where('company_id', $company_id)->sum('due_amount');
        $account_balance = $account_details->balance ?? $due_amount;

        // dd($account_balance);

        // dd($due_payment->paid_amount);
        // Update account balance
        if($due_payment->updated_at == null){
            $account_details = new AccountDetail();
            $transaction = new Transaction();
        } else {
            $account_details = AccountDetail::where('due_payment_id', $due_payment->id)->first();
            $transaction = Transaction::where('customer_due_id', $due_payment->id)->first();
        }

        $account_details->paid_amount = $due_payment->paid_amount;
        $account_details->company_id = $company_id;
        $account_details->due_payment_id = $due_payment->id;
        $account_details->date = $due_payment->date;
        $account_details->voucher = $due_payment->voucher;
        $account_details->balance = $account_balance - $due_payment->paid_amount;
        // dd($account_details->balance);
        $account_details->approval_status = 'approved';
        $account_details->paid_source = $due_payment->paid_status;
        if ($companyInfo->status == '1') {
            $account_details->status = '1';
        } elseif ($companyInfo->status == '0') {
            $account_details->status = '0';
        }

        // dd($account_details->balance);
        $account_details->save();

        // transaction
        $transaction->date = date('Y-m-d', strtotime($due_payment->date));
        $transaction->customer_due_id = $due_payment->id;
        $transaction->party_name = Company::findOrFail($company_id)->name;
        $transaction->bill_no = $due_payment->code;
        $transaction->paid_by = $due_payment->paid_status;
        $transaction->paid_amount = $due_payment->paid_amount;
        $transaction->type = 'customer due payment';
        $transaction->updated_at = NULL;
        $transaction->approval_status = 'approved';

        // $this->resetDuePayment($due_payment);

        $this->updateNextAccountBalance($due_payment);

        // Update payment and due amount
        foreach ($due_payment_details as $detail) { 
            $payment = Payment::where('invoice_id', $detail->invoice_id)->first();
            
            if ($payment) {
            $payment->paid_amount = min($payment->total_amount, $payment->paid_amount + $total_paid_amount);
            $payment->due_amount = max(0, $payment->total_amount - $payment->paid_amount);
            $payment->save();

            $total_paid_amount -= $payment->paid_amount;
            if ($total_paid_amount <= 0) break; // Breaks the loop if no amount left to distribute
            }
        }
        

        // Mark the due payment as approved
        $due_payment->approved_at = now();
        $due_payment->status = 'approved';
        $due_payment->save();
        $transaction->save();

        if ($companyInfo->status == '1') {
            $notification = array(
                'message' => 'Due Payment Successfully Deleted!',
                'alert_type' => 'success',
            );

            return redirect()->route('all.corporate.due.payment')->with($notification);
        } elseif ($companyInfo->status == '0') {   

            $notification = array(
                'message' => 'Due Payment Successfully Deleted!',
                'alert_type' => 'success',
            );

        return redirect()->route('all.due.payment')->with($notification);
      }
    }

    public function UniqueNumberForDuePayment()
    {
        $code = 'DP-' . date('Y-m') . '-' . Str::random(6);
        return $code;
    }

}
