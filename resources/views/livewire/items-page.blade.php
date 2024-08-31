<div class="w-full py-10 px-4 sm:px-6 lg:px-8 mx-auto bg-gray-100">
  <section class="py-10 bg-white font-poppins w-[80%] ml-[10%] dark:bg-gray-800 rounded-lg shadow-lg">
      <div class="px-4 py-4 mx-auto max-w-7xl lg:py-6 md:px-6">

          <div class="mb-6">
              <h4 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                  Hello user, please select the customer to get the items catalog.
              </h4>
          </div>

          <div class="mb-4">
              <div class="flex py-2 bg-gray-100 dark:bg-gray-900">
                  <div class="flex ml-6 items-center"> <b>Customer:</b>
                      <select wire:model.live="selectedCustomer" class="block w-60 text-base bg-gray-100 cursor-pointer dark:text-gray-400 dark:bg-gray-900 ml-6">
                          <option value="">Select a Customer</option>
                          @foreach($customers as $customer)
                              <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                          @endforeach
                      </select>
                  </div>
              </div>
          </div>

          @if($selectedCustomer && $items->isNotEmpty())
              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                  @foreach($items as $item)
                      <div class="border border-gray-300 dark:border-gray-700 rounded-lg overflow-hidden shadow-md" wire:key='{{$item->id}}'>
                          <div class="relative bg-gray-200">
                              {{-- <img src="{{ !empty($item->images) ? asset('storage/' . $item->images[$imageIndexes[$item->id]]) : asset('images/placeholder.jpg') }}"
                                  alt="{{$item->name}}"
                                  class="w-full h-48 object-cover transition-all duration-500 ease-in-out"> --}}

                              <!-- Left Arrow -->
                              <button
                                  wire:click="previousImage({{ $item->id }})"
                                  class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-r
                                  {{ $imageIndexes[$item->id] == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                  {{ $imageIndexes[$item->id] == 0 ? 'disabled' : '' }}>
                                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                  </svg>
                              </button>

                              <!-- Right Arrow -->
                              <button
                                  wire:click="nextImage({{ $item->id }})"
                                  class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-l
                                  {{ $imageIndexes[$item->id] == count($item->images) - 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                  {{ $imageIndexes[$item->id] == count($item->images) - 1 ? 'disabled' : '' }}>
                                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                  </svg>
                              </button>
                          </div>

                          <div class="p-4">
                              <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">
                                  {{$item->name}}
                              </h3>
                              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                <b>Item No:</b>
                                {{$item->item_no}}</p>
                              {{-- <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                  Customer Barcode: {{$item->prices->first()->customer_barcode ?? 'N/A'}}
                              </p> --}}
                              <!-- Display the actual barcode -->
                              @if(!empty($item->prices->first()->customer_barcode))
                              <div class="mb-4">
                                <b class="text-gray-600 text-sm">Barcode:</b>
                                  <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($item->prices->first()->customer_barcode, 'C39', 2, 60) }}" alt="barcode" class="w-44 h-8">
                                  <small class="text-gray-600 dark:text-gray-400">{{ $item->prices->first()->customer_barcode }}</small>
                              </div>
                          @else
                              <p class="text-sm text-red-600 dark:text-red-400">No barcode available</p>
                          @endif
                              <p class="text-lg font-bold text-green-600 dark:text-green-400 mt-2">
                                  {{ Number::currency($item->prices->first()->price ?? 'N/A', 'AED') }}
                              </p>
                          </div>
                          <div class="flex justify-center p-4 border-t border-gray-300 dark:border-gray-700">
                                <a wire:click.prevent="addToCart({{$item->id}}, {{$selectedCustomer}})" href="#" class="text-gray-500 flex items-center space-x-2 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-300">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="w-4 h-4 bi bi-cart3" viewBox="0 0 16 16">
                                      <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                                  </svg>
                                  <span wire:loading.remove wire:target="addToCart({{$item->id}}, {{$selectedCustomer}})">
                                    {{-- @php dd($selectedCustomer); @endphp --}}
                                    Add to Cart
                                  </span>
                                  <span wire:loading wire:target="addToCart({{$item->id}}, {{$selectedCustomer}})">
                                    Adding...
                                  </span>
                              </a>
                          </div>
                      </div>
                  @endforeach
              </div>
          @elseif($selectedCustomer)
              <p class="text-center text-gray-500 dark:text-gray-400">No items found for this customer.</p>
          @endif
      </div>
  </section>
</div>
