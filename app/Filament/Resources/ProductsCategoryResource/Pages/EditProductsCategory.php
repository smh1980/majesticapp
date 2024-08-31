<?php

namespace App\Filament\Resources\ProductsCategoryResource\Pages;

use App\Filament\Resources\ProductsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductsCategory extends EditRecord
{
    protected static string $resource = ProductsCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
