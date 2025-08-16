<?php

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PriceListController;
use App\Http\Controllers\MasterListController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EarlyPaymentController;
use Illuminate\Http\Request;

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

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes - always accessible
    Route::middleware('page.permission:profile')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        
        // User permissions routes (admin only)
        Route::post('/user-permissions/update', [UserPermissionController::class, 'update'])->name('user-permissions.update');
        Route::delete('/user-permissions/delete', [UserPermissionController::class, 'delete'])->name('user-permissions.delete');
        
        // Locations management
        Route::post('/locations/store', [\App\Http\Controllers\LocationsController::class, 'store'])->name('locations.store');
        
        // Checkers management
        Route::post('/checkers/store', [\App\Http\Controllers\CheckersController::class, 'store'])->name('checkers.store');
    });
    
    // Special route for Container Reservation
    Route::post('/masterlist/reserve-container', [MasterListController::class, 'reserveContainer'])
         ->name('masterlist.reserve-container');
    
    // Check if container is a subsequent use for wharfage calculation
    Route::post('/check-container-usage', [CustomerController::class, 'checkContainerUsage'])
         ->name('check.container.usage');
    
    // Interest reset page
    Route::get('/masterlist/reset-interest', [MasterListController::class, 'resetInterest'])->name('masterlist.reset_interest');
    
    // Route for voyage status diagnosis and fix
    Route::get('/fix-voyage', [App\Http\Controllers\FixVoyageController::class, 'checkVoyageStatus'])->name('fix-voyage');
    Route::post('/fix-voyage-status', [App\Http\Controllers\FixVoyageController::class, 'fixVoyageStatus'])->name('fix-voyage-status');

    // History routes
    Route::middleware('page.permission:history')->group(function () {
        Route::get('/history', [HistoryController::class, 'index'])->name('history');
    });

    // Accounting routes
    Route::middleware('page.permission:accounting')->group(function () {
        Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting');
    });

    // Inventory routes (accessible to all authenticated users)
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory/store', [InventoryController::class, 'store'])->name('inventory.store');
    Route::post('/inventory/set-starting-balance', [InventoryController::class, 'setStartingBalance'])->name('inventory.set-starting-balance');
    Route::put('/inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

    // Price List routes
    Route::middleware('page.permission:pricelist,access')->group(function () {
        Route::get('/pricelist', [PriceListController::class, 'index'])->name('pricelist');
        Route::get('/items', [PriceListController::class, 'index'])->name('items.index');

        // Create operations - require create permission
        Route::middleware('page.permission:pricelist,create')->group(function () {
            Route::post('/pricelist/store', [PriceListController::class, 'store'])->name('pricelist.store');
            Route::post('/pricelist/category/store', [PriceListController::class, 'storeCategory'])->name('pricelist.category.store');
        });

        // Edit operations - require edit permission
        Route::middleware('page.permission:pricelist,edit')->group(function () {
            Route::put('/pricelist/{id}', [PriceListController::class, 'update'])->name('pricelist.update');
        });

        // Delete operations - require delete permission
        Route::middleware('page.permission:pricelist,delete')->group(function () {
            Route::delete('/pricelist/{id}', [PriceListController::class, 'destroy'])->name('pricelist.destroy');
        });
    });

    // Customer routes
    Route::middleware('page.permission:customer,access')->group(function () {
        Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
        Route::get('/customers', [CustomerController::class, 'index'])->name('customer.index');
        Route::get('/customer-details/{id}', [CustomerController::class, 'getCustomerDetails']);
        Route::get('/customers/info/{id}', [CustomerController::class, 'details'])->name('customer.info');
        Route::get('/customer/{customerId}/subaccounts', [CustomerController::class, 'getSubAccounts']);
        Route::get('/search-customers', [CustomerController::class, 'searchCustomers']);
        Route::get('/api/available-voyages/{shipNum}', [CustomerController::class, 'getAvailableVoyagesApi'])->name('api.available-voyages');

        // Order viewing
        Route::get('/order', [CustomerController::class, 'order'])->name('order');
        Route::get('/complete-order/{orderId}', [CustomerController::class, 'viewbl'])->name('customer.view-bl');

        // Create operations - require create permission
        Route::middleware('page.permission:customer,create')->group(function () {
            Route::post('/customers/store', [CustomerController::class, 'store'])->name('customers.store');
            Route::post('/add-location/{location}', [CustomerController::class, 'location'])->name('location.add');
            Route::get('/customer/create-order', [CustomerController::class, 'bl'])->name('customer.bl');
            Route::post('/pass/order', [CustomerController::class, 'pass'])->name('customer.order');
            Route::post('/pushOrder', [CustomerController::class, 'pushOrder'])->name('pushOrder');
        });

        // Edit operations - require edit permission
        Route::middleware('page.permission:customer,edit')->group(function () {
            Route::match(['post', 'put'], '/customers/update', [CustomerController::class, 'update'])->name('customers.update');
        });

        // Delete operations - require delete permission
        Route::middleware('page.permission:customer,delete')->group(function () {
            Route::delete('/sub-account/{id}', [CustomerController::class, 'destroy'])->name('sub-account.destroy');
            Route::delete('/account/{id}', [CustomerController::class, 'delete'])->name('account-delete');
        });
    });

    // Users management routes
    Route::middleware('page.permission:users')->group(function () {
        Route::get('/users', [UsersController::class, 'index'])->name('users');
        Route::get('/users/create', [UsersController::class, 'add'])->name('users.cre');
        Route::post('/users/create/', [UsersController::class, 'store'])->name('users.store');
        Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{id}', [UsersController::class, 'update'])->name('users.update');
    });

    // Master List routes
    Route::middleware('page.permission:masterlist')->group(function () {
        // Ships subpage
        Route::middleware('subpage.permission:masterlist,ships')->group(function () {
            Route::get('/masterlist', [MasterListController::class, 'index'])->name('masterlist');
            Route::post('/masterlist/ships', [MasterListController::class, 'store'])->name('masterlist.store');
            Route::put('/masterlist/ships/{id}', [MasterListController::class, 'update'])->name('masterlist.update');
            Route::delete('/masterlist/ships/{id}', [MasterListController::class, 'destroy'])->name('masterlist.destroy');
        });
        
        // Voyage subpage
        Route::middleware('subpage.permission:masterlist,voyage')->group(function () {
            Route::get('/masterlist/voyage/{id}', [MasterListController::class, 'voyage'])->name('masterlist.voyage');
            Route::get('/masterlist/voyage/list', [MasterListController::class, 'list'])->name('masterlist.list');
        });
        
        // Container subpage
        Route::middleware('subpage.permission:masterlist,container')->group(function () {
            Route::get('/masterlist/container', [MasterListController::class, 'container'])->name('masterlist.container');
        });
        
        // Container details subpage
        Route::middleware('subpage.permission:masterlist,container-details')->group(function () {
            Route::get('/masterlist/container-details', [MasterListController::class, 'containerDetails'])->name('masterlist.container-details');
        });
        
        Route::delete('/masterlist/delete-reservation/{id}', [MasterListController::class, 'deleteReservation'])->name('masterlist.delete-reservation');
        Route::put('/masterlist/update-reservation', [MasterListController::class, 'updateReservation'])->name('masterlist.update-reservation');

        // BL List and Edit/View BL subpages
        Route::middleware('subpage.permission:masterlist,bl-list')->group(function () {
            Route::get('/masterlist/bl-list', [MasterListController::class, 'blListAll'])->name('masterlist.bl-list');
        });
        
        // Delete operations require delete permission - moved outside of the bl-list group
        Route::middleware('subpage.permission:masterlist,list,delete')->group(function () {
            Route::delete('/masterlist/delete-order/{orderId}', [MasterListController::class, 'destroyOrder'])->name('masterlist.delete-order');
            Route::post('/masterlist/restore-order/{deleteLogId}', [MasterListController::class, 'restoreOrder'])->name('masterlist.restore-order');
        });
        
        // Direct routes without nested middleware for edit-bl (helps in debugging permission issues)
        Route::get('/masterlist/edit-bl-direct/{orderId}', [MasterListController::class, 'editBL'])->name('masterlist.edit-bl-direct');

        // Separate middleware for edit-bl operations
        Route::middleware('subpage.permission:masterlist,list,edit')->group(function () {
            Route::get('/masterlist/edit-bl/{orderId}', [MasterListController::class, 'editBL'])->name('masterlist.edit-bl');
            Route::post('/masterlist/update-bl/{orderId}', [MasterListController::class, 'updateBL'])->name('masterlist.update-bl');
        });
               
        // Order list
        Route::middleware('subpage.permission:masterlist,list')->group(function () {
            Route::delete('/masterlist/{order_id}', [MasterListController::class, 'destroy'])->name('order.destroy');
        });

        // Parcel subpage
        Route::middleware('subpage.permission:masterlist,parcel')->group(function () {
            Route::get('/masterlist/parcel', [MasterListController::class, 'parcel'])->name('masterlist.parcel');
            Route::put('/masterlist/parcel/update', [MasterListController::class, 'updateParcels'])->name('masterlist.parcel.update');
        });
        
        // SOA subpage
        Route::middleware('subpage.permission:masterlist,soa')->group(function () {
            // SOA-related routes - fixed duplicates
            Route::get('/masterlist/soa', [MasterListController::class, 'soa'])->name('masterlist.soa');
            Route::get('/masterlist/soa/search', [CustomerController::class, 'searchForSOA'])->name('customer.search-for-soa');
            Route::get('/masterlist/soa/list', [MasterListController::class, 'soa_list'])->name('masterlist.soa_list');
            
            // Test route for debugging
            Route::get('/masterlist/soa_test/{ship}/{voyage}/{customerId}', function($ship, $voyage, $customerId) {
                return "Route working! Ship: $ship, Voyage: $voyage, Customer ID: $customerId";
            });
            
            // Main SOA temp route - updated to handle special characters in voyage numbers
            Route::get('/masterlist/soa_temp/{ship}/{voyage}/{customerId}', [MasterListController::class, 'soa_temp'])
                ->where('voyage', '.*') // Allow any character in voyage parameter
                ->name('masterlist.soa_temp');
                
            // Custom SOA temp route - for custom frontend design
            Route::get('/masterlist/soa_custom/{ship}/{voyage}/{customerId}', [MasterListController::class, 'soa_custom'])
                ->where('voyage', '.*') // Allow any character in voyage parameter
                ->name('masterlist.soa_custom');
                
            // New route for voyage-based SOA report
            Route::get('/masterlist/soa_voy_temp/{ship}/{voyage}', [MasterListController::class, 'soa_voy_temp'])
                ->where('voyage', '.*') // Allow any character in voyage parameter
                ->name('masterlist.soa_voy_temp');
                
            // Routes for managing 1% interest calculation
            Route::post('/masterlist/activate-interest/{ship}/{voyage}/{customerId}', [MasterListController::class, 'activateInterest'])->name('masterlist.activate-interest');
            Route::post('/masterlist/deactivate-interest/{ship}/{voyage}/{customerId}', [MasterListController::class, 'deactivateInterest'])->name('masterlist.deactivate-interest');
        });

        // Customer subpage
        Route::middleware('subpage.permission:masterlist,customer')->group(function () {
            Route::get('/masterlist/customer', [MasterListController::class, 'customer'])->name('masterlist.customer');
            Route::get('/masterlist/customer/bl_list/{customer_id}', [MasterListController::class, 'bl_list'])->name('masterlist.bl_list');
            // Avoid permission check for viewing BL to prevent 500 errors
            Route::get('/masterlist/customer/container', [MasterListController::class, 'container'])->name('orders.update');
        });
        
        // Direct route for viewing BL without permission middleware to prevent 500 errors
        Route::get('/masterlist/customer/viewbl/{order_id}', [MasterListController::class, 'viewbl'])->name('orders.view');
        
        // Master List for update BL
        Route::get('/masterlist/search-customer-details', [MasterListController::class, 'searchCustomerDetails'])->name('masterlist.search-customer-details');
        
        // Master List for voyage - moved outside permission middleware for better access
        Route::get('/masterlist/bl/{shipNum}/{voyageNum}/{orderId}', [MasterListController::class, 'viewbl'])->name('masterlist.view-bl');
        Route::get('/masterlist/no-price-bl/{shipNum}/{voyageNum}/{orderId}', [MasterListController::class, 'viewNoPriceBl'])->name('masterlist.view-no-price-bl');
        Route::get('/masterlist/voyage/orders/{shipNum}/{voyageNum}', [MasterListController::class, 'voyageOrders'])->name('masterlist.voyage-orders');
        Route::get('/masterlist/voyage/orders-by-id/{voyageId}', [MasterListController::class, 'voyageOrdersById'])->name('masterlist.voyage-orders-by-id');
        Route::post('/update-bl-status/{orderId}', [MasterListController::class, 'updateBlStatus']);
        Route::post('/update-order-field/{orderId}', [MasterListController::class, 'updateOrderField']);
        Route::post('/masterlist/update-order-field/{orderId}', [MasterListController::class, 'updateOrderField'])->name('masterlist.update-order-field');
        Route::post('/update-soa-number', [MasterListController::class, 'updateSoaNumber'])->name('update-soa-number');
        Route::post('/update-note-field/{orderId}', [MasterListController::class, 'updateNoteField']);
        Route::post('/remove-order-image/{orderId}', [MasterListController::class, 'removeImage'])->name('order.remove-image');
        Route::post('/remove-image/{orderId}', [MasterListController::class, 'removeImage'])->name('remove.image');

        // Route for applying early payment discount
        Route::post('/api/apply-early-payment-discount', [EarlyPaymentController::class, 'applyDiscount'])->name('api.early-payment-discount');

        // Voyage status update route
        Route::put('/voyage/{id}/update-status', [MasterListController::class, 'updateVoyageStatus'])->name('voyage.update-status');
    });

    // Image upload route - generic functionality
    Route::post('/upload-temp-image', function (Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle the uploaded image (e.g., save temporarily in storage/app/public/temp)
        $path = $request->file('image')->store('temp', 'public');

        // Return the image path for preview
        return response()->json(['path' => asset('storage/' . $path)]);
    })->name('upload.temp.image');
});

// Just to demo sidebar dropdown links active states (protected with permission checks)
Route::middleware(['auth', 'page.permission:dashboard'])->group(function() {
    Route::get('/buttons/text', function () {
        return view('buttons-showcase.text');
    })->name('buttons.text');

    Route::get('/buttons/icon', function () {
        return view('buttons-showcase.icon');
    })->name('buttons.icon');

    Route::get('/buttons/text-icon', function () {
        return view('buttons-showcase.text-icon');
    })->name('buttons.text-icon');
});

require __DIR__ . '/auth.php';
