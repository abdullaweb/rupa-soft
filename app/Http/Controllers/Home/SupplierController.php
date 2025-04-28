<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierAccountDetail;

class SupplierController extends Controller
{
    public function AllSupplier()
    {
        $suppliers = Supplier::all();
        return view('admin.supplier.all_supplier', compact('suppliers'));
    }

    public function AddSupplier()
    {
        return view('admin.supplier.add_supplier');
    }

    public function StoreSupplier(Request $request)
    {
        $supplier = new Supplier();
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;
        $supplier->opening_balance = $request->opening_balance;
        $supplier->status = 'active';
        $supplier->save();

        // Insert opening balance into supplier account details
        if($request->opening_balance){
            $supplierAccountDetail = new SupplierAccountDetail();
            $supplierAccountDetail->supplier_id = $supplier->id;
            $supplierAccountDetail->date = date('Y-m-d');
            $supplierAccountDetail->balance = $request->opening_balance;
            $supplierAccountDetail->status = 'opening';
            $supplierAccountDetail->approval_status = 'approved';
            $supplierAccountDetail->save();
        }
        

        $notification = array(
            'message' => 'Supplier Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.supplier')->with($notification);
    }

    public function EditSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.supplier.edit_supplier', compact('supplier'));
    }

    public function UpdateSupplier(Request $request)
    {
        $supplier = Supplier::findOrFail($request->id);
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;
        $supplier->opening_balance = $request->opening_balance;
        $supplier->status = 'active';
        $supplier->save();


        // update opening balance into supplier account details
        if($request->opening_balance){
            $supplierAccountDetail = SupplierAccountDetail::where('supplier_id', $request->id)->where('status', 'opening')->first() ?? new SupplierAccountDetail();
            
            $supplierAccountDetail->supplier_id = $request->id;
            $supplierAccountDetail->date = date('Y-m-d');
            $supplierAccountDetail->status = 'opening';
            $supplierAccountDetail->balance = $request->opening_balance;
            $supplierAccountDetail->approval_status = 'approved';
            $supplierAccountDetail->save();
        }

        // If the opening balance is not provided, delete the existing opening balance record
        if($request->opening_balance == null){
            SupplierAccountDetail::where('supplier_id', $request->id)->where('status', 'opening')->delete();
        }

        $notification = array(
            'message' => 'Supplier Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.supplier')->with($notification);
    }

    public function DeleteSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        // Delete the supplier account details associated with this supplier
        SupplierAccountDetail::where('supplier_id', $id)->delete();
        $supplier->delete();


        $notification = array(
            'message' => 'Supplier Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.supplier')->with($notification);
    }


    public function SupplierPaymentDetails($id)
    {
        $supplierInfo = Supplier::findOrFail($id);
        $billDetails = SupplierAccountDetail::where('approval_status', 'approved')->where('supplier_id', $id)->get();
        return view('admin.supplier.supplier_payment_details', compact('supplierInfo', 'billDetails'));
    }

}
