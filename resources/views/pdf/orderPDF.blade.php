{{-- <!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Order #{{ $order->id }}</title>
        <style>
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 5px; text-align: center; }
            th { 
                background-color: #F5E8EF; 
                font-size: 14px;
                font-weight: bold;
                letter-spacing: 0.6px;
            }
            td{
                font-size: 14px;
            }
            @page {
            size: A4 landscape;
            }
            .header{
                text-align: center;
                margin-top: -3rem;
            }
            .heading{
                text-decoration: underline;
            }
            .trading{
                margin-top: -1rem;
                text-decoration: none;
            }
            .contact{
                font-size: 11px;
                margin-top: -1.5rem;
            }
            .subheading {
                display: flex;
                align-items: center;
            }

            .subheading h3 {
                margin-right: 1rem; /* Adds space between h3 and p */
            }
            .details{
                border: 1px solid #989898;
                /* margin-top: -10px; */
                padding: 5px;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 15px;
                font-weight: normal;
                width: 40%;
                margin: 2px 0;
            }
            .details span{
                font-weight: 450;
                color: #627AD9;
                text-transform: uppercase;
                float: right;
            }
            .order_details {
                display: flex;
                justify-content: space-between;
                width: 100%;
                margin-bottom: 20px; /* Adjust margin as needed */
            }

            .details_group {
                display: flex;
                flex-direction: column;
                margin-right: 20px; /* Adjust spacing between groups */
            }

        </style>
    </head>
    <body>
        <div class="header">
            <h2 class="heading">MAJESTIC HOUSE</h2>
            <p class="trading">Trading L.L.C.</p>
        </div>
        <div class="contact">
            <p>
                Call: 04-3334545 |
                050-5788807 <br>
                Dubai, UAE. <br>
                Email: majestichouse@gmail.com <br>
            </p>
        </div>
        <hr style="margin-top: -0.5rem;">        
        <div class="subheading">
            <h3 style="margin-top: -0.2rem;">Order Summary</h3>
            @php
                $date = date("d-m-y");
            @endphp
            <p style="margin-top: -2.5rem; float: right;font-size: 11px;">Date: {{$date}}</p>
            <div class="order_details">
                <div class="details_group">
                    <h5 class="details">Order ID: <span>{{ $order->id }}</span></h5>
                    <h5 class="details">Customer Name: <span>{{ $order->customer->name }}</span></h5>
                </div>
                <div class="details_group" style="margin-top: -5rem; margin-left: 26.2rem;">
                    <h5 class="details">Order Date: <span>{{ $order->created_at->format('d-m-y') }}</span></h5>
                    <h5 class="details">Items in the Order: <span>{{ $order->items->count() }}</span></h5>
                </div>
            </div>            
        </div>
        @if(count($items) > 0)
            <table style="margin-top: -12px !important;">
                <thead>
                    <tr>
                        <th>Item Image</th>
                        <th>Item Name</th>
                        <th>Item No.</th>
                        <th style="width: 8rem;">Supplier Barcode</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th style="width: 4rem;">Unit VAT <small style="font-size: 10px;">5%</small> </th>
                        <th>Item Total</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($items as $item)
                        @php 
                            // Check if the item has images
                            if (isset($item->item->images[0])) {
                                $firstImage = 'storage/' . $item->item->images[0];
                            } else {                    
                                $firstImage = 'storage/default-image.jpg';
                            }
                
                            $price = $item->item->prices()
                                        ->where('customer_id', $order->customer_id)
                                        ->where('item_id', $item->item_id)
                                        ->first();
                        @endphp 
                        <tr>
                            <td>
                                <img src="{{$firstImage}}" alt="" width="83" height="50">
                            </td>
                            <td>{{ $item->item->name }}</td>
                            <td>{{ $item->item->item_no }}</td>
                            <td>
                                @if($price)
                                    <!-- Display the barcode image -->
                                    <img style="margin-top: 10px;" src="data:image/png;base64,{{ DNS1D::getBarcodePNG($price->customer_barcode, 'C39', 2, 60) }}" alt="barcode" width="60" height="25">
                                    <!-- Display the barcode text -->
                                    <p class="text-gray-600 dark:text-gray-400 text-[5px]" style="font-size: 10px; margin-top: -0.2px">{{ $price->customer_barcode }}</p>
                                @else
                                    No Barcode Available
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit_price }}</td>
                            <td>{{ $item->vat }}</td>
                            <td>{{ $item->total_price }}</td>
                        </tr>
                    @endforeach
                </tbody>            
        @else
            <p>No items found for this order.</p>
        @endif
    </body>
