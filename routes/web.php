<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminProfilController;
use App\Http\Controllers\Admin\AdminSliderController;
use App\Http\Controllers\Admin\AdminFeatureController;
use App\Http\Controllers\Admin\AdminTestimonialController;


use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\AboutController;

/* ------------------------- Front--------------------- */

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');







/* ---------------------- Admin ---------------------- */




// Login
Route::get('/admin/login', [AdminLoginController::class, 'index'])->name('admin_login');
Route::post('/admin/login-submit', [AdminLoginController::class, 'login_submit'])->name('admin_login_submit');

// Logout
Route::get('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin_logout');

// Forget password
Route::get('/admin/forget-password', [AdminLoginController::class, 'forget_password'])->name('admin_forget_password');
Route::post('/admin/forget-password-submit', [AdminLoginController::class, 'forget_password_submit'])->name('admin_forget_password_submit');

// Reset pwd
Route::get('/admin/reset-password/{token}/{email}', [AdminLoginController::class, 'reset_password'])->name('admin_reset_password');
Route::post('/admin/reset-password/{token}/{email}', [AdminLoginController::class, 'reset_password_submit'])->name('admin_reset_password_submit');



// Admin - Middleware
Route::group(['middleware' => 'admin'], function(){ 

   //Dashbord Home
   Route::get('/admin/home', [AdminHomeController::class, 'index'])->name('admin_home');

   // Edit profile
   Route::get('/admin/edit-profile', [AdminProfilController::class, 'index'])->name('admin_profile');
   Route::post('/admin/edit-profile-submit', [AdminProfilController::class, 'profile_submit'])->name('admin_profile_submit');

   // Slider
   Route::get('/admin/slide/view', [AdminSliderController::class, 'index'])->name('admin_slide_view');
   Route::get('/admin/slide/add', [AdminSliderController::class, 'add'])->name('admin_slide_add');
   Route::post('/admin/slide/store', [AdminSliderController::class, 'store'])->name('admin_slide_store');
   Route::get('/admin/slide/edit/{id}', [AdminSliderController::class, 'edit'])->name('admin_slide_edit');
   Route::post('/admin/slide/edit/{id}', [AdminSliderController::class, 'update'])->name('admin_slide_update');
   Route::get('/admin/slide/delete/{id}', [AdminSliderController::class, 'delete'])->name('admin_slide_delete');

   // Feature
   Route::get('/admin/feature/view', [AdminFeatureController::class, 'index'])->name('admin_feature_view');
   Route::get('/admin/feature/add', [AdminFeatureController::class, 'add'])->name('admin_feature_add');
   Route::post('/admin/feature/store', [AdminFeatureController::class, 'store'])->name('admin_feature_store');
   Route::get('/admin/feature/edit/{id}', [AdminFeatureController::class, 'edit'])->name('admin_feature_edit');
   Route::post('/admin/feature/edit/{id}', [AdminFeatureController::class, 'update'])->name('admin_feature_update');
   Route::get('/admin/feature/delete/{id}', [AdminFeatureController::class, 'delete'])->name('admin_feature_delete');

   // Testimonial
   Route::get('/admin/testimonial/view', [AdminTestimonialController::class, 'index'])->name('admin_testimonial_view');
   Route::get('/admin/testimonial/add', [AdminTestimonialController::class, 'add'])->name('admin_testimonial_add');
   Route::post('/admin/testimonial/store', [AdminTestimonialController::class, 'store'])->name('admin_testimonial_store');
   Route::get('/admin/testimonial/edit/{id}', [AdminTestimonialController::class, 'edit'])->name('admin_testimonial_edit');
   Route::post('/admin/testimonial/edit/{id}', [AdminTestimonialController::class, 'update'])->name('admin_testimonial_update');
   Route::get('/admin/testimonial/delete/{id}', [AdminTestimonialController::class, 'delete'])->name('admin_testimonial_delete');





});
