<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\PurchaseCategory;
use App\Models\PurchaseSubCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PurchaseSubCategoryController extends Controller
{
    public function AllPurchaseSubCategory()
    {
        $subcatAll = PurchaseSubCategory::all();
        return view('admin.purchase_sub_category.purchase_sub_category_all', compact('subcatAll'));
    }


    public function AddPurchaseSubCategory()
    {
        $categories = PurchaseCategory::all();
        $units = Unit::orderBy('name', 'asc')->get();
        return view('admin.purchase_sub_category.purchase_sub_category_add', compact('categories', 'units'));
    }

    public function StorePurchaseSubCategory(Request $request)
    {
        PurchaseSubCategory::insert([
            'name' => $request->name,
            'unit_id' => $request->unit_id,
            'category_id' => $request->category_id,
            'created_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);
        $notification = array([
            'message' => 'Sub Category Inserted Successfully',
            'alert_type' => 'success',
        ]);
        return redirect()->route('all.purchase.sub.category')->with($notification);
    }

    public function EditPurchaseSubCategory($id)
    {
        $category = PurchaseCategory::all();
        $sub_cat = PurchaseSubCategory::findOrFail($id);
        $units = Unit::all();
        return view('admin.purchase_sub_category.purchase_sub_category_edit', compact('category', 'sub_cat','units'));
    }

    public function UpdatePurchaseSubCategory(Request $request)
    {
        $sub_cat_id = $request->id;
        PurchaseSubCategory::findOrFail($sub_cat_id)->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
             'unit_id' => $request->unit_id,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);

        $notification = array([
            'message' => 'Sub Category Updated Successfully',
            'alert_type' => 'success',
        ]);
        return redirect()->route('all.purchase.sub.category')->with($notification);
    }

    public function DeletePurchaseSubCategory($id)
    {
        PurchaseSubCategory::findOrFail($id)->delete();
        $notification = array([
            'message' => 'Sub Category Deleted Successfully',
            'alert_type' => 'success',
        ]);
        return redirect()->back()->with($notification);
    }

    public function GetPurchaseSubCategory(Request $request)
    {
        $category_id = $request->category_id;
        $allSubCat = PurchaseSubCategory::where('category_id', $category_id)->get();
        return response()->json($allSubCat);
    }
}
