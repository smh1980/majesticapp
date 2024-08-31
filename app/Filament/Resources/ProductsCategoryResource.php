<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\ProductsCategory;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductsCategoryResource\Pages;
use App\Filament\Resources\ProductsCategoryResource\RelationManagers;

class ProductsCategoryResource extends Resource
{
    protected static ?string $model = ProductsCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Category Name')
                    ->live(onBlur:true)
                    ->afterStateUpdated(fn (string $operation, $state, Set $set ) => $operation
                    === 'create'? $set('slug', Str::slug($state)) : null),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->unique(ProductsCategory::class, 'slug', ignoreRecord: true)
                    ->label('Slug'),

                FileUpload::make('image')
                    ->label('Images')->directory('productscategory')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
            ])->columns(2)
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Category Name'),

                Tables\Columns\ImageColumn::make('image')
                    ->label('Image'),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label('Active'),
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
            'index' => Pages\ListProductsCategories::route('/'),
            'create' => Pages\CreateProductsCategory::route('/create'),
            'edit' => Pages\EditProductsCategory::route('/{record}/edit'),
        ];
    }
}
