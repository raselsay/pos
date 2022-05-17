<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
include "delivery.php";
Route::group(['middleware' => ['permission:bank_edit']], function () {
    Route::post('/admin/banks/{id}', 'BankController@Update');
});
Route::get('/admin/user', 'UserController@Form');
Route::post('/admin/user_search', 'UserController@searchUser');
Route::get('/', 'Auth\LoginController@showLoginForm');
Route::get('/lisencekey', 'LisencekeyController@Form');
Route::post('/lisencekey', 'LisencekeyController@Create')->name('lisence');
Auth::routes();
// home controller
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');
Route::get('/admin/getVoucerFormData', 'HomeController@getVoucerFormData');
Route::get('/admin/get_voucer_data/{id}', 'VoucerController@getVoucerData');
Route::get('/admin/sales_chart', 'HomeController@SaleChart');
Route::get('/admin/pie_sales_chart', 'HomeController@PieSaleChart');
Route::delete('/admin/voucer/{id}', 'VoucerController@Delete')->middleware('permission:voucer_delete');
// banks route
Route::get('/admin/banks', 'BankController@bankForm')->middleware('permission:bank_view');
Route::post('/admin/banks', 'BankController@insertBank')->middleware('permission:bank_create');

Route::get('/admin/all_banks', 'BankController@allBanks')->middleware('permission:bank_view');
Route::get('/admin/test', 'BankController@test');
Route::get('/admin/get_account', 'BankController@getAccount');
Route::get('/admin/get_banks/{id}', 'BankController@getBanksById');
Route::post('/admin/get_banks', 'BankController@getBanks');
Route::get('/admin/get_balance/{id}', 'BankController@getBalanceById');

// Employee routes
Route::get('/admin/employee', 'EmployeeController@ManageEmployee')->middleware('permission:view_employee');
Route::get('/admin/employee/{id}', 'EmployeeController@getEmployee');
Route::post('/admin/employee/{id}', 'EmployeeController@Update')->middleware('permission:edit_employee');
Route::post('/admin/employee', 'EmployeeController@insertEmployee')->middleware('permission:create_employee');
Route::delete('/admin/employee/{id}', 'EmployeeController@Delete')->middleware('permission:delete_employee');
Route::post('/admin/search_employee', 'EmployeeController@SearchEmployee');
Route::get('/admin/get_basic_salary/{id}', 'EmployeeController@getBasicSalary');
// employee salary
Route::get('/admin/employee_salary', 'EmployeeSalaryController@Form')->middleware('permission:employee_salary_view');
Route::post('/admin/employee_salary', 'EmployeeSalaryController@Create')->middleware('permission:employee_salary_create');
Route::get('/admin/employee_balance/{id}', 'EmployeeSalaryController@EmployeeBalance');
Route::delete('/admin/employee_salary/{id}', 'EmployeeSalaryController@Delete')->middleware('permission:employee_salary_delete');
// Supplier Route
Route::get('/admin/supplier', 'SupplierController@ManageSupplier')->middleware('permission:supplier_view');
Route::post('/admin/supplier', 'SupplierController@insertSupplier')->middleware('permission:supplier_create');
Route::delete('/admin/supplier/{id}', 'SupplierController@DeleteSupplier')->middleware('permission:supplier_delete');
Route::get('/admin/get-supplier/{id}', 'SupplierController@getSupplier');
Route::post('/admin/supplier/{id}', 'SupplierController@UpdateSupplier')->middleware('permission:supplier_edit');
Route::post('/admin/search_supplier', 'SupplierController@searchSupplier');
Route::get('/admin/supplier_balance/{id}', 'SupplierController@getBalance');
//  route
Route::get('/admin/customer', 'CustomerController@CustomerForm')->middleware('permission:customer_view');
Route::post('/admin/customer', 'CustomerController@CreateNew')->middleware('permission:customer_create');
Route::post('/admin/inv-customer', 'CustomerController@InvCreateNew')->middleware('permission:customer_create');
Route::post('/admin/search_customer', 'CustomerController@searchCustomer');
Route::get('/admin/customer_balance/{id?}', 'CustomerController@getBalance');
Route::delete('/admin/customer/{id?}', 'CustomerController@Delete')->middleware('permission:customer_delete');
Route::get('/admin/all-customer', 'CustomerController@getAll');
Route::get('/admin/get-customer/{id}', 'CustomerController@getCustomer');
Route::post('/admin/customer/{id}', 'CustomerController@Update')->middleware('permission:customer_edit');
//category route
Route::get('/admin/category', 'CategoryController@ManageCategory')->middleware('permission:category_view');
Route::get('/admin/category_get/{id}', 'CategoryController@getCatById');
Route::delete('/admin/category/{id}', 'CategoryController@Delete')->middleware('permission:category_delete');
Route::post('/admin/category', 'CategoryController@insertCategory')->middleware('permission:category_create');
Route::post('/admin/category/{id}', 'CategoryController@Update')->middleware('permission:category_edit');
Route::get('/admin/get_all_category', 'CategoryController@getCat');
Route::post('/admin/search_category', 'CategoryController@SearchCategory');
//Child category route
Route::get('/admin/child_category', 'ChildCategoryController@ManageCategory')->middleware('permission:child_category_view');
Route::get('/admin/get_child_cat/{id}', 'ChildCategoryController@getChildCat');
Route::get('/admin/get_child_cat_by_id/{id}', 'ChildCategoryController@getChildCatById');

