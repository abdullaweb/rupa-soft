<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\PurchaseMeta;
use App\Models\SupplierPaymentDetail;
use App\Models\SupplierAccountDetail;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class PurchaseController extends Controller
{
    public function AllPurchase()
    {
        $allPurchase = Purchase::latest()->get();
        return view('admin.purchase_page.all_purchase', compact('allPurchase'));
    }

    public function AddPurchase()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        $categories = Category::get();
        return view('admin.purchase_page.add_purchase', compact('suppliers', 'categories'));
    }

    public function StorePurchase(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $purchase = new Purchase();
            $purchase->purchase_no = $this->PurchaseNumber();
            $purchase->date = date('Y-m-d', strtotime($request->date));
            $purchase->supplier_id = $request->supplier_id;
            $purchase->total_amount = $request->estimated_amount;
            $purchase->discount_amount = $request->discount_amount;
            $purchase->paid_status = $request->paid_status;
            $purchase->save();

            foreach ($request->product_name as $key => $value) {
                $purchase_meta = new PurchaseMeta();
                $purchase_meta->purchase_id = $purchase->id;
                $purchase_meta->product_name = $request->product_name[$key];
                $purchase_meta->category_id = $request->category_id[$key];
                $purchase_meta->sub_cat_id = $request->sub_cat_id[$key];
                $purchase_meta->description = $request->description[$key];
                $purchase_meta->quantity = $request->selling_qty[$key];
                $purchase_meta->unit_price = $request->unit_price[$key];
                $purchase_meta->save();
            }

            $supplier_payment = new SupplierPaymentDetail();
            $supplier_payment->supplier_id = $request->supplier_id;
            $supplier_payment->purchase_id = $purchase->id;
            $supplier_payment->date = date('Y-m-d', strtotime($request->date));
            $supplier_payment->total_amount = $request->estimated_amount;
            $supplier_payment->balance = $request->estimated_amount - $request->paid_amount;

            $supplier_account_details = new SupplierAccountDetail();
            $supplier_account_details->supplier_id = $request->supplier_id;
            $supplier_account_details->purchase_id = $purchase->id;
            $supplier_account_details->date = date('Y-m-d', strtotime($request->date));
            $supplier_account_details->total_amount = $request->estimated_amount;
            $supplier_account_details->balance = $request->estimated_amount - $request->paid_amount;



            if ($request->paid_status == 'full_paid') {
                $supplier_payment->paid_amount = $request->estimated_amount;
                $supplier_payment->due_amount = '0';

                $supplier_account_details->paid_amount = $request->estimated_amount;
                $supplier_account_details->due_amount = '0';
            } elseif ($request->paid_status == 'full_due') {
                $supplier_payment->paid_amount = '0';
                $supplier_payment->due_amount = $request->estimated_amount;

                $supplier_account_details->paid_amount = '0';
                $supplier_account_details->due_amount = $request->estimated_amount;
            } elseif ($request->paid_status == 'partial_paid') {
                $supplier_payment->paid_amount = $request->paid_amount;
                $supplier_payment->due_amount = $request->estimated_amount - $request->paid_amount;

                $supplier_account_details->paid_amount = $request->paid_amount;
                $supplier_account_details->due_amount = $request->estimated_amount - $request->paid_amount;
            }

            $supplier_payment->save();
            $supplier_account_details->save();

            DB::commit();

            $notification = array(
                'message' => 'Purchase Addedd Successfully',
                'alert_type' => 'success'
            );
    
            return redirect()->route('all.purchase')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error storing purchase: ' . $e->getMessage());

            $notification = array(
                'message' => 'Purchase Add Failed ' . $e->getMessage(),
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }
    }


    public function EditPurchase($id)
    {
        $purchaseInfo = Purchase::findOrFail($id);
        // dd($purchaseInfo);
        $purchaseDetails = PurchaseMeta::where('purchase_id', $id)->get();
        $suppliers = Supplier::where('status', 'active')->get();
        $categories = Category::get();
        return view('admin.purchase_page.edit_purchase', compact('purchaseInfo', 'suppliers', 'categories', 'purchaseDetails'));
    }

    public function resetPurchase($purchaseId)
    {
        $purchase = Purchase::findOrFail($purchaseId);
        $purchase->delete();

        PurchaseMeta::where('purchase_id', $purchaseId)->delete();

        SupplierPaymentDetail::where('purchase_id', $purchaseId)->delete();

        SupplierAccountDetail::where('purchase_id', $purchaseId)->delete();
    }

    public function UpdatePurchase(Request $request)
    {
        // dd($request->all());
        $purchaseId  =  $request->id;

        DB::beginTransaction();
        try {
            $this->resetPurchase($purchaseId);

            $purchase = new Purchase();
            $purchase->purchase_no = $request->purchase_no ? $request->purchase_no : $this->PurchaseNumber();
            $purchase->date = date('Y-m-d', strtotime($request->date));
            $purchase->supplier_id = $request->supplier_id;
            $purchase->total_amount = $request->estimated_amount;
            $purchase->discount_amount = $request->discount_amount;
            $purchase->paid_status = $request->paid_status;
            $purchase->save();

            foreach ($request->product_name as $key => $value) {
                $purchase_meta = new PurchaseMeta();
                $purchase_meta->purchase_id = $purchase->id;
                $purchase_meta->product_name = $request->product_name[$key];
                $purchase_meta->category_id = $request->category_id[$key];
                $purchase_meta->sub_cat_id = $request->sub_cat_id[$key];
                $purchase_meta->description = $request->description[$key];
                $purchase_meta->quantity = $request->selling_qty[$key];
                $purchase_meta->unit_price = $request->unit_price[$key];
                $purchase_meta->save();
            }

            $supplier_payment = new SupplierPaymentDetail();
            $supplier_payment->supplier_id = $request->supplier_id;
            $supplier_payment->purchase_id = $purchase->id;
            $supplier_payment->date = date('Y-m-d', strtotime($request->date));
            $supplier_payment->total_amount = $request->estimated_amount;
            $supplier_payment->balance = $request->estimated_amount - $request->paid_amount;

            $supplier_account_details = new SupplierAccountDetail();
            $supplier_account_details->supplier_id = $request->supplier_id;
            $supplier_account_details->purchase_id = $purchase->id;
            $supplier_account_details->date = date('Y-m-d', strtotime($request->date));
            $supplier_account_details->total_amount = $request->estimated_amount;
            $supplier_account_details->balance = $request->estimated_amount - $request->paid_amount;

            if ($request->paid_status == 'full_paid') {
                $supplier_payment->paid_amount = $request->estimated_amount;
                $supplier_payment->due_amount = '0';

                $supplier_account_details->paid_amount = $request->estimated_amount;
                $supplier_account_details->due_amount = '0';
            } elseif ($request->paid_status == 'full_due') {
                $supplier_payment->paid_amount = '0';
                $supplier_payment->due_amount = $request->estimated_amount;

                $supplier_account_details->paid_amount = '0';
                $supplier_account_details->due_amount = $request->estimated_amount;
            } elseif ($request->paid_status == 'partial_paid') {
                $supplier_payment->paid_amount = $request->paid_amount;
                $supplier_payment->due_amount = $request->estimated_amount - $request->paid_amount;

                $supplier_account_details->paid_amount = $request->paid_amount;
                $supplier_account_details->due_amount = $request->estimated_amount - $request->paid_amount;
            }

            $supplier_payment->save();
            $supplier_account_details->save();

            DB::commit();

            $notification = array(
                'message' => 'Purchase Updated Successfully',
                'alert_type' => 'success'
            );
    
            return redirect()->route('all.purchase')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error storing purchase: ' . $e->getMessage());

            $notification = array(
                'message' => 'Purchase Update Failed ' . $e->getMessage(),
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }
    }

    public function GetPurchase(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;


        if ($start_date == null && $end_date == null) {
            $allPurchase = Purchase::all();
        }

        if ($start_date && $end_date) {
            $startDate = Carbon::parse($start_date)->toDateTimeString();
            $endDate = Carbon::parse($end_date)->toDateTimeString();
            $allPurchase = Purchase::whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->get();
        }

        return view('admin.purchase_page.search_purchase_result', compact('allPurchase', 'start_date', 'end_date',));
    }

    public function StockPurchase(Request $request, $id)
    {
        $purchaseInfo = Purchase::findOrFail($id);
        $purchases = Purchase::all();
        return view('admin.purchase_page.stock_purchase', compact('purchases', 'purchaseInfo'));
    }

    public function UpdateStockPurchase(Request $request)
    {
        $purchase_id = $request->id;

        $purchase = Purchase::findOrFail($request->id);


        $quantity = $purchase->product_qty + $request->product_qty;
        $total = $purchase->total_amount + $request->total_amount;

        Purchase::findOrFail($purchase_id)->update([
            'product_name' => $request->product_name,
            'product_qty' => $quantity,
            'product_price' => $request->product_price,
            'total_amount' => $total,
        ]);

        $notification = array(
            'message' => 'Stock Updated Successfully',
            'alert-type' => 'success',
        );
        return redirect()->route('all.purchase')->with($notification);
    }


    public function StockDeduct()
    {
        $products = PurchaseMeta::distinct()->pluck('product_name');
        return view('admin.purchase_page.deduct_stock', compact('products'));
    }

    public function StockDeductUpdate(Request $request)
    {
        $deduct_id = $request->id;

        $purchase = Purchase::findOrFail($request->id);


        $quantity = $purchase->product_qty - $request->duduct_qty;
        $total =  $purchase->product_price * $quantity;


        Purchase::findOrFail($deduct_id)->update([
            'product_name' => $request->product_name,
            'product_qty' => $quantity,
            'total_amount' => $total,
        ]);

        $notification = array(
            'message' => 'Stock Deduct Successfully',
            'alert-type' => 'success',
        );
        return redirect()->route('all.purchase')->with($notification);
    }

    public function DeletePurchase($id)
    {
        Purchase::findOrFail($id)->delete();

        PurchaseMeta::where('purchase_id', $id)->delete();

        SupplierPaymentDetail::where('purchase_id', $id)->delete();

        $notification = array(
            'message' => 'Purchase Deleted Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }


    public function PurchaseDetails($id)
    {
        $purchaseDetails = PurchaseMeta::where('purchase_id', $id)->get();
        return view('admin.purchase_page.purchase_details', compact('purchaseDetails'));
    }

    public function PurchaseNumber()
    {
        $purchaseNumber = 'PUR-' . date('Y-m') . '-' . Str::random(6);
        return $purchaseNumber;
    }

    // purchase due payment
    public function PurchaseDuePayment($id)
    {
        $purchase = Purchase::findOrFail($id);
        $supplier_payment = SupplierPaymentDetail::where('purchase_id', $id)->latest('id')->first();
        return view('admin.purchase_page.purchase_due_payment', compact('purchase', 'supplier_payment'));
    }

    public function StorePurchaseDuePayment(Request $request){

        $purchase = Purchase::findOrFail($request->id);

        $supplier_payment_details = SupplierPaymentDetail::where('purchase_id', $request->id)->latest('id')->first();

        if ($request->paid_amount > $supplier_payment_details->due_amount) {
            $notification = array(
                'message' => 'Paid Amount is greater than Due Amount',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }

        $supplier_payment = new SupplierPaymentDetail();
        $supplier_payment->supplier_id = $purchase->supplier_id;
        $supplier_payment->purchase_id = $request->id;
        $supplier_payment->date = date('Y-m-d', strtotime($request->date));
        $supplier_payment->paid_amount = $request->paid_amount;
        $supplier_payment->due_amount = $supplier_payment_details->due_amount - $request->paid_amount;
        $supplier_payment->total_amount = $supplier_payment_details->total_amount;
        $supplier_payment->balance = $supplier_payment_details->balance - $request->paid_amount;
        $supplier_payment->status = $request->paid_status;
        $supplier_payment->save();

        $notification = array(
            'message' => 'Purchase Due Payment Added Successfully',
            'alert-type' => 'success',
        );
        return redirect()->route('all.purchase')->with($notification);
    }

    public function PurchasePaymentHistory($id)
    {
        $purchaseInfo = Purchase::findOrFail($id);
        $supplier_payment = SupplierPaymentDetail::where('purchase_id', $id)->get();
        return view('admin.purchase_page.purchase_payment_history', compact('purchaseInfo', 'supplier_payment'));
    }   
    
    public function GetPurchaseDuePaymentHistory(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if ($start_date == null && $end_date == null) {
           $supplier_payment = SupplierPaymentDetail::where('purchase_id', $id)->get();
        }

        if ($start_date && $end_date) {
            $startDate = Carbon::parse($start_date)->toDateTimeString();
            $endDate = Carbon::parse($end_date)->toDateTimeString();
            $supplier_payment = SupplierPaymentDetail::whereBetween('date', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->get();
        }

        return view('admin.purchase_page.search_purchase_due_payment_history', compact('supplier_payment', 'start_date', 'end_date',));
    }
}
