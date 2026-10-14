<?php

namespace App\Filament\Resources\Projects;

use App\Enums\Industry;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\RelationManagers\PageTemplatesRelationManager;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('admin.project.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.project.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.project.plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.content');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('name')->label(__('admin.project.name'))->required()->maxLength(160)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug((string) $state, '-', 'sr')) : null),
                TextInput::make('slug')->label(__('admin.project.slug'))->required()->maxLength(80)
                    ->alphaDash()->unique(ignoreRecord: true),
                Select::make('industry')->label(__('admin.project.industry'))->options(Industry::options())->required(),
                TextInput::make('url')->label(__('admin.project.url'))->url()->required()->maxLength(255),
                FileUpload::make('cover')->label(__('admin.project.cover'))
                    ->image()->disk('public')->directory('projects')->maxSize(5120)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->columnSpanFull(),
                Toggle::make('is_published')->label(__('admin.project.published'))->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('cover')->label('')->disk('public')->imageHeight(48)->extraImgAttributes(['style' => 'border-radius:.5rem;object-fit:cover;width:76px']),
                TextColumn::make('name')->label(__('admin.project.name'))->searchable()->weight('bold'),
                TextColumn::make('industry')->label(__('admin.project.industry'))->badge()
                    ->formatStateUsing(fn (Industry $state) => $state->label()),
                TextColumn::make('page_templates_count')->counts('pageTemplates')->label(__('admin.project.templates')),
                ToggleColumn::make('is_published')->label(__('admin.project.published')),
            ])
            ->filters([
                SelectFilter::make('industry')->label(__('admin.project.industry'))->options(Industry::options()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PageTemplatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
