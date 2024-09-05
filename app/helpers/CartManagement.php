<?php

// namespace App\Services;

// use Illuminate\Support\Facades\Session;

// class CartManagement
// {
//     private const CART_SESSION_KEY = 'cart_items';

//     public function addToCart($item)
//     {
//         $cartItems = $this->getCartItems();

//         if (isset($cartItems[$item['id']])) {
//             $cartItems[$item['id']]['quantity'] += 1;
//         } else {
//             $cartItems[$item['id']] = [
//                 'id' => $item['id'],
//                 'name' => $item['name'],
//                 'price' => $item['price'],
//                 'quantity' => 1,
//             ];
//         }

//         $this->updateCartSession($cartItems);
//     }

//     public function removeFromCart($itemId)
//     {
//         $cartItems = $this->getCartItems();

//         if (isset($cartItems[$itemId])) {
//             unset($cartItems[$itemId]);
//             $this->updateCartSession($cartItems);
//         }
//     }

//     public function addCartItemsToSession($items)
//     {
//         $this->updateCartSession($items);
//     }

//     public function clearCartFromSession()
//     {
//         Session::forget(self::CART_SESSION_KEY);
//     }

//     public function getCartItems()
//     {
//         return Session::get(self::CART_SESSION_KEY, []);
//     }

//     public function incrementItemQuantity($itemId)
//     {
//         $cartItems = $this->getCartItems();

//         if (isset($cartItems[$itemId])) {
//             $cartItems[$itemId]['quantity'] += 1;
//             $this->updateCartSession($cartItems);
//         }
//     }

//     public function decrementItemQuantity($itemId)
//     {
//         $cartItems = $this->getCartItems();

//         if (isset($cartItems[$itemId])) {
//             $cartItems[$itemId]['quantity'] = max(1, $cartItems[$itemId]['quantity'] - 1);
//             $this->updateCartSession($cartItems);
//         }
//     }

//     public function calculateGrandTotal()
//     {
//         $cartItems = $this->getCartItems();
//         $total = 0;

//         foreach ($cartItems as $item) {
//             $total += $item['price'] * $item['quantity'];
//         }

//         return $total;
//     }

//     private function updateCartSession($cartItems)
//     {
//         Session::put(self::CART_SESSION_KEY, $cartItems);
//     }
// }

namespace App\Helpers;

use App\Models\Item;
use App\Models\Price;

use Illuminate\Support\Facades\Cookie;

class CartManagement
{
    public static function addItemToCart($cartItem)
    {
        $cart_items = self::getCartItemsFromCookie();
        $existing_item = null;

        foreach ($cart_items as $key => $item) {
            if ($item['item_id'] == $cartItem['item_id'] && $item['customer_id'] == $cartItem['customer_id']) {
                $existing_item = $key;
                break;
            }
        }

        if ($existing_item !== null) {
            $cart_items[$existing_item]['quantity']++;
            $cart_items[$existing_item]['total_amount'] =
                $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['price'];
        } else {
            // Include the price information in the cart item
            $cart_items[] = [
                'item_id' => $cartItem['item_id'],
                'name' => $cartItem['name'],
                'image' => $cartItem['image'],
                'quantity' => 1,
                'price' => $cartItem['price'],
                'total_amount' => $cartItem['price'],
                'customer_id' => $cartItem['customer_id'],
                'prices' => [
                    [
                        'customer_id' => $cartItem['customer_id'],
                        'price' => $cartItem['price']
                    ]
                ]
            ];
        }

        self::addCartItemsToCookie($cart_items);
        return count($cart_items);
    }

    //Remove items from Cart
    static public function removeItemFromCart($item_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['item_id'] == $item_id) {
                unset($cart_items[$key]);
            }
        }

        self::addCartItemsToCookie($cart_items);
        return $cart_items;
    }

    // Add Cart items to session
    static public function addCartItemsToCookie($cart_items)
    {
        // Cookie::queue('cart_items', json_encode($cart_items), 60 * 24 * 30);
        Cookie::queue('cart_items', json_encode($cart_items), 5);
    }

    // Clear Cart items from cookie
    static public function clearCartItems(){
        Cookie::queue(Cookie::forget('cart_items'));
    }

    //Get all Cart items from cookie
    static public function getCartItemsFromCookie(){
        $cart_items = json_decode(Cookie::get('cart_items'), true);
        if(!$cart_items){
            $cart_items = [];
        }
        return $cart_items;
    }

    // Increament item quantity
    static public function incrementQuantityToCartItem($item_id){
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['item_id'] == $item_id) {
                $cart_items[$key]['quantity']++;
                // $cart_items[$key]['total_amount'] =
                //     $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
                $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['price'];
            }
        }

        self::addCartItemsToCookie($cart_items);
        return $cart_items;
    }

    public static function decrementQuantityToCartItem($item_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['item_id'] == $item_id) {
                if ($cart_items[$key]['quantity'] > 1) {
                    $cart_items[$key]['quantity']--;
                    $cart_items[$key]['total_amount'] =
                        $cart_items[$key]['quantity'] * $cart_items[$key]['price'];
                }
            }
        }

        self::addCartItemsToCookie($cart_items);
        return $cart_items;
    }

    public static function updateItemQuantity($itemId, $newQuantity)
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as &$item) {
            if ($item['item_id'] == $itemId) {
                $item['quantity'] = $newQuantity;
                $item['total_amount'] = $item['price'] * $newQuantity;
                break;
            }
        }

        self::addCartItemsToCookie($cart_items);
        return $cart_items;
    }

    //Calculate grand total
    public static function calculateGrandTotal($items){
        return array_sum(array_column($items, 'total_amount'));
    }
}
