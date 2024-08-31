<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use App\Filament\Resources\CustomersResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomersResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\OrdersRelationManager;

class CustomersResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Customer Details')
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
                Forms\Components\TextInput::make('persons_name')->label('Contact Persons Name')
                ->required()
                ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                ->nullable()
                ->maxLength(20), // Adjust maxLength as needed

                Forms\Components\Toggle::make('is_active')
                ->default(true)
                ->label('Active'),
            ])->columns(3)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('persons_name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('phone')
                    ->sortable()
                    ->searchable(),

                BooleanColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
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
            OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomers::route('/create'),
            'edit' => Pages\EditCustomers::route('/{record}/edit'),
        ];
    }
}
