<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\ListRecords;

/** Upiti stižu samo sa sajta — ne pravimo ih ručno. */
class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;
}
