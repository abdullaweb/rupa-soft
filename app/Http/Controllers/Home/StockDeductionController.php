<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseMeta;
use App\Models\StockDeduction;
use App\Models\PurchaseSubCategory;
use App\Models\PurchaseCategory;
use App\Models\StockDeductionDetail;
use App\Models\PurchaseSummery;
use Auth;
use DB;


class StockDeductionController extends Controller
{
   public function AllStockDeduction(){
        $stock_deductions = StockDeduction::all();
        return view('admin.stock_deduction.all_stock_deduction', compact('stock_deductions'));
    }

    public function AddStockDeduction(){
        $categories = PurchaseCategory::get();
        return view('admin.stock_deduction.add_stock_deduction', compact('categories'));
    }

    public function StoreStockDeduction(Request $request){
    
        // dd($request->all());
     DB::beginTransaction();
       try{
            $stock_deduction = new StockDeduction();
            $stock_deduction->deduction_no = $this->DeductionNumberGeneration();
            $stock_deduction->date = $request->date;
            $stock_deduction->total_qty = $request->estimated_qty;   
            $stock_deduction->created_by = Auth::user()->id;
            $stock_deduction->updated_by = NULL;
            $stock_deduction->updated_at = NULL;
            $stock_deduction->save();


            foreach ($request->category_id as $key => $value) {

                if ($request->selling_qty[$key] > $request->stock_qty[$key]) {

                    DB::rollBack();

                    $notification = array(
                        'message' => 'Deducted Quantity Must be Less than Stock Quantity!',
                        'alert-type' => 'error'
                    );
                    return redirect()->back()->with($notification);
                }

                $remaining_qty = $request->selling_qty[$key];
                $purchase_metas = PurchaseMeta::where('sub_cat_id', $request->sub_cat_id[$key])
                                            ->where('current_qty', '>', 0)
                                            ->orderBy('created_at', 'asc')
                                            ->get();

                foreach ($purchase_metas as $purchase_meta) {
                    $stock_deduction_details = new StockDeductionDetail();
                    $stock_deduction_details->deduction_id = $stock_deduction->id;
                    $stock_deduction_details->category_id = $request->category_id[$key];
                    $stock_deduction_details->sub_cat_id = $request->sub_cat_id[$key];
                    $stock_deduction_details->description = $request->description[$key];
                    $stock_deduction_details->created_by = Auth::user()->id;
                    $stock_deduction_details->updated_by = NULL;
    
                    $latest_purchase_summery = PurchaseSummery::where('purchase_sub_cat_id', $request->sub_cat_id[$key])->latest('id')->first();
    
                    $purchase_summery = new PurchaseSummery();
                    $purchase_summery->deduction_id = $stock_deduction->id;
                    $purchase_summery->purchase_sub_cat_id = $request->sub_cat_id[$key];
                    
                    
                    if ($remaining_qty <= 0) {
                        break;
                    }

                    if ($purchase_meta->current_qty >= $remaining_qty) {

                        $purchase_summery->quantity = $remaining_qty;
                        $purchase_summery->unit_price = $purchase_meta->unit_price;
                        $purchase_summery->amount = $remaining_qty * $purchase_meta->unit_price;
                        $purchase_summery->stock = $latest_purchase_summery->stock - $remaining_qty;
                        $purchase_summery->purchase_meta_id = $purchase_meta->id;

                        $stock_deduction_details->quantity = $remaining_qty;
                        $stock_deduction_details->unit_price = $purchase_meta->unit_price;
                        $stock_deduction_details->total_price = $purchase_meta->unit_price * $remaining_qty;

                        $purchase_meta->current_qty -= $remaining_qty;

                        $purchase_summery->save();
                        $stock_deduction_details->save();
                        $purchase_meta->save();
                        $remaining_qty = 0;
                    } else {
                        
                        $stock_deduction_details->quantity = $purchase_meta->current_qty;
                        $stock_deduction_details->unit_price = $purchase_meta->unit_price;
                        $stock_deduction_details->total_price = $purchase_meta->unit_price * $purchase_meta->current_qty;

                        $purchase_summery->unit_price = $purchase_meta->unit_price;
                        $purchase_summery->amount = $purchase_meta->current_qty * $purchase_meta->unit_price;
                        $purchase_summery->quantity = $purchase_meta->current_qty;
                        $purchase_summery->stock = $latest_purchase_summery->stock - $purchase_meta->current_qty;
                        $purchase_summery->purchase_meta_id = $purchase_meta->id;

                        $remaining_qty -= $purchase_meta->current_qty;
                        $purchase_meta->current_qty = 0;

                        $purchase_summery->save();
                        $stock_deduction_details->save();

                        $purchase_meta->save();
                    }
                }
            }

            DB::commit();

            $notification = array(
                'message' => 'Stock Deduction Successfully Done!',
                'alert_type' => 'success'
            );

            return redirect()->route('all.stock.deduction')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error Stock Deduction on line ' . $e->getLine() . ': ' . $e->getMessage());

            $notification = array(
                'message' => 'Stock Deduction Failed ' . $e->getMessage(),
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }
    }


    public function EditStockDeduction($id){
        $stock_deduction = StockDeduction::findOrFail($id);
        $categories = PurchaseCategory::get();
        $stock_deduction_details = StockDeductionDetail::where('deduction_id', $id)
            ->select('category_id', 'sub_cat_id', 'description', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_price) as total_price'))
            ->groupBy('category_id', 'sub_cat_id', 'description')
            ->get();

        return view('admin.stock_deduction.edit_stock_deduction', compact('stock_deduction', 'categories', 'stock_deduction_details'));
    }


    public function resetStockDeduction($id){
        $stock_deduction = StockDeduction::findOrFail($id);

        $stock_deduction_details = StockDeductionDetail::where('deduction_id', $id)->get();

        foreach ($stock_deduction_details as $detail) {
            $purchase_summaries = PurchaseSummery::where('deduction_id', $id)
                ->where('purchase_sub_cat_id', $detail->sub_cat_id)
                ->get();

            foreach ($purchase_summaries as $summary) {
                $purchase_meta = PurchaseMeta::find($summary->purchase_meta_id);
                if ($purchase_meta) {
                    $purchase_meta->current_qty += $summary->quantity;
                    $purchase_meta->save();
                }

                $summary->delete();
            }

            $detail->delete();
        }

        $stock_deduction->delete();
    }

    public function UpdateStockDeduction(Request $request){

        // dd($request->all());

        DB::beginTransaction();

        try {
             $id = $request->stock_deduction_id;
             $this->resetStockDeduction($id);

             $stock_deduction = new StockDeduction();
             $stock_deduction->deduction_no = $request->deduction_no;
             $stock_deduction->date = $request->date;
             $stock_deduction->total_qty = $request->estimated_qty;   
             $stock_deduction->created_by = Auth::user()->id;
             $stock_deduction->updated_by = NULL;
             $stock_deduction->updated_at = NULL;
             $stock_deduction->save();
 
 
             foreach ($request->category_id as $key => $value) {

                if ($request->selling_qty[$key] > $request->stock_qty[$key]) {

                    DB::rollBack();

                    $notification = array(
                        'message' => 'Deducted Quantity Must be Less than Stock Quantity!',
                        'alert-type' => 'error'
                    );
                    return redirect()->back()->with($notification);
                }
                
                 $remaining_qty = $request->selling_qty[$key];
                 $purchase_metas = PurchaseMeta::where('sub_cat_id', $request->sub_cat_id[$key])
                                             ->where('current_qty', '>', 0)
                                             ->get();
 
                 foreach ($purchase_metas as $purchase_meta) {
                     $stock_deduction_details = new StockDeductionDetail();
                     $stock_deduction_details->deduction_id = $stock_deduction->id;
                     $stock_deduction_details->category_id = $request->category_id[$key];
                     $stock_deduction_details->sub_cat_id = $request->sub_cat_id[$key];
                     $stock_deduction_details->description = $request->description[$key];
                     $stock_deduction_details->created_by = Auth::user()->id;
                     $stock_deduction_details->updated_by = NULL;
     
                     $latest_purchase_summery = PurchaseSummery::where('purchase_sub_cat_id', $request->sub_cat_id[$key])->latest('id')->first();
     
                     $purchase_summery = new PurchaseSummery();
                     $purchase_summery->deduction_id = $stock_deduction->id;
                     $purchase_summery->purchase_sub_cat_id = $request->sub_cat_id[$key];
                     
                     
                     if ($remaining_qty <= 0) {
                         break;
                     }
 
                     if ($purchase_meta->current_qty >= $remaining_qty) {
 
                         $purchase_summery->quantity = $remaining_qty;
                         $purchase_summery->unit_price = $purchase_meta->unit_price;
                         $purchase_summery->amount = $remaining_qty * $purchase_meta->unit_price;
                         $purchase_summery->stock = $latest_purchase_summery->stock - $remaining_qty;
                         $purchase_summery->purchase_meta_id = $purchase_meta->id;
 
                         $stock_deduction_details->quantity = $remaining_qty;
                         $stock_deduction_details->unit_price = $purchase_meta->unit_price;
                         $stock_deduction_details->total_price = $purchase_meta->unit_price * $remaining_qty;
 
                         $purchase_meta->current_qty -= $remaining_qty;
 
                         $purchase_summery->save();
                         $stock_deduction_details->save();
                         $purchase_meta->save();
                         $remaining_qty = 0;
                     } else {
                         
                         $stock_deduction_details->quantity = $purchase_meta->current_qty;
                         $stock_deduction_details->unit_price = $purchase_meta->unit_price;
                         $stock_deduction_details->total_price = $purchase_meta->unit_price * $purchase_meta->current_qty;
 
                         $purchase_summery->unit_price = $purchase_meta->unit_price;
                         $purchase_summery->amount = $purchase_meta->current_qty * $purchase_meta->unit_price;
                         $purchase_summery->quantity = $purchase_meta->current_qty;
                         $purchase_summery->stock = $latest_purchase_summery->stock - $purchase_meta->current_qty;
                         $purchase_summery->purchase_meta_id = $purchase_meta->id;
 
                         $remaining_qty -= $purchase_meta->current_qty;
                         $purchase_meta->current_qty = 0;
 
                         $purchase_summery->save();
                         $stock_deduction_details->save();
 
                         $purchase_meta->save();
                     }
                 }
             }
 
             DB::commit();
 
             $notification = array(
                 'message' => 'Stock Deduction Update Done!',
                 'alert_type' => 'success'
             );
 
             return redirect()->route('all.stock.deduction')->with($notification);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error in Stock Deduction Updating on line ' . $e->getLine() . ': ' . $e->getMessage());

            $notification = [
                'message' => 'Stock Deduction Updating Failed: ' . $e->getMessage(),
                'alert-type' => 'error',
            ];

            return redirect()->back()->with($notification);
        }
    }
                                          


    public function DeleteStockDeduction($id)
    {
        DB::beginTransaction();

        try {
            $stock_deduction = StockDeduction::findOrFail($id);

            $deduction_details = StockDeductionDetail::where('deduction_id', $id)->get();

            foreach ($deduction_details as $detail) {
                $purchase_summaries = PurchaseSummery::where('deduction_id', $id)
                    ->where('purchase_sub_cat_id', $detail->sub_cat_id)
                    ->get();

                foreach ($purchase_summaries as $summary) {
                    $purchase_meta = PurchaseMeta::find($summary->purchase_meta_id);
                    if ($purchase_meta) {
                        $purchase_meta->current_qty += $summary->quantity;
                        $purchase_meta->save();
                    }

                    $summary->delete();
                }

                $detail->delete();
            }

            $stock_deduction->delete();

            DB::commit();

            $notification = [
                'message' => 'Stock Deduction Successfully Deleted!',
                'alert-type' => 'success',
            ];

            return redirect()->route('all.stock.deduction')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error in Stock Deduction Deletion on line ' . $e->getLine() . ': ' . $e->getMessage());

            $notification = [
                'message' => 'Stock Deduction Deletion Failed: ' . $e->getMessage(),
                'alert-type' => 'error',
            ];

            return redirect()->back()->with($notification);
        }
    }


    public function DeductionNumberGeneration(){
        $latest_deduction = StockDeduction::latest()->first();
        if($latest_deduction){
            $latest_deduction_no = $latest_deduction->deduction_no;
            $latest_deduction_no = explode('-', $latest_deduction_no);
            $latest_deduction_no = $latest_deduction_no[1];
            $latest_deduction_no = $latest_deduction_no + 1;
            $new_deduction_no = 'DED-'.$latest_deduction_no;
        }else{
            $new_deduction_no = 'DED-1001';
        }
        return $new_deduction_no;
    }


    public function GetStockQuantity(Request $request){
        $sub_cat_id = $request->sub_category;
        $stock_quantity = PurchaseMeta::where('sub_cat_id', $sub_cat_id)->sum('current_qty');
        return response()->json($stock_quantity);
    }

}
