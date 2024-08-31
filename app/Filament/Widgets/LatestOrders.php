<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\OrderResource;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;
    
    public function table(Table $table): Table
    {
        return $table
        ->query(OrderResource::getEloquentQuery())->defaultPaginationPageOption(2)
        ->defaultSort('created_at', 'desc')
        ->columns([
            // Split::make([
                TextColumn::make('items_in_order')
                ->label('Items in Order')
                // ->label(function (): Htmlable {
                //     return new HtmlString('<h2 class="text-lg font-bold">Items in Order</h2>');
                // })
                ->default(function ($record) {
                    // Assuming $record is an Order model instance
                    $orderId = $record->id;
                    $orderItemCount = OrderItem::where('order_id', $orderId)->count();
                    // $outputorderItemCount = $orderItemCount . '<small> Items in this Order</small>';
                    // return $outputorderItemCount . ' Items in this Order';
                    // return new HtmlString($orderItemCount);
                    return new HtmlString( '<bold style="font-size: 16px; font-weight:bold;">' . $orderItemCount . '</bold>' . ',<small> Items</small>');
                })->searchable()->sortable(),
                TextColumn::make('customer.name')
                ->label('Customer Name')->searchable()->sortable(),
                TextColumn::make('orders_total_amount')->money('AED')
                ->label('Subtotal')->searchable()->sortable(),
                TextColumn::make('vat')->money('AED')
                ->label('VAT')->searchable()->sortable(),
                TextColumn::make('grand_total')->money('AED')
                ->label('Grand Total')->searchable()->sortable(),
                TextColumn::make('remarks')
                    ->label('Remarks')->searchable()->sortable(),
                TextColumn::make('user.name')
                    ->label('Sales Person')->searchable()->sortable(),
                // SelectColumn::make('status')->label('Status')
                // ->options([
                //     'new' => 'New',
                //     'processing' => 'Processing',
                //     'delivered' => 'Delivered',
                //     'cancelled' => 'Cancelled',
                // ]),
                TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'new' => 'info',
                    'processing' => 'warning',
                    'delivered' => 'success',
                    'cancelled' => 'danger',
                })
                ->icon(fn (string $state): string => match ($state) {
                    'new' => 'heroicon-m-sparkles',
                    'processing' => 'heroicon-m-arrow-path',
                    'delivered' => 'heroicon-m-check-badge',
                    'cancelled' => 'heroicon-m-x-circle',
                })->searchable()->sortable(),
                TextColumn::make('created_at')
                ->label('Craeted At')->date('d-m-y'),
                // TextColumn::make('updated_at')
                // ->label('Updated At')->date('d-m-y')->toggleable(isToggledHiddenByDefault: false),

            // ])->from('md'),

            // Panel::make([
                // Split::make([
                    // TextColumn::make('items')
                    // ->label('Order ID')
                    // ->getStateUsing(function ($record) {
                    //     $output = '<table style="width: 200%;border: 2px solid #fdde6c; border-radius: 25px;">';
                    //     $output .=
                    //     '
                    //     <h2 style="margin: 10px; font-size: 16px; font-weight:bold;">Order Items</h2>
                    //     <tr style="width: full; background:#fdde6c; border: 2px solid fdde6c; border-radius: 25px;">
                    //         <th style="text-align: left; padding: 8px;">Item Name</th>
                    //         <th style="text-align: left; padding: 8px;">Quantity</th>
                    //         <th style="text-align: left; padding: 8px;">Unit Price</th>
                    //         <th style="text-align: left; padding: 8px;">VAT</th>
                    //         <th style="text-align: left; padding: 8px;">Total</th>
                    //     </tr>';
                    //     foreach ($record->items as $item) {
                    //         $output .=
                    //         "<tr>
                    //             <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$item->item->name}</td>
                    //             <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$item->quantity} {$item->item->unit_measure}</td>
                    //             <td style='padding: 8px; border-bottom: 1px solid #ddd;'>AED {$item->unit_price}</td>
                    //             <td style='padding: 8px; border-bottom: 1px solid #ddd;'>AED {$item->vat}</td>
                    //             <td style='padding: 8px; border-bottom: 1px solid #ddd;'>AED {$item->total_price}</td>
                    //         </tr>";
                    //     }
                    //     $output .= '</table>';
                    //     return $output;
                    // })
                    // ->html(),
                // ]),
            // ])->collapsed(true)
        ])
        ->actions([
            // Tables\Actions\ActionGroup::make([
                Action::make('View Order')
                ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
                ->icon('heroicon-m-eye'),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            // ])
        ]);
    }
}
