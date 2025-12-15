<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $relatedResource = StudentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