Route::get('/admin/get_all_child_category', 'ChildCategoryController@allChildCat');
Route::post('/admin/child_category', 'ChildCategoryController@insertCategory')->middleware('permission:child_category_create');
Route::post('/admin/child_category/{id}', 'ChildCategoryController@Update')->middleware('permission:child_category_create');
// product routes
Route::get('/admin/product', 'ProductController@ManageProduct')->middleware('permission:product_view');
Route::post('/admin/product', 'ProductController@insertProduct')->middleware('permission:product_create');
Route::get('/admin/product_price_by_id/{product_id}/{customer_id?}', 'ProductController@ProductSalePrice');
Route::get('/admin/product_buy_price_by_id/{id?}', 'ProductController@ProductBuyPrice');
Route::get('/admin/product_by_cat/{id}', 'ProductController@getProduct');
Route::get('/admin/product_by_id/{id}', 'ProductController@getProductById');
Route::delete('/admin/product/{id}', 'ProductController@Delete')->middleware('permission:product_delete');
Route::post('/admin/product/{id}', 'ProductController@Update')->middleware('permission:product_edit');
Route::post('/admin/product_code', 'ProductController@productBarcode');
Route::get('/admin/product_qantity/{product_id}/{store_id}', 'ProductController@getQantity');
Route::get('/admin/product_by_barcode/{code}', 'ProductController@ProdByBarcode');
// productType Routes
Route::get('/admin/product_type', 'ProductTypeController@ManageProductType')->middleware('permission:product_unit_view');
Route::delete('/admin/product_type/{id}', 'ProductTypeController@Delete')->middleware('permission:product_unit_delete');
Route::get('/admin/product_type/{id}', 'ProductTypeController@getPtype');
Route::post('/admin/product_type', 'ProductTypeController@insertProductType')->middleware('permission:product_unit_create');
Route::post('/admin/product_type/{id}', 'ProductTypeController@Update')->middleware('permission:product_unit_edit');
// purchase route
Route::get('/admin/purchase', 'PurchaseController@ManagePurchase')->middleware('permission:purchase_view');
Route::get('/admin/all-purchase', 'PurchaseController@AllPurchase')->middleware('permission:purchase_view');
Route::post('/admin/purchase', 'PurchaseController@insertPurchase')->middleware('permission:purchase_create');
Route::get('/admin/purchase-update/{id}', 'PurchaseController@UpdateForm')->middleware('permission:purchase_edit');
Route::post('/admin/purchase-update/{id}', 'PurchaseController@Update')->middleware('permission:purchase_edit');
Route::delete('/admin/purchase/{id}', 'PurchaseController@Delete')->middleware('permission:purchase_delete');
Route::get('/admin/purchase_data/{id}', 'PurchaseController@GetData');

