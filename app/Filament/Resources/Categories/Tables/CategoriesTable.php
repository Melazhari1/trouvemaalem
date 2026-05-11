<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=Cat&background=random'),

                TextColumn::make('name')
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', app()->getLocale(), false) ?: $record->getTranslation('name', 'en', false))
                    ->searchable(query: fn ($query, $search) => $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$search}%"]))
                    ->sortable(query: fn ($query, $direction) => $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) {$direction}")),

                TextColumn::make('slug')
                    ->searchable(),

                TextColumn::make('artisans_count')
                    ->label('Artisans')
                    ->counts('artisans')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('slug');
    }
}
