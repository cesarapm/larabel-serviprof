<?php

namespace App\Filament\Resources\Almacen\Pages;

use App\Filament\Resources\Almacen\AlmacenResource;
use Filament\Resources\Pages\ListRecords;

class ListAlmacen extends ListRecords
{
    protected static string $resource = AlmacenResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
