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

        return $order->fresh(['items.item', 'customer']); // Eager load relationships
    }

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
