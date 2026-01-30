<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\Auth\CustomerPasswordResetController;
use App\Http\Controllers\Auth\CustomerRegisterController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController; // Assuming you have this
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSerialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\DeliveryChallanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TrackOrderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Maintenance & Coming Soon Routes
Route::get('/maintenance', function () {
    if (!settings('maintenance_mode')) return redirect('/');
    return view('errors.maintenance');
})->name('maintenance');

Route::get('/coming-soon', function () {
    if (!settings('coming_soon_mode')) return redirect('/');
    return view('errors.coming-soon');
})->name('coming-soon');

// ====================================================
//  FRONTEND ROUTES (Public Access)
// ====================================================

Route::group(['middleware' => ['maintenance']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/shop', [HomeController::class, 'shop'])->name('shop.index'); // Example
    Route::get('/category/{slug}', [HomeController::class, 'category'])->name('category.show');
    Route::get('/product/{slug}', [HomeController::class, 'product'])->name('product.show');

    // Cart Routes (Example)
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/order/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::match(['get', 'post'], '/track-order', [TrackOrderController::class, 'index'])->name('track.order');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/category/{slug}', [ShopController::class, 'index'])->name('category.show');
    Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
    Route::get('/search/ajax', [App\Http\Controllers\SearchController::class, 'ajaxSearch'])
        ->middleware('throttle:30,1')
        ->name('search.ajax');

    Route::get('/cart/mini', [App\Http\Controllers\CartController::class, 'miniCart'])->name('cart.mini');

    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // IT Solutions Routes
    Route::get('/solutions', [App\Http\Controllers\SolutionController::class, 'index'])->name('solutions.index');
    Route::get('/solutions/{slug}', [App\Http\Controllers\SolutionController::class, 'show'])->name('solutions.show');

    // Store Locator Route
    Route::get('/store-locator', [App\Http\Controllers\StoreLocatorController::class, 'index'])->name('store.locator');

    // Coupon Routes
    Route::post('/cart/coupon/apply', [App\Http\Controllers\CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::post('/cart/coupon/remove', [App\Http\Controllers\CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    // Public Dynamic Pages (MUST BE LAST)
    Route::get('/{slug}', [App\Http\Controllers\PageController::class, 'show'])->name('pages.show');
});

// ====================================================
//  BACKEND / ADMIN ROUTES (Protected)
// ====================================================

// We apply 'auth' middleware AND the 'backend' prefix here
Route::group(['middleware' => ['auth'], 'prefix' => 'backend'], function () {

    // --- COMMON BACKEND ROUTES ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/clear', [\App\Http\Controllers\Admin\NotificationController::class, 'clear'])->name('notifications.clear');

    // Profile Management

    // --- POS TERMINAL (Authorized access) ---
    // URL: /backend/pos
    Route::group(['middleware' => ['permission:access pos']], function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
        Route::get('/pos/check-serial', [PosController::class, 'checkSerial'])->name('pos.check-serial');
        Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');
        Route::post('/pos/customer', [PosController::class, 'storeCustomer'])->name('pos.customer.store');

        Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
    });

    // --- INVENTORY & SALES MANAGEMENT (Granular access) ---
    Route::group(['middleware' => ['permission:view products|manage categories|manage brands']], function () {

        // Products & Catalog
        Route::post('/products/print-barcodes', [ProductController::class, 'printBarcode'])->name('products.print_barcodes');
        
        // Import Products Routes
        Route::get('/products/import', [ProductController::class, 'importForm'])->name('products.import.form');
        Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');

        Route::resource('products', ProductController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('categories', CategoryController::class);

        // Attributes
        Route::resource('attributes', AttributeController::class);
        Route::delete('/attributes/value/{id}', [AttributeController::class, 'destroyValue'])->name('attributes.value.destroy');

        // Purchases & Suppliers
        Route::get('/purchases/{purchase_order}/print', [PurchaseOrderController::class, 'print'])->name('purchases.print');
        Route::get('/purchases/search', [PurchaseOrderController::class, 'searchProducts'])->name('purchases.search');
        Route::get('/purchases/{purchase_order}/serials', [ProductSerialController::class, 'entry'])->name('purchases.serials');
        Route::post('/serials/store', [ProductSerialController::class, 'store'])->name('serials.store');
        Route::get('/serials/check', [ProductSerialController::class, 'check'])->name('serials.check');
        Route::post('/purchases/{id}/bulk-receive', [PurchaseOrderController::class, 'bulkReceive'])->name('purchases.bulk_receive');
        Route::get('/purchases/{id}/reception/{receptionId}/print', [PurchaseOrderController::class, 'printReception'])->name('purchases.reception.print');
        Route::get('/purchases/{id}/download-pdf', [PurchaseOrderController::class, 'downloadPdf'])->name('purchases.download_pdf');
        Route::post('/purchases/{id}/mark-completed', [PurchaseOrderController::class, 'markAsCompleted'])->name('purchases.mark_completed');
        Route::resource('purchases', PurchaseOrderController::class);
        Route::resource('suppliers', SupplierController::class);

        // Customers & Returns
        Route::resource('customers', CustomerController::class);
        Route::post('/returns/find-order', [ReturnController::class, 'findOrder'])->name('returns.find');
        Route::resource('returns', ReturnController::class)->except(['edit', 'update', 'destroy']);

        // Quotations
        Route::get('/quotations/search', [QuotationController::class, 'search'])->name('quotations.search');
        Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convertToSale'])->name('quotations.convert');
        Route::get('/quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
        Route::get('/quotations/{quotation}/download-pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.download-pdf');
        Route::get('/quotations/{quotation}/download-pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.download-pdf');
        
        // Delivery Challans
        Route::get('/delivery-challans', [DeliveryChallanController::class, 'index'])->name('delivery-challans.index');
        Route::get('/quotations/{quotation}/challan/create', [DeliveryChallanController::class, 'create'])->name('quotations.challan.create');
        Route::post('/quotations/{quotation}/challan', [DeliveryChallanController::class, 'store'])->name('quotations.challan.store');
        Route::get('/delivery-challans/{id}', [DeliveryChallanController::class, 'show'])->name('delivery-challans.show');
        Route::get('/delivery-challans/{id}/edit', [DeliveryChallanController::class, 'edit'])->name('delivery-challans.edit');
        Route::put('/delivery-challans/{id}', [DeliveryChallanController::class, 'update'])->name('delivery-challans.update');
        Route::delete('/delivery-challans/{id}', [DeliveryChallanController::class, 'destroy'])->name('delivery-challans.destroy');
        Route::get('/delivery-challans/{id}/print', [DeliveryChallanController::class, 'print'])->name('delivery-challans.print');
        Route::get('/delivery-challans/{id}/pdf', [DeliveryChallanController::class, 'pdf'])->name('delivery-challans.pdf');

        Route::resource('quotations', QuotationController::class);
    });

    // --- FINANCIALS & EXPENSES ---
    Route::group(['middleware' => ['permission:view expenses|manage expenses']], function () {
        Route::resource('expense-categories', App\Http\Controllers\ExpenseCategoryController::class)->except(['create', 'edit', 'show', 'update']);
        Route::resource('expenses', App\Http\Controllers\ExpenseController::class);
    });

    // --- SYSTEM ADMINISTRATION ---
    Route::group(['middleware' => ['permission:manage users|manage roles|manage settings']], function () {

        // Orders Management
        Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
        Route::get('/orders/{order}/download-pdf', [OrderController::class, 'downloadPdf'])->name('orders.download-pdf');
        Route::resource('orders', OrderController::class);

        // Reports
        Route::group(['middleware' => ['permission:view reports']], function () {
            Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
            Route::get('/reports/sales/pdf', [ReportController::class, 'salesPdf'])->name('reports.sales.pdf');
            Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
            Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit_loss');
            Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
            Route::get('/reports/vat', [ReportController::class, 'vat'])->name('reports.vat');
            Route::get('/reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');
            Route::get('/reports/expenses', [ReportController::class, 'expenses'])->name('reports.expenses');
            Route::get('/reports/expenses/pdf', [ReportController::class, 'expensesPdf'])->name('reports.expenses.pdf');
            Route::get('/reports/sales-by-person', [ReportController::class, 'salesByPerson'])->name('reports.sales-by-person');
            Route::get('/reports/sales-by-person/pdf', [ReportController::class, 'salesByPersonPdf'])->name('reports.sales-by-person.pdf');
        });

        // System Settings
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/test-smtp', [SettingController::class, 'testSMTP'])->name('settings.test_smtp');
        Route::post('/settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');
        Route::post('/settings/storage-link', [SettingController::class, 'linkStorage'])->name('settings.storage-link');

        // User Management
        Route::resource('users', UserController::class);

        // Role Management
        Route::resource('roles', RoleController::class);

        // Banner Management
        Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
        Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
        Route::post('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
        Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

        // Offers Popup Management
        Route::get('popups/{popup}/preview', [\App\Http\Controllers\Admin\OfferPopupController::class, 'preview'])->name('popups.admin.preview');
        Route::resource('popups', \App\Http\Controllers\Admin\OfferPopupController::class)->names([
            'index' => 'popups.admin.index',
            'create' => 'popups.admin.create',
            'store' => 'popups.admin.store',
            'edit' => 'popups.admin.edit',
            'update' => 'popups.admin.update',
            'destroy' => 'popups.admin.destroy',
        ]);

        // Solutions Management
        Route::resource('solutions', \App\Http\Controllers\Admin\SolutionController::class)->names([
            'index' => 'solutions.admin.index',
            'create' => 'solutions.admin.create',
            'store' => 'solutions.admin.store',
            'show' => 'solutions.admin.show',
            'edit' => 'solutions.admin.edit',
            'update' => 'solutions.admin.update',
            'destroy' => 'solutions.admin.destroy',
        ]);



        // Dynamic Pages Management
        Route::resource('pages', \App\Http\Controllers\Admin\PageController::class)->names([
            'index' => 'pages.admin.index',
            'create' => 'pages.admin.create',
            'store' => 'pages.admin.store',
            'edit' => 'pages.admin.edit',
            'update' => 'pages.admin.update',
            'destroy' => 'pages.admin.destroy',
        ]);

        // Coupon Management
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class)->names([
            'index' => 'coupons.admin.index',
            'create' => 'coupons.admin.create',
            'store' => 'coupons.admin.store',
            'edit' => 'coupons.admin.edit',
            'update' => 'coupons.admin.update',
            'destroy' => 'coupons.admin.destroy',
        ]);
    });

});

// Authentication Routes (Login, Register, Password Reset)
require __DIR__ . '/auth.php';


// Guest Routes
Route::prefix('account')->name('customer.')->group(function () {

    // Guest Middleware: prevent logged-in customers from seeing login page
    Route::middleware('guest:customer')->group(function () {

        // Login Routes
        Route::get('login', [CustomerLoginController::class, 'create'])->name('login');
        Route::post('login', [CustomerLoginController::class, 'store'])->name('login.store');

        // Register Routes
        Route::get('register', [CustomerRegisterController::class, 'create'])->name('register');
        Route::post('register', [CustomerRegisterController::class, 'store'])->name('register.store');

        // Forgot Password (Request Link)
        Route::get('forgot-password', [CustomerPasswordResetController::class, 'create'])
            ->name('password.request');

        Route::post('forgot-password', [CustomerPasswordResetController::class, 'store'])
            ->name('password.email');

        // Reset Password (Enter New Password)
        Route::get('reset-password/{token}', [CustomerPasswordResetController::class, 'edit'])
            ->name('password.reset');

        Route::post('reset-password', [CustomerPasswordResetController::class, 'update'])
            ->name('password.update');
    });

    // Auth Middleware: Only for logged-in customers
    Route::middleware('auth:customer')->group(function () {
        Route::post('logout', [CustomerLoginController::class, 'destroy'])->name('logout');

        Route::get('profile', [CustomerProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [CustomerProfileController::class, 'update'])->name('profile.update');

        // Customer Dashboard
        Route::get('dashboard', [App\Http\Controllers\CustomerController::class, 'dashboard'])->name('dashboard');
        Route::get('orders', [App\Http\Controllers\CustomerController::class, 'orders'])->name('orders');
        Route::get('orders/{order}', [App\Http\Controllers\CustomerController::class, 'showOrder'])->name('orders.show');
        Route::get('orders/{order}/download', [App\Http\Controllers\CustomerController::class, 'downloadInvoice'])->name('orders.download');

        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');


        Route::get('addresses', [CustomerAddressController::class, 'index'])->name('addresses');
        Route::post('addresses', [CustomerAddressController::class, 'store'])->name('addresses.store');
        Route::put('addresses/{id}', [CustomerAddressController::class, 'update'])->name('addresses.update');
        Route::delete('addresses/{id}', [CustomerAddressController::class, 'destroy'])->name('addresses.destroy');
        Route::get('addresses/{id}/default', [CustomerAddressController::class, 'setDefault'])->name('addresses.default');

    });

});



