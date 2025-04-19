<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SupplierDuePayment;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\SupplierPaymentDetail;
use App\Models\SupplierAccountDetail;
use App\Models\SupplierDuePaymentDetail;
use DB;


class SupplierDuePaymentController extends Controller
{
    public function AllDuePayment()
    {
        $dueAll = SupplierDuePayment::all();
        return view('admin.supplier_due_payment.all_supplier_due', compact('dueAll'));
    }

    public function AddDuePayment()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        return view('admin.supplier_due_payment.add_supplier_due', compact('suppliers'));
    }

    public function StoreDuePayment(Request $request)
    {
        $supplier_id = $request->supplier_id;
        $supplierInfo = Supplier::where('id', $supplier_id)->first();

        if ($request->paid_amount > $request->due_amount) {
            $notification = array(
                'message' => 'Sorry, Paid amount is maximum the due amount',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        } else {

            if ($request->paid_status == 'check' || $request->paid_status == 'online-banking') {
                $paid_status = $request->paid_status;
                $check_number = $request->check_or_banking;
            } else {
                $paid_status = $request->paid_status;
                $check_number = null;
            }

            $account_details = SupplierAccountDetail::where('supplier_id', $supplier_id)
                ->latest('id')
                ->first();

            $due_amount = SupplierPaymentDetail::where('supplier_id', $supplier_id)->sum('due_amount');

            $account_details_balance = $account_details->balance ?? $due_amount;
           
            $due_payment = new SupplierDuePayment();
            $due_payment->supplier_id = $request->supplier_id;
            $due_payment->paid_amount = $request->paid_amount;
            $due_payment->date = $request->date;
            $due_payment->voucher = $request->voucher;
            $due_payment->paid_status = $request->paid_status;
            $due_payment->status = 'pending';
            $due_payment->approved_at = null;
            $due_payment->updated_at = null;
            $due_payment->save();

            
            
            $notification = array(
                'message' => 'Due Payment Successfully Done!',
                'alert_type' => 'success',
            );
            return redirect()->route('all.supplier.due.payment')->with($notification);
        }

    }

    public function EditDuePayment($id)
    {
        $due_payment_info = SupplierDuePayment::findOrFail($id);
        $suppliers = Supplier::get();
        $purchases = Purchase::whereIn('id', SupplierDuePaymentDetail::where('due_payment_id', $due_payment_info->id)->pluck('purchase_id'))->get();

        $due_amount = SupplierAccountDetail::where('supplier_id', $due_payment_info->supplier_id)->latest('id')->first()->balance ?? SupplierPaymentDetail::where('supplier_id', $due_payment_info->supplier_id)->sum('due_amount');
        
        $supplierInfo = Supplier::where('id', $due_payment_info->supplier_id)->first();

        return view('admin.supplier_due_payment.edit_supplier_due', compact('due_payment_info', 'suppliers', 'purchases', 'due_amount', 'supplierInfo'));
    }

    private function resetDuePayment($due_payment, $id)
    {
        SupplierDuePayment::where('id', $id)->delete();

        $due_payment_details = SupplierDuePaymentDetail::where('due_payment_id', $due_payment->id)->get();

        foreach ($due_payment_details as $detail) {
            $payment = SupplierPaymentDetail::where('supplier_id', $detail->invoice_id)->first();
            if ($payment) {
                $payment->paid_amount -= $due_payment->paid_amount;
                $payment->due_amount += $due_payment->paid_amount;
                $payment->save();
            }

            $detail->delete();
        }

        $accountDetail = SupplierAccountDetail::where('supplier_id', $due_payment->supplier_id)
        ->where('paid_amount', $due_payment->paid_amount)
        ->where('date', $due_payment->date)
        ->first();

        if ($accountDetail) {
            $nextAccountDetail = SupplierAccountDetail::where('supplier_id', $due_payment->supplier_id)
                ->where('id', '>', $accountDetail->id)
                ->get();
        
            foreach ($nextAccountDetail as $next) {
                $next->balance = $next->balance + $due_payment->paid_amount;
                $next->save();
            }

           $accountDetail->delete();
      }
    }

    public function UpdateDuePayment(Request $request)
    {
        DB::beginTransaction();
        try {
                $id = $request->id;
                $due_payment = SupplierDuePayment::findOrFail($id);
                $supplier_id = $due_payment->supplier_id;
                $supplierInfo = Supplier::where('id', $supplier_id)->first();

                $this->resetDuePayment($due_payment, $id);

                // $supplier_id = $request->supplier_id;
                // $supplierInfo = Supplier::where('id', $supplier_id)->first();
        
                if ($request->paid_amount > $request->due_amount) {
                    $notification = array(
                        'message' => 'Sorry, Paid amount is maximum the due amount',
                        'alert-type' => 'error',
                    );
                    return redirect()->back()->with($notification);
                } else {
        
                    if ($request->paid_status == 'check' || $request->paid_status == 'online-banking') {
                        $paid_status = $request->paid_status;
                        $check_number = $request->check_or_banking;
                    } else {
                        $paid_status = $request->paid_status;
                        $check_number = null;
                    }
        
                    $account_details = SupplierAccountDetail::where('supplier_id', $supplier_id)
                        ->latest('id')
                        ->first();
        
                    $due_amount = SupplierPaymentDetail::where('supplier_id', $supplier_id)->sum('due_amount');
        
                    $account_details_balance = $account_details->balance ?? $due_amount;
            
                    // $account_details = new SupplierAccountDetail();
                    // $account_details->paid_amount = $request->paid_amount;
                    // $account_details->supplier_id = $supplier_id;
                    // $account_details->date = date('Y-m-d', strtotime($request->date));
                    // $account_details->balance = $account_details_balance - $request->paid_amount;
                    // $account_details->save();
                   
                    $due_payment = new SupplierDuePayment();
                    $due_payment->supplier_id = $request->supplier_id;
                    $due_payment->paid_amount = $request->paid_amount;
                    $due_payment->date = $request->date;
                    $due_payment->voucher = $request->voucher;
                    $due_payment->paid_status = $request->paid_status;
                    $due_payment->status = 'pending';
                    $due_payment->save();                    
                    
                }

                DB::commit();

                $notification = array(
                    'message' => 'Due Payment Successfully Updated!',
                    'alert_type' => 'success',
                );

                return redirect()->route('all.supplier.due.payment')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating due payment: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while updating the due payment.']);
        }
    }


    public function DeleteDuePayment($id)
    {
        $due_payment = SupplierDuePayment::findOrFail($id);
        $supplierInfo = Supplier::where('id', $due_payment->supplier_id)->first();

        $accountDetail = SupplierAccountDetail::where('supplier_id', $due_payment->supplier_id)
        ->where('paid_amount', $due_payment->paid_amount)
        ->where('date', $due_payment->date)
        ->first();

        if ($accountDetail) {
                $nextAccountDetail = SupplierAccountDetail::where('supplier_id', $due_payment->supplier_id)
                    ->where('id', '>', $accountDetail->id)
                    ->get();
            
                foreach ($nextAccountDetail as $next) {
                    $next->balance = $next->balance + $due_payment->paid_amount;
                    $next->save();
                }
    
            $accountDetail->delete();
        }

        $due_payment->delete();

        Transaction::where('supplier_due_id', $id)->delete();

            $notification = array(
                'message' => 'Due Payment Successfully Deleted!',
                'alert_type' => 'success',
            );

        return redirect()->route('all.supplier.due.payment')->with($notification);
    }


    public function GetDuePayment(Request $request)
    {
        $supplierInfo = Supplier::where('id', $request->supplier_id)->first();

        $due_amount = SupplierAccountDetail::where('supplier_id', $request->supplier_id)->latest('id')->first()->balance ?? SupplierPaymentDetail::where('supplier_id', $request->supplier_id)->sum('due_amount');

        return response()->json(
            [
                'due_amount' => $due_amount,
            ]
        );
    }


    public function DuePaymentApproval(){
        $dueAll = SupplierDuePayment::where('status', 'pending')->get();
        return view('admin.supplier_due_payment.supplier_due_payment_approval', compact('dueAll'));
    }

    public function DuePaymentApprovalNow($id)
    {
        $due_payment = SupplierDuePayment::findOrFail($id);

        $supplier_id = $due_payment->supplier_id;
        $supplierInfo = Supplier::where('id', $supplier_id)->first();

        $supplier_id = $due_payment->supplier_id;
        $total_paid_amount = $due_payment->paid_amount;

        // Get account details
        // $account_details = SupplierAccountDetail::where('supplier_id', $supplier_id)->latest('id')->first();
        $due_amount = SupplierPaymentDetail::where('supplier_id', $supplier_id)->sum('due_amount');
        $account_balance = $account_details->balance ?? $due_amount;

        if($due_payment->updated_at == null){ 
            $account_details = new SupplierAccountDetail();
            $transaction = new Transaction();
        } else {
            $account_details = SupplierAccountDetail::where('due_payment_id', $due_payment->id)->first();
            $transaction = Transaction::where('supplier_due_id', $due_payment->id)->first();
        }

        // Update account balance
        $account_details = new SupplierAccountDetail();
        $account_details->paid_amount = $due_payment->paid_amount;
        $account_details->supplier_id = $supplier_id;
        $account_details->due_payment_id = $due_payment->id;
        $account_details->date = $due_payment->date;
        $account_details->voucher = $due_payment->voucher;
        $account_details->balance = $account_balance - $due_payment->paid_amount;
        $account_details->save();

        // transaction
        $transaction->date = date('Y-m-d', strtotime($due_payment->date));
        $transaction->supplier_due_id = $due_payment->id;
        $transaction->party_name = Supplier::findOrFail($supplier_id)->name;
        // $transaction->bill_no = $due_payment->code;
        $transaction->paid_by = $due_payment->paid_status;
        $transaction->paid_amount = $due_payment->paid_amount;
        $transaction->type = 'supplier due payment';
        

        // Mark the due payment as approved
        $due_payment->approved_at = now();
        $due_payment->status = 'approved';
        $due_payment->save();
        $transaction->save();

        $notification = array(
            'message' => 'Due Payment Successfully Approved!',
            'alert_type' => 'success',
        );

        return redirect()->route('all.supplier.due.payment')->with($notification);
    }
}
