<?php

namespace App\Livewire;

use App\Models\Item;
use Livewire\Component;
use App\Models\Customer;
use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use Illuminate\Support\Facades\Cookie;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ItemsPage extends Component
{
    use LivewireAlert;
    public $selectedCustomer = null;
    public $customers;
    public $items = [];
    public $imageIndexes = []; // Track image index for each item

    public function mount()
    {
        $this->customers = Customer::all();
        $this->items = Item::with('prices')->get();
        $this->items = collect();
        $lastSelectedCustomerId = Cookie::get('selected_customer_id');
        if ($lastSelectedCustomerId) {
            $this->selectedCustomer = $lastSelectedCustomerId;
            $this->refreshItems($lastSelectedCustomerId);
        } else {
            $this->items = collect();
        }
    }

    public function clearAll()
    {
        // Clear the selected customer
        $this->selectedCustomer = null;

        // Clear the cart
        CartManagement::clearCartItems();

        // Clear the cookie storing the selected customer ID
        Cookie::queue(Cookie::forget('selected_customer_id'));

        // Reset the items collection
        $this->items = collect();

        // Reset image indexes
        $this->imageIndexes = [];

        // Update the cart count in the navbar
        $this->dispatch('update-cart-count', total_count: 0)->to(Navbar::class);

        // Show a success message
        $this->alert('success', 'All data has been cleared.', [
            'position' => 'bottom-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    // add items to cart method
    public function addToCart($itemId, $customerId)
    {
        $item = $this->items->find($itemId);
        $customerPrice = $item->prices->where('customer_id', $customerId)->first();

        if (!$customerPrice) {
            $this->alert('error', 'No price found for this customer.', [
                'position' => 'bottom-end',
                'timer' => 3000,
                'toast' => true,
            ]);
            return;
        }

        $cartItem = [
            'item_id' => $item->id,
            'name' => $item->name,
            'image' => $item->images[0] ?? null,
            'quantity' => 1,
            'price' => $customerPrice->price,
            'total_amount' => $customerPrice->price,
            'customer_id' => $customerId,
            'prices' => [
                [
                    'customer_id' => $customerId,
                    'price' => $customerPrice->price
                ]
            ]
        ];

        $total_count = CartManagement::addItemToCart($cartItem);
        // Store the customerId in a cookie
        Cookie::queue('selected_customer_id', $customerId, 60 * 24 * 30); // 30 days expiration
        $this->refreshItems($customerId);

        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);
        $this->alert('success', 'Item added to the cart successfully!', [
            'position' => 'bottom-end',
            'timer' => 3000,
            'toast' => true,
        ]);

        // Instead of redirecting, you might want to emit an event to update the cart
        $this->dispatch('cartUpdated');
    }

    private function refreshItems($customerId)
    {
        $this->items = Item::whereHas('prices', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })->with(['prices' => function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        }])->get();

        // Reset image indexes if necessary
        foreach ($this->items as $item) {
            $this->imageIndexes[$item->id] = 0;
        }
    }

    public function updatedSelectedCustomer($customerId)
    {
        if ($customerId) {
            // Clear the cart when a new customer is selected
            CartManagement::clearCartItems();

            // Store the new customer ID in a cookie
            Cookie::queue('selected_customer_id', $customerId, 60 * 24 * 30); // 30 days expiration

            $this->items = Item::whereHas('prices', function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })->with(['prices' => function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            }])->get();

            // Initialize image index for each item
            foreach ($this->items as $item) {
                $this->imageIndexes[$item->id] = 0;
            }

            $this->alert('success', 'Customer changed. Cart has been cleared.', [
                'position' => 'bottom-end',
                'timer' => 3000,
                'toast' => true,
            ]);

            // Dispatch an event to update the cart count in the navbar
            $this->dispatch('update-cart-count', total_count: 0)->to(Navbar::class);
        } else {
            $this->items = collect();
        }
    }

    public function nextImage($itemId)
    {
        if (isset($this->items->find($itemId)->images) && is_array($this->items->find($itemId)->images)) {
            $totalImages = count($this->items->find($itemId)->images);
            if ($this->imageIndexes[$itemId] < $totalImages - 1) {
                $this->imageIndexes[$itemId]++;
            }
        }
    }

    public function previousImage($itemId)
    {
        if ($this->imageIndexes[$itemId] > 0) {
            $this->imageIndexes[$itemId]--;
        }
    }

    // public function render()
    // {
    //     return view('livewire.items-page');
    // }
    public function render()
    {
        return view('livewire.items-page', [
            'customerId' => $this->selectedCustomer,
        ]);
    }
}

