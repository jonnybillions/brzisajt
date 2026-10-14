<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\PageTemplates\PageTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PageTemplatesRelationManager extends RelationManager
{
    protected static string $relationship = 'pageTemplates';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.template.plural');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components(PageTemplateResource::fields(withProject: false));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modelLabel(__('admin.template.model'))
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns(PageTemplateResource::columns(withProject: false))
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
