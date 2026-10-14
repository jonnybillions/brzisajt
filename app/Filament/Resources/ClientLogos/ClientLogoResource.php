<?php

namespace App\Filament\Resources\ClientLogos;

use App\Filament\Resources\ClientLogos\Pages\ManageClientLogos;
use App\Models\ClientLogo;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ClientLogoResource extends Resource
{
    protected static ?string $model = ClientLogo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('admin.logo.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.logo.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.logo.plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.content');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label(__('admin.logo.name'))->required()->maxLength(160),
            FileUpload::make('logo')->label(__('admin.logo.logo'))
                ->disk('public')->directory('logos')->maxSize(1024)
                ->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/webp']),
            Toggle::make('is_published')->label(__('admin.logo.published'))->default(true),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('logo')->label('')->disk('public')->imageHeight(32)->extraImgAttributes(['style' => 'max-width:140px;object-fit:contain']),
                TextColumn::make('name')->label(__('admin.logo.name'))->searchable()->weight('bold'),
                ToggleColumn::make('is_published')->label(__('admin.logo.published')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageClientLogos::route('/'),
        ];
    }
}
