<?php

namespace App\Filament\Resources\Artisans\Pages;

use App\Filament\Resources\Artisans\ArtisanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArtisan extends EditRecord
{
    protected static string $resource = ArtisanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $data['image'] = $record->getRawOriginal('image');
        foreach (['name', 'bio'] as $field) {
            foreach (['en', 'fr', 'ar'] as $locale) {
                $data["{$field}_{$locale}"] = $record->getTranslation($field, $locale, false) ?? '';
            }
        }
        return $data;
    }
}
