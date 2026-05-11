<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

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
        foreach (['name', 'description'] as $field) {
            foreach (['en', 'fr', 'ar'] as $locale) {
                $data["{$field}_{$locale}"] = $record->getTranslation($field, $locale, false) ?? '';
            }
        }
        return $data;
    }
}
