<?php

use App\Http\Controllers\Auth\LoginUserController;
use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColourController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProductGroupsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DealersController;
use App\Http\Controllers\ViewsController;
use App\Http\Controllers\ContactPersonController;
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





    Route::get('admin/user', [UserController::class, 'index'])->name('admin.user');
    Route::post('admin/user/update-status/{id}', [UserController::class, 'updateStatus'])->name('admin.user.status');
    Route::get('admin/user/create', [UserController::class, 'create'])->name('admin.user.create');
    Route::get('admin/user/edit/{id}', [UserController::class, 'edit'])->name('admin.user.edit');
    Route::get('admin/user/delete/{id}', [UserController::class, 'remove'])->name('admin.user.delete');
    Route::delete('admin/user/delete/{id}', [UserController::class, 'destroy'])->name('admin.user.destroy');
    Route::post('admin/user/create', [UserController::class, 'store'])->name('admin.user.create.post');
    Route::put('admin/user/{user}', [UserController::class, 'update'])->name('admin.user.edit.post');
    Route::delete('admin/user/delete-selected', [UserController::class, 'deleteSelected'])->name('admin.user.deleteSelected');
    Route::post('admin/user/import', [UserController::class, 'import'])->name('admin.user.import');
    Route::get('admin/user/export', [UserController::class, 'export'])->name('admin.user.export');
    Route::get('/sample-file-download-user', [UserController::class, 'sampleFileDownloadUser'])->name('sample-file-download-user');






    Route::get('admin/role', [RoleController::class, 'index'])->name('admin.role');
    Route::post('admin/role/update-status/{id}', [RoleController::class, 'updateStatus'])->name('admin.role.status');
    Route::get('admin/role/create', [RoleController::class, 'create'])->name('admin.role.create');
    Route::get('admin/role/edit/{id}', [RoleController::class, 'edit'])->name('admin.role.edit');
    Route::get('admin/role/delete/{id}', [RoleController::class, 'remove'])->name('admin.role.delete');
    Route::post('admin/role/create', [RoleController::class, 'store'])->name('admin.role.create.post');
    Route::put('admin/role/edit/{id}', [RoleController::class, 'store'])->name('admin.role.edit.post'); // Updated to PUT method
    Route::delete('admin/role/delete-selected', [RoleController::class, 'deleteSelected'])->name('admin.role.deleteSelected');
    Route::get('admin/role/export', [RoleController::class, 'export'])->name('admin.role.export');
    Route::post('admin/role/import', [RoleController::class, 'import'])->name('admin.role.import');
    Route::get('/sample-file-download-role', [RoleController::class, 'sampleFileDownloadRole'])->name('sample-file-download-role');



    // -----------------------------------colors Routes--------------------------------------



    Route::get('admin/colour', [ColourController::class, 'index'])->name('admin.colour');
    Route::post('admin/colour/update-status/{id}', [ColourController::class, 'updateStatus'])->name('admin.colour.status');
    Route::get('admin/colour/create', [ColourController::class, 'create'])->name('admin.colour.create');
    Route::get('admin/colour/edit/{id}', [ColourController::class, 'edit'])->name('admin.colour.edit');
    Route::get('admin/colour/delete/{id}', [ColourController::class, 'remove'])->name('admin.colour.delete');
    Route::post('admin/colour/create', [ColourController::class, 'store'])->name('admin.colour.create.post');
    Route::put('admin/colour/edit/{id}', [ColourController::class, 'store'])->name('admin.colour.edit.post'); // Updated to PUT method
    Route::delete('admin/colour/delete-selected', [ColourController::class, 'deleteSelected'])->name('admin.colour.deleteSelected');
    Route::get('admin/colour/export', [ColourController::class, 'export'])->name('admin.colour.export');
    Route::post('admin/colour/import', [ColourController::class, 'import'])->name('admin.colour.import');
    Route::get('/sample-file-download-colour', [ColourController::class, 'sampleFileDownloadColour'])->name('sample-file-download-colour');



    // -----------------------------------Size Routes--------------------------------------



    Route::get('admin/size', [SizeController::class, 'index'])->name('admin.size');
    Route::post('admin/size/update-status/{id}', [SizeController::class, 'updateStatus'])->name('admin.size.status');
    Route::get('admin/size/create', [SizeController::class, 'create'])->name('admin.size.create');
    Route::get('admin/size/edit/{id}', [SizeController::class, 'edit'])->name('admin.size.edit');
    Route::get('admin/size/delete/{id}', [SizeController::class, 'remove'])->name('admin.size.delete');
    Route::post('admin/size/create', [SizeController::class, 'store'])->name('admin.size.create.post');
    Route::put('admin/size/edit/{id}', [SizeController::class, 'store'])->name('admin.size.edit.post'); // Updated to PUT method
    Route::delete('admin/size/delete-selected', [SizeController::class, 'deleteSelected'])->name('admin.size.deleteSelected');
    Route::get('admin/size/export', [SizeController::class, 'export'])->name('admin.size.export');
    Route::post('admin/size/import', [SizeController::class, 'import'])->name('admin.size.import');
    Route::get('/sample-file-download-size', [SizeController::class, 'sampleFileDownloadSize'])->name('sample-file-download-size');
 




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
    Route::get('/sample-file-download-category', [CategoryController::class, 'sampleFileDownloadCategory'])->name('sample-file-download-category');
    Route::get('admin/category/sample-file-download', [CategoryController::class, 'downloadSampleFile'])->name('admin.category.download-sample-file');



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
    Route::get('/sample-file-download-brand', [BrandController::class, 'sampleFileDownloadBrand'])->name('sample-file-download-brand');



    // --------------------------------------Plans Routes----------------------------------

    Route::get('admin/plan', [PlanController::class, 'index'])->name('admin.plan');
    Route::post('admin/plan/update-status/{id}', [PlanController::class, 'updateStatus'])->name('admin.plan.status');
    Route::get('admin/plan/create', [PlanController::class, 'create'])->name('admin.plan.create');
    Route::get('admin/plan/edit/{id}', [PlanController::class, 'edit'])->name('admin.plan.edit');
    Route::get('admin/plan/delete/{id}', [PlanController::class, 'remove'])->name('admin.plan.delete');
    Route::post('admin/plan/create', [PlanController::class, 'store'])->name('admin.plan.create.post');
    Route::post('admin/plan/edit/{id}', [PlanController::class, 'store'])->name('admin.plan.edit.post');
    Route::delete('admin/plan/delete-selected', [PlanController::class, 'deleteSelected'])->name('admin.plan.deleteSelected');
    Route::get('admin/plan/export', [PlanController::class, 'export'])->name('admin.plan.export');
    Route::post('admin/plan/import', [PlanController::class, 'import'])->name('admin.plan.import');
    Route::get('/sample-file-download-plan', [PlanController::class, 'sampleFileDownloadPlan'])->name('sample-file-download-plan');
  





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
    Route::get('/sample-file-download-productgroup', [ProductGroupsController::class, 'sampleFileDownloadProductGroup'])->name('sample-file-download-productgroup');


    // -------------------------------------Product Routes---------------------------------------


    Route::get('admin/product', [ProductsController::class, 'index'])->name('admin.product');
    Route::post('admin/product/update-status/{id}', [ProductsController::class, 'updateStatus'])->name('admin.product.status');
    Route::get('admin/product/create', [ProductsController::class, 'create'])->name('admin.product.create');
    Route::get('admin/product/edit/{id}', [ProductsController::class, 'edit'])->name('admin.product.edit');
    Route::get('admin/product/delete/{id}', [ProductsController::class, 'remove'])->name('admin.product.delete');
    Route::post('admin/product/create', [ProductsController::class, 'store'])->name('admin.product.create.post');
    Route::put('admin/product/edit/{id}', [ProductsController::class, 'update'])->name('admin.product.edit.post');
    Route::delete('admin/product/delete-selected', [ProductsController::class, 'deleteSelected'])->name('admin.product.deleteSelected');
    Route::get('admin/product/export', [ProductsController::class, 'export'])->name('admin.product.export');
    Route::post('admin/product/import', [ProductsController::class, 'import'])->name('admin.product.import');
    Route::get('/sample-file-download-product', [ProductsController::class, 'sampleFileDownloadProduct'])->name('sample-file-download-product');


    // ------------------------------------Dealers Routes----------------------------------------

    Route::get('admin/dealers', [DealersController::class, 'index'])->name('admin.dealers');
    Route::post('admin/dealer/update-status/{id}', [DealersController::class, 'updateStatus'])->name('admin.grouprelation.status');
    Route::get('admin/dealers/create', [DealersController::class, 'create'])->name('admin.dealers.create');
    Route::get('admin/dealers/edit/{id}', [DealersController::class, 'edit'])->name('admin.dealers.edit');
    Route::post('admin/dealers/edit/{id}', [DealersController::class, 'store'])->name('admin.dealers.edit.post');
    Route::post('admin/dealers/create', [DealersController::class, 'store'])->name('admin.dealers.create.post');
    Route::get('admin/dealers/delete/{id}', [DealersController::class, 'remove'])->name('admin.dealers.delete');
    Route::delete('admin/dealers/delete-selected', [DealersController::class, 'deleteSelected'])->name('admin.dealers.deleteSelected');
    Route::get('admin/dealers/export', [DealersController::class, 'export'])->name('admin.dealers.export');
    Route::post('admin/dealers/import', [DealersController::class, 'import'])->name('admin.dealers.import');
    // Route::get('admin/dealers/view/{id}', [DealersController::class, 'view'])->name('admin.dealers.view');
    Route::get('/sample-file-download-dealer', [DealersController::class, 'sampleFileDownloadDealer'])->name('sample-file-download-dealer');


    // -------------------------------------Dealers View Routes----------------------------------
    Route::get('admin/dealers/view/{id}', [ViewsController::class, 'getData'])->name('admin.dealers.viewdata');
    Route::post('admin/dealers/view/create/{id}', [ViewsController::class, 'store'])->name('admin.dealers.view.create.post');
    Route::post('admin/dealers/view/setprimary', [ViewsController::class, 'setPrimary'])->name('admin.dealers.view.contact.setprimary');



    Route::post('admin/dealers/{id}/update-primary-contact', [DealersController::class, 'updatePrimaryContact'])->name('admin.dealers.updatePrimary');
    //-------------------------------------Conatact Persons--------------------------------------

    Route::get('admin/contact-persons', [ContactPersonController::class, 'index'])->name('admin.contactPersons');
    Route::post('admin/contact-persons/update-status/{id}', [ContactPersonController::class, 'updateStatus'])->name('admin.contactPersons.status');
    Route::get('admin/contact-persons/create', [ContactPersonController::class, 'create'])->name('admin.contactPersons.create');
    Route::post('admin/contact-persons/create', [ContactPersonController::class, 'store'])->name('admin.contactPersons.create.post');
    Route::get('admin/contact-persons/edit/{id}', [ContactPersonController::class, 'edit'])->name('admin.contactPersons.edit');
    Route::delete('admin/contact-persons/delete-selected', [ContactPersonController::class, 'deleteSelected'])->name('admin.contactPersons.deleteSelected');
    Route::get('admin/contact-persons/export', [ContactPersonController::class, 'export'])->name('admin.contactPersons.export');
    Route::post('admin/contact-persons/import', [ContactPersonController::class, 'import'])->name('admin.contactPersons.import');
    Route::delete('admin/contact-persons/delete-selected', [ContactPersonController::class, 'deleteSelected'])->name('admin.contactPersons.deleteSelected');
<<<<<<< HEAD
    Route::get('/sample-file-download-contactpersons', [ContactPersonController::class, 'sampleFileDownloadDealer'])->name('sample-file-download-contactpersons');
=======
    Route::get('/sample-file-download-contactperson', [ContactPersonController::class, 'sampleFileDownloadContactPerson'])->name('sample-file-download-contactperson');

>>>>>>> 9021d0a439904143981df9aa966756f289ba0c59




    Route::resource('blogs', BlogController::class);
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');


});







