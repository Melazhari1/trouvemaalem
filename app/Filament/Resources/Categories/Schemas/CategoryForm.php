<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Slug & Image')
                    ->columns(2)
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(191)
                            ->unique(ignoreRecord: true),

                        FileUpload::make('image')
                            ->image()
                            ->directory('categories'),
                    ]),

                Tabs::make('Translations')
                    ->tabs([
                        Tab::make('🇬🇧 English')
                            ->schema([
                                TextInput::make('name_en')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(191)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) =>
                                        $set('slug', \Illuminate\Support\Str::slug($state))
                                    ),

                                Textarea::make('description_en')
                                    ->label('Description')
                                    ->rows(3),
                            ]),

                        Tab::make('🇫🇷 Français')
                            ->schema([
                                TextInput::make('name_fr')
                                    ->label('Nom')
                                    ->maxLength(191),

                                Textarea::make('description_fr')
                                    ->label('Description')
                                    ->rows(3),
                            ]),

                        Tab::make('🇸🇦 العربية')
                            ->schema([
                                TextInput::make('name_ar')
                                    ->label('الاسم')
                                    ->maxLength(191)
                                    ->extraAttributes(['dir' => 'rtl', 'class' => 'text-right']),

                                Textarea::make('description_ar')
                                    ->label('الوصف')
                                    ->rows(3)
                                    ->extraAttributes(['dir' => 'rtl', 'class' => 'text-right']),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
