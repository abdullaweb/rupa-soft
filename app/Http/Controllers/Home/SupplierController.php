<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

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
        $supplier->status = 'active';
        $supplier->save();

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
        $supplier->status = 'active';
        $supplier->save();

        $notification = array(
            'message' => 'Supplier Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.supplier')->with($notification);
    }

    public function DeleteSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();


        $notification = array(
            'message' => 'Supplier Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.supplier')->with($notification);
    }

}
