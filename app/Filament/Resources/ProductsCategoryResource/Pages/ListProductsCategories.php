<?php

namespace App\Filament\Resources\ProductsCategoryResource\Pages;

use App\Filament\Resources\ProductsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductsCategories extends ListRecords
{
    protected static string $resource = ProductsCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
