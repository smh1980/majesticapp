<?php 

namespace App\Helpers;

use App\Models\Product;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Cookie;

    class AllCalculations {
        
        private static function calculateTotals($quantity, callable $get, Set $set): void
        {
            $unitPrice = floatval($get('unit_price'));
            $subtotal = $quantity * $unitPrice;
            $vat = $subtotal * 0.05; // 5% VAT
            $total = $subtotal + $vat;

            $set('vat', round($vat, 2));
            $set('total_price', round($total, 2));
        }
    }    

?>