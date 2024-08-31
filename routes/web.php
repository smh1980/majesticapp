<?php

use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\CancelPage;
use App\Livewire\CartPage;
use App\Livewire\HomePage;
use App\Livewire\ItemsPage;
use App\Livewire\MyOrderDetailPage;
use App\Livewire\MyOrdersPage;
use App\Livewire\ProductsCategoriesPage;
use App\Livewire\SuccessPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class);
Route::get('/productscategories', ProductsCategoriesPage::class);
Route::get('/items', ItemsPage::class);
Route::get('/cart', CartPage::class);

Route::get('/my-orders', MyOrdersPage::class);
Route::get('/my-orders/{order}', MyOrderDetailPage::class);


Route::get('/login', LoginPage::class)->name('login');
Route::get('/register', RegisterPage::class);
Route::get('/forgot', ForgotPasswordPage::class)->name('password.request');
Route::get('/reset/{token}', ResetPasswordPage::class)->name('password.reset');

Route::get('/success', SuccessPage::class)->name('success');
// Route::get('/success/{orderId}', SuccessPage::class)->name('success');
Route::get('/cancel', CancelPage::class)->name('cancel');