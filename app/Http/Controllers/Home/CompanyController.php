<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\BillPayment;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\AccountDetail;
use App\Models\SupplierAccountDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function CompanyAll()
    {
        $allData = Company::where('status', '1')->get();
        return view('admin.company_page.all_company', compact('allData'));
    }
    public function CompanyAdd()
    {
        return view('admin.company_page.add_company');
    }

    public function CompanyStore(Request $request)
    {
        $company = Company::orderBy('id', 'desc')->first();
        if ($company == null) {
            $firstReg = '0';
            $companyId = $firstReg + 1;
        } else {
            $company = Company::orderBy('id', 'desc')->first()->id;
            $companyId = $company + 1;
        }

        if ($companyId < 10) {
            $id_no = '000' . $companyId; //0009
        } elseif ($companyId < 100) {
            $id_no = '00' . $companyId; //0099
        } elseif ($companyId < 1000) {
            $id_no = '0' . $companyId; //0999
            $id_no = '0' . $companyId; //0999
        }

        $check_year = date('Y');

        $name = $request->name;
        $words = explode(' ', $name);
        $acronym = '';
        foreach ($words as $w) {
            $acronym .= mb_substr($w, 0, 1);
        }

        $company_id = $acronym . '-' . $check_year . '.' . $id_no;

        Company::insert([
            'name' => $request->name,
            'company_id' => $company_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'telephone' => $request->telephone,
            'address' => $request->address,
            'cor_address' => $request->cor_address,
            'bin_number' => $request->bin_number,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Company Added Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.company')->with($notification);
    } //end method



    public function CompanyEdit($id)
    {
        $companyInfo = Company::findOrFail($id);
        return view('admin.company_page.edit_company', compact('companyInfo'));
    }

    public function CompanyUpdate(Request $request)
    {
        $companyId = $request->id;
        Company::findOrFail($companyId)->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'telephone' => $request->telephone,
            'address' => $request->address,
            'cor_address' => $request->cor_address,
            'bin_number' => $request->bin_number,
        ]);

        $notification = array(
            'message' => 'Company Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    public function CompanyDelete($id)
    {
        $invoiceInfo = Invoice::where('company_id', $id)->get();

        foreach ($invoiceInfo as $invoice) {
            PaymentDetail::where('invoice_id', $invoice->id)->delete();
        }

        Company::findOrFail($id)->delete();
        Invoice::where('company_id', $id)->delete();
        InvoiceDetail::where('company_id', $id)->delete();
        Payment::where('company_id', $id)->delete();
        AccountDetail::where('company_id', $id)->delete();

        $notification = array(
            'message' => 'Company Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.company')->with($notification);
    }
    
    
    public function CompanyBillDelete($id)
    {
        $invoiceInfo = Invoice::where('company_id', $id)->get();

        foreach ($invoiceInfo as $invoice) {
            PaymentDetail::where('invoice_id', $invoice->id)->delete();
        }

        Invoice::where('company_id', $id)->delete();
        InvoiceDetail::where('company_id', $id)->delete();
        Payment::where('company_id', $id)->delete();
        AccountDetail::where('company_id', $id)->delete();

        $notification = array(
            'message' => 'Company Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }


    // credit compnay method
    public function CreditCustomer()
    {
        $allData = Payment::whereIn('paid_status', ['partial_paid', 'full_due'])->where('due_amount', '!=', '0')->get();
        return view('admin.company_page.credit_company', compact('allData'));
    }


    public function EditCreditCustomerInvoice($invoice_id)
    {
        $payment = Payment::where('invoice_id', $invoice_id)->first();
        return view('admin.company_page.edit_customer_invoice', compact('payment'));
    }

    public function UpdateCustomerInvoice(Request $request, $invoice_id)
    {
        // dd($request->all());
        if ($request->new_paid_amount < $request->paid_amount) {
            $notification = array(
                'message' => 'Sorry, You Paid maximum amount!',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        } else {
            $payment = Payment::where('invoice_id', $invoice_id)->first();
            // dd($payment);
            $payment_details = new PaymentDetail();
            $payment->paid_status = $request->paid_status;

            // account details
            $account_details = AccountDetail::where('invoice_id', $invoice_id)->first();
            
            $account_details->date = date('Y-m-d', strtotime($request->date));

            $latestBalance = AccountDetail::where('company_id', $payment->company_id)->latest('id')->first();

            $updateBalance = AccountDetail::where('id', '>=', $account_details->id)->get();

            // dd($updateBalance);

            if ($request->paid_status == 'full_paid') {
                $payment->paid_amount = Payment::where('invoice_id', $invoice_id)->first()['paid_amount'] + $request->new_paid_amount;
                $payment->due_amount = '0';
                $payment->check_number = $request->check_number;
                $payment_details->current_paid_amount = $request->new_paid_amount;

                 //account details
               $account_details->paid_amount += $request->new_paid_amount;
               $account_details->due_amount = 0;
               if($updateBalance){
                foreach ($updateBalance as $value) {
                    $value->balance -= $request->new_paid_amount;
                    $value->save();
                }
               }
            } elseif ($request->paid_status == 'partial_paid') {
                $payment->paid_amount = Payment::where('invoice_id', $invoice_id)->first()['paid_amount'] + $request->paid_amount;
                $payment->due_amount = Payment::where('invoice_id', $invoice_id)->first()['due_amount'] - $request->paid_amount;
                $payment->check_number = $request->check_number;
                $payment_details->current_paid_amount = $request->paid_amount;

               //account details
               $account_details->paid_amount += $request->paid_amount;
               $account_details->due_amount = ($request->new_paid_amount - $request->paid_amount);
               if($updateBalance){
                foreach ($updateBalance as $value) {
                    $value->balance -= $request->paid_amount;
                    $value->save();
                }
               }
            }

            $payment->save();
            $payment_details->invoice_id = $invoice_id;
            $payment_details->date = date('Y-m-d', strtotime($request->date));
            $payment_details->updated_by = Auth::user()->id;
            $payment_details->save();
            $account_details->save();

            $notification = array(
                'message' => 'Payment Updated Successfully!',
                'alert_type' => 'success',
            );
            return redirect()->route('all.company')->with($notification);
        }
    }


    public function CustomerInvoiceDetails($invoice_id)
    {
        $payment = Payment::where('invoice_id', $invoice_id)->first();
        // dd($payment);
        return view('admin.pdf.invoice_details_pdf', compact('payment'));
    }


    public function CompanyBill($id)
    {
        $allData = Invoice::orderBy('date', 'desc')->orderBy('invoice_no', 'desc')->where('company_id', $id)->where('status', '1')->get();
        // $allData = InvoiceDetail::orderBy('date', 'desc')->orderBy('invoice_no_gen', 'desc')->where('company_id', $id)->get();
        // // dd($allData);
        return view('admin.company_page.company_invoice', compact('allData','id'));
    }




    // local Company all method
    public function CustomerAll()
    {
        $allData = Company::where('status', '0')->get();
        return view('admin.local_customer.all_customer', compact('allData'));
    }
    public function CustomerAdd()
    {
        return view('admin.local_customer.add_customer');
    }

    public function CustomerStore(Request $request)
    {
        $company = Company::orderBy('id', 'desc')->first();
        if ($company == null) {
            $firstReg = '0';
            $companyId = $firstReg + 1;
        } else {
            $company = Company::orderBy('id', 'desc')->first()->id;
            $companyId = $company + 1;
        }

        if ($companyId < 10) {
            $id_no = '000' . $companyId; //0009
        } elseif ($companyId < 100) {
            $id_no = '00' . $companyId; //0099
        } elseif ($companyId < 1000) {
            $id_no = '0' . $companyId; //0999
            $id_no = '0' . $companyId; //0999
        }

        $check_year = date('Y');

        $name = $request->name;
        $words = explode(' ', $name);
        $acronym = '';
        foreach ($words as $w) {
            $acronym .= mb_substr($w, 0, 1);
        }

        $company_id = $acronym . '-' . $check_year . '.' . $id_no;


        Company::insert([
            'name' => $request->name,
            'company_id' => $company_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'telephone' => $request->telephone,
            'address' => $request->address,
            'status' => '0',
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Customer Added Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.customer')->with($notification);
    } //end method



    public function CompanyBillLocal($id)
    {
        // $allData = Invoice::orderBy('date', 'desc')->orderBy('invoice_no', 'desc')->where('company_id', $id)->where('status', '0')->get();
        $allData = Invoice::where('company_id', $id)->where('status', '0')->latest()->get();
        $accountBill = AccountDetail::where('company_id', $id)->get();
        return view('admin.local_customer.local_company_invoice', compact('allData', 'accountBill'));
    }

    public function LocalCompanyDuePayment($id)
    {
        $companyInfo = Company::where('id', $id)->first();
        $companyBill = BillPayment::where('company_id', $id)->get();
        $accountBill = AccountDetail::where('company_id', $id)->get();
        return view('admin.local_customer.company_due_payment', compact('companyBill', 'accountBill', 'companyInfo'));
    }

    // public function LocalCompanyDuePaymentStore(Request $request)
    // {
    //     $selectedInvoice = $request->input('invoice');
    //     // dd($selectedInvoice);
    //     $company_id = $request->id;
    //     if ($request->paid_amount > $request->due_amount) {
    //         $notification = array(
    //             'message' => 'Sorry, Paid amount is maximum the due amount',
    //             'alert-type' => 'error',
    //         );
    //         return redirect()->back()->with($notification);
    //     } else {

    //         if ($request->paid_status == 'check' || $request->paid_status == 'online-banking') {
    //             $paid_status = $request->paid_status;
    //             $check_number = $request->check_or_banking;
    //         } else {
    //             $paid_status = $request->paid_status;
    //             $check_number = null;
    //         }

    //         for ($i=0; $i < count($selectedInvoice); $i++) { 
                
    //         }
           
    //         $latestBalance = AccountDetail::where('company_id', $company_id)->latest('id')->first();
    
    //         $account_details = new AccountDetail();
    //         $account_details->paid_amount = $request->paid_amount;
    //         $account_details->voucher = $request->voucher;
    //         $account_details->company_id = $company_id;
    //         $account_details->date = date('Y-m-d', strtotime($request->date));
    //         $account_details->balance = $latestBalance->balance - $request->paid_amount;
    //         $account_details->save();
            
            
    //         $notification = array(
    //             'message' => 'Payment Updated Successfully!',
    //             'alert_type' => 'success',
    //         );
    //         return redirect()->route('all.customer')->with($notification);
    //     }
    // }//end method


    // public function LocalCompanyDuePaymentStore(Request $request)
    // {
    //     $selectedInvoice = $request->input('invoice');
    //     $invoice_ids = json_encode($request->input('invoice'));
    //     // dd($invoice_ids);
    //     $company_id = $request->id;


    //     // Check if the paid amount exceeds the total due amount
    //     if ($request->paid_amount > $request->due_amount) {
    //         $notification = [
    //             'message' => 'Sorry, Paid amount exceeds the total due amount for the selected invoices.',
    //             'alert-type' => 'error',
    //         ];
    //         return redirect()->back()->with($notification);
    //     }

    //     // Handle payment status and check/banking details
    //     $paid_status = $request->paid_status;
    //     $check_number = ($paid_status == 'check' || $paid_status == 'online-banking') 
    //         ? $request->check_or_banking 
    //         : null;


        

    //     // Update account details for each selected invoice
    //     foreach ($selectedInvoice as $invoiceId) {

    //         // dd($invoiceId);
    //         $account_details = AccountDetail::where('invoice_id', $invoiceId)
    //             ->where('company_id', $company_id)
    //             ->first();

    //         $previousAccount = AccountDetail::where('id', '<', $account_details->id) 
    //             ->where('company_id', $company_id)
    //             ->latest('id')            
    //             ->first();

    //         $nextAccount = AccountDetail::where('id', '>', $account_details->id) 
    //             ->where('company_id', $company_id)            
    //             ->get();

    //         // dd($previousAccount);

            


    //         if ($account_details) {
    //             if($request->paid_amount > $account_details->total_amount ){ 
    //                 $account_details->paid_amount = $account_details->total_amount; 
    //                 $account_details->due_amount -= $request->paid_amount; 
    //                 $account_details->balance = ($previousAccount->balance ?? 0) + max(0, $account_details->due_amount); 
    //                 $account_details->due_amount = max(0, $account_details->due_amount);

    //                 $account_details->save();
                    



    //                 if($nextAccount){
    //                     $previous_balance = $account_details->balance;
    //                     foreach ($nextAccount as $key => $next) {
    //                         $next->balance = $next->due_amount + $previous_balance;
    //                         $next->save();

    //                         $previous_balance = $next->balance;
    //                     }
    //                 }
    
                    
    //             } else{
    //                 $account_details->paid_amount += $request->paid_amount; 
    //                 $account_details->due_amount -= $request->paid_amount; 
    //                 $account_details->balance = ($previousAccount->balance ?? 0) + max(0, $account_details->due_amount); 
    //                 $account_details->due_amount = max(0, $account_details->due_amount);

    //                 $account_details->save();



    //                 if($nextAccount){
    //                     $previous_balance = $account_details->balance;
    //                     foreach ($nextAccount as $key => $next) {
    //                         $next->balance = $next->due_amount + $previous_balance;
    //                         $next->save();

    //                         $previous_balance = $next->balance;
    //                     }
    //                 }
    //             }
               
    //         }

            
    //     }

    //     // Update company-level balance
    //     $latestBalance = AccountDetail::where('company_id', $company_id)->latest('id')->first();
    //     $account_details = new AccountDetail();
    //     $account_details->paid_amount = $request->paid_amount;
    //     $account_details->voucher = $invoice_ids;
    //     $account_details->company_id = $company_id;
    //     $account_details->date = date('Y-m-d', strtotime($request->date));
    //     $account_details->balance = $latestBalance->balance;
    //     $account_details->save();

    //     // Notification for successful payment
    //     $notification = [
    //         'message' => 'Payment Updated Successfully!',
    //         'alert-type' => 'success',
    //     ];
    //     return redirect()->route('all.customer')->with($notification);
    // }


    public function LocalCompanyDuePaymentStore(Request $request)
    {
        $selectedInvoice = $request->input('invoice'); // Array of selected invoice IDs
        $invoice_ids = json_encode($request->input('invoice'));
        $company_id = $request->id;

        $remainingPaidAmount = $request->paid_amount;

        $total_due_amount = AccountDetail::whereIn('invoice_id', $selectedInvoice)
            ->where('company_id', $company_id)
            ->sum('due_amount');

        if ($remainingPaidAmount > $total_due_amount) {
            $notification = [
                'message' => 'Sorry, Paid amount exceeds the total due amount for the selected invoices.',
                'alert-type' => 'error',
            ];
            return redirect()->back()->with($notification);
        }

        $paid_status = $request->paid_status;
        $check_number = ($paid_status == 'check' || $paid_status == 'online-banking') 
            ? $request->check_or_banking 
            : null;

        foreach ($selectedInvoice as $invoiceId) {
            if ($remainingPaidAmount <= 0) {
                break; 
            }

            $account_details = AccountDetail::where('invoice_id', $invoiceId)
                ->where('company_id', $company_id)
                ->first();

            if ($account_details) {
                $previousAccount = AccountDetail::where('id', '<', $account_details->id) 
                    ->where('company_id', $company_id)
                    ->latest('id')
                    ->first();

                $currentDue = $account_details->due_amount;

                if ($remainingPaidAmount >= $currentDue) {
                    $account_details->paid_amount += $currentDue;
                    $remainingPaidAmount -= $currentDue;
                    $account_details->due_amount = 0;
                } else {
                    $account_details->paid_amount += $remainingPaidAmount;
                    $account_details->due_amount -= $remainingPaidAmount;
                    $remainingPaidAmount = 0;
                }

                $account_details->balance = ($previousAccount->balance ?? 0) + max(0, $account_details->due_amount);
                $account_details->save();

                $nextAccounts = AccountDetail::where('id', '>', $account_details->id)
                    ->where('company_id', $company_id)
                    ->get();

                if ($nextAccounts) {
                    $previous_balance = $account_details->balance;
                    foreach ($nextAccounts as $next) {
                        $next->balance = $next->due_amount + $previous_balance;
                        $next->save();
                        $previous_balance = $next->balance;
                    }
                }
            }
        }

        $latestBalance = AccountDetail::where('company_id', $company_id)->latest('id')->first();
        $account_details = new AccountDetail();
        $account_details->paid_amount = $request->paid_amount;
        $account_details->voucher = $invoice_ids;
        $account_details->company_id = $company_id;
        $account_details->date = date('Y-m-d', strtotime($request->date));
        $account_details->balance = $latestBalance->balance;
        $account_details->save();

        $notification = [
            'message' => 'Payment Updated Successfully!',
            'alert-type' => 'success',
        ];
        return redirect()->route('all.customer')->with($notification);
    }
    
    public function CompanyBillLocalDetails($id)
    {
        $billDetails = AccountDetail::where('approval_status', 'approved')->where('company_id', $id)->get();
        $companyInfo = Company::where('id', $id)->first();
        return view('admin.local_customer.company_bill_details', compact('billDetails','companyInfo'));
    }

    public function CorporateBillDetails($id)
    {
        $billDetails = AccountDetail::where('approval_status', 'approved')->where('company_id', $id)->get();
        $companyInfo = Company::where('id', $id)->first();
        return view('admin.local_customer.company_bill_details', compact('billDetails','companyInfo'));
    }

    //Corporate Company Due Payment

    public function CompanyBillCorporate($id){
        $allData = Invoice::where('company_id', $id)->where('status', '1')->latest()->get();
        $accountBill = AccountDetail::where('company_id', $id)->get();
        return view('admin.corporate_customer.corporate_invoice', compact('allData', 'accountBill'));
    }

    public function CorporateCompanyDuePayment($id){
        $companyInfo = Company::where('id', $id)->first();
        $companyBill = BillPayment::where('company_id', $id)->get();
        $accountBill = AccountDetail::where('company_id', $id)->get();
        return view('admin.corporate_customer.corporate_due_payment', compact('companyBill', 'accountBill', 'companyInfo'));
    }

    public function CorporateCompanyDuePaymentStore(Request $request)
    {
        $selectedInvoice = $request->input('invoice'); // Array of selected invoice IDs
        $invoice_ids = json_encode($request->input('invoice'));
        $company_id = $request->id;

        $remainingPaidAmount = $request->paid_amount;

        $total_due_amount = AccountDetail::whereIn('invoice_id', $selectedInvoice)
            ->where('company_id', $company_id)
            ->sum('due_amount');

        if ($remainingPaidAmount > $total_due_amount) {
            $notification = [
                'message' => 'Sorry, Paid amount exceeds the total due amount for the selected invoices.',
                'alert-type' => 'error',
            ];
            return redirect()->back()->with($notification);
        }

        $paid_status = $request->paid_status;
        $check_number = ($paid_status == 'check' || $paid_status == 'online-banking') 
            ? $request->check_or_banking 
            : null;

        foreach ($selectedInvoice as $invoiceId) {
            if ($remainingPaidAmount <= 0) {
                break; 
            }

            $account_details = AccountDetail::where('invoice_id', $invoiceId)
                ->where('company_id', $company_id)
                ->first();

            if ($account_details) {
                $previousAccount = AccountDetail::where('id', '<', $account_details->id) 
                    ->where('company_id', $company_id)
                    ->latest('id')
                    ->first();

                $currentDue = $account_details->due_amount;

                if ($remainingPaidAmount >= $currentDue) {
                    $account_details->paid_amount += $currentDue;
                    $remainingPaidAmount -= $currentDue;
                    $account_details->due_amount = 0;
                } else {
                    $account_details->paid_amount += $remainingPaidAmount;
                    $account_details->due_amount -= $remainingPaidAmount;
                    $remainingPaidAmount = 0;
                }

                $account_details->balance = ($previousAccount->balance ?? 0) + max(0, $account_details->due_amount);
                $account_details->save();

                $nextAccounts = AccountDetail::where('id', '>', $account_details->id)
                    ->where('company_id', $company_id)
                    ->get();

                if ($nextAccounts) {
                    $previous_balance = $account_details->balance;
                    foreach ($nextAccounts as $next) {
                        $next->balance = $next->due_amount + $previous_balance;
                        $next->save();
                        $previous_balance = $next->balance;
                    }
                }
            }
        }

        $latestBalance = AccountDetail::where('company_id', $company_id)->latest('id')->first();
        $account_details = new AccountDetail();
        $account_details->paid_amount = $request->paid_amount;
        $account_details->voucher = $invoice_ids;
        $account_details->company_id = $company_id;
        $account_details->date = date('Y-m-d', strtotime($request->date));
        $account_details->balance = $latestBalance->balance;
        $account_details->save();

        $notification = [
            'message' => 'Payment Updated Successfully!',
            'alert-type' => 'success',
        ];
        return redirect()->route('all.customer')->with($notification);
    }

    public function CompanyDynamicQuery(){

        $billDetails = AccountDetail::get();
        if($billDetails->isEmpty()) {
            dd('No data found');
        } else {
            foreach ($billDetails as $key => $item) {
                $item->approval_status = 'approved';
                $item->save();
            }
        }

        $supplierbillDetails = SupplierAccountDetail::get();
        if($supplierbillDetails->isEmpty()) {
            dd('No data found');
        } else {
            foreach ($supplierbillDetails as $key => $details) {
                $details->approval_status = 'approved';
                $details->save();
            }
        }

        dd('done');

        $payment = Payment::where('company_id', 1)->get();
        // dd($payment->sum('total_amount'), $payment->sum('paid_amount'), $payment->sum('due_amount'));
        $companies = Company::where('id', 20)->get();

        // dd($companies);

        foreach ($companies as $company) {
            $billDetails = AccountDetail::where('company_id', $company->id)->orderBy('id')->get();
            $previousBalance = 0;
                    
            foreach ($billDetails as $key => $item) {
                $item->balance = $previousBalance + $item->total_amount - $item->paid_amount;
                $item->update();
                $previousBalance = $item->balance; 
            }
        }

        dd('done');

    }
}
