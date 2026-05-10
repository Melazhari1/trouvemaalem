<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        TextInput::make('order')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),

                Tabs::make('Translations')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('🇬🇧 English')
                            ->schema([
                                TextInput::make('question_en')
                                    ->label('Question')
                                    ->required()
                                    ->maxLength(500),

                                Textarea::make('answer_en')
                                    ->label('Answer')
                                    ->required()
                                    ->rows(4),
                            ]),

                        Tab::make('🇫🇷 Français')
                            ->schema([
                                TextInput::make('question_fr')
                                    ->label('Question')
                                    ->maxLength(500),

                                Textarea::make('answer_fr')
                                    ->label('Réponse')
                                    ->rows(4),
                            ]),

                        Tab::make('🇸🇦 العربية')
                            ->schema([
                                TextInput::make('question_ar')
                                    ->label('السؤال')
                                    ->maxLength(500)
                                    ->extraAttributes(['dir' => 'rtl', 'class' => 'text-right']),

                                Textarea::make('answer_ar')
                                    ->label('الجواب')
                                    ->rows(4)
                                    ->extraAttributes(['dir' => 'rtl', 'class' => 'text-right']),
                            ]),
                    ]),
            ]);
    }
}
