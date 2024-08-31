<?php

namespace App\Filament\Resources\ProductsCategoryResource\Pages;

use App\Filament\Resources\ProductsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductsCategory extends CreateRecord
{
    protected static string $resource = ProductsCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
