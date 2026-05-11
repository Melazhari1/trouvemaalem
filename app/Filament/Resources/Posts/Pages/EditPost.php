<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

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
        foreach (['title', 'excerpt', 'content'] as $field) {
            foreach (['en', 'fr', 'ar'] as $locale) {
                $data["{$field}_{$locale}"] = $record->getTranslation($field, $locale, false) ?? '';
            }
        }
        return $data;
    }
}