// opening stock
Route::get('admin/opening_stock', 'OpeningStockController@Form')->middleware('permission:opening_stock_view');
Route::post('admin/opening_stock', 'OpeningStockController@Create')->middleware('permission:opening_stock_create');
// stock transfer route
Route::get('admin/stock_transfer', 'StockTransferController@Form')->middleware('permission:stock_transfer_view');
Route::post('admin/stock_transfer', 'StockTransferController@Create')->middleware('permission:stock_transfer_create');
Route::get('admin/all_stock_transfer', 'StockTransferController@AllStockTransfer')->middleware('permission:stock_transfer_view');
Route::delete('admin/stock_transfer/{id}', 'StockTransferController@Delete')->middleware('permission:stock_transfer_delete');
// stock transfer
Route::get('admin/damage_out', 'DamageOutController@Form')->middleware('permission:damage_out_view');
Route::get('admin/all_damage_out', 'DamageOutController@AllDamageOut')->middleware('permission:damage_out_view');
Route::post('admin/damage_out', 'DamageOutController@Create')->middleware('permission:damage_out_create');
Route::delete('admin/damage_out/{id}', 'DamageOutController@Delete')->middleware('permission:damage_out_delete');
// stock ledger
Route::get('admin/stock_ledger', 'StockLedgerController@Form');
Route::post('admin/stock_ledger', 'StockLedgerController@Report');
// name route
Route::get('/admin/name', 'NameController@ManageName')->middleware('permission:accounts_name_view');
Route::post('/admin/name', 'NameController@insertName')->middleware('permission:accounts_name_edit');
Route::post('/admin/search_name', 'NameController@searchName');
// name relation route here
Route::get('/admin/name_relation', 'NameRelationController@ManageNameRelation')->middleware('permission:accounts_head_view');
Route::post('/admin/name_relation', 'NameRelationController@insertNameRelation')->middleware('permission:accounts_head_create');
Route::post('/admin/relation_search/{id}', 'NameRelationController@getRelationById');
// voucer controller
Route::get('/admin/voucer', 'VoucerController@ManageVoucer')->middleware('permission:voucer_view');
Route::post('/admin/voucer', 'VoucerController@insertVoucer')->middleware('permission:voucer_create');
Route::get('/admin/voucer_get_name/{id}', 'VoucerController@getNameData');

// invoice route
Route::get('/admin/invoice', 'InvoiceController@invoiceForm')->middleware('permission:invoice_view');
Route::get('/admin/invoice-update/{id}', 'InvoiceController@UpdateForm')->middleware('permission:invoice_edit');
Route::post('/admin/invoice-update/{id}', 'InvoiceController@Update')->middleware('permission:invoice_edit');
Route::post('/admin/invoice', 'InvoiceController@insertInvoice')->middleware('permission:invoice_create');
Route::post('/admin/invoice/{id}', 'InvoiceController@Update')->middleware('permission:invoice_edit');
Route::get('/admin/get_child_cat_by_cat_id/{id?}', 'InvoiceController@getChildCat');
Route::get('/admin/all_invoice', 'InvoiceController@allInvoices')->middleware('permission:invoice_view');
Route::delete('/admin/invoice/{id}', 'InvoiceController@Delete')->middleware('permission:invoice_delete');
Route::get('/admin/get-invoice-data/{id}', 'InvoiceController@GetInvoiceData');
// Running Total Route
Route::get('/admin/running-total', 'RunningTotalController@Form');
Route::post('/admin/running-total', 'RunningTotalController@CreateRunningTotal');

