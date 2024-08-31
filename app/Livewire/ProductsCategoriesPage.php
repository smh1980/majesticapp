<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\ProductsCategory;


#[Title('Products Category - Majestic')]
class ProductsCategoriesPage extends Component
{
    public function render()
    {
        $productsCategories = ProductsCategory::where('is_active', 1)->get();
        return view('livewire.products-categories-page', [
            'productsCategories' => $productsCategories,
        ]);
    }
}
