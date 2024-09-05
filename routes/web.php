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
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

// Route::get('/test-pdf/{order}', function (Order $order) {
//     $debug = [
//         'order_id' => $order->id ?? 'No ID',
//         'customer_id' => $order->customer_id ?? 'No customer ID',
//         'orders_total_amount' => $order->orders_total_amount ?? 'No total amount',
//         'grand_total' => $order->grand_total ?? 'No grand total',
//         'items_count' => $order->items->count(),
//         'cart_items' => $order->items,
//         'order_attributes' => $order->getAttributes(),
//     ];

//     try {
//         $pdf = Pdf::loadView('pdf.orderPDF', [
//             'order' => $order, 
//             'items' => $order->items,
//             'debug' => $debug,
//         ]);

//         return $pdf->stream('test.pdf');
//     } catch (\Exception $e) {
//         return response()->json([
//             'error' => $e->getMessage(),
//             'trace' => $e->getTraceAsString()
//         ], 500);
//     }
// });

use Illuminate\Support\Facades\Storage;

// Route::get('/download-pdf/{order}', function (Order $order) {
//     $filename = 'order_' . $order->id . '.pdf';
//     $path = storage_path('app/public/' . $filename);
    
//     if (file_exists($path)) {
//         return response()->file($path, [
//             'Content-Type' => 'application/pdf',
//             'Content-Disposition' => 'inline; filename="' . $filename . '"',
//         ]);
//     } else {
//         abort(404, 'PDF file not found');
//     }
// })->name('download.pdf');
use Illuminate\Support\Facades\Response;

Route::get('/view-pdf/{order}', function (Order $order) {
    \Log::info('View PDF route hit', ['order_id' => $order->id]);
    $filename = 'order_' . $order->id . '.pdf';
    $path = storage_path('app/public/' . $filename);
    
    if (file_exists($path)) {
        \Log::info('PDF file found', ['path' => $path]);
        $file = file_get_contents($path);
        $response = Response::make($file, 200);
        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'inline; filename="' . $filename . '"');
        return $response;
    } else {
        \Log::error('PDF file not found', ['path' => $path]);
        abort(404, 'PDF file not found');
    }
})->name('view.pdf');

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
Route::get('/order/{id}/pdf', [CartPage::class, 'generatePDF'])->name('order.pdf');

Route::get('/success', SuccessPage::class)->name('success');
// Route::get('/success/{orderId}', SuccessPage::class)->name('success');
Route::get('/cancel', CancelPage::class)->name('cancel');
