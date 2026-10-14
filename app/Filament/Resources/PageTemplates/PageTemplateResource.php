<?php

namespace App\Filament\Resources\PageTemplates;

use App\Enums\PageType;
use App\Filament\Resources\PageTemplates\Pages\CreatePageTemplate;
use App\Filament\Resources\PageTemplates\Pages\EditPageTemplate;
use App\Filament\Resources\PageTemplates\Pages\ListPageTemplates;
use App\Models\PageTemplate;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PageTemplateResource extends Resource
{
    protected static ?string $model = PageTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('admin.template.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.template.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.template.plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.content');
    }

    /** Polja forme — zajednička za resurs i karticu u projektu. */
    public static function fields(bool $withProject = true): array
    {
        return array_values(array_filter([
            $withProject ? Select::make('project_id')->label(__('admin.template.project'))
                ->relationship('project', 'name')->required()->searchable()->preload() : null,
            Select::make('page_type')->label(__('admin.template.page_type'))->options(PageType::options())->required(),
            TextInput::make('name')->label(__('admin.template.name'))->required()->maxLength(160),
            FileUpload::make('image')->label(__('admin.template.image'))
                ->image()->disk('public')->directory('templates')->maxSize(5120)->required()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->columnSpanFull(),
        ]));
    }

    /** Kolone tabele — zajedničke za resurs i karticu u projektu. */
    public static function columns(bool $withProject = true): array
    {
        return array_values(array_filter([
            ImageColumn::make('image')->label('')->disk('public')->imageHeight(48)->extraImgAttributes(['style' => 'border-radius:.5rem;object-fit:cover;width:76px']),
            TextColumn::make('name')->label(__('admin.template.name'))->searchable()->weight('bold'),
            TextColumn::make('page_type')->label(__('admin.template.page_type'))->badge()
                ->formatStateUsing(fn (PageType $state) => $state->label()),
            $withProject ? TextColumn::make('project.name')->label(__('admin.template.project'))->sortable() : null,
        ]));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema(self::fields()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns(self::columns())
            ->filters([
                SelectFilter::make('page_type')->label(__('admin.template.page_type'))->options(PageType::options()),
                SelectFilter::make('project')->label(__('admin.template.project'))->relationship('project', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageTemplates::route('/'),
            'create' => CreatePageTemplate::route('/create'),
            'edit' => EditPageTemplate::route('/{record}/edit'),
        ];
    }
}