</html> --}}

{{-- <!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Order #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: center; }
        th { 
            background-color: #F5E8EF; 
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.6px;
        }
        td{
            font-size: 14px;
        }
        @page {
            size: A4 landscape;
        }
        .header{
            text-align: center;
            margin-top: -3rem;
        }
        .heading{
            text-decoration: underline;
        }
        .trading{
            margin-top: -1rem;
            text-decoration: none;
        }
        .contact{
            font-size: 11px;
            margin-top: -1.5rem;
        }
        .subheading {
            display: flex;
            align-items: center;
        }
        .subheading h3 {
            margin-right: 1rem;
        }
        .details{
            border: 1px solid #989898;
            padding: 5px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            font-weight: normal;
            width: 40%;
            margin: 2px 0;
        }
        .details span{
            font-weight: 450;
            color: #627AD9;
            text-transform: uppercase;
            float: right;
        }
        .order_details {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 20px;
        }
        .details_group {
            display: flex;
            flex-direction: column;
            margin-right: 20px;
        }
        /* Add the summary table style */
        .summary-table {
            width: 30%;
            margin: 20px 0;
            float: right;
        }
        .summary-table td {
            /* width: 73%; */
            float: right !important;
            padding: 8px;
            font-size: 14px;
        }
        .summary-label {
            background: #F5E8EF;
            width: 14%;
            font-weight: bold;
            text-align: right;
        }
        .summary-value {
            width: 25%;
            text-align: right;
        }
        /* Footer styles */
        .footer {
            text-align: center;
            font-size: 11px;
            width: 100%;
            background-color: #627AD9;
            color: white;
            text-align: center;
            position: absolute;
            bottom: 0;
            left: 0;
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 class="heading">MAJESTIC HOUSE</h2>
        <p class="trading">Trading L.L.C.</p>
    </div>
    <div class="contact">
        <p>
            Call: 04-3334545 |
            050-5788807 <br>
            Dubai, UAE. <br>
            Email: majestichouse@gmail.com <br>
        </p>
    </div>
    <hr style="margin-top: -0.5rem;">        
    <div class="subheading">
        <h3 style="margin-top: -0.2rem;">Order Summary</h3>
        @php
            $date = date("d-m-y");
        @endphp
        <p style="margin-top: -2.5rem; float: right;font-size: 11px;">Date: {{$date}}</p>
        <div class="order_details">
            <div class="details_group">
                <h5 class="details">Order ID: <span>{{ $order->id }}</span></h5>
                <h5 class="details">Customer Name: <span>{{ $order->customer->name }}</span></h5>
            </div>
            <div class="details_group" style="margin-top: -5rem; margin-left: 26.2rem;">
                <h5 class="details">Order Date: <span>{{ $order->created_at->format('d-m-y') }}</span></h5>
                <h5 class="details">Items in the Order: <span>{{ $order->items->count() }}</span></h5>
            </div>
        </div>            
    </div>
    @if(count($items) > 0)
        <table style="margin-top: -12px !important;">
            <thead>
                <tr>
                    <th>Item Image</th>
                    <th>Item Name</th>
                    <th>Item No.</th>
                    <th style="width: 8rem;">Supplier Barcode</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th style="width: 4rem;">Unit VAT <small style="font-size: 10px;">5%</small> </th>
                    <th>Item Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    @php 
                        if (isset($item->item->images[0])) {
                            $firstImage = 'storage/' . $item->item->images[0];
                        } else {                    
                            $firstImage = 'storage/default-image.jpg';
                        }

                        $price = $item->item->prices()
                                    ->where('customer_id', $order->customer_id)
                                    ->where('item_id', $item->item_id)
                                    ->first();
                    @endphp 
                    <tr>
                        <td><img src="{{$firstImage}}" alt="" width="83" height="50"></td>
                        <td>{{ $item->item->name }}</td>
                        <td>{{ $item->item->item_no }}</td>
                        <td>
                            @if($price)
                                <img style="margin-top: 10px;" src="data:image/png;base64,{{ DNS1D::getBarcodePNG($price->customer_barcode, 'C39', 2, 60) }}" alt="barcode" width="60" height="25">
                                <p class="text-gray-600 dark:text-gray-400 text-[5px]" style="font-size: 10px; margin-top: -0.2px">{{ $price->customer_barcode }}</p>
                            @else
                                No Barcode Available
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit_price }}</td>
                        <td>{{ $item->vat }}</td>
                        <td>{{ $item->total_price }}</td>
                    </tr>
                @endforeach
            </tbody>            
        </table>
    @else
        <p>No items found for this order.</p>
    @endif
    
    <!-- Summary Section -->
    <table class="summary-table">
        <tr>
            <td class="summary-label">Subtotal:</td>
            <td class="summary-value">{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="summary-label">Total VAT:</td>
            <td class="summary-value">{{ number_format($order->total_vat, 2) }}</td>
        </tr>
        <tr>
            <td class="summary-label">Grand Total:</td>
            <td class="summary-value">{{ number_format($order->grand_total, 2) }}</td>
        </tr>
    </table>

    <!-- Footer Section -->
    <div class="footer" style="text-align: center;">
        <span style="margin-left: 17rem !important;">&copy; 2024. All rights reserved.</span>
    </div>
</body>
</html> --}}


