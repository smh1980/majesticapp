<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Item;
use App\Models\Customer;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

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
    }

    // add items to cart method
    public function addToCart($item_id, $selectedCustomer) {
        $total_count = CartManagement::addItemToCart($item_id, $selectedCustomer);
        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);
        $this->alert('success', 'Item added to the cart successfully!', [
            'position' => 'bottom-end',
            'timer' => 3000,
            'toast' => true,
           ]);
    }

    public function updatedSelectedCustomer($customerId)
    {
        if ($customerId) {
            $this->items = Item::whereHas('prices', function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })->with(['prices' => function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            }])->get();

            // Initialize image index for each item
            foreach ($this->items as $item) {
                $this->imageIndexes[$item->id] = 0;
            }
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

