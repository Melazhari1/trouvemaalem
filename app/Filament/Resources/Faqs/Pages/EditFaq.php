<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFaq extends EditRecord
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        foreach (['question', 'answer'] as $field) {
            foreach (['en', 'fr', 'ar'] as $locale) {
                $data["{$field}_{$locale}"] = $record->getTranslation($field, $locale, false) ?? '';
            }
        }
        return $data;
    }
}
