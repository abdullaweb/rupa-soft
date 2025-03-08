<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PurchaseCategoryController extends Controller
{
    public function AllPurchaseCategory()
    {
        $categoryAll = PurchaseCategory::latest()->get();
        return view('admin.purchase_category.purchase_category_all', compact('categoryAll'));
    } //end method

    public function AddPurchaseCategory()
    {

        return view('admin.purchase_category.purchase_category_add');
    } //end method

    public function StorePurchaseCategory(Request $request)
    {
        PurchaseCategory::insert([
            'name' => $request->name,
            'created_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);


        $notification = array(
            'message' => 'Category  Inserted Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.purchase.category')->with($notification);
    }

    public function EditPurchaseCategory($id)
    {
        $categoryInfo = PurchaseCategory::findOrFail($id);
        return view('admin.purchase_category.purchase_category_edit', compact('categoryInfo'));
    }

    public function UpdatePurchaseCategory(Request $request)
    {
        $categoryId  =  $request->id;
        PurchaseCategory::findOrFail($categoryId)->update([
            'name' => $request->name,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Category Updated Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.purchase.category')->with($notification);
    } //end method

    public function DeletePurchaseCategory($id)
    {
        PurchaseCategory::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Category Deleted Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }
}
