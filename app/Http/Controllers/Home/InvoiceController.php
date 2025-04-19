<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\AccountDetail;
use App\Models\SubCategory;
use App\Models\Tax;
use App\Models\VatChalan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class InvoiceController extends Controller
{
    public function InvoiceAll()
    {
        // $allData = Invoice::orderBy('date', 'desc')->where('status', '1')->get();
        $allData = Invoice::latest()->where('status', '1')->get();
        // dd($allData);
        return view('admin.invoice.invoice_all', compact('allData'));
    }

    public function ChalanAll()
    {
        $allData = Invoice::orderBy('date', 'desc')->orderBy('invoice_no', 'desc')->where('status', '1')->get();
        return view('admin.chalan.chalan_all', compact('allData'));
    }
    public function VatChalanAll()
    {
        $allData = VatChalan::latest()->get();
        return view('admin.vat_chalan.vat_chalan_all', compact('allData'));
    }

    public function VatChalanPrint($id)
    {
        $vat_chalan = VatChalan::findOrFail($id);
        $invoice = Invoice::findOrFail($vat_chalan->invoice_id);
        return view('admin.vat_chalan.vat_chalan_print', compact('invoice', 'vat_chalan'));
    }

    public function InvoiceAdd()
    {
        $invoice_data = Invoice::orderBy('id', 'desc')->first();
        if ($invoice_data == null) {
            $firstReg = '0';
            $invoice_no = $firstReg + 1;
        } else {
            $invoice_data = Invoice::orderBy('id', 'desc')->first()->invoice_no;
            $invoice_no = $invoice_data + 1;
        }

        $vat_invoice_no = $this->UniqueNumber();

        $date = date('Y-m-d');
        $companies = Company::where('status', '1')->get();
        $categories = Category::where('status', '0')->get();
        $taxes = Tax::where('status', '1')->OrderBy('name', 'ASC')->get();
        return view('admin.invoice.invoice_add', compact('invoice_no', 'companies', 'date', 'categories', 'vat_invoice_no', 'taxes'));
    } //end method


  
    public function InvoiceStore(Request $request)
    {
        // dd($request->all());
        if ($request->company_id == null) {
            $notification = array(
                'message' => 'Sorry, you do not any company',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        } else {
            if ($request->paid_amount > $request->estimated_amount) {
                $notification = array(
                    'message' => 'Sorry, Paid amount is maximum the total amount',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            } else {

                // generate invoice by company wise
                $count_com = count($request->company_id);
                for ($i = 0; $i < $count_com; $i++) {
                    $get_company_id = $request->company_id[$i];
                }
                $currentYear = date('Y');
                $invoice_data = Invoice::orderBy('id', 'desc')->first();
                if ($invoice_data == null) {
                    $firstReg = '0';
                    $invoice_no = $firstReg + 1;

                    $inv_no_gen = 'INV-'  . $currentYear . '-' . $get_company_id  . '-' . $invoice_no;
                } else {
                    $invoice_number = Invoice::where('company_id', $get_company_id)->latest()->first();
                    if ($invoice_number == null) {

                        $invoice_data = Invoice::orderBy('id', 'desc')->first()->invoice_no;
                        $invoice_no = $invoice_data + 1;

                        $inv_no_gen = 'INV-' . $currentYear . '-' . $get_company_id  . '-' . '1';
                    } else {
                        $current_inv_no =  $invoice_number->id;
                        $invoice_no = $current_inv_no + 1;
                        $invoice_no_gen = $invoice_number->invoice_no_gen;
                        $in = explode("-", $invoice_no_gen, 5);
                        $invo_no = $in[3];
                        $inv_no_gen = 'INV-' . $currentYear . '-' . $get_company_id . '-' . $invo_no + 1;
                    }
                }


                $invoice = new Invoice();
                $invoice->invoice_no = $invoice_no;
                $invoice->invoice_no_gen = $inv_no_gen;
                $invoice->company_id = $get_company_id;
                $invoice->date = date('Y-m-d', strtotime($request->date));
                $invoice->po_number = $request->po_number;
                $invoice->status = '1';
                $invoice->created_by = Auth::user()->id;

                

                
                // dd($latestBalance);

                DB::transaction(function () use ($request, $invoice) {
                    if ($invoice->save()) {
                        $count_company = count($request->company_id);

                        for ($i = 0; $i < $count_company; $i++) {

                            $invoice_details = new InvoiceDetail();
                            $invoice_details->date = date('Y-m-d', strtotime($request->date));
                            $invoice_details->invoice_id = $invoice->id;
                            $invoice_details->invoice_no_gen = $invoice->invoice_no_gen;
                            $invoice_details->company_id = $request->company_id[$i];
                            $invoice_details->category_id = $request->category_id[$i];
                            $invoice_details->sub_cat_id = $request->sub_cat_id[$i];
                            $invoice_details->description = $request->description[$i];
                            $invoice_details->selling_qty = $request->selling_qty[$i];
                            $invoice_details->unit_price = $request->unit_price[$i];
                            $invoice_details->selling_price = $request->selling_price[$i];
                            $invoice_details->size = $request->size[$i];
                            $invoice_details->status = '1';
                            $invoice_details->save();
                        }



                        $payment = new Payment();
                        $payment_details = new PaymentDetail();
                        $payment->invoice_id = $invoice->id;
                        $payment->company_id = $invoice_details->company_id;
                        $payment->paid_status = $request->paid_status;
                        $payment->due_amount = $request->due_amount;
                        $payment->discount_amount = $request->discount_amount;
                        $payment->sub_total = $request->sub_total;


                        $latestBalance = AccountDetail::where('company_id', $request->company_id)->latest('id')->first();
                         // account details
                         $account_details = new AccountDetail();
                         $account_details->invoice_id = $invoice->id;
                         $account_details->company_id = $invoice_details->company_id;
                         $account_details->total_amount = $request->estimated_amount;
                         $account_details->date = date('Y-m-d', strtotime($request->date));

                         // transaction
                        $transaction = new Transaction();
                        $transaction->date = date('Y-m-d', strtotime($request->date));
                        $transaction->invoice_id = $invoice->id;
                        $transaction->party_name = Company::findOrFail($invoice->company_id)->name;
                        $transaction->bill_no = $invoice->invoice_no_gen;
                        $transaction->paid_by = $request->paid_source;
                        $transaction->type = 'sales';
                        $transaction->updated_at = NULL;

                        
                        if ($request->vat_tax_field == '0') {

                            $payment->vat_tax = '0';
                            $payment->vat_amount = '0';
                            $payment->total_amount = $request->estimated_amount;

                            if ($request->paid_status == 'full_paid') {
                                $payment->paid_amount = $request->estimated_amount;
                                $payment->due_amount = '0';
                                $payment_details->current_paid_amount = $request->estimated_amount;

                                // transaction
                                $transaction->paid_amount = $request->estimated_amount;
                                $transaction->due_amount = '0';

                                // account details
                                $account_details->paid_amount = $request->estimated_amount;
                                $account_details->due_amount = '0';
                                $account_details->balance = $latestBalance->balance ?? 0;
                            } elseif ($request->paid_status == 'full_due') {
                                $payment->paid_amount = '0';
                                $payment->due_amount = $request->estimated_amount;
                                $payment_details->current_paid_amount = '0';

                                // transaction
                                $transaction->paid_amount = '0';
                                $transaction->due_amount = $request->estimated_amount;

                                //account details
                                $account_details->paid_amount = '0';
                                $account_details->due_amount = $request->estimated_amount;
                                $account_details->balance = ($latestBalance->balance ?? 0) + $request->estimated_amount;

                                
                            } elseif ($request->paid_status == 'partial_paid') {
                                $payment->paid_amount = $request->paid_amount;
                                $payment->due_amount = $request->estimated_amount - $request->paid_amount;
                                $payment_details->current_paid_amount = $request->paid_amount;

                                // transaction
                                $transaction->paid_amount = $request->paid_amount;
                                $transaction->due_amount = $request->estimated_amount - $request->paid_amount;

                               //account details
                               $account_details->paid_amount = $request->paid_amount;
                               $account_details->due_amount = $request->estimated_amount - $request->paid_amount;
                               $account_details->balance = ($latestBalance->balance ?? 0) + ($request->estimated_amount - $request->paid_amount);
                            }

                            if ($request->paid_source == 'check' || $request->paid_source == 'online-banking') {
                                $payment->paid_source = $request->check_or_banking;
                            } else {
                                $payment->paid_source = $request->paid_source;
                            }



                        } else {
                            $taxes = Tax::findOrFail($request->vat_tax_field);
                            $payment->vat_tax = $taxes->rate;
                            $payment->vat_amount = $request->vat_amount;
                            $payment->total_amount = $request->estimated_amount;


                            if ($request->paid_status == 'full_paid') {
                                $payment->paid_amount = $request->estimated_amount;
                                $payment->due_amount = '0';
                                $payment_details->current_paid_amount = $request->estimated_amount;

                                // transaction
                                $transaction->paid_amount = $request->estimated_amount;
                                $transaction->due_amount = '0';

                                // account details
                                $account_details->paid_amount = $request->estimated_amount;
                                $account_details->due_amount = '0';
                                $account_details->balance = $latestBalance->balance ?? 0;
                            } elseif ($request->paid_status == 'full_due') {
                                $payment->paid_amount = '0';
                                $payment->due_amount = $request->estimated_amount;
                                $payment_details->current_paid_amount = '0';

                                // dd('dhukse with tax');

                                // transaction
                                $transaction->paid_amount = '0';
                                $transaction->due_amount = $request->estimated_amount;

                                 //account details
                                 $account_details->paid_amount = '0';
                                 $account_details->due_amount = $request->estimated_amount;
                                 $account_details->balance = $latestBalance->balance ?? 0 + $request->estimated_amount;
                            } elseif ($request->paid_status == 'partial_paid') {
                                $payment->paid_amount = $request->paid_amount;
                                $payment->due_amount = $request->estimated_amount - $request->paid_amount;
                                $payment_details->current_paid_amount = $request->paid_amount;

                                // transaction
                                $transaction->paid_amount = $request->paid_amount;
                                $transaction->due_amount = $request->estimated_amount - $request->paid_amount;

                                //account details
                                $account_details->paid_amount = $request->paid_amount;
                                $account_details->due_amount = $request->estimated_amount - $request->paid_amount;
                                $account_details->balance = $latestBalance->balance ?? 0 + ($request->estimated_amount - $request->paid_amount);
                            }

                            $vat_chalan = new VatChalan();
                            $vat_chalan->invoice_no = $request->vat_invoice_no;
                            $vat_chalan->invoice_id = $invoice->id;
                            $vat_chalan->date = date('Y-m-d', strtotime($request->date));
                            $vat_chalan->save();
                        }


                        $payment->save();

                        $payment_details->invoice_id = $invoice->id;
                        $payment_details->date = date('Y-m-d', strtotime($request->date));
                        $payment_details->save();
                        $account_details->save();
                        $transaction->save();
                    }
                });
            } //end else

        }
        $notification = array(
            'message' => 'Invoice Data Inserted Successfully',
            'alert-type' => 'success',
        );
        return redirect()->route('invoice.all')->with($notification);
    } //end method



    public function InvoiceEdit($id)
    {
        $invoiceInfo = Invoice::findOrFail($id);
        $companies = Company::where('status', '1')->get();
        $categories = Category::where('status', '0')->get();
        $invoiceDetails = InvoiceDetail::where('invoice_id', $id)->get();
        $subCategories = SubCategory::get();
        $taxes = Tax::where('status', '1')->OrderBy('name', 'ASC')->get();
        $payment = Payment::where('invoice_id', $id)->first();
        return view('admin.invoice.invoice_edit', compact('invoiceInfo', 'companies', 'categories', 'invoiceDetails', 'subCategories', 'payment', 'taxes'));
    }

    public function InvoiceUpdate(Request $request)
    {
        // dd($request->all());
        $invoiceID = $request->id;

        if ($request->company_id == null) {
            $notification = array(
                'message' => 'Sorry, you do not any company',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        } else {
            if ($request->paid_amount > $request->estimated_amount) {
                $notification = array(
                    'message' => 'Sorry, Paid amount is maximum the total amount',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            } else {

                $invoice = Invoice::findOrFail($invoiceID);
                $invoice->company_id = $request->company_id[0];
                $invoice->date = date('Y-m-d', strtotime($request->date));
                $invoice->status = '1';
                $invoice->updated_by = Auth::user()->id;
                $invoice->update();

                InvoiceDetail::where('invoice_id', $invoiceID)->delete();

                DB::transaction(function () use ($request, $invoice) {
                    if ($invoice->save()) {
                        // dd('hello');
                        $count_company = count($request->company_id);

                        for ($i = 0; $i < $count_company; $i++) {

                            $invoice_details = new InvoiceDetail();
                            $invoice_details->date = date('Y-m-d', strtotime($request->date));
                            $invoice_details->invoice_id = $invoice->id;
                            $invoice_details->invoice_no_gen = $invoice->invoice_no_gen;
                            $invoice_details->company_id = $request->company_id[$i];
                            $invoice_details->category_id = $request->category_id[$i];
                            $invoice_details->sub_cat_id = $request->sub_cat_id[$i];
                            $invoice_details->description = $request->description[$i];
                            $invoice_details->selling_qty = $request->selling_qty[$i];
                            $invoice_details->unit_price = $request->unit_price[$i];
                            $invoice_details->selling_price = $request->selling_price[$i];
                            $invoice_details->size = $request->size[$i];
                            $invoice_details->status = '1';
                            $invoice_details->save();
                        }



                        $payment = Payment::where('invoice_id', $invoice->id)->first();
                        // dd($payment->due_amount);
                        $payment_details = PaymentDetail::where('invoice_id', $invoice->id)->first();
                        $account_details = AccountDetail::where('invoice_id', $invoice->id)->first();
                        $payment->company_id = $invoice_details->company_id;
                        $payment->paid_status = $request->paid_status;
                        $payment->due_amount = $request->due_amount;
                        $payment->discount_amount = $request->discount_amount;
                        $payment->sub_total = $request->sub_total;


                        // account details
                        $account_details->company_id = $invoice_details->company_id;
                        $account_details->total_amount = $request->estimated_amount;
                        $account_details->date = date('Y-m-d', strtotime($request->date));

                        $updateBalance = AccountDetail::where('id', '>', $account_details->id)->where('company_id', $invoice_details->company_id)->get();


                        $previousBalance = AccountDetail::where('id', '<', $account_details->id)->where('company_id', $invoice_details->company_id)->latest('id')->first();

                        // transaction
                        $transaction = Transaction::where('invoice_id', $invoice->id)->first();
                        if ($transaction) {
                            $transaction->date = date('Y-m-d', strtotime($request->date));
                            $transaction->invoice_id = $invoice->id;
                            $transaction->party_name = Company::findOrFail($request->company_id)->name;
                            $transaction->paid_by = $request->paid_source;
                            $transaction->type = 'sales';
                        }


                        if ($request->vat_tax_field == '0') {

                            VatChalan::where('invoice_id', $invoice->id)->delete();

                            $payment->total_amount = $request->estimated_amount;
                            $payment->vat_tax = '0';
                            $payment->vat_amount = '0';

                            if ($request->paid_status == 'full_paid') {
                                $payment->paid_amount = $request->estimated_amount;
                                $payment->due_amount = '0';
                                $payment_details->current_paid_amount = $request->estimated_amount;

                                $transaction->paid_amount = $request->estimated_amount;
                                $transaction->due_amount = '0';

                                 // account details
                                $account_details->paid_amount = $request->estimated_amount;
                                $account_details->due_amount = '0';

                                $account_details->balance = $previousBalance->balance ?? 0;

                                if($updateBalance != NULL){
                                    $previous_balance = $account_details->balance ?? 0;
                                    foreach ($updateBalance as $value) {
                                        $value->balance = $previous_balance + $value->due_amount;
                                        $value->save();
                                        $previous_balance = $value->balance;
                                    }
                                }

                            } elseif ($request->paid_status == 'full_due') {
                                $payment->paid_amount = '0';
                                $payment->due_amount = $request->estimated_amount;
                                $payment_details->current_paid_amount = '0';

                                //transaction
                                $transaction->paid_amount = '0';
                                $transaction->due_amount = $request->estimated_amount;

                                 // account details
                                $account_details->paid_amount = '0';
                                $account_details->due_amount = $request->estimated_amount;
                                $account_details->balance = $request->estimated_amount + $previousBalance->balance;
                                if($updateBalance != NULL){
                                    $previous_balance = $account_details->balance;
                                    foreach ($updateBalance as $value) {
                                        $value->balance = $previous_balance + $value->due_amount;
                                        $value->save();
                                        $previous_balance = $value->balance;
                                    }
                                }

                            } elseif ($request->paid_status == 'partial_paid') {
                                $payment->paid_amount = $request->paid_amount;
                                $payment->due_amount = $request->estimated_amount - $request->paid_amount;
                                $payment_details->current_paid_amount = $request->paid_amount;

                                // transaction
                                $transaction->paid_amount = $request->paid_amount;
                                $transaction->due_amount = $request->estimated_amount - $request->paid_amount;

                                // account details
                                $account_details->paid_amount = $request->paid_amount;
                                $account_details->due_amount = $request->estimated_amount - $request->paid_amount;
                                $account_details->balance = $previousBalance->balance + ($request->estimated_amount - $request->paid_amount);

                                if ($updateBalance != NULL) {
                                    $previous_balance = $account_details->balance; 
                                    foreach ($updateBalance as $key => $value) {
                                        $value->balance = $previous_balance + $value->due_amount; 
                                        $value->save();

                                        $previous_balance = $value->balance;
                                    }
                                }
                            }

                            if ($request->paid_source == 'check' || $request->paid_source == 'online-banking') {
                                $payment->paid_source = $request->check_or_banking;
                            } else {
                                $payment->paid_source = $request->paid_source;
                            }

                        } else {

                            $vat_chalan = VatChalan::where('invoice_id', $invoice->id)->first();
                            $chalan_no = VatChalan::latest()->first();
                            $newChalanNo = (Int)($chalan_no->invoice_no) + 1;
                            if (!$vat_chalan) {
                                $vat_chalan = new VatChalan();
                                $vat_chalan->invoice_no = $newChalanNo;
                                $vat_chalan->invoice_id = $invoice->id;
                                $vat_chalan->date = date('Y-m-d', strtotime($request->date));
                                $vat_chalan->save();
                            }
                            $taxes = Tax::findOrFail($request->vat_tax_field);
                            $payment->vat_tax = $taxes->rate;
                            $payment->vat_amount = $request->vat_amount;
                            $payment->total_amount = $request->estimated_amount;

                            if ($request->paid_status == 'full_paid') {
                                $payment->paid_amount = $request->estimated_amount;
                                $payment->due_amount = '0';
                                $payment_details->current_paid_amount = $request->estimated_amount;

                                // transaction
                                $transaction->paid_amount = $request->estimated_amount;
                                $transaction->due_amount = '0';

                                  // account details
                                  $account_details->paid_amount = $request->estimated_amount;
                                  $account_details->due_amount = '0';
                                  if($updateBalance){
                                      foreach ($updateBalance as $value) {
                                          $value->balance -= $request->estimated_amount;
                                          $value->save();
                                      }
                                     }
                            } elseif ($request->paid_status == 'full_due') {
                                $payment->paid_amount = '0';
                                $payment->due_amount = $request->estimated_amount;
                                $payment_details->current_paid_amount = '0';

                                //transaction
                                $transaction->paid_amount = '0';
                                $transaction->due_amount = $request->estimated_amount;

                                 // account details
                                 $account_details->paid_amount = '0';
                                 $account_details->due_amount = $request->estimated_amount;
                                 if($updateBalance){
                                     foreach ($updateBalance as $value) {
                                         $value->balance += $request->estimated_amount;
                                         $value->save();
                                     }
                                    }
                            } elseif ($request->paid_status == 'partial_paid') {
                                $payment->paid_amount = $request->paid_amount;
                                $payment->due_amount = $request->estimated_amount - $request->paid_amount;
                                $payment_details->current_paid_amount = $request->paid_amount;

                                // transaction
                                $transaction->paid_amount = $request->paid_amount;
                                $transaction->due_amount = $request->estimated_amount - $request->paid_amount;

                                 // account details
                                 $account_details->paid_amount = $request->paid_amount;
                                 $account_details->due_amount = $request->estimated_amount - $request->paid_amount;
                                 $account_details->balance = $previousBalance + ($request->estimated_amount - $request->paid_amount);
                                 if($updateBalance){
                                     foreach ($updateBalance as $value) {
                                         $value->balance -= $request->paid_amount;
                                         $value->save();
                                     }
                                    }
                            }
                        }


                        $payment->update();

                        $payment_details->invoice_id = $invoice->id;
                        $payment_details->date = date('Y-m-d', strtotime($request->date));
                        $payment_details->update();
                        $account_details->save();
                        $transaction->save();
                    }
                });
            } //end else

        }
        $notification = array(
            'message' => 'Invoice Data Updated Successfully',
            'alert-type' => 'success',
        );
        return redirect()->route('invoice.all')->with($notification);
    }
    
    public function InvoiceDelete($id)
    {
        $invoice = Invoice::findOrFail($id);
        $accountInvoice = AccountDetail::where('invoice_id', $invoice->id)->first();
        if ($accountInvoice != null) {
            $accountInvoice->delete();
        }
        $invoice->delete();
        InvoiceDetail::where('invoice_id', $invoice->id)->delete();
        Payment::where('invoice_id', $invoice->id)->delete();
        PaymentDetail::where('invoice_id', $invoice->id)->delete();
        Transaction::where('invoice_id', $invoice->id)->delete();

        $notification = array(
            'message' => 'Invoice Data Deleted Successfully',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    } //end method

    public function InvoicePrint($id)
    {
        $invoice = Invoice::with('invoice_details')->findOrFail($id);
        $vat_invoice = VatChalan::where('invoice_id', $id)->first();
        return view('admin.pdf.invoice_pdf', compact('invoice', 'vat_invoice'));
    } //end method


    public function ChalanPrint($id)
    {
        $invoice = Invoice::with('invoice_details')->findOrFail($id);
        return view('admin.chalan.chalan_pdf', compact('invoice'));
    } //end method




    // local customer invoice all method
    public function InvoiceAllLocal()
    {
        $allData = Invoice::orderBy('date', 'desc')->orderBy('invoice_no', 'desc')->where('status', '0')->get();
        return view('admin.invoice.local_customer.invoice_all', compact('allData'));
    }

    public function ChalanAllLocal()
    {
        $allData = Invoice::orderBy('date', 'desc')->orderBy('invoice_no', 'desc')->where('status', '0')->get();
        return view('admin.chalan.chalan_all', compact('allData'));
    }


    public function InvoiceAddLocal()
    {
        $invoice_data = Invoice::orderBy('id', 'desc')->first();
        if ($invoice_data == null) {
            $firstReg = '0';
            $invoice_no = $firstReg + 1;
        } else {
            $invoice_data = Invoice::orderBy('id', 'desc')->first()->invoice_no;
            $invoice_no = $invoice_data + 1;
        }
        $date = date('Y-m-d');
        $companies = Company::where('status', '0')->get();
        $categories = Category::orderBy('name', 'asc')->get();
        return view('admin.invoice.local_customer.invoice_add', compact('invoice_no', 'companies', 'date', 'categories'));
    } //end method

    public function InvoiceStoreLocal(Request $request)
    {
        // dd($request->all());

        if ($request->category_id == null) {
            $notification = array(
                'message' => 'Sorry, you do not any category',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        } else {
            if ($request->paid_amount > $request->estimated_amount) {
                $notification = array(
                    'message' => 'Sorry, Paid amount is maximum the total amount',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            } else {

                // generate invoice by company wise
                $count_com = count($request->company_id);
                for ($i = 0; $i < $count_com; $i++) {
                    // $get_company_id = $request->company_id[$i];

                    if ($request->company_id[$i] == '0') {
                        $company = new Company();
                        $company->name = $request->customer_name;
                        $company->phone = $request->customer_phone;
                        $company->email = $request->customer_email;
                        $company->status = '0';
                        // $company->company_id = $request->company_id;
                        $company->save();
                        $get_company_id = $company->id;
                        break;
                    } else {
                        $get_company_id = $request->company_id[$i];
                    }
                }

                $currentYear = date('Y');
                $invoice_data = Invoice::orderBy('id', 'desc')->first();
                if ($invoice_data == null) {
                    $firstReg = '0';
                    $invoice_no = $firstReg + 1;

                    $inv_no_gen = 'INV-'  . $currentYear . '-' . $get_company_id  . '-' . $invoice_no;
                } else {
                    $invoice_number = Invoice::where('company_id', $get_company_id)->latest()->first();
                    if ($invoice_number == null) {

                        $invoice_data = Invoice::orderBy('id', 'desc')->first()->invoice_no;
                        $invoice_no = $invoice_data + 1;

                        $inv_no_gen = 'INV-' . $currentYear . '-' . $get_company_id  . '-' . '1';
                    } else {
                        $current_inv_no =  $invoice_number->invoice_id;
                        $invoice_no = $current_inv_no + 1;
                        $invoice_no_gen = $invoice_number->invoice_no_gen;
                        $in = explode("-", $invoice_no_gen, 5);
                        $invo_no = $in[3];
                        $inv_no_gen = 'INV-' . $currentYear . '-' . $get_company_id . '-' . $invo_no + 1;
                    }
                }
    
           
                $invoice = new Invoice();
                $invoice->invoice_no = $invoice_no;
                $invoice->invoice_no_gen = $inv_no_gen;
                $invoice->company_id = $get_company_id;
                $invoice->date = date('Y-m-d', strtotime($request->date));
                $invoice->po_number = $request->po_number;
                $invoice->status = '0';
                $invoice->created_by = Auth::user()->id;


                DB::transaction(function () use ($request, $invoice, $get_company_id) {
                    if ($invoice->save()) {
                        $count_category = count($request->category_id);

                        for ($i = 0; $i < $count_category; $i++) {

                            $invoice_details = new InvoiceDetail();
                            $invoice_details->date = date('Y-m-d', strtotime($request->date));
                            $invoice_details->invoice_id = $invoice->id;
                            $invoice_details->invoice_no_gen = $invoice->invoice_no_gen;
                            // $invoice_details->company_id = $request->company_id[$i];
                            $invoice_details->company_id = $get_company_id;
                            $invoice_details->category_id = $request->category_id[$i];
                            $invoice_details->sub_cat_id = $request->sub_cat_id[$i];
                            $invoice_details->product_name = $request->product_name[$i];
                            $invoice_details->description = $request->description[$i];
                            $invoice_details->selling_qty = $request->selling_qty[$i];
                            $invoice_details->unit_price = $request->unit_price[$i];
                            $invoice_details->selling_price = $request->selling_price[$i];
                            $invoice_details->size_length = $request->size_length[$i];
                            $invoice_details->size_width = $request->size_width[$i];
                            $invoice_details->side = $request->side[$i];
                            $invoice_details->status = '0';
                            $invoice_details->save();
                        }




                        $payment = new Payment();
                        $payment_details = new PaymentDetail();
                        $account_details = new AccountDetail();
                        $payment->invoice_id = $invoice->id;
                        $payment->company_id = $invoice_details->company_id;
                        $payment->paid_status = $request->paid_status;
                        $payment->due_amount = $request->due_amount;
                        $payment->discount_amount = $request->discount_amount;
                        $payment->total_amount = $request->estimated_amount;

                        $latestBalance = AccountDetail::where('company_id', $request->company_id)->latest('id')->first();

                        // account details
                        $account_details->invoice_id = $invoice->id;
                        $account_details->company_id = $invoice_details->company_id;
                        $account_details->total_amount = $request->estimated_amount;
                        $account_details->date = date('Y-m-d', strtotime($request->date));

                         // transaction
                         $transaction = new Transaction();
                         $transaction->date = date('Y-m-d', strtotime($request->date));
                         $transaction->invoice_id = $invoice->id;
                         $transaction->party_name = Company::findOrFail($invoice->company_id)->name;
                         $transaction->bill_no = $invoice->invoice_no_gen;
                         $transaction->paid_by = $request->paid_source;
                         $transaction->type = 'sales';
                         $transaction->updated_at = NULL;



                        if ($request->paid_status == 'full_paid') {
                            $payment->paid_amount = $request->estimated_amount;
                            $payment->due_amount = '0';
                            $payment_details->current_paid_amount = $request->estimated_amount;

                            // transaction
                            $transaction->paid_amount = $request->estimated_amount; 
                            $transaction->due_amount = '0';

                            // account details
                            $account_details->paid_amount = $request->estimated_amount;
                            $account_details->due_amount = 0;
                            $account_details->balance = $latestBalance->balance ?? 0;
                        } elseif ($request->paid_status == 'full_due') {
                            $payment->paid_amount = 0;
                            $payment->due_amount = $request->estimated_amount;
                            $payment_details->current_paid_amount = 0;

                            // transaction
                            $transaction->paid_amount = 0;  
                            $transaction->due_amount = $request->estimated_amount;


                            //account details
                            $account_details->paid_amount = 0;
                            $account_details->due_amount = $request->estimated_amount;
                            $account_details->balance = ($latestBalance->balance ?? 0) + $request->estimated_amount;
                        } elseif ($request->paid_status == 'partial_paid') {
                            $payment->paid_amount = $request->paid_amount;
                            $payment->due_amount = $request->estimated_amount - $request->paid_amount;
                            $payment_details->current_paid_amount = $request->paid_amount;

                            // transaction
                            $transaction->paid_amount = $request->paid_amount;  
                            $transaction->due_amount = $request->estimated_amount - $request->paid_amount;


                            //account details
                            $account_details->paid_amount = $request->paid_amount;
                            $account_details->due_amount = $request->estimated_amount - $request->paid_amount;
                            $account_details->balance = ($latestBalance->balance ?? 0) + ($request->estimated_amount - $request->paid_amount);
                            
                        }

                        if ($request->paid_source == 'check' || $request->paid_source == 'online-banking') {
                            $payment->paid_source = $request->check_or_banking;
                        } else {
                            $payment->paid_source = $request->paid_source;
                        }

                        $payment->save();

                        $payment_details->invoice_id = $invoice->id;
                        $payment_details->date = date('Y-m-d', strtotime($request->date));
                        $payment_details->save();
                        $account_details->save();
                        $transaction->save();
                    }
                });
            } //end else

        }
        $notification = array(
            'message' => 'Invoice Data Inserted Successfully',
            'alert-type' => 'success',
        );
        return redirect()->route('invoice.all.local')->with($notification);
    } //end method
    
    
     public function InvoiceEditLocal($id)
    {

        $invoiceInfo = Invoice::findOrFail($id);
        $companies = Company::where('status', '0')->get();
        $categories = Category::orderBy('name', 'asc')->get();
        $invoiceDetails = InvoiceDetail::where('invoice_id', $id)->get();
        $subCategories = SubCategory::get();
        $payment = Payment::where('invoice_id', $id)->first();
        return view('admin.invoice.local_customer.invoice_edit', compact('invoiceInfo', 'companies', 'invoiceDetails', 'categories', 'subCategories', 'payment'));
    }

    public function InvoiceUpdateLocal(Request $request)
    {
        $invoiceID = $request->id;
        if ($request->category_id == null) {
            $notification = array(
                'message' => 'Sorry, you do not any category',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        } else {
            if ($request->paid_amount > $request->estimated_amount) {
                $notification = array(
                    'message' => 'Sorry, Paid amount is maximum the total amount',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            } else {

                $invoice = Invoice::findOrFail($invoiceID);
                $invoice->company_id = $request->company_id[0];
                $invoice->date = date('Y-m-d', strtotime($request->date));
                $invoice->updated_by = Auth::user()->id;
                $invoice->update();


                InvoiceDetail::where('invoice_id', $invoiceID)->delete();
                DB::transaction(function () use ($request, $invoice) {
                    if ($invoice->save()) {
                        $count_category = count($request->category_id);

                        for ($i = 0; $i < $count_category; $i++) {

                            $invoice_details = new InvoiceDetail();
                            $invoice_details->date = date('Y-m-d', strtotime($request->date));
                            $invoice_details->invoice_id = $invoice->id;
                            $invoice_details->invoice_no_gen = $invoice->invoice_no_gen;
                            $invoice_details->company_id = $request->company_id[$i];
                            $invoice_details->category_id = $request->category_id[$i];
                            $invoice_details->sub_cat_id = $request->sub_cat_id[$i];
                            $invoice_details->product_name = $request->product_name[$i];
                            $invoice_details->description = $request->description[$i];
                            $invoice_details->selling_qty = $request->selling_qty[$i];
                            $invoice_details->unit_price = $request->unit_price[$i];
                            $invoice_details->selling_price = $request->selling_price[$i];
                            $invoice_details->size_length = $request->size_length[$i];
                            $invoice_details->size_width = $request->size_width[$i];
                            $invoice_details->side = $request->side[$i];
                            $invoice_details->status = '0';
                            $invoice_details->save();
                        }




                        $payment = Payment::where('invoice_id', $invoice->id)->first();
                        $payment_details = PaymentDetail::where('invoice_id', $invoice->id)->first();
                        $account_details = AccountDetail::where('invoice_id', $invoice->id)->first();
                        $payment->company_id = $invoice_details->company_id;
                        $payment->paid_status = $request->paid_status;
                        $payment->due_amount = $request->due_amount;
                        $payment->discount_amount = $request->discount_amount;
                        $payment->sub_total = $request->sub_total;
                        $payment->total_amount = $request->estimated_amount;

                        // transaction
                        $transaction = Transaction::where('invoice_id', $invoice->id)->first();
                        if ($transaction) {
                            $transaction->date = date('Y-m-d', strtotime($request->date));
                            $transaction->invoice_id = $invoice->id;
                            $transaction->party_name = Company::findOrFail($request->company_id)->name;
                            $transaction->paid_by = $request->paid_source;
                            $transaction->type = 'sales';
                        }


                        $account_details->company_id = $invoice_details->company_id;
                        $account_details->total_amount = $request->estimated_amount;
                        $account_details->date = date('Y-m-d', strtotime($request->date));

                        $updateBalance = AccountDetail::where('id', '>', $account_details->id)->where('company_id', $invoice_details->company_id)->get();


                        $previousBalance = AccountDetail::where('id', '<', $account_details->id)->where('company_id', $invoice_details->company_id)->latest('id')->first();

                        if ($request->paid_status == 'full_paid') {
                            $payment->paid_amount = $request->estimated_amount;
                            $payment->due_amount = '0';
                            $payment_details->current_paid_amount = $request->estimated_amount;

                            // transaction
                            $transaction->paid_amount = $request->estimated_amount;
                            $transaction->due_amount = '0';

                             // account details
                             $account_details->paid_amount = $request->estimated_amount;
                             $account_details->due_amount = '0';

                             $account_details->balance = $previousBalance->balance ?? 0;

                             // dd($account_details->balance);


                             if($updateBalance != NULL){
                                 $previous_balance = $account_details->balance ?? 0;
                                 foreach ($updateBalance as $value) {
                                     $value->balance = $previous_balance + $value->due_amount;
                                     $value->save();
                                     $previous_balance = $value->balance;
                                 }
                             }
                        } elseif ($request->paid_status == 'full_due') {
                            $payment->paid_amount = '0';
                            $payment->due_amount = $request->estimated_amount;
                            $payment_details->current_paid_amount = '0';

                            //transaction
                            $transaction->paid_amount = '0';
                            $transaction->due_amount = $request->estimated_amount;


                            // account details
                            $account_details->paid_amount = '0';
                            $account_details->due_amount = $request->estimated_amount;
                            $account_details->balance = $request->estimated_amount + $previousBalance->balance;
                            if($updateBalance != NULL){
                                $previous_balance = $account_details->balance;
                                foreach ($updateBalance as $value) {
                                    $value->balance = $previous_balance + $value->due_amount;
                                    $value->save();
                                    $previous_balance = $value->balance;
                                }
                            }
                        } elseif ($request->paid_status == 'partial_paid') {
                            $payment->paid_amount = $request->paid_amount;
                            $payment->due_amount = $request->estimated_amount - $request->paid_amount;
                            $payment_details->current_paid_amount = $request->paid_amount;

                            // transaction
                            $transaction->paid_amount = $request->paid_amount;
                            $transaction->due_amount = $request->estimated_amount - $request->paid_amount;


                            // account details
                            $account_details->paid_amount = $request->paid_amount;
                            $account_details->due_amount = $request->estimated_amount - $request->paid_amount;
                            $account_details->balance = ($previousBalance->balance ?? 0) + ($request->estimated_amount - $request->paid_amount);

                            if ($updateBalance != NULL) {
                                $previous_balance = $account_details->balance; 
                                foreach ($updateBalance as $key => $value) {
                                    $value->balance = $previous_balance + $value->due_amount; 
                                    $value->save();

                                    $previous_balance = $value->balance;
                                }
                            }
                        }

                        if ($request->paid_source == 'check' || $request->paid_source == 'online-banking') {
                            $payment->paid_source = $request->check_or_banking;
                        } else {
                            $payment->paid_source = $request->paid_source;
                        }

                        $payment->save();

                        $payment_details->date = date('Y-m-d', strtotime($request->date));
                        $payment_details->save();
                        $account_details->save();
                        $transaction->save();
                    }
                });
            } //end else

        }
        $notification = array(
            'message' => 'Invoice Updated Successfully',
            'alert-type' => 'success',
        );
        return redirect()->route('invoice.all.local')->with($notification);
    }



    public function InvoicePrintLocal($id)
    {

        $invoice = Invoice::with('invoice_details')->findOrFail($id);

        $data = InvoiceDetail::select('product_name')->where('invoice_id', $invoice->id)->get()->groupBy('product_name');
        // dd($data);

        return view('admin.pdf.invoice_pdf_local', compact('invoice', 'data'));
    } //end method

    // invoice report all method
    public function DailyInvoiceReport()
    {
        return view('admin.invoice.daily_invoice_report');
    } //end method

    public function DailyInvoiceReportPdf(Request $request)
    {
        $sdate = date('Y-m-d', strtotime($request->start_date));
        $edate = date('Y-m-d', strtotime($request->end_date));
        $allData = Invoice::whereBetween('date', [$sdate, $edate])->get();
        return view('admin.pdf.daily_invoice_report_pdf', compact('allData', 'sdate', 'edate'));
        // dd($allData);
    }

    public function UniqueNumber()
    {
        $vat_invoice_no = VatChalan::latest()->first();
        if ($vat_invoice_no) {
            $name = $vat_invoice_no->invoice_no;
            $vat_invoice_no = str_pad((int)$name + 1, 2, "0", STR_PAD_LEFT);
        } else {
            $vat_invoice_no = '01';
        }
        return $vat_invoice_no;
    }
}