<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Order #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: center; }
        th { 
            background-color: #F5E8EF; 
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.6px;
        }
        td{
            font-size: 14px;
        }
        @page {
            size: A4 landscape;
        }
        .header{
            text-align: center;
            margin-top: -3rem;
        }
        .heading{
            text-decoration: underline;
        }
        .trading{
            margin-top: -1rem;
            text-decoration: none;
        }
        .contact{
            font-size: 11px;
            margin-top: -1.5rem;
        }
        .subheading {
            display: flex;
            align-items: center;
        }
        .subheading h3 {
            margin-right: 1rem;
        }
        .details{
            border: 1px solid #989898;
            padding: 5px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            font-weight: normal;
            width: 40%;
            margin: 2px 0;
        }
        .details span{
            font-weight: 450;
            color: #627AD9;
            text-transform: uppercase;
            float: right;
        }
        .order_details {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 20px;
        }
        .details_group {
            display: flex;
            flex-direction: column;
            margin-right: 20px;
        }
        /* Add the summary table style */
        .summary-table {
            width: 31%;
            margin: 20px 0;
            float: right;
        }
        .summary-table td {
            /* width: 73%; */
            float: right !important;
            padding: 8px;
            font-size: 14px;
        }
        .summary-label {
            background: #F5E8EF;
            width: 15%;
            font-weight: bold;
            text-align: right;
        }
        .summary-value {
            width: 25%;
            text-align: right;
        }
        /* Footer styles */
        .footer {
            text-align: center;
            font-size: 11px;
            width: 100%;
            background-color: #627AD9;
            color: white;
            text-align: center;
            position: absolute;
            bottom: 0;
            left: 0;
            padding: 5px 0;
        }
    </style>
