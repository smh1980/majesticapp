<div class="w-full max-w-[85rem] bg-[#F5E8EF] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
  <div class="container mx-auto px-4">
    @php
        $customer = App\Models\Customer::find($customerId);
        $customerName = $customer ? $customer->name : 'Unknown Customer';
    @endphp
    <h1 class="text-2xl font-semibold text-slate-500 mb-4">Items in Cart for
        <span class="text-black">{{$customerName}}</span>
    </h1>
    <div class="flex flex-col md:flex-row gap-4">
      <div class="md:w-3/4">
        <div class="bg-white overflow-x-auto rounded-lg shadow-md p-6 mb-4">
          <table class="table-auto w-full">
            <thead class="py-2 rounded-lg">
              <tr class="bg-[#c7edff]">
                <th class="text-center font-semibold">Item Image</th>
                <th class="text-center font-semibold">Item Name</th>
                <th class="text-center font-semibold">Unit Price</th>
                <th class="text-center font-semibold">Unit VAT (<small>@5%</small>)</th>
                <th class="text-center font-semibold">Quantity</th>
                <th class="text-center font-semibold">Total</th>
                <th class="text-center font-semibold">Remove</th>
              </tr>
            </thead>
            <tbody>
                @forelse ($cart_items as $item)
                @php
                $customerName = App\Models\Customer::find($customerId, 'name');
                // dd($customerName);
                    $price = $customerId ? collect($item['prices'])->firstWhere('customer_id', $customerId) : null;
                @endphp
                <tr wire:key="{{$item['item_id']}}">
                    <td class="text-center">
                        <div class="flex justify-center items-center">
                            <img class="h-16 w-16" src="{{ url('storage', $item['image']) }}" alt="{{ $item['name'] }}">
                        </div>
                    </td>

                  <td class="text-center text-sm font-semibold">
                      {{$item['name']}}
                  </td>

                  <td class="text-center text-sm">
                    {{-- @if($price) --}}
                    <p>{{ Number::currency($price['price'], 'AED') }}</p>
                  </td>
                  <td class="text-center text-sm">
                    <p>{{ $price['price'] * 5 /100 }}</p>
                    {{-- <p>{{ $item['vat'], 'AED' }}</p> --}}
                  </td>
                  <td class="text-center">
                    {{-- <div class="">
                        <button wire:click="decrementQuantity({{ $item['item_id'] }})" class="border rounded-md py-0 px-2 mr-1 hover:bg-red-200">-</button>

                        <!-- Input box for quantity -->
                        <input type="number" wire:model.defer="itemQuantities.{{ $item['item_id'] }}" min="1" class="text-center w-16 border text-sm rounded-md py-1 px-2" value="{{ $item['quantity'] }}" />

                        <button wire:click="incrementQuantity({{ $item['item_id'] }})" class="border rounded-md py-0 px-2 ml-1 hover:bg-green-200">+</button>
                    </div> --}}

                    <div class="">
                        <button wire:click="decrementQuantity({{ $item['item_id'] }})" class="border rounded-md py-0 px-2 mr-1 hover:bg-red-200">-</button>
                        <input type="number"
                               wire:model="cart_items.{{ $loop->index }}.quantity"
                               wire:change="updateQuantity({{ $item['item_id'] }}, $event.target.value)"
                               min="1"
                               class="text-center w-16 border text-sm rounded-md py-1 px-2" />
                        <button wire:click="incrementQuantity({{ $item['item_id'] }})" class="border rounded-md py-0 px-2 ml-1 hover:bg-green-200">+</button>
                    </div>


                    {{-- <div class="flex items-center">
                      <button wire:click="decrementQuantity({{ $item['item_id'] }})" class="border rounded-md py-2 px-4 mr-2">-</button>
                      <span class="text-center w-8">{{$item['quantity']}}</span>
                      <button wire:click="incrementQuantity({{ $item['item_id'] }})" class="border rounded-md py-2 px-4 ml-2">+</button>
                    </div> --}}
                  </td>
                  <td class="text-sm text-center ">{{Number::currency($item['total_amount'], 'AED')}}</td>
                  <td class="text-slate-500 text-center">
                    <x-heroicon-o-trash wire:click="removeItem({{ $item['item_id'] }})"
                    class="w-6 h-6 cursor-pointer mx-auto"
                    style="color: #f55353;" />
                  </td>

                </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-2xl text-slate-500">Your Cart is empty!</td>
                    </tr>
                @endforelse
                {{-- @php
                    dd($cart_items);
                @endphp --}}

                <!-- More product rows -->
              </tbody>
          </table>
        </div>
      </div>
      <div class="md:w-1/4">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-l bg-[#c7edff] py-1 px-1 items-center justify-between dark:bg-gray-800 font-semibold mb-4">Summary</h2>
          <div class="flex justify-between text-sm mb-2 px-1">
            <span>Subtotal</span>
            <span>{{number_format($grand_total, 2)}}</span>
          </div>
          <div class="flex justify-between text-sm px-1 mb-2">
            <span>Total VAT</span>
            <span>
                @php
                    $vat = $grand_total * 5 / 100;
                @endphp
                {{number_format($vat, 2)}}
            <input type="hidden" name="vat" value="{{ $vat }}">
          </div>
          {{-- <div class="flex justify-between mb-2">
            <span>Shipping</span>
            <span>$0.00</span>
          </div> --}}
          <hr class="my-2">
          <div class="flex justify-between px-1 mb-2">
            <span class="font-semibold">Grand Total</span>
            <span class="font-semibold">{{number_format($grand_total + $vat, 2)}}</span>
          </div>
          {{-- <button wire:click="placeOrder" class="bg-[#627AD9] text-white py-2 px-4 rounded-lg mt-4 w-full">Place Order</button> --}}

          <button wire:click="placeOrder" class="bg-[#627AD9] text-white py-2 px-4 rounded-lg mt-4 w-full">
            Place Order
          </button>

          {{-- <button wire:click="placeOrder; generatePDF" class="bg-[#627AD9] text-white py-2 px-4 rounded-lg mt-4 w-full">Place Order</button> --}}
        </div>
      </div>
    </div>
  </div>
</div>

{{-- <script>
  document.addEventListener('livewire:initialized', () => {
      @this.on('downloadPdf', (data) => {
          window.open(`/view-pdf/${data.orderId}`, '_blank');
      });
  });
</script> --}}

<script>
  console.log('Script loaded');
  document.addEventListener('livewire:initialized', () => {
      console.log('Livewire initialized');
      @this.on('viewPdf', (data) => {
          console.log('viewPdf event received', data);
          var url = `/view-pdf/${data.orderId}`;
          console.log('Attempting to open PDF:', url);
          window.open(url, '_blank');
      });
  });
</script>
