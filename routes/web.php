<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Home\SalaryController;
use App\Http\Controllers\Home\AccountController;
use App\Http\Controllers\Home\CategoryController;
use App\Http\Controllers\Home\CompanyController;
use App\Http\Controllers\Home\EmployeeController;
use App\Http\Controllers\Home\EmployeeSalaryController;
use App\Http\Controllers\Home\InvoiceController;
use App\Http\Controllers\Home\PurchaseController;
use App\Http\Controllers\Home\ReportController;
use App\Http\Controllers\Home\UnitController;
use App\Http\Controllers\Home\AdvancedController;
use App\Http\Controllers\Home\DefaultController;
use App\Http\Controllers\Home\DuePaymentController;
use App\Http\Controllers\Home\TaxController;
use App\Http\Controllers\Home\RoleController;
use App\Http\Controllers\Home\SupplierController;
use App\Http\Controllers\Home\SupplierDuePaymentController;
use App\Http\Controllers\Home\PurchaseCategoryController;
use App\Http\Controllers\Home\PurchaseSubCategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\WastesSaleController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\Home\StockDeductionController;
use App\Models\WastesSale;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/dashboard', function () {
    return view('users.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/logout', [AdminController::class, 'UserLogout'])->name('user.logout');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware('auth', 'role:admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
    Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');
    Route::get('/', [AdminController::class, 'RedirectDashboard']);
    Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
    Route::post('/admin/profile/store', [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store');
    Route::get('/admin/change/password', [AdminController::class, 'ChangeAdminPassword'])->name('change.admin.password');
    Route::post('/update/change/password', [AdminController::class, 'UpdateAdminPassword'])->name('update.admin.password');



    Route::controller(CompanyController::class)->group(function () {
        // compnay all route
        Route::get('/company/all', 'CompanyAll')->name('all.company');
        Route::get('/company/add', 'CompanyAdd')->name('add.company');
        Route::post('/company/store', 'CompanyStore')->name('store.company');
        Route::get('/company/edit/{id}', 'CompanyEdit')->name('edit.company');
        Route::post('/company/update', 'CompanyUpdate')->name('update.company');
        Route::get('/company/delete/{id}', 'CompanyDelete')->name('delete.company');

        Route::get('/company/bill/{id}', 'CompanyBill')->name('company.bill');
        Route::get('/company/bill/delete/{id}', 'CompanyBillDelete')->name('company.bill.delete');
        //credit company
        Route::get('/credit/customer/all', 'CreditCustomer')->name('credit.customer');
        Route::get('/credit/customer/invoice/{invoice_id}', 'EditCreditCustomerInvoice')->name('edit.credit.customer');
        Route::post('/credit/customer/update/invoice/{invoice_id}', 'UpdateCustomerInvoice')->name('customer.update.invoice');
        Route::get('customer/invoice/details/{invoice_id}', 'CustomerInvoiceDetails')->name('customer.invoice.details');



        // all local customer route
        Route::get('/local-customer/all', 'CustomerAll')->name('all.customer');
        Route::post('/customer/store', 'CustomerStore')->name('store.customer');
        Route::get('/local-customer/add', 'CustomerAdd')->name('add.customer');

        //local customer due payment
        Route::get('/local-customer/bill/{id}', 'CompanyBillLocal')->name('company.bill.local');
        Route::get('/local-company/due-payment/{id}', 'LocalCompanyDuePayment')->name('local.company.due.payment');
        Route::post('/store/due-payment', 'LocalCompanyDuePaymentStore')->name('store.due.payment');
        Route::get('/local-company/bill-details/{id}', 'CompanyBillLocalDetails')->name('company.bill.details');
        Route::get('/corporate-company/bill-details/{id}', 'CorporateBillDetails')->name('corporate.bill.details');
        Route::get('/company-dynamic-query', 'CompanyDynamicQuery')->name('company.query');

        //corporate customer due payment
        Route::get('/corporate-customer/bill/{id}', 'CompanyBillCorporate')->name('company.bill.corporate');
        Route::get('/corporate-company/due-payment/{id}', 'CorporateCompanyDuePayment')->name('corporate.due.payment');
        Route::post('/store/corporate-due-payment', 'CorporateCompanyDuePaymentStore')->name('store.corporate.due.payment');
    });

    Route::controller(DuePaymentController::class)->group(function () {
        Route::get('/all/due-payment', 'AllDuePayment')->name('all.due.payment');
        Route::get('/all/corporate-due-payment', 'AllCorporateDuePayment')->name('all.corporate.due.payment');
        Route::get('/add/corporate-due-payment', 'AddCorporateDuePayment')->name('add.corporate.due.payment');
        Route::get('/add/due-payment', 'AddDuePayment')->name('add.due.payment');
        Route::post('/submit/due-payment', 'StoreDuePayment')->name('submit.due.payment');
        Route::get('/edit/due-payment/{id}', 'EditDuePayment')->name('edit.due.payment');
        Route::post('/update/due-payment', 'UpdateDuePayment')->name('update.due.payment');
        Route::get('/delete/due-payment/{id}', 'DeleteDuePayment')->name('delete.due.payment');

        Route::post('/get/due-payment', 'GetDuePayment')->name('get.due.amount');
        Route::get('/due-payment/approval', 'DuePaymentApproval')->name('due.payment.approval');
        Route::get('/due-payment/approval/{id}', 'DuePaymentApprovalNow')->name('due.payment.approval.now');
    });

     // Ledger Controller All Route

     Route::controller(LedgerController::class)->group(function () {
        Route::get('/customer-ledger', 'CustomerLedger')->name('customer.ledger.index');
        Route::post('/customer-ledger/fetch',  'CustomerfetchLedger')->name('customer.ledger.fetch');
        Route::post('/customer-ledger/download',  'CustomerdownloadLedger')->name('customer.ledger.download');
        Route::post('/customer-ledger/download-excel', 'CustomerdownloadLedgerExcel')->name('customer.ledger.download.excel');
        Route::get('/pdf/view', 'PdfView');
    });




    // employee all route
    Route::controller(EmployeeController::class)->group(function () {
        Route::get('/emplopyee/all', 'EmployeeAll')->name('all.employee');
        Route::get('/emplopyee/add', 'EmployeeAdd')->name('add.employee');
        Route::post('/emplopyee/store', 'EmployeeStore')->name('store.employee');
        Route::get('/emplopyee/edit/{id}', 'EmployeeEdit')->name('edit.employee');
        Route::post('/emplopyee/update', 'EmployeeUpdate')->name('update.employee');
        Route::get('/emplopyee/delete/{id}', 'EmployeeDelete')->name('delete.employee');
        Route::get('/emplopyee/view/{id}', 'EmployeeView')->name('employee.view');
    });


    //salary all route
    Route::controller(EmployeeSalaryController::class)->group(function () {
        Route::get('/emplopyee/salary/view/', 'SalaryView')->name('employee.salary.view');
        Route::get('/emplopyee/salary/increment/{id}', 'SalaryIncrement')->name('employee.salary.increment');
        Route::post('/emplopyee/salary/store/{id}', 'SalaryIncrementUpdate')->name('update.employee.salary');
        Route::get('/emplopyee/salary/details/{id}', 'SalaryDetails')->name('employee.salary.details');
        Route::post('/add/over/time/', 'AddOverTime')->name('add.ot.hour');
        Route::get('/emplopyee/salary/monthly', 'AddMonthlySalary')->name('add.monthly.salary');
        Route::post('/get/monthly/salary', 'MonthlySalary')->name('get.monthly.salary');
        Route::get('/ot/details/{id}', 'OtDetails')->name('get.ot.details');

        Route::get('/get/monthly/salary', 'GetCurrentSalary')->name('get.salary');
        Route::post('/add/payment', 'AddPayment')->name('add.payment');
        Route::get('/payment/deatils/{id}', 'PaymentDetails')->name('payment.details');
        Route::get('/pay/slip/{id}', 'PaySlip')->name('pay.slip');

        Route::post('/add/bonus/', 'AddBonus')->name('add.bonus');
    });


    // Category All Route
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/category/all', 'categoryAll')->name('category.all');
        Route::get('/category/add', 'categoryAdd')->name('category.add');
        Route::post('/category/store', 'categoryStore')->name('category.store');
        Route::get('/category/edit/{id}', 'categoryEdit')->name('category.edit');
        Route::post('/category/update', 'categoryUpdate')->name('category.update');
        Route::get('/category/delete/{id}', 'categoryDelete')->name('category.delete');
    });

    // Category All Route
    Route::controller(SubCategoryController::class)->group(function () {
        Route::get('/sub-category/all', 'SubCategoryAll')->name('sub.category.all');
        Route::get('/sub-category/add', 'SubCategoryAdd')->name('sub.category.add');
        Route::post('/sub-category/store', 'SubCategoryStore')->name('sub.category.store');
        Route::get('/sub-category/edit/{id}', 'SubCategoryEdit')->name('sub.category.edit');
        Route::post('/sub-category/update', 'SubCategoryUpdate')->name('sub.category.update');
        Route::get('/sub-category/delete/{id}', 'SubCategoryDelete')->name('sub.category.delete');
        Route::get('/get-sub-category', 'GetSubCategory')->name('get.sub.cat');
    });


    // Unit All Route
    Route::controller(UnitController::class)->group(function () {
        Route::get('/unit/all', 'unitAll')->name('unit.all');
        Route::get('/unit/add', 'unitAdd')->name('unit.add');
        Route::post('/unit/store', 'unitStore')->name('unit.store');
        Route::get('/unit/edit/{id}', 'unitEdit')->name('unit.edit');
        Route::post('/unit/update', 'unitUpdate')->name('unit.update');
        Route::get('/unit/delete/{id}', 'unitDelete')->name('unit.delete');
    });


    // Default All Route
    Route::controller(InvoiceController::class)->group(function () {
        Route::get('/invoice/all', 'InvoiceAll')->name('invoice.all');
        Route::get('/invoice/add', 'InvoiceAdd')->name('invoice.add');
        Route::post('/invoice/store', 'InvoiceStore')->name('invoice.store');
        Route::get('/invoice/edit/{id}', 'InvoiceEdit')->name('invoice.edit');
        Route::post('/invoice/update', 'InvoiceUpdate')->name('invoice.update');
        Route::get('/invoice/print/{id}', 'Invoiceprint')->name('invoice.print');
        Route::get('/invoice/delete/{id}', 'InvoiceDelete')->name('invoice.delete');

        // chalan all route
        Route::get('/chalan/all', 'ChalanAll')->name('chalan.all');
        Route::get('/chalan/all/local', 'ChalanAllLocal')->name('chalan.all.local');
        Route::get('/chalan/print/{id}', 'ChalanPrint')->name('chalan.pdf');
        
         // vat chalan all route
        Route::get('/vat-chalan/all', 'VatChalanAll')->name('vat.chalan.all');
        Route::get('/vat-chalan/print/{id}', 'VatChalanPrint')->name('vat.chalan.print');

        // local customer all route
        Route::get('/invoice/all/local', 'InvoiceAllLocal')->name('invoice.all.local');
        Route::get('/invoice/add/local', 'InvoiceAddLocal')->name('invoice.add.local');
        Route::get('/invoice/print/local/{id}', 'InvoicePrintLocal')->name('invoice.print.local');
        Route::post('/invoice/store/local', 'InvoiceStoreLocal')->name('invoice.store.local');
        Route::get('/invoice/edit/local/{id}', 'InvoiceEditLocal')->name('invoice.edit.local');
        Route::post('/invoice/update/local', 'InvoiceUpdateLocal')->name('invoice.update.local');
        Route::get('/invoice/delete/local/{id}', 'LocalInvoiceDelete')->name('invoice.local.delete');

        // invoice report
        Route::get('daily/invoice/report', 'DailyInvoiceReport')->name('daily.invoice.report');
        Route::get('daily/invoice/pdf', 'DailyInvoiceReportPdf')->name('daily.invoice.pdf');
    });


    // purchase all route
    Route::controller(PurchaseController::class)->group(function () {
        Route::get('/all/purchase', 'AllPurchase')->name('all.purchase');
        Route::get('/add/purchase', 'AddPurchase')->name('add.purchase');
        Route::get('/purchase/details/{id}', 'PurchaseDetails')->name('purchase.details');
        Route::post('/store/purchase', 'StorePurchase')->name('store.purchase');
        Route::get('/edit/purchase/{id}', 'EditPurchase')->name('edit.purchase');
        Route::post('/update/purchases', 'UpdatePurchase')->name('update.purchase');
        Route::get('/delete/purchase/{id}', 'DeletePurchase')->name('delete.purchase');
        Route::post('/get/purchase', 'GetPurchase')->name('get.purchase');

        // purchase due payment
        Route::get('/purchase/due-payment/{id}', 'PurchaseDuePayment')->name('purchase.due.payment');
        Route::post('/store/purchase-due-payment', 'StorePurchaseDuePayment')->name('store.purchase.due.payment');
        Route::get('/purchase/due-payment-history/{id}', 'PurchasePaymentHistory')->name('purchase.due.payment.history');
        Route::post('get/due-payment-history', 'GetPurchaseDuePaymentHistory')->name('get.purchase.due.payment.history');

        Route::get('/stock/purchase/{id}', 'StockPurchase')->name('purchase.stock');
        Route::post('/update/purchase', 'UpdateStockPurchase')->name('update.purchase.stock');
        // stock deduct
        Route::get('/stock/deduct', 'StockDeduct')->name('deduct.stock');
        Route::post('/update/deduct/purchase', 'StockDeductUpdate')->name('update.deduct.stock');
    });

    //Supplier Due Payment
    Route::controller(SupplierDuePaymentController::class)->group(function () {
        Route::get('/all/supplier-due-payment', 'AllDuePayment')->name('all.supplier.due.payment');
        Route::get('/add/supplier-due-payment', 'AddDuePayment')->name('add.supplier.due.payment');
        Route::post('/submit/supplier-due-payment', 'StoreDuePayment')->name('submit.supplier.due.payment');
        Route::get('/edit/supplier-due-payment/{id}', 'EditDuePayment')->name('edit.supplier.due.payment');
        Route::post('/update/supplier-due-payment', 'UpdateDuePayment')->name('update.supplier.due.payment');
        Route::get('/delete/supplier-due-payment/{id}', 'DeleteDuePayment')->name('delete.supplier.due.payment');

        Route::post('/get/supplier-due-payment', 'GetDuePayment')->name('get.supplier.due.amount');
        Route::get('/supplier-due-payment/approval', 'DuePaymentApproval')->name('supplier.due.payment.approval');
        Route::get('/supplier-due-payment/approval/{id}', 'DuePaymentApprovalNow')->name('supplier.due.payment.approval.now');
    });

    //stock deduction
    Route::controller(StockDeductionController::class)->group(function () {
        Route::get('/stock/deduction/all', 'AllStockDeduction')->name('all.stock.deduction');
        Route::get('/stock/deduction/add', 'AddStockDeduction')->name('add.stock.deduction');
        Route::post('/stock/deduction/store', 'StoreStockDeduction')->name('store.stock.deduction');
        Route::get('/stock/deduction/edit/{id}', 'EditStockDeduction')->name('edit.stock.deduction');
        Route::post('/stock/deduction/update', 'UpdateStockDeduction')->name('update.stock.deduction');
        Route::get('/stock/deduction/delete/{id}', 'DeleteStockDeduction')->name('delete.stock.deduction');

        //stock quantity
        Route::get('/get/stock/quantity', 'GetStockQuantity')->name('get.stock.quantity');
    });


    Route::controller(PurchaseCategoryController::class)->group(function () {
        Route::get('/purchase/category/all', 'AllPurchaseCategory')->name('all.purchase.category');
        Route::get('/purchase/category/add', 'AddPurchaseCategory')->name('add.purchase.category');
        Route::post('/purchase/category/store', 'StorePurchaseCategory')->name('store.purchase.category');
        Route::get('/purchase/category/edit/{id}', 'EditPurchaseCategory')->name('edit.purchase.category');
        Route::post('/purchase/category/update', 'UpdatePurchaseCategory')->name('update.purchase.category');
        Route::get('/purchase/category/delete/{id}', 'DeletePurchaseCategory')->name('delete.purchase.category'); 
    });


    
    Route::controller(PurchaseSubCategoryController::class)->group(function () {
        Route::get('/purchase/sub/category/all', 'AllPurchaseSubCategory')->name('all.purchase.sub.category');
        Route::get('/purchase/sub/category/add', 'AddPurchaseSubCategory')->name('add.purchase.sub.category');
        Route::post('/purchase/sub/category/store', 'StorePurchaseSubCategory')->name('store.purchase.sub.category');
        Route::get('/purchase/sub/category/edit/{id}', 'EditPurchaseSubCategory')->name('edit.purchase.sub.category');
        Route::post('/purchase/sub/category/update', 'UpdatePurchaseSubCategory')->name('update.purchase.sub.category');
        Route::get('/purchase/sub/category/delete/{id}', 'DeletePurchaseSubCategory')->name('delete.purchase.sub.category');
        Route::get('/get-purchase-sub-category', 'GetPurchaseSubCategory')->name('get.purchase.sub.cat');
    });


    // wastage sale route
    Route::controller(WastesSaleController::class)->group(function () {
        Route::get('/all/wastes/sale', 'AllWastesSale')->name('all.wastes.sale');
        Route::get('/add/wastes/sale', 'AddWastesSale')->name('add.wastes.sale');
        Route::post('/store/wastes/sale', 'StoreWastesSale')->name('store.wastes.sale');
        Route::get('/edit/wastes/sale/{id}', 'EdtWastesSale')->name('edit.wastes.sale');
        Route::post('/update/wastes/sale', 'UpdateWastesSale')->name('update.wastes.sale');
    });

    // supplier all route
    Route::controller(SupplierController::class)->group(function () {
        Route::get('/all/supplier', 'AllSupplier')->name('all.supplier');
        Route::get('/add/supplier', 'AddSupplier')->name('add.supplier');
        Route::post('/store/supplier', 'StoreSupplier')->name('store.supplier');
        Route::get('/edit/supplier/{id}', 'EditSupplier')->name('edit.supplier');
        Route::post('/update/supplier', 'UpdateSupplier')->name('update.supplier');
        Route::get('/delete/supplier/{id}', 'DeleteSupplier')->name('delete.supplier');

        //supplier payment details
        Route::get('/supplier/payment/details/{id}', 'SupplierPaymentDetails')->name('supplier.payment.details');
    });


    // account all route
    Route::controller(AccountController::class)->group(function () {
        Route::get('/all/expense', 'AllExpense')->name('all.expense');
        Route::get('/add/expense', 'AddExpense')->name('add.expense');
        Route::post('/store/expense', 'StoreExpense')->name('store.expense');
        Route::get('/edit/expense/{id}', 'EditExpense')->name('edit.expense');
        Route::post('/update/expense}', 'UpdateExpense')->name('update.expense');
        Route::get('/delete/expense/{id}', 'DeleteExpense')->name('delete.expense');
        Route::get('/daily/expense', 'DailyExpense')->name('daily.expense');
        Route::get('/monthly/expense', 'MonthlyExpense')->name('monthly.expense');
        Route::get('/yearly/expense', 'YearlyExpense')->name('yearly.expense');
        Route::post('/get/expense', 'GetExpense')->name('get.expense');

        // account details filtering method
        Route::post('/get/account/details', 'GetAccountDetails')->name('get.account.detail');

        //profit calculation
        Route::get('/calculate/profit', 'AddProfit')->name('add.profit');
        Route::post('/get/profit', 'GetProfit')->name('get.profit');


        // opening balance
        Route::get('all/opening/balance', 'AllOpeningBalance')->name('all.opening.balance');
        Route::get('add/opening/balance', 'AddOpeningBalance')->name('add.opening.balance');
        Route::post('store/opening/balance', 'StoreOpeningBalance')->name('store.opening.balance');
        Route::get('edit/opening/balance/{id}', 'EditOpeningBalance')->name('edit.opening.balance');
        Route::get('delete/opening/balance/{id}', 'DeleteOpeningBalance')->name('delete.opening.balance');
        Route::post('update/opening/balance', 'UpdateOpeningBalance')->name('update.opening.balance');
    });
    
     // Tax all route
    Route::controller(TaxController::class)->group(function () {
        Route::get('/all/vat', 'AllTax')->name('all.tax');
        Route::get('/add/vat', 'AddTax')->name('add.tax');
        Route::post('/store/vat', 'StoreTax')->name('store.tax');
        Route::get('/edit/vat/{id}', 'EditTax')->name('edit.tax');
        Route::post('/update/vat}', 'UpdateTax')->name('update.tax');
        Route::get('/delete/tax/{id}', 'DeleteTax')->name('delete.tax');
        Route::get('/deactive/vat/{id}', 'DeactiveTax')->name('deactive.tax');
        Route::get('/active/vat/{id}', 'ActiveTax')->name('active.tax');
        Route::get('/get/vat/{id}', 'GetTaxPercentage')->name('get.tax.percentage');
    });



    // advance all route
    Route::controller(AdvancedController::class)->group(function () {
        Route::get('/all/advanced/salary', 'AllAdvancedSalary')->name('all.advanced.salary');
        Route::get('/add/advanced/salary', 'AddAdvancedSalary')->name('add.advanced.salary');
        Route::post('/store/advanced/salary', 'StoreAdvancedSalary')->name('store.advanced.salary');
        Route::get('/advanced/salary/{id}', 'EditAdvancedSalary')->name('edit.advanced.salary');
        Route::post('/update/salary', 'UpdateAdvancedSalary')->name('update.advanced.salary');
        Route::get('delete/advanced-salary/{id}', 'DeleteAdvancedSalary')->name('delete.advanced.salary');
    });

    // pay salary all route
    Route::controller(SalaryController::class)->group(function () {
        Route::get('/pay/salary', 'PaySalary')->name('pay.salary');
        Route::get('/pay/salary/{id}', 'PaySalaryNow')->name('pay.salary.now');
        Route::post('/store/salary', 'StorePaySalary')->name('pay.salary.store');

        // add slary
        Route::get('/add/salary', 'AddSalary')->name('add.salary');
        // Route::get('/store/salary', 'StoreSalary')->name('store.salary');

        // overtimes all routes
        Route::get('/all/overtime', 'AllOvertime')->name('all.overtime');
        Route::get('/add/overtime', 'AddOvertime')->name('add.overtime');
        Route::post('/store/overtime', 'StoreOvertime')->name('store.overtime');
        Route::get('/edit/overtime/{id}', 'EditOvertime')->name('edit.overtime');
        Route::post('/update/overtime', 'UpdateOvertime')->name('update.overtime');
        Route::get('/delete/overtime/{id}', 'DeleteOvertime')->name('delete.overtime');


        // overtimes all routes
        Route::get('/all/bonus', 'AllBonus')->name('all.bonus');
        Route::get('/add/bonus', 'AddBonus')->name('add.bonus');
        Route::post('/store/bonus', 'StoreBonus')->name('store.bonus');
        Route::get('/edit/bonus/{id}', 'EditBonus')->name('edit.bonus');
        Route::post('/update/bonus', 'UpdateBonus')->name('update.bonus');
        Route::get('/delete/bonus/{id}', 'DeleteBonus')->name('delete.bonus');


        // payment details
        Route::get('/payment/details/{id}', 'EmployeePaymentDetails')->name('employee.payment.details');
    });

    // category report method
    Route::controller(ReportController::class)->group(function () {
        Route::get('category/report', 'CategoryReport')->name('category.report');
        Route::post('/get/category-report', 'GetCategoryReport')->name('get.cat.report');
        Route::get('/category-report/summary', 'GetCategoryReportSummary')->name('get.cat.report.summary');
        Route::post('/print/category/summary', 'PrintCategorySummary')->name('get.cat.report.summary.print');

        Route::get('/invoice/report', 'InvoiceReport')->name('invoice.report');
        Route::post('/get/invoice/report', 'GetInvoiceReport')->name('get.invoice.report');

        Route::get('/purchase/summery/report', 'PurchaseSummeryReport')->name('purchase.summery.report');
        Route::post('/get/purchase/summery/report', 'GetPurchaseSummeryReport')->name('get.purchase.summery.report');

        Route::get('/purchase/report', 'PurchaseReport')->name('purchase.report');
        Route::post('/get/purchase/report', 'GetPurchaseReport')->name('get.purchase.report');

    });


    // category report method
    Route::controller(DefaultController::class)->group(function () {
        Route::get('get/salary', 'GetEmployeeSalary')->name('get.employee.salary');
        Route::get('get/advance', 'GetEmployeeAdvance')->name('get.employee.advance');
    });


    // permission all route
    Route::controller(RoleController::class)->group(function () {
        Route::get('/all/permission', 'AllPermission')->name('all.permission');
        Route::get('/add/permission', 'AddPermission')->name('add.permission');
        Route::post('/store/permission', 'StorePermission')->name('store.permission');
        Route::post('/update/permission', 'UpdatePermission')->name('update.permission');
        Route::get('/edit/permission/{id}', 'EditPermission')->name('edit.permission');
        Route::get('/delete/permission/{id}', 'DeletePermission')->name('delete.permission');
    });

    // role  all route
    Route::controller(RoleController::class)->group(function () {
        Route::get('/all/role', 'AllRole')->name('all.role');
        Route::get('/add/role', 'AddRole')->name('add.role');
        Route::post('/store/role', 'StoreRole')->name('store.role');
        Route::post('/update/role', 'UpdateRole')->name('update.role');
        Route::get('/edit/role/{id}', 'EditRole')->name('edit.role');
        Route::get('/delete/role/{id}', 'DeleteRole')->name('delete.role');

        // role in permission
        Route::get('/add/role/permission', 'AddRolepermission')->name('add.role.permission');
        Route::get('/all/role/permission', 'AllRolepermission')->name('all.role.permission');
        Route::post('/store/role/permission', 'StoreRolepermission')->name('role.permission.store');
        Route::get('admin/edit/role/{id}', 'AdminEditRole')->name('admin.edit.role');
        Route::post('/admin/role/update/{id}', 'AdminUpdateRole')->name('admin.role.update');
        Route::get('admin/delete/role/{id}', 'AdminDeleteRole')->name('admin.delete.role');
    });
    // end role all route

    // admin all route
    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/all', 'AdminAll')->name('all.admin');
        Route::get('/admin/add', 'AdminAdd')->name('add.admin');
        Route::post('/admin/store', 'AdminStore')->name('store.admin');
        Route::get('/edit/admin/role/{id}', 'EditAdminRole')->name('edit.admin.role');
        Route::post('/update/admin/role', 'UpdateAdminRole')->name('update.admin.role');
        Route::get('/delete/admin/role/{id}', 'DeleteAdminRole')->name('delete.admin.role');
    });
});


require __DIR__ . '/auth.php';
