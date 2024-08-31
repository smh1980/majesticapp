<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\ProductsCategory;

#[Title('Home Page - Majestic')]
class HomePage extends Component
{
    public $productsCategories;
    public $activeSlide = 0;
    public $slides = [
        ['type' => 'image', 'src' => 'hp_assets/image-1.svg'],
        ['type' => 'image', 'src' => 'hp_assets/image-2.jpg'],
        // ['type' => 'video', 'src' => 'hp_assets/video-1.mov'],
        // ['type' => 'video', 'src' => 'hp_assets/video-2.mp4']
    ];

    public function mount()
    {
        $this->productsCategories = ProductsCategory::where('is_active', 1)->get();
    }

    public function nextSlide()
    {
        $this->activeSlide = ($this->activeSlide + 1) % count($this->slides);
    }

    public function render()
    {
        return view('livewire.home-page');
    }
}