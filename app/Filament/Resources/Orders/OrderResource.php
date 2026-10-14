<?php

namespace App\Filament\Resources\Orders;

use App\Configurator\OrderSummary;
use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('admin.order.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.order.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.nav.orders');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Order::query()->new()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make(__('admin.order.choices'))
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('price')
                            ->label(__('admin.order.price'))
                            ->state(fn (Order $record) => OrderSummary::price($record->extras ?? []))
                            ->badge()
                            ->color('success'),
                        ViewEntry::make('config')
                            ->hiddenLabel()
                            ->view('filament.orders.summary')
                            ->viewData(fn (Order $record) => ['rows' => OrderSummary::lines($record->config ?? [])]),
                    ]),

                Grid::make(1)
                    ->columnSpan(1)
                    ->schema([
                        Section::make(__('admin.order.contacts'))->schema([
                            TextEntry::make('status')->label(__('admin.order.status'))->badge()
                                ->formatStateUsing(fn (OrderStatus $state) => $state->label())
                                ->color(fn (OrderStatus $state) => $state->color()),
                            TextEntry::make('name')->label(__('admin.order.name')),
                            TextEntry::make('company')->label(__('admin.order.company'))->placeholder('—'),
                            TextEntry::make('phone')->label(__('admin.order.phone'))->placeholder('—')
                                ->url(fn (Order $record) => $record->phone ? tel_href($record->phone) : null),
                            TextEntry::make('email')->label(__('admin.order.email'))->placeholder('—')
                                ->url(fn (Order $record) => $record->email ? 'mailto:'.$record->email : null)->copyable(),
                            TextEntry::make('channel')->label(__('admin.order.channel'))
                                ->formatStateUsing(fn (?string $state) => $state ? __("cfg.channel.options.$state") : '—'),
                            TextEntry::make('comment')->label(__('admin.order.comment'))->placeholder('—'),
                            TextEntry::make('cloud_link')->label(__('admin.order.cloud_link'))->placeholder('—')
                                ->url(fn (Order $record) => $record->cloud_link, shouldOpenInNewTab: true),
                        ]),

                        Section::make(__('admin.order.files'))->schema([
                            ViewEntry::make('files')
                                ->hiddenLabel()
                                ->view('filament.orders.files'),
                        ]),

                        Section::make(__('admin.order.notes'))->schema([
                            TextEntry::make('notes')->hiddenLabel()->placeholder(__('admin.order.notes_help')),
                        ]),

                        Section::make(__('admin.order.meta'))->collapsed()->schema([
                            TextEntry::make('created_at')->label(__('admin.order.date'))->dateTime('d.m.Y H:i'),
                            TextEntry::make('consent_at')->label(__('admin.order.consent_at'))->dateTime('d.m.Y H:i:s'),
                            TextEntry::make('ip')->label(__('admin.order.ip'))->placeholder('—'),
                            TextEntry::make('user_agent')->label(__('admin.order.user_agent'))->placeholder('—'),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('number')->label(__('admin.order.number'))->searchable()->weight('bold'),
                TextColumn::make('created_at')->label(__('admin.order.date'))->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('name')->label(__('admin.order.name'))->searchable()
                    ->description(fn (Order $record) => $record->company),
                TextColumn::make('phone')->label(__('admin.order.phone'))->searchable()->toggleable(),
                TextColumn::make('email')->label(__('admin.order.email'))->searchable()->toggleable(),
                TextColumn::make('status')->label(__('admin.order.status'))->badge()
                    ->formatStateUsing(fn (OrderStatus $state) => $state->label())
                    ->color(fn (OrderStatus $state) => $state->color()),
                TextColumn::make('extras')->label(__('admin.order.extras'))->badge()->color('warning')
                    ->formatStateUsing(fn (string $state) => __("admin.order.extra_$state"))
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('admin.order.status'))->options(OrderStatus::options())->multiple(),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('from')->label(__('admin.order.created_from')),
                        DatePicker::make('until')->label(__('admin.order.created_until')),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date))),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }
}
