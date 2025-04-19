<?php

namespace App\Http\Controllers;

use App\Models\AccountDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Exports\CustomerLedgerExport;
use Maatwebsite\Excel\Facades\Excel;


class LedgerController extends Controller
{
      // Show supplier ledger view
      public function CustomerLedger()
      {
          $customers = Company::all();
          $title = 'Customer Ledger';
          return view('admin.ledger.customer_ledger', compact('customers', 'title'));
      }
  
      // Fetch supplier ledger via AJAX
      public function CustomerfetchLedger(Request $request)
      {
          $customerId = $request->customer_id;
          $customerInfo = Company::findOrFail($customerId);
          // $account = Account::where('name', $customerInfo->name)->first();
          if ($customerInfo) {
              $ledger = AccountDetail::where('company_id', $customerInfo->id)
                  ->get();
              return response()->json(['ledger' => $ledger, 'customer' => $customerInfo]);
          } else {
              return response()->json(['notfound' => null, 'customer' => $customerInfo]);
          }
      }
  
      // Download the supplier ledger as a PDF
      public function CustomerdownloadLedger(Request $request)
      { {
              $customer = Company::find($request->customer_id);
              $ledger = AccountDetail::where('company_id', $request->customer_id)
                  ->get();
  
              if (!$ledger) {
                  return response()->json(['error' => 'No ledger data found'], 404);
              }
  
              $pdf = Pdf::loadView('admin.ledger.customer_ledger_pdf', compact('customer', 'ledger'));
  
              return $pdf->download('admin.ledger.customer_ledger_pdf');
          }
  
          
      }
  
      public function CustomerdownloadLedgerExcel(Request $request)
      {
          $customerId = $request->customer_id;
          $customer = Company::find($customerId);
          return Excel::download(new CustomerLedgerExport($customerId), 'customer_ledger.xlsx');
  
          // return Excel::download(new CustomerLedgerExport($customerId), 'customer_ledger_' . $customer->name . '.xlsx');
      }
  
      public function  PdfView()
      {
          return view('admin.ledger.report.supplier.view_test');
      }
}
