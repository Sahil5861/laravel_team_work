<?php

use App\Http\Controllers\Auth\LoginUserController;
use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColourController;
use App\Http\Controllers\ProductGroupsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('index');
})->name('home');

// Authentication Routes
Route::middleware(['redirectIfAuthenticated'])->group(function () {
    Route::get('/login', [LoginUserController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [RegisterUserController::class, 'showRegistrationForm'])->name('register');
    Route::post('/login', [LoginUserController::class, 'login']);
    Route::post('/register', [RegisterUserController::class, 'register']);
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::post('/logout', [LoginUserController::class, 'logout'])->name('logout');
    Route::get('/logout', [LoginUserController::class, 'logout'])->name('logout.get');


    Route::resource('users', UserController::class);
    Route::post('users/delete-selected', [UserController::class, 'deleteSelected'])->name('users.deleteSelected');
    Route::post('users/activate-selected', [UserController::class, 'activateSelected'])->name('users.activateSelected');
    Route::post('users/deactivate-selected', [UserController::class, 'deactivateSelected'])->name('users.deactivateSelected');


    Route::resource('role', RoleController::class);
    Route::post('roles/bulk-delete', [RoleController::class, 'bulkDelete'])->name('roles.bulkDelete');
    Route::post('roles/bulk-status-update', [RoleController::class, 'bulkStatusUpdate'])->name('roles.bulkStatusUpdate');
    Route::post('role/{id}/toggle-status', [RoleController::class, 'toggleStatus'])->name('role.toggleStatus');


    Route::resource('size', SizeController::class);
    Route::post('admin/sizes/toggle-status/{id}', [SizeController::class, 'toggleStatus'])->name('sizes.toggleStatus');
    Route::post('admin/sizes/bulk-delete', [SizeController::class, 'bulkDelete'])->name('sizes.bulkDelete');
    Route::post('admin/sizes/bulk-status-update', [SizeController::class, 'bulkStatusUpdate'])->name('sizes.bulkStatusUpdate');

    // -----------------------------------colors Routes--------------------------------------

    Route::resource('admin/colour', ColourController::class);
    Route::post('admin/colours/toggle-status/{id}', [ColourController::class, 'toggleStatus'])->name('colours.toggleStatus');
    Route::post('admin/colours/bulk-delete', [ColourController::class, 'bulkDelete'])->name('colours.bulkDelete');
    Route::post('admin/colours/bulk-status-update', [ColourController::class, 'bulkStatusUpdate'])->name('colours.bulkStatusUpdate');
    Route::get('admin/colours/export', [ColourController::class, 'export'])->name('colours.export');
    Route::post('admin/colours/import', [ColourController::class, 'import'])->name('colours.import');


    Route::resource('blogs', BlogController::class);
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');



    // --------------------------------------category Routes----------------------------------

    Route::get('admin/category', [CategoryController::class, 'index'])->name('admin.category');
    Route::post('admin/category', [CategoryController::class, 'getData'])->name('admin.category.post');
    Route::post('admin/category/update-status/{id}', [CategoryController::class, 'updateStatus'])->name('admin.category.status');
    Route::get('admin/category/create', [CategoryController::class, 'create'])->name('admin.category.create');
    Route::get('admin/category/edit/{id}', [CategoryController::class, 'edit'])->name('admin.category.edit');
    Route::get('admin/category/delete/{id}', [CategoryController::class, 'remove'])->name('admin.category.delete');
    Route::post('admin/category/create', [CategoryController::class, 'store'])->name('admin.category.create.post');
    Route::post('admin/category/edit/{id}', [CategoryController::class, 'store'])->name('admin.category.edit.post');
    Route::delete('admin/category/delete-selected', [CategoryController::class, 'deleteSelected'])->name('admin.category.deleteSelected');
    Route::get('admin/category/export', [CategoryController::class, 'export'])->name('admin.category.export');
    Route::post('admin/category/import', [CategoryController::class, 'import'])->name('admin.category.import');



    // --------------------------------------brand Routes----------------------------------

    Route::get('admin/brand', [BrandController::class, 'index'])->name('admin.brand');
    Route::post('admin/brand/update-status/{id}', [BrandController::class, 'updateStatus'])->name('admin.brand.status');
    Route::get('admin/brand/create', [BrandController::class, 'create'])->name('admin.brand.create');
    Route::get('admin/brand/edit/{id}', [BrandController::class, 'edit'])->name('admin.brand.edit');
    Route::get('admin/brand/delete/{id}', [BrandController::class, 'remove'])->name('admin.brand.delete');
    Route::post('admin/brand/create', [BrandController::class, 'store'])->name('admin.brand.create.post');
    Route::post('admin/brand/edit/{id}', [BrandController::class, 'store'])->name('admin.brand.edit.post');
    Route::delete('admin/brand/delete-selected', [BrandController::class, 'deleteSelected'])->name('admin.brand.deleteSelected');
    Route::get('admin/brand/export', [BrandController::class, 'export'])->name('admin.brand.export');
    Route::post('admin/brand/import', [BrandController::class, 'import'])->name('admin.brand.import');



    // -------------------------------------Product Group Routes---------------------------------

    Route::get('admin/group-relation', [ProductGroupsController::class, 'index'])->name('admin.grouprelation');
    Route::post('admin/group-relation/update-status/{id}', [ProductGroupsController::class, 'updateStatus'])->name('admin.grouprelation.status');
    Route::get('admin/group-relation/create', [ProductGroupsController::class, 'create'])->name('admin.grouprelation.create');
    Route::get('admin/group-relation/edit/{id}', [ProductGroupsController::class, 'edit'])->name('admin.grouprelation.edit');
    Route::get('admin/group-relation/delete/{id}', [ProductGroupsController::class, 'remove'])->name('admin.grouprelation.delete');
    Route::post('admin/group-relation/create', [ProductGroupsController::class, 'store'])->name('admin.grouprelation.create.post');
    Route::post('admin/group-relation/edit/{id}', [ProductGroupsController::class, 'store'])->name('admin.grouprelation.edit.post');
    Route::delete('admin/group-relation/delete-selected', [ProductGroupsController::class, 'deleteSelected'])->name('admin.grouprelation.deleteSelected');
    Route::get('admin/group-relation/export', [ProductGroupsController::class, 'export'])->name('admin.grouprelation.export');
    Route::post('admin/group-relation/import', [ProductGroupsController::class, 'import'])->name('admin.grouprelation.import');

    // -------------------------------------Product Routes---------------------------------------

    Route::get('admin/products', [ProductsController::class, 'index'])->name('admin.products');
    Route::post('admin/products/update-status/{id}', [ProductsController::class, 'updateStatus'])->name('admin.products.status');
    Route::get('admin/products/create', [ProductsController::class, 'create'])->name('admin.products.create');
    Route::get('admin/products/edit/{id}', [ProductsController::class, 'edit'])->name('admin.products.edit');
    Route::get('admin/products/delete/{id}', [ProductsController::class, 'remove'])->name('admin.products.delete');
    Route::post('admin/products/create', [ProductsController::class, 'store'])->name('admin.products.create.post');
    Route::post('admin/products/edit/{id}', [ProductsController::class, 'store'])->name('admin.products.edit.post');
    Route::delete('admin/products/delete-selected', [ProductsController::class, 'deleteSelected'])->name('admin.products.deleteSelected');
    
});