<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Item;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ItemsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ItemsResource\RelationManagers;
use App\Models\Price;
use Filament\Tables\Columns\ImageColumn;

class ItemsResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            Section::make()->schema([
                // Main Form Section: Item Details
                Section::make('Item Details')
                ->schema([
                    // Category Selection
                    Select::make('category_id')->label('Product Category')
                    ->relationship('category', 'name')
                    ->required(),

                    // Item Name
                    TextInput::make('name')
                    ->required()
                    ->label('Item Name'),

                    // Item Number
                    TextInput::make('item_no')
                    ->nullable()
                    ->label('Item Number')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('images_directory', "items/{$state}")),

                    // Item Description
                    Textarea::make('item_description')
                    ->nullable()
                    ->label('Item Description')->columnSpanFull(),

                    // Images (JSON)
                    FileUpload::make('images')
                    ->multiple()
                    ->enableReordering()
                    ->directory(fn ($get) => $get('images_directory') ?? 'items') // Set the directory dynamically
                    ->label('Images')->columnSpanFull(),

                    Toggle::make('is_active')
                    ->label('Active Item')
                    ->default(true),
                ])->columns(3),

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

                        Toggle::make('is_linked')
                        ->label('Linked')
                        ->default(true),
                    ])->columns(2)
                    ->live()
                    // ->collapsed(fn ($get) => !$get('show_all'))
                    ->collapsed(false)
                    ->createItemButtonLabel('Add Customer'),
                ])->columns(1),
            ])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItems::route('/create'),
            'edit' => Pages\EditItems::route('/{record}/edit'),
        ];
    }
}
