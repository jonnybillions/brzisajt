<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('status')
                ->label(__('admin.order.change_status'))
                ->icon('heroicon-o-arrow-path')
                ->fillForm(fn (Order $record) => ['status' => $record->status->value])
                ->schema([
                    Select::make('status')->label(__('admin.order.status'))->options(OrderStatus::options())->required(),
                ])
                ->action(fn (Order $record, array $data) => $record->update(['status' => $data['status']])),

            Action::make('notes')
                ->label(__('admin.order.notes'))
                ->icon('heroicon-o-pencil-square')
                ->color('gray')
                ->fillForm(fn (Order $record) => ['notes' => $record->notes])
                ->schema([
                    Textarea::make('notes')->label(__('admin.order.notes'))->helperText(__('admin.order.notes_help'))->rows(6),
                ])
                ->action(fn (Order $record, array $data) => $record->update(['notes' => $data['notes']])),
        ];
    }
}
