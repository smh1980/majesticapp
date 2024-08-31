<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Price;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Models\OrderItem;
use Filament\Tables\Table;
use Illuminate\Support\Number;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Card::make()
            ->schema([
                Grid::make(2)->schema([
                    Select::make('user_id')
                    ->label('User')
                    ->options(User::all()->pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->default(function (RelationManager $livewire): int {
                        return $livewire->getOwnerRecord()->id;
                    })
                    ->disabled(function (RelationManager $livewire): bool {
                        return $livewire->getOwnerRecord() !== null;
                    })
                    ->afterStateUpdated(function (Set $set, $state) {
                        // Preserve one item in the repeater
                        $set('items', [['item_id' => null, 'quantity' => 1, 'unit_price' => 0, 'vat' => 0, 'total_price' => 0]]);
                        // Update customer name in summary
                        $customer = User::find($state);
                        $set('customer_name', $customer ? $customer->name : 'N/A');
                    }),

                    Select::make('user_id')
                        ->label('Sales Staff')
                        ->options(User::all()->pluck('name', 'id'))
                        ->required(),

                    Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3),

                    ToggleButtons::make('status')
                    ->inline()
                    ->label('Order Status')
                    ->options([
                        'new' => 'New',
                        'processing' => 'Processing',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                    ->colors([
                        'new' => 'info',
                        'processing' => 'warning',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    ])
                    ->icons([
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-m-arrow-path',
                        'delivered' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle',
                    ])
                    ->default('new')
                    ->required(),
                ])->columns(2),

                Section::make('Order Items')
                ->schema([
                    Repeater::make('items')
                    ->relationship()
                    ->label('Order Items')
                    ->schema([
                        Select::make('item_id')
                        ->label('Item')
                        ->options(function (callable $get) {
                            $customerId = $get('../../customer_id');
                            if (!$customerId) return [];
                            return Price::where('customer_id', $customerId)
                                ->with('item')
                                ->get()
                                ->pluck('item.name', 'item_id');
                        })->columnSpan(3)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $get, Set $set) {
                            $customerId = $get('../../customer_id');
                            $price = Price::where('customer_id', $customerId)
                                ->where('item_id', $state)
                                ->value('price');
                            $set('unit_price', $price ?? 0);
                            $set('quantity', 1);
                            $set('vat', 0);
                            $set('total_price', 0);
                        })
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                        TextInput::make('quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->required()
                        ->default(1)
                        ->reactive()->columnSpan(1)
                        ->afterStateUpdated(fn ($state, callable $get, Set $set) => self::calculateTotals($state, $get, $set)),
                        //USE THE BELOW CODE TO MAKE MINVALUE = 1 WORKING.
                        // ->afterStateUpdated(function ($state, callable $get, Set $set) {
                        //     if ($state < 1) {
                        //         $set('quantity', 1);
                        //     }
                        //     self::calculateTotals($state, $get, $set);
                        // }),

                        TextInput::make('unit_price')
                        ->label('Unit Price')
                        ->numeric()
                        ->step(0.01)
                        ->disabled()->columnSpan(2)
                        ->required()->dehydrated(),

                        TextInput::make('vat')
                            ->label('Item VAT')
                            ->numeric()
                            ->step(0.01)->columnSpan(2)
                            ->disabled()
                            ->dehydrated()
                            ->afterStateHydrated(function ($state, $get, Set $set) {
                                $quantity = $get('quantity');
                                $unitPrice = $get('unit_price');
                                $vat = $quantity * $unitPrice * 0.05; // Assuming 5% VAT
                                $set('vat', round($vat, 2));
                            }),

                        TextInput::make('total_price')
                        ->label('Item Total Amount')
                        ->numeric()
                        ->step(0.01)
                        ->disabled()->columnSpan(2)
                        ->required()
                        ->dehydrated()
                        ->afterStateHydrated(function ($state, $get, Set $set) {
                            $quantity = $get('quantity');
                            $unitPrice = $get('unit_price');
                            $vat = $get('vat');
                            // $total = ($quantity * $unitPrice) + $vat;
                            $total = ($quantity * $unitPrice);
                            $set('total_price', round($total, 2));
                        }),
                    ])->columns(10)
                    ->collapsible()
                    ->defaultItems(1),

                    //Orders Summary
                    Section::make('Orders Summary')->schema([
                        Placeholder::make('customer_name')
                            ->label('Customer Name')
                            ->content(function (Get $get) {
                                $customerId = $get('customer_id');
                                if ($customerId) {
                                    $customer = Customer::find($customerId);
                                    return $customer ? $customer->name : 'N/A';
                                }
                                return 'No Customer Selected';
                            }),

                        Placeholder::make('orders_total_amount')
                            ->label('Orders Total Amount')
                            ->content(function (Get $get, Set $set) {
                                $total = 0;
                                if ($repeaters = $get('items')) {
                                    foreach ($repeaters as $repeater) {
                                        $total += floatval($repeater['total_price'] ?? 0);
                                    }
                                }
                                $set('orders_total_amount', $total);
                                return Number::currency($total, 'AED');
                            }),

                        Placeholder::make('vat')
                            ->label('VAT (5%)')
                            ->content(function (Get $get, Set $set) {
                                $total = 0;
                                if ($repeaters = $get('items')) {
                                    foreach ($repeaters as $repeater) {
                                        $total += floatval($repeater['total_price'] ?? 0);
                                    }
                                }
                                $vat = $total * 0.05;
                                $set('vat', $vat);
                                return Number::currency($vat, 'AED');
                            }),

                        Placeholder::make('grand_total')
                            ->label('Grand Total')
                            ->content(function (Get $get, Set $set) {
                                $total = 0;
                                if ($repeaters = $get('items')) {
                                    foreach ($repeaters as $repeater) {
                                        $total += floatval($repeater['total_price'] ?? 0);
                                    }
                                }
                                $vat = $total * 0.05;
                                $grandTotal = $total + $vat;
                                $set('grand_total', $grandTotal);
                                return new \Illuminate\Support\HtmlString('<strong style="color:red;" class="bold-text">' . Number::currency($grandTotal, 'AED') . '</strong>');
                            }),
                    ])->columns(4),

                    Hidden::make('orders_total_amount')->default(0),
                    Hidden::make('vat')->default(0),
                    Hidden::make('grand_total')->default(0),
                ])
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->recordTitleAttribute('customer_id')
        ->columns([
            Split::make([
                TextColumn::make('items_in_order')
                ->label(function (): Htmlable {
                    return new HtmlString('<h2 class="text-lg font-bold">Items in Order</h2>');
                })
                ->default(function ($record) {
                    // Assuming $record is an Order model instance
                    $orderId = $record->id;
                    $orderItemCount = OrderItem::where('order_id', $orderId)->count();
                    // $outputorderItemCount = $orderItemCount . '<small> Items in this Order</small>';
                    // return $outputorderItemCount . ' Items in this Order';
                    // return new HtmlString($orderItemCount);
                    return new HtmlString( '<bold style="font-size: 16px; font-weight:bold;">' . $orderItemCount . '</bold>' . ',<small> Items</small>');
                }),
                // TextColumn::make('customer.name')
                // ->label('Customer Name'),
                TextColumn::make('orders_total_amount')->money('AED')
                ->label('Orders Total Amount'),
                TextColumn::make('vat')->money('AED')
                ->label('Total VAT'),
                TextColumn::make('grand_total')->money('AED')
                ->label('Grand Total'),
                TextColumn::make('remarks')
                    ->label('Remarks'),
                TextColumn::make('user.name')
                    ->label('Sales Person'),
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
                }),
                TextColumn::make('created_at')
                ->label('Craeted At')->date('d-m-y'),
                // TextColumn::make('updated_at')
                // ->label('Updated At')->date('d-m-y')->toggleable(isToggledHiddenByDefault: false),

            ])->from('md'),

            Panel::make([
                Split::make([
                    TextColumn::make('items')
                    ->label('Order ID')
                    ->getStateUsing(function ($record) {
                        $output = '<table style="width: 200%;border: 2px solid #fdde6c; border-radius: 25px;">';
                        $output .=
                        '
                        <h2 style="margin: 10px; font-size: 16px; font-weight:bold;">Order Items</h2>
                        <tr style="width: full; background:#fdde6c; border: 2px solid fdde6c; border-radius: 25px;">
                            <th style="text-align: left; padding: 8px;">Item Name</th>
                            <th style="text-align: left; padding: 8px;">Quantity</th>
                            <th style="text-align: left; padding: 8px;">Unit Price</th>
                            <th style="text-align: left; padding: 8px;">VAT</th>
                            <th style="text-align: left; padding: 8px;">Total</th>
                        </tr>';
                        foreach ($record->items as $item) {
                            $output .=
                            "<tr>
                                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$item->item->name}</td>
                                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$item->quantity} {$item->item->unit_measure}</td>
                                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>AED {$item->unit_price}</td>
                                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>AED {$item->vat}</td>
                                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>AED {$item->total_price}</td>
                            </tr>";
                        }
                        $output .= '</table>';
                        return $output;
                    })
                    ->html(),
                ]),
            ])->collapsed(true)
        ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->header(function () {
                $userName = $this->getOwnerRecord()->name;
                $headerOutput = '
                    <h3 class="text-base font-bold"style="margin: 15px;">Orders taken by ' . $userName . '</h3>
                    <div class="flex justify-items justify-content">
                        <p style="margin: 0.5rem 0.5rem 0.5rem 5%;font-size:12px; font-weight: bold;">Order Contains</p>
                        <p style="margin: 0.5rem 0.5rem 0.5rem 3.5%;font-size:12px; font-weight: bold;">Subtotal</p>
                        <p style="margin: 0.5rem 0.5rem 0.5rem 6%;font-size:12px; font-weight: bold;">5% VAT</p>
                        <p style="margin: 0.5rem 0.5rem 0.5rem 6%;font-size:12px; font-weight: bold;">Grand Total</p>
                        <p style="margin: 0.5rem 0.5rem 0.5rem 4.8%;font-size:12px; font-weight: bold;">Remarks</p>
                        <p style="margin: 0.5rem 0.5rem 0.5rem 5.5%;font-size:12px; font-weight: bold;">Sales Person</p>
                        <p style="margin: 0.5rem 0.5rem 0.5rem 4.2%;font-size:12px; font-weight: bold;">Order Status</p>
                        <p style="margin: 0.5rem 0.5rem 0.5rem 3.9%;font-size:12px; font-weight: bold;">Order Date</p>
                    </div>
                ';

                return new \Illuminate\Support\HtmlString($headerOutput);
                // return new \Illuminate\Support\HtmlString("<h3 class='text-base font-bold'style='margin: 15px;'>Order history for $customerName</h3>");
            });
    }

    private static function calculateTotals($quantity, callable $get, Set $set): void
    {
        $unitPrice = floatval($get('unit_price'));
        $subtotal = $quantity * $unitPrice;
        $vat = $subtotal * 0.05; // 5% VAT
        // $total = $subtotal + $vat;
        $total = $subtotal;

        $set('vat', round($vat, 2));
        $set('total_price', round($total, 2));
    }

    public function getHeader(): View | Htmlable | null
    {
        return $this->evaluate($this->header);
    }
}