// sales report route
Route::get('/admin/sales_summery', 'SaleSummeryController@Form');
Route::post('/admin/sales_summery', 'SaleSummeryController@Report');
Route::get('/admin/customer_wise_sales', 'CWSReportController@Form');
Route::post('/admin/customer_wise_sales', 'CWSReportController@Report');
Route::get('/admin/product_wise_sales', 'PWSReportController@Form');
Route::post('/admin/product_wise_sales', 'PWSReportController@Report');
Route::get('/admin/user_wise_sales', 'UWSReportController@Form');
Route::post('/admin/user_wise_sales', 'UWSReportController@Report');
Route::get('/admin/warehouse_wise_sales', 'WWSReportController@Form');
Route::post('/admin/warehouse_wise_sales', 'WWSReportController@Report');
Route::get('admin/invoice_summery', 'InvoiceSummeryReport@Form');
Route::post('admin/invoice_summery', 'InvoiceSummeryReport@Report');
Route::get('admin/daily_statement', 'DailyStatementController@Form');
Route::post('admin/daily_statement', 'DailyStatementController@Report');
// purchase report route
Route::get('/admin/purchase_summery', 'PurchaseSummeryReportController@Form');
Route::post('/admin/purchase_summery', 'PurchaseSummeryReportController@Report');
Route::get('/admin/supplier_wise_purchase', 'SWPReportController@Form');
Route::post('/admin/supplier_wise_purchase', 'SWPReportController@Report');
Route::get('/admin/product_wise_purchase', 'PWPReportController@Form');
Route::post('/admin/product_wise_purchase', 'PWPReportController@Report');
Route::get('/admin/user_wise_purchase', 'UWPReportController@Form');
Route::post('/admin/user_wise_purchase', 'UWPReportController@Report');
Route::get('/admin/warehouse_wise_purchase', 'WWPReportController@Form');
Route::post('/admin/warehouse_wise_purchase', 'WWPReportController@Report');
// profit loss report
Route::get('/admin/profit_loss', 'ProfitLossController@Form');
Route::post('/admin/profit_loss', 'ProfitLossController@Report');
// info controller route
Route::get('admin/info_form', 'InfoController@Form');
Route::post('admin/add_info/{id}', 'InfoController@Insert');
// barcode Route
Route::get('admin/barcode', 'BarcodeController@Form');
Route::post('admin/barcode', 'BarcodeController@Generate');
// backup Controller Route
Route::get('admin/backup', 'BackupController@Form');
Route::get('admin/get_filename', 'BackupController@FileName')->middleware('permission:backup_view');
Route::get('admin/backup-delete/{data}', 'BackupController@Delete')->middleware('permission:backup_delete');
Route::get('admin/backup-db', 'BackupController@Backup');
Route::get('admin/backup-download/{data}', 'BackupController@Download');
// stock controller
Route::get('admin/stock', 'StockController@Stock')->middleware('permission:stock_view');
// BuyerReportController
Route::get('admin/buyerlistform', 'BuyerReportController@Form');
Route::get('admin/getbuyerlist', 'BuyerReportController@BuyerList');
Route::get('admin/getbuyerbalancesheet', 'BuyerReportController@BuyerBalanceSheet');
Route::get('admin/getbuyerbalanceform', 'BuyerReportController@BuyerBlnceForm');
// cash Details controller
Route::get('/admin/cash_details_form', 'CashDetailsController@Form');
Route::get('/admin/cash_details', 'CashDetailsController@cashDetails');
// store routes
Route::get('/admin/store', 'StoreController@Form');
Route::post('/admin/store', 'StoreController@Create');
Route::post('/admin/store/{id}', 'StoreController@Update');
Route::post('/admin/get_store', 'StoreController@getStore');
Route::get('/admin/store_data/{id}', 'StoreController@getData');
Route::post('/admin/get_store_by_user', 'StoreController@getStoreByAuthUser');
// fund transter Route
Route::get('/admin/fund_transfer', 'FundTransferController@Form')->middleware('permission:fund_transfer_view');
Route::post('/admin/fund_transfer', 'FundTransferController@Transfer')->middleware('permission:fund_transfer_create');
// transport route
Route::get('/admin/transport', 'TransportController@Form')->middleware('permission:transport_view');
Route::post('/admin/transport', 'TransportController@Create')->middleware('permission:transport_create');
Route::post('/admin/transport/{id}', 'TransportController@Update')->middleware('permission:transport_edit');
Route::post('/admin/get_transport', 'TransportController@getTransport');
Route::post('/admin/get_transport_import', 'TransportController@getTransportImport');
Route::post('/admin/get_transport_export', 'TransportController@getTransportExport');
Route::get('/admin/get_transport/{id}', 'TransportController@Data');
// custom report route
Route::get('/admin/custom_report', 'CustomReportController@Form');
Route::post('/admin/custom_report', 'CustomReportController@Report');
// expence report
Route::get('/admin/expence_report', 'ExpenceReportController@Form');
Route::post('/admin/expence_report', 'ExpenceReportController@Report');
// installment Route
Route::get('/admin/installment', 'InstallmentController@Form')->middleware('permission:installment_view');
Route::post('/admin/installment', 'InstallmentController@Create')->middleware('permission:installment_create');
// installment status Controller
Route::get('/admin/installment_status', 'InstallmentStatusController@Form')->middleware('permission:installment_view');
Route::get('/admin/installment_status/{id}', 'InstallmentStatusController@getInvoice');
Route::get('/admin/get_ins_invoice/{id?}', 'InstallmentStatusController@getInvoice');
Route::delete('/admin/installment/{id?}', 'InstallmentStatusController@Delete')->middleware('permission:installment_delete');
Route::get('/admin/get-installment-data/{id}', 'InstallmentStatusController@getInstallmentData');

