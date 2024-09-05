<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use App\Models\OrderItem;
use App\Exports\OrderExport;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use App\Helpers\CartManagement;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Livewire\Partials\Navbar;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cookie;
use Jantinnerezo\LivewireAlert\LivewireAlert;

#[Title('Majestic Aap. - Cart')]
class CartPage extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $cart_items = [];
    public $grand_total;
    public $subtotal;
    public $vat;
    public $customerId;


    public function mount($customerId = null){
        $this->cart_items = CartManagement::getCartItemsFromCookie();
        $this->subtotal = CartManagement::calculateGrandTotal($this->cart_items);
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
        $this->customerId = Cookie::get('selected_customer_id');
    }

    public function incrementQuantity($itemId)
    {
        $updatedItems = CartManagement::incrementQuantityToCartItem($itemId);
        $this->updateCartState($updatedItems);
    }

    public function decrementQuantity($itemId)
    {
        $updatedItems = CartManagement::decrementQuantityToCartItem($itemId);
        $this->updateCartState($updatedItems);
    }

    public function updateQuantity($itemId, $newQuantity)
    {
        $newQuantity = max(1, intval($newQuantity)); // Ensure quantity is at least 1
        $updatedItems = CartManagement::updateItemQuantity($itemId, $newQuantity);
        $this->refreshCartState($updatedItems);
    }

    private function refreshCartState($updatedItems = null)
    {
        if ($updatedItems === null) {
            $this->cart_items = CartManagement::getCartItemsFromCookie();
        } else {
            $this->cart_items = $updatedItems;
        }
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
    }

    public function removeItem($itemId)
    {
        // $updatedItems = CartManagement::removeItemFromCart($itemId);
        $this->cart_items = CartManagement::removeItemFromCart($itemId);
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
        $this->updateCartState($this->cart_items);
        $this->dispatch('update-cart-count', total_count: count($this->cart_items))->to(Navbar::class);
        $this->alert('success', 'Item removed from the cart successfully!', [
            'position' => 'bottom-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    private function updateCartState($updatedItems)
    {
        $this->cart_items = $updatedItems;
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
    }

    // public function placeOrder()
    // {
    //     // 1. Save the order
    //     $order = $this->saveOrder();

    //     // 2. Generate PDF
    //     // $this->generatePDF($order);
    //     $pdf = $this->generatePDF($order);
        
    //     $this->dispatch('fileDownload', [
    //         'content' => base64_encode($pdf->output()),
    //         'contentType' => 'application/pdf',
    //         'fileName' => 'order_' . $order->id . '.pdf'
    //     ]);

    //     // 3. Generate Excel
    //     $this->generateExcel($order);

    //     // 4. Clear the cart
    //     CartManagement::clearCartItems();

    //     // 5. Update component state
    //     $this->cart_items = [];
    //     $this->grand_total = 0;

    //     // 6. Show success message
    //     $this->alert('success', 'Order placed successfully!', [
    //         'position' => 'top-end',
    //         'timer' => 3000,
    //         'toast' => true,
    //     ]);

    //     // 7. Emit event to update cart count in navbar
    //     $this->dispatch('update-cart-count', total_count: 0)->to(Navbar::class);
    //     // return $this->generatePDF($order);
    // }

    // public function placeOrder()
    // {
    //     try {
    //         // 1. Save the order
    //         $order = $this->saveOrder();

    //         if (!$order) {
    //             throw new \Exception('Failed to create order.');
    //         }

    //         // 2. Generate PDF
    //         $pdf = $this->generatePDF($order);
            
    //         if (!$pdf) {
    //             throw new \Exception('Failed to generate PDF.');
    //         }

    //         $pdfContent = $pdf->output();
            
    //         if (empty($pdfContent)) {
    //             throw new \Exception('PDF content is empty.');
    //         }

    //         $this->dispatch('fileDownload', [
    //             'content' => base64_encode($pdfContent),
    //             'contentType' => 'application/pdf',
    //             'fileName' => 'order_' . $order->id . '.pdf'
    //         ]);

    //         // 3. Generate Excel
    //         $this->generateExcel($order);

    //         // 4. Clear the cart
    //         CartManagement::clearCartItems();

    //         // 5. Update component state
    //         $this->cart_items = [];
    //         $this->grand_total = 0;

    //         // 6. Show success message
    //         $this->alert('success', 'Order placed successfully!', [
    //             'position' => 'top-end',
    //             'timer' => 3000,
    //             'toast' => true,
    //         ]);

    //         // 7. Emit event to update cart count in navbar
    //         $this->dispatch('update-cart-count', total_count: 0)->to(Navbar::class);

    //     } catch (\Exception $e) {
    //         // Log the error
    //         \Log::error('Order placement failed: ' . $e->getMessage());
            
    //         // Show error message to user
    //         $this->alert('error', 'Failed to place order: ' . $e->getMessage(), [
    //             'position' => 'top-end',
    //             'timer' => 5000,
    //             'toast' => true,
    //         ]);
    //     }
    // }
//     public function placeOrder()
// {
//     try {
//         $order = $this->saveOrder();

//         if (!$order) {
//             throw new \Exception('Failed to create order.');
//         }

//         $pdf = $this->generatePDF($order);
        
//         if (!$pdf) {
//             throw new \Exception('Failed to generate PDF.');
//         }

//         $pdfContent = $pdf->output();
        
//         if (empty($pdfContent)) {
//             throw new \Exception('PDF content is empty.');
//         }

//         $this->dispatch('fileDownload', [
//             'content' => base64_encode($pdfContent),
//             'contentType' => 'application/pdf',
//             'fileName' => 'order_' . $order->id . '.pdf'
//         ]);

//         // Rest of your method...

//     } catch (\Exception $e) {
//         \Log::error('Order placement failed: ' . $e->getMessage());
//         $this->alert('error', 'Failed to place order: ' . $e->getMessage(), [
//             'position' => 'top-end',
//             'timer' => 5000,
//             'toast' => true,
//         ]);
//     }
// }

    // public function placeOrder()
    // {
    //     try {
    //         // 1. Save the order
    //         $order = $this->saveOrder();

    //         if (!$order) {
    //             throw new \Exception('Failed to create order.');
    //         }

    //         // 2. Generate PDF
    //         $pdf = $this->generatePDF($order);
            
    //         if (!$pdf) {
    //             throw new \Exception('Failed to generate PDF.');
    //         }

    //         $pdfContent = $pdf->output();
            
    //         if (empty($pdfContent)) {
    //             throw new \Exception('PDF content is empty.');
    //         }

    //         // Save the PDF content to a file for debugging
    //         $filename = 'order_' . $order->id . '.pdf';
    //         $path = storage_path('app/public/' . $filename);
    //         file_put_contents($path, $pdfContent);

    //         // Generate a temporary URL for the file
    //         $url = url('storage/' . $filename);

    //         // Dispatch an event to open the PDF in a new tab
    //         $this->dispatch('openPdfInNewTab', ['url' => $url]);

    //         // 3. Generate Excel (if needed)
    //         // $this->generateExcel($order);

    //         // 4. Clear the cart
    //         CartManagement::clearCartItems();

    //         // 5. Update component state
    //         $this->cart_items = [];
    //         $this->grand_total = 0;

    //         // 6. Show success message
    //         $this->alert('success', 'Order placed successfully!', [
    //             'position' => 'top-end',
    //             'timer' => 3000,
    //             'toast' => true,
    //         ]);

    //         // 7. Emit event to update cart count in navbar
    //         $this->dispatch('update-cart-count', total_count: 0)->to(Navbar::class);

    //     } catch (\Exception $e) {
    //         \Log::error('Order placement failed: ' . $e->getMessage());
    //         $this->alert('error', 'Failed to place order: ' . $e->getMessage(), [
    //             'position' => 'top-end',
    //             'timer' => 5000,
    //             'toast' => true,
    //         ]);
    //     }
    // }

    // public function placeOrder()
    // {
    //     try {
    //         // 1. Save the order
    //         $order = $this->saveOrder();

    //         if (!$order) {
    //             throw new \Exception('Failed to create order.');
    //         }

    //         // 2. Generate PDF
    //         $pdfResult = $this->generatePDF($order);
            
    //         if (!$pdfResult['success']) {
    //             throw new \Exception($pdfResult['message']);
    //         }

    //         // 3. Clear the cart
    //         CartManagement::clearCartItems();

    //         // 4. Update component state
    //         $this->cart_items = [];
    //         $this->grand_total = 0;

    //         // 5. Show success message
    //         $this->alert('success', 'Order placed successfully!', [
    //             'position' => 'top-end',
    //             'timer' => 3000,
    //             'toast' => true,
    //         ]);

    //         // 6. Emit event to update cart count in navbar
    //         $this->dispatch('update-cart-count', total_count: 0)->to(Navbar::class);

    //         // Generate PDF
    //         $pdf = Pdf::loadView('pdf.order', ['order' => $order]);
    //         $content = base64_encode($pdf->output());

    //         // 7. Emit event to view PDF
    //         $filename = 'order_' . $order->id . '.pdf';
    //         $path = storage_path('app/public/' . $filename);
    //         $content = base64_encode(file_get_contents($path));
    //         $this->dispatch('viewPdf', [
    //             'content' => $content,
    //             'contentType' => 'application/pdf',
    //             'fileName' => 'order_' . $order->id . '.pdf'
    //         ]);
    
    //         \Log::info('ViewPdf event dispatched', ['order_id' => $order->id]);
    
    //     } catch (\Exception $e) {
    //         \Log::error('Order placement failed: ' . $e->getMessage());
    //         $this->alert('error', 'Failed to place order: ' . $e->getMessage(), [
    //             'position' => 'top-end',
    //             'timer' => 5000,
    //             'toast' => true,
    //         ]);
    //     }
    // }


public function placeOrder()
{
    try {
        // 1. Save the order
        $order = $this->saveOrder();

        if (!$order) {
            throw new \Exception('Failed to create order.');
        }

        // 2. Generate PDF
        $pdf = Pdf::loadView('pdf.orderPDF', ['order' => $order]);
        $content = base64_encode($pdf->output());

        // 3. Clear the cart
        CartManagement::clearCartItems();

        // 4. Update component state
        $this->cart_items = [];
        $this->grand_total = 0;

        // 5. Show success message
        $this->alert('success', 'Order placed successfully!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);

        // 6. Emit event to update cart count in navbar
        $this->dispatch('update-cart-count', total_count: 0)->to(Navbar::class);

        // 7. Emit event to view PDF
        $this->dispatch('viewPdf', [
            'content' => $content,
            'contentType' => 'application/pdf',
            'fileName' => 'order_' . $order->id . '.pdf'
        ]);

        \Log::info('ViewPdf event dispatched', ['order_id' => $order->id]);

    } catch (\Exception $e) {
        \Log::error('Order placement failed: ' . $e->getMessage());
        $this->alert('error', 'Failed to place order: ' . $e->getMessage(), [
            'position' => 'top-end',
            'timer' => 5000,
            'toast' => true,
        ]);
    }
}

    // private function saveOrder()
    // {
    //     $order = Order::create([
    //         'customer_id' => $this->customerId,
    //         'user_id' => auth()->id(),
    //         'orders_total_amount' => $this->grand_total,
    //         'vat' => $this->grand_total * 5 / 100,
    //         'grand_total' => $this->grand_total + $this->grand_total * 5 / 100,
    //     ]);

    //     foreach ($this->cart_items as $item) {
    //         OrderItem::create([
    //             'order_id' => $order->id,
    //             'item_id' => $item['item_id'],
    //             'quantity' => $item['quantity'],
    //             'unit_price' => $item['price'],
    //             'vat' => $item['price'] * $item['quantity'] * 5 / 100,
    //             'total_price' => $item['total_amount'],
    //         ]);
    //     }

    //     return $order;
    // }

    // private function generatePDF($order)
    // {
    //     $debug = [
    //         'order_id' => $order->id ?? 'No ID',
    //         'customer_id' => $order->customer_id ?? 'No customer ID',
    //         'total_amount' => $order->total_amount ?? 'No total amount',
    //         'items_count' => count($this->cart_items),
    //     ];

    //     $pdf = Pdf::loadView('pdf.orderPDF', [
    //         'order' => $order, 
    //         'items' => $this->cart_items,
    //         'debug' => $debug,
    //     ]);

    //     // Add debug information to the PDF
    //     $pdf->output();
    //     $pdfContent = $pdf->output();
    //     file_put_contents(storage_path('app/debug.txt'), json_encode($debug));
        
    //     return $pdf;
    // }

    // private function saveOrder()
    // {
    //     $order = Order::create([
    //         'customer_id' => $this->customerId,
    //         'user_id' => auth()->id(),
    //         'orders_total_amount' => $this->grand_total,
    //         'vat' => $this->grand_total * 5 / 100,
    //         'grand_total' => $this->grand_total + $this->grand_total * 5 / 100,
    //     ]);

    //     foreach ($this->cart_items as $item) {
    //         OrderItem::create([
    //             'order_id' => $order->id,
    //             'item_id' => $item['item_id'],
    //             'quantity' => $item['quantity'],
    //             'unit_price' => $item['price'],
    //             'vat' => $item['price'] * $item['quantity'] * 5 / 100,
    //             'total_price' => $item['total_amount'],
    //         ]);
    //     }

    //     return $order->fresh(); // This will reload the order with its relationships
    // }

    private function saveOrder()
{
    $order = Order::create([
        'customer_id' => $this->customerId,
        'user_id' => auth()->id(),
        'orders_total_amount' => $this->grand_total,
        'vat' => $this->grand_total * 5 / 100,
        'grand_total' => $this->grand_total + $this->grand_total * 5 / 100,
    ]);

    foreach ($this->cart_items as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'item_id' => $item['item_id'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['price'],
            'vat' => $item['price'] * $item['quantity'] * 5 / 100,
            'total_price' => $item['total_amount'],
        ]);
    }

    return $order->fresh(['items.item', 'customer']);
}

    // private function generatePDF($order)
    // {
    //     $debug = [
    //         'order_id' => $order->id ?? 'No ID',
    //         'customer_id' => $order->customer_id ?? 'No customer ID',
    //         'orders_total_amount' => $order->orders_total_amount ?? 'No total amount',
    //         'grand_total' => $order->grand_total ?? 'No grand total',
    //         'items_count' => count($this->cart_items),
    //         'cart_items' => $this->cart_items,
    //         'order_attributes' => $order->getAttributes(),
    //     ];

    //     $pdf = Pdf::loadView('pdf.orderPDF', [
    //         'order' => $order, 
    //         'items' => $this->cart_items,
    //         'debug' => $debug,
    //     ]);

    //     // Add debug information to the PDF
    //     $pdfContent = $pdf->output();
    //     file_put_contents(storage_path('app/debug.txt'), json_encode($debug, JSON_PRETTY_PRINT));
        
    //     if (empty($pdfContent)) {
    //         \Log::error('PDF content is empty. Debug info: ' . json_encode($debug));
    //     }

    //     return $pdf;
    // }

//     private function generatePDF($order)
// {
//     $debug = [
//         'order_id' => $order->id ?? 'No ID',
//         'customer_id' => $order->customer_id ?? 'No customer ID',
//         'orders_total_amount' => $order->orders_total_amount ?? 'No total amount',
//         'grand_total' => $order->grand_total ?? 'No grand total',
//         'items_count' => count($this->cart_items),
//         'cart_items' => $this->cart_items,
//         'order_attributes' => $order->getAttributes(),
//     ];

//     try {
//         $pdf = Pdf::loadView('pdf.orderPDF', [
//             'order' => $order, 
//             'items' => $this->cart_items,
//             'debug' => $debug,
//         ]);

//         $pdfContent = $pdf->output();

//         if (empty($pdfContent)) {
//             throw new \Exception('PDF content is empty');
//         }

//         // Save the PDF content to a file for debugging
//         file_put_contents(storage_path('app/test_order.pdf'), $pdfContent);

//         file_put_contents(storage_path('app/debug.txt'), json_encode($debug, JSON_PRETTY_PRINT));
        
//         return $pdf;
//     } catch (\Exception $e) {
//         \Log::error('PDF generation failed: ' . $e->getMessage());
//         file_put_contents(storage_path('app/pdf_error.txt'), $e->getMessage() . "\n" . $e->getTraceAsString());
//         throw $e;
//     }
// }

    // private function generatePDF($order)
    // {
    //     // dd($order['total_price']);
    //     $debug = [
    //         'order_id' => $order->id ?? 'No ID',
    //         'customer_id' => $order->customer_id ?? 'No customer ID',
    //         'orders_total_amount' => $order->orders_total_amount ?? 'No total amount',
    //         'total_amount' => $order->total_amount ?? 'No total amount',
    //         'grand_total' => $order->grand_total ?? 'No grand total',
    //         'items_count' => $order->items->count(),
    //         'cart_items' => $order->items,
    //         'order_attributes' => $order->getAttributes(),
    //     ];

    //     $pdf = Pdf::loadView('pdf.orderPDF', [
    //         'order' => $order, 
    //         'items' => $order->items,
    //         'item' => $order->item,
    //         'debug' => $debug,
    //     ]);
    //     // dd($pdf);
    //     $pdf->set_paper('A4', 'landscape');
    //     $pdf->render();
    //     return $pdf;
    // }

    // private function generatePDF($order)
    // {
    //     try {
    //         $debug = [
    //             'order_id' => $order->id ?? 'No ID',
    //             'customer_id' => $order->customer_id ?? 'No customer ID',
    //             'orders_total_amount' => $order->orders_total_amount ?? 'No total amount',
    //             'total_amount' => $order->total_amount ?? 'No total amount',
    //             'grand_total' => $order->grand_total ?? 'No grand total',
    //             'items_count' => $order->items->count(),
    //             'cart_items' => $order->items,
    //             'order_attributes' => $order->getAttributes(),
    //         ];

    //         $pdf = Pdf::loadView('pdf.orderPDF', [
    //             'order' => $order, 
    //             'items' => $order->items,
    //             'item' => $order->item,
    //             'debug' => $debug,
    //         ]);

    //         $pdf->set_paper('A4', 'landscape');
    //         $pdf->render();

    //         // Save the PDF to a file
    //         $filename = 'order_' . $order->id . '.pdf';
    //         $path = storage_path('app/public/' . $filename);
    //         $pdf->save($path);

    //         return ['success' => true, 'message' => 'PDF generated successfully', 'path' => $path];
    //     } catch (\Exception $e) {
    //         \Log::error('PDF generation failed: ' . $e->getMessage());
    //         return ['success' => false, 'message' => 'Failed to generate PDF: ' . $e->getMessage()];
    //     }
    // }

    private function generatePDF($order)
    {
        try {
            $order = $order->load('items.item', 'customer'); // Eager load relationships
    
            $pdf = Pdf::loadView('pdf.orderPDF', [
                'order' => $order
            ]);
    
            $pdf->set_paper('A4', 'landscape');
            $pdf->render();
    
            // Save the PDF to a file
            $filename = 'order_' . $order->id . '.pdf';
            $path = storage_path('app/public/' . $filename);
            $pdf->save($path);
    
            return ['success' => true, 'message' => 'PDF generated successfully', 'path' => $path];
        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to generate PDF: ' . $e->getMessage()];
        }
    }

    private function generateExcel($order)
    {
        return Excel::download(new OrderExport($order, $this->cart_items), 'order_'.$order->id.'.xlsx');
    }

    public function render()
    {
        return view('livewire.cart-page');
    }
}
