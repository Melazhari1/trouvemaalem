<?php

namespace App\Filament\Resources\Artisans\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ArtisansTable
{
    public static function configure(Table $table): Table
    {
        $locale = app()->getLocale();

        return $table
            ->columns([
                ImageColumn::make('image')
                    ->circular(),

                TextColumn::make('name')
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', $locale, false) ?: $record->getTranslation('name', 'en', false))
                    ->searchable(query: fn ($query, $search) => $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(artisans.name, '$.en')) LIKE ?", ["%{$search}%"]))
                    ->sortable(query: fn ($query, $direction) => $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(artisans.name, '$.en')) {$direction}")),

                TextColumn::make('categories.name')
                    ->label('Categories')
                    ->getStateUsing(fn ($record) => $record->categories->map(
                        fn ($c) => $c->getTranslation('name', $locale, false) ?: $c->getTranslation('name', 'en', false)
                    )->filter()->join(', '))
                    ->badge(),

                TextColumn::make('city')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('average_rating')
                    ->label('Rating')
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('rating', $direction)),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(fn () => Category::all()->mapWithKeys(fn ($c) => [
                        $c->id => $c->getTranslation('name', $locale, false) ?: $c->getTranslation('name', 'en', false),
                    ]))
                    ->query(fn ($query, array $data) => filled($data['value'])
                        ? $query->whereHas('categories', fn ($q) => $q->where('categories.id', $data['value']))
                        : $query
                    ),

                TernaryFilter::make('is_verified')
                    ->label('Verified'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(
                column: 'artisans.name',
                direction: 'asc',
            );
    }
}