// installment pay controller
Route::get('/admin/installment_pay', 'InstallmentPayController@Form')->middleware('permission:installment_view');
Route::post('/admin/installment_pay', 'InstallmentPayController@Create')->middleware('permission:installment_create');
Route::post('/admin/get_ins_invoice', 'InstallmentPayController@getInsInvoice');
Route::get('/admin/get_ins_ammount/{id}', 'InstallmentPayController@getInsAmmount');
// day by day Installment status controller
Route::get('admin/day_by_day_installment_status', 'DayByDayInstallmentStatusController@Form')->middleware('permission:installment_view');
// installment report controller
Route::get('/admin/installment_report', 'InstallmentReportController@Form');
Route::get('/admin/get_installment_report/{id?}', 'InstallmentReportController@getReport');
// test controller route
Route::get('admin/testpage', 'TestController@page');
Route::post('admin/testpage', 'TestController@testArray');
Route::post('admin/select2', 'TestController@select2');
Route::get('admin/create', 'TestController@array');
// change password route
Route::get('admin/change_password', 'ChangePasswordController@Form');
Route::post('admin/change_password', 'ChangePasswordController@Change');
// permission Manage Controller
Route::get('admin/manage_role', 'PermissionManageController@CreateRoleForm')->middleware('role:Super-Admin');
Route::post('admin/manage_role', 'PermissionManageController@CreateRole')->middleware('role:Super-Admin');
Route::get('admin/manage_permission', 'PermissionManageController@CreatePermissionForm')->middleware('role:Super-Admin');
Route::post('admin/manage_permission', 'PermissionManageController@CreatePermission')->middleware('role:Super-Admin');
Route::get('admin/set_role_has_permission', 'PermissionManageController@roleHasPermissionForm')->middleware('role:Super-Admin');
Route::post('admin/set_role_has_permission', 'PermissionManageController@setRoleHasPermission')->name('roleHasPermission')->middleware('role:Super-Admin');
Route::get('admin/get_role_has_permission', 'PermissionManageController@getRoleHasPermission')->middleware('role:Super-Admin');
Route::get('admin/user_wise_role', 'PermissionManageController@userWiseRoleForm')->middleware('role:Super-Admin');
Route::post('admin/user_wise_role', 'PermissionManageController@userWiseRole')->middleware('role:Super-Admin');
// setting Controllers InvoiceSettingController
Route::get('admin/setting/make-setting', 'setting\MultiSettingController@Form')->middleware('permission:setting_view');
Route::post('admin/setting/make-setting', 'setting\MultiSettingController@Create');
Route::get('admin/setting/get_data', 'setting\MultiSettingController@GetData');
// warehouse permission controller
Route::get('admin/warehouse-permission', 'WarehousePermissionController@Form');
Route::post('admin/warehouse-permission', 'WarehousePermissionController@Create');
// Notification Controller
Route::get('admin/notification', 'NotificationController@Form');
// Sms Controller 
Route::get('admin/sms_dashboard','SmsController@Dashboard')->middleware('permission:sms_view');
Route::get('admin/custom_sms','SmsController@CustomSms')->middleware('permission:sms_view');
Route::get('admin/get_numbers/{id}','SmsController@getNumber');
// bank ledger report
Route::get('admin/bank_ledger','BankLedgerController@Form');
Route::post('admin/bank_ledger','BankLedgerController@Report');
// order form route
// Route::get('order','PayOrderController@Form');
Route::post('order','PayOrderController@Create');
// spo controller
Route::get('/admin/spo','SpoController@Form');
Route::post('/admin/spo','SpoController@Create');
Route::post('/admin/get_spo','SpoController@SearchSpo');
// ofcontact controller
Route::get('/admin/ofcontact','OfcontractController@Form');
Route::post('/admin/ofcontact','OfcontractController@Create');
Route::post('/admin/get_ofcontact','OfcontractController@SearchSpo');