</head>
<body>        
    <div class="header">
        <h2 class="heading">MAJESTIC HOUSE</h2>
        <p class="trading">Trading L.L.C.</p>
    </div>
    <div class="contact">
        <p>
            Call: 04-3334545 |
            050-5788807 <br>
            Dubai, UAE. <br>
            Email: majestichouse@gmail.com <br>
        </p>
    </div>
    <hr style="margin-top: -0.5rem;">        
    <div class="subheading">
        <h3 style="margin-top: -0.2rem;">Order Summary</h3>
        @php
            $date = date("d-m-y");
        @endphp
        <p style="margin-top: -2.5rem; float: right;font-size: 11px;">Date: {{$date}}</p>
        <div class="order_details">
            <div class="details_group">
                <h5 class="details">Order ID: <span>{{ $order->id }}</span></h5>
                <h5 class="details">Customer Name: <span>{{ $order->customer->name }}</span></h5>
            </div>
            <div class="details_group" style="margin-top: -5rem; margin-left: 26.2rem;">
                <h5 class="details">Order Date: <span>{{ $order->created_at->format('d-m-y') }}</span></h5>
                <h5 class="details">Items in the Order: <span>{{ $order->items->count() }}</span></h5>
            </div>
        </div>            
    </div>
    {{-- @if(count($items) > 0) --}}
    @if($order->items && $order->items->isNotEmpty())
        <table style="margin-top: -12px !important;">
            <thead>
                <tr>
                    <th>Item Image</th>
                    <th>Item Name</th>
                    <th>Item No.</th>
                    <th style="width: 8rem;">Supplier Barcode</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th style="width: 4rem;">Unit VAT <small style="font-size: 10px;">5%</small> </th>
                    <th>Item Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    @php 
                    // dd($items);
                        if (isset($item->item->images[0])) {
                            $firstImage = 'storage/' . $item->item->images[0];
                        } else {                    
                            $firstImage = 'storage/default-image.jpg';
                        }

                        $price = $item->item->prices()
                                    ->where('customer_id', $order->customer_id)
                                    ->where('item_id', $item->item_id)
                                    ->first();
                    @endphp 
                    <tr>
                        <td><img src="{{$firstImage}}" alt="" width="83" height="50"></td>
                        <td>{{ $item->item->name }}</td>
                        <td>{{ $item->item->item_no }}</td>
                        <td>
                            @if($price)
                                <img style="margin-top: 10px;" src="data:image/png;base64,{{ DNS1D::getBarcodePNG($price->customer_barcode, 'C128', 2, 2) }}" alt="barcode" width="75" height="25">
                                <p class="text-gray-600 dark:text-gray-400 text-[5px]" style="font-size: 10px; margin-top: -0.2px">{{ $price->customer_barcode }}</p>
                            @else
                                No Barcode Available
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ $item->unit_price }}</td>
                        <td style="text-align: right;">{{ $item->vat }}</td>
                        <td style="text-align: right;">{{ $item->total_price }}</td>
                    </tr>
                @endforeach
            </tbody>            
        </table>
    @else
        <p>No items found for this order.</p>
    @endif
    
    <!-- Summary Section -->
    <table class="summary-table">
        <tr>
            <td class="summary-label">Subtotal:</td>
            <td class="summary-value">{{ number_format($order->orders_total_amount, 2) }}</td>
        </tr>
        <tr>
            <td class="summary-label">Total VAT:</td>
            <td class="summary-value">{{ number_format($order->vat, 2) }}</td>
        </tr>
        <tr>
            <td class="summary-label">Grand Total:</td>
            <td class="summary-value" style="font-size: 14px; font-weight: 700 !important;">{{ number_format($order->grand_total, 2) }}</td>
        </tr>
    </table>

    <!-- Footer Section -->
    <div class="footer" style="text-align: center;">
        <span style="margin-left: 17rem !important;">&copy; 2024. All rights reserved.</span>
    </div>
</body>
</html>

