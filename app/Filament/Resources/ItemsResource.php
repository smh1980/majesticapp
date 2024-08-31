<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Item;
use Filament\Tables;
use App\Models\Price;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\ItemsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ItemsResource\RelationManagers;

class ItemsResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 4;

    // public static function form(Form $form): Form
    // {
    //     return $form
    //     ->schema([
    //         Section::make()->schema([
    //             // Main Form Section: Item Details
    //             Section::make('Item Details')
    //             ->schema([
    //                 // Category Selection
    //                 Select::make('category_id')->label('Product Category')
    //                 ->relationship('category', 'name')
    //                 ->required(),

    //                 // Item Name
    //                 TextInput::make('name')
    //                 ->required()
    //                 ->label('Item Name'),

    //                 // Item Number
    //                 TextInput::make('item_no')
    //                 ->nullable()
    //                 ->label('Item Number'),

    //                 // Item Description
    //                 Textarea::make('item_description')
    //                 ->nullable()
    //                 ->label('Item Description')->columnSpanFull(),

    //                 // Images (JSON)
    //                 $item_no =
    //                 FileUpload::make('images')
    //                 ->multiple()
    //                 ->enableReordering()
    //                 ->image()->directory('items/{$item_no}')
    //                 ->label('Images')->columnSpanFull(),

    //                 Toggle::make('is_active')
    //                 ->label('Active Item')
    //                 ->default(true),
    //             ])->columns(3),

    //             // Other fields like images, etc.

    //             // Pricing / Linking Section
    //             Section::make('Item Pricing / Linking')->label('Item Pricing / Linking')
    //                 ->schema([
    //                     Checkbox::make('show_all')
    //                     ->label('Show All')
    //                     ->reactive()
    //                     ->afterStateUpdated(fn ($state, $set) => $state ? $set('customers', Customer::all()) : $set('customers', null)),

    //                     Repeater::make('prices')
    //                     ->relationship('prices')
    //                     ->schema([
    //                         Select::make('customer_id')
    //                         ->relationship('customer', 'name')
    //                         ->label('Customer Name'),

    //                         TextInput::make('customer_barcode')
    //                         ->label('Customer Barcode')
    //                         ->required(),

    //                         TextInput::make('customer_ref')
    //                         ->label('Customer Reference')
    //                         ->required(),

    //                         TextInput::make('price')
    //                         ->numeric()
    //                         ->label('Price')
    //                         ->required(),

    //                         Toggle::make('is_linked')
    //                         ->label('Linked')
    //                         ->default(true),
    //                     ])->columns(2)
    //                     ->collapsed(fn ($get) => !$get('show_all'))
    //                     ->createItemButtonLabel('Add Customer'),
    //                 ])
    //             ->columns(1),
    //         ])
    //     ]);
    // }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
            ->schema([
                // Main Form Section: Item Details
                Section::make('Item Details')
                ->schema([
                    Group::make()
                    ->schema([
                        // Category Selection
                        Select::make('category_id')->label('Product Category')
                        ->relationship('category', 'name')
                        ->required()->columnSpan(2),

                        // Item Name
                        TextInput::make('name')
                        ->required()
                        ->label('Item Name')->columnSpan(2),

                        // Item Number
                        // TextInput::make('item_no')
                        // ->nullable()
                        // ->label('Item Number')->columnSpan(2)
                        // ->afterStateUpdated(fn ($state, callable $set) => $set('images_directory', "items/{$state}")),

                        TextInput::make('item_no')
                        ->nullable()
                        ->label('Item Number')
                        ->columnSpan(2)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $directory = "items/{$state}";
                                if (!Storage::exists($directory)) {
                                    Storage::makeDirectory($directory);
                                }
                                $set('images_directory', $directory);
                            }
                        }),

                        // TextInput::make('unit_measure')
                        // ->label('Measures in')->columnSpan(1),

                        Select::make('unit_measure')
                        ->label('Measures in')
                        ->options([
                            'kgs' => 'kgs',
                            'liters' => 'liters',
                            'meters' => 'meters',
                            'gallons' => 'gallons',
                            'pcs' => 'pcs',
                            'dozens' => 'dozens',
                            'boxes' => 'boxes',
                            'packs' => 'packs',
                            'bags' => 'bags',
                            'barrels' => 'barrels',
                            'tons' => 'tons',
                            'quintals' => 'quintals',
                        ])
                        ->columnSpan(2)
                        ->required()
                        ->reactive(),
                    ])->columnSpan(3)->columns(8),
                    
                        Group::make()
                            ->schema([
                            Group::make()->schema([
                                Textarea::make('item_description')
                                ->nullable()
                                ->label('Item Description')->columnSpan(2)->columns(2),
                                // Images (JSON)
                                // FileUpload::make('images')
                                // ->multiple()
                                // ->enableReordering()
                                // ->directory(fn ($get) => $get('images_directory') ?? 'items') // Set the directory dynamically
                                // ->label('Images')
                                // ->reactive()->columnSpan(2)->columns(2),
                                FileUpload::make('images')
                ->multiple()
                ->enableReordering()
                ->directory(function (callable $get) {
                    $itemNo = $get('item_no');
                    $directory = $itemNo ? "items/{$itemNo}" : 'items';
                    if ($itemNo && !Storage::exists($directory)) {
                        Storage::makeDirectory($directory);
                    }
                    return $directory;
                })
                ->label('Images')
                ->reactive()
                ->columnSpan(2)
                ->columns(2)
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $itemNo = $get('item_no');
                    if ($itemNo && $state) {
                        $directory = "items/{$itemNo}";
                        if (!Storage::exists($directory)) {
                            Storage::makeDirectory($directory);
                        }
                        $set('images_directory', $directory);
                    }
                }),
                            ])->columnSpan(3)->columns(2),  
                            Section::make('Item Status')->label('Item Status')->schema([
                                Group::make()->label('Item Status')->schema([
                                    Toggle::make('is_active')->label('Change here')->inline(false)->default(true)->reactive()
                                    ->afterStateUpdated(function ($state, $set){}) ->onColor('success')
                                    ->offColor('danger'),   
                                    
                                    Placeholder::make('status')->label('Currently')->content(function ($state, $get) {
                                        $status = $get('is_active') === true ? 'ACTIVE' : 'INACTIVE';
                                        $style = $status === 'ACTIVE' ? 'color: green;' : 'color: red;';
                                        return new HtmlString("<span style='" . $style . "'>" . $status . "</span>");
                                    })->extraAttributes(['class' => 'text-primary-500 items-center font-bold']),

                                ])->columnSpan(4)->columns(2), 
                            ])->extraAttributes(['style' => 'margin-top: 30px; height: 195px;'])
                            ->columnSpan(1)->columns(3),                             
                        ])->columnSpan(3)->columns(4),  

                       
                    ])->columnSpan(3)->columns(1),

                    // Group::make()
                    //     ->schema([
                    //     Group::make()->schema([
                    //         // Item Description
                    //             Textarea::make('item_description')
                    //             ->nullable()
                    //             ->label('Item Description'),

                    //             // Images (JSON)
                    //             FileUpload::make('images')
                    //             ->multiple()
                    //             ->enableReordering()
                    //             ->directory(fn ($get) => $get('images_directory') ?? 'items') // Set the directory dynamically
                    //             ->label('Images')
                    //         ->reactive(),
                    //     ])->columns(2),  
                    //     Section::make('Craft Status')->schema([
                    //         Group::make()->schema([
                    //             Toggle::make('is_active')->label('Change here')->inline(false)->default(true)->reactive()
                    //             ->afterStateUpdated(function ($state, $set){}) ->onColor('success')
                    //             ->offColor('danger'),   
                                
                    //             Placeholder::make('status')->label('Currently')->content(function ($state, $get) {
                    //                 $status = $get('is_active') === true ? 'ACTIVE' : 'CANCELLED';
                    //                 $style = $status === 'ACTIVE' ? 'color: green;' : 'color: red;';
                    //                 // dd($status);
                    //                 return new HtmlString("<span style='" . $style . "'>" . $status . "</span>");
                    //             })->extraAttributes(['class' => 'text-primary-500 mt-15 items-center font-bold']),
                    //         ])->columns(2), 
                    //     ])->columnSpan(1),                          
                    // ])->columnSpan(8),     

                    // // Item Description
                    // Textarea::make('item_description')
                    // ->nullable()
                    // ->label('Item Description')->columnSpanFull(),

                    // // Images (JSON)
                    // FileUpload::make('images')
                    // ->multiple()
                    // ->enableReordering()
                    // ->directory(fn ($get) => $get('images_directory') ?? 'items') // Set the directory dynamically
                    // ->label('Images')->columnSpanFull(),

            ]),

            // Pricing / Linking Section
            Section::make('Item Pricing / Linking')->label('Item Pricing / Linking')
            ->schema([
                Checkbox::make('show_all')
                ->label('Show All')
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $customers = Customer::all()->toArray();
                        $set('prices', collect($customers)->map(fn ($customer) => [
                            'customer_id' => $customer['id'],
                            'customer_barcode' => '',
                            'customer_ref' => '',
                            'price' => '',
                            'is_linked' => true,
                        ])->toArray());
                    } else {
                        $set('prices', []);
                    }
                }),

                Repeater::make('prices')->label('Link Price to the Customer')
                ->relationship('prices')
                ->schema([
                    Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->label('Customer Name')
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                    TextInput::make('customer_barcode')
                    ->label('Customer Barcode')
                    ->required(),

                    TextInput::make('customer_ref')
                    ->label('Customer Reference')
                    ->required(),

                    TextInput::make('price')
                    ->numeric()
                    ->label('Price')
                    ->required(),

                    // Toggle::make('is_linked')
                    // ->label('Linked')
                    // ->default(true),
                ])->columns(2)
                ->live()
                // ->collapsed(fn ($get) => !$get('show_all'))
                ->collapsed(false)
                ->createItemButtonLabel('Add Customer'),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Split::make([
                TextColumn::make('category.name')
                    ->label('Product Category Name'),
                TextColumn::make('name')
                    ->label('Item Name'),
                TextColumn::make('item_no')
                    ->label('Item No.'),
                TextColumn::make('item_description')
                ->label('Item Description'),
                ImageColumn::make('images')
                    ->label('Images')
                    ->stacked()
                    ->limit(1),
                TextColumn::make('unit_measure')
                    ->label('Unit of Measure'),

                Tables\Columns\BooleanColumn::make('is_active')
                ->label('Active'),
            ]),

            Panel::make([
                Split::make([
                    TextColumn::make('prices')
                    ->label('Customer Details')
                    ->getStateUsing(function ($record) {
                        $output = '<table style="width: 150%;">';
                        $output .=
                        '<tr style="background:white; border: 2px solid white; border-radius: 25px;">
                            <th style="text-align: left; padding: 8px;">Customer Name</th>
                            <th style="text-align: left; padding: 8px;">Customer Barcode</th>
                            <th style="text-align: left; padding: 8px;">Customer Reference</th>
                            <th style="text-align: left; padding: 8px;">Price</th>
                        </tr>';
                        foreach ($record->prices as $price) {
                            $output .=
                            "<tr>
                                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$price->customer->name}</td>
                                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$price->customer_barcode}</td>
                                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$price->customer_ref}</td>
                                <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$price->price}</td>
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
        ->actions([
            Tables\Actions\ActionGroup::make([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    private function getItemDirectory($itemNo)
    {
        $directory = "items/{$itemNo}";
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        return $directory;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'item_no', 'prices.customer_barcode'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItems::route('/create'),
            'edit' => Pages\EditItems::route('/{record}/edit'),
        ];
    }
}