// Order Controller
Route::get('/admin/create-order','OrderController@Index');
Route::post('/admin/create-order','OrderController@Create');

// Artisan Call
Route::get('admin/storage-link','settingCommandController@storageLink');
Route::get('admin/route-cache','settingCommandController@routeCache');
// event Controller
Route::get('admin/events','EventController@Form');
Route::post('admin/events','EventController@Create');
Route::post('admin/events/{id}','EventController@Create');
Route::delete('admin/events/{id}','EventController@Delete');
Route::get('admin/events_data/{id}','EventController@getData');

// Dpermission Controller
Route::get('admin/dpermission','DpermissionController@Form');
Route::post('admin/dpermission','DpermissionController@Create');
// Customer Site
Route::get('admin/customer_site','SiteController@Form');
Route::post('admin/customer_site','SiteController@Create');
Route::post('admin/search_site/{id}','SiteController@searchSite');
// group controller
Route::get('admin/group','GroupController@Form');
Route::post('admin/group','GroupController@Create');
// earn report Controller
Route::get('admin/earn_statement', 'EarnReportController@Form');
Route::post('admin/earn_statement', 'EarnReportController@Report');
// EarnPayReportController 
Route::get('admin/earn_pay_statement', 'EarnPayReportController@Form');
Route::post('admin/earn_pay_statement', 'EarnPayReportController@Report');
// head wise Report Controller
Route::get('admin/head_wise_summation', 'HeadWiseSummationReportController@Form');
Route::post('admin/head_wise_summation', 'HeadWiseSummationReportController@Report');
// totalExpenceSummationController
Route::get('admin/total_expence_summation', 'TotalSheetExpenceReportController@Form');
Route::post('admin/total_expence_summation', 'TotalSheetExpenceReportController@Report');
// StockSummeryReportController
Route::get('admin/stock_summery', 'StockSummeryReportController@Form');
Route::post('admin/stock_summery', 'StockSummeryReportController@Report');
// StockDetailsByProductController
Route::get('admin/stock_details_by_product', 'StockDetailsReportByProductController@Form');
Route::post('admin/stock_details_by_product', 'StockDetailsReportByProductController@Report');
// Supplier Wise Total Purchase
Route::get('admin/supplier_wise_total_purchase', 'SupplierWiseTotalPurchaseReportController@Form');
Route::post('admin/supplier_wise_total_purchase', 'SupplierWiseTotalPurchaseReportController@Report');
// Customer Wise Total Sale
Route::get('admin/customer_wise_total_sale', 'CustomerWiseTotalSaleReportController@Form');
Route::post('admin/customer_wise_total_sale', 'CustomerWiseTotalSaleReportController@Report');
// customer wise payment
Route::get('admin/customer_wise_payment', 'CustomerWisePaymentReportController@Form');
Route::post('admin/customer_wise_payment', 'CustomerWisePaymentReportController@Report');
// supplier wise Payment
Route::get('admin/supplier_wise_payment', 'SupplierWisePaymentReportController@Form');
Route::post('admin/supplier_wise_payment', 'SupplierWisePaymentReportController@Report');