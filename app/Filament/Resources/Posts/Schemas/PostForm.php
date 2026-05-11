<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Slug & Publishing')
                    ->columns(2)
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(191)
                            ->unique(ignoreRecord: true),

                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true),

                        FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('posts')
                            ->columnSpanFull(),
                    ]),

                Tabs::make('Translations')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('🇬🇧 English')
                            ->schema([
                                TextInput::make('title_en')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(191)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) =>
                                        $set('slug', \Illuminate\Support\Str::slug($state))
                                    ),

                                Textarea::make('excerpt_en')
                                    ->label('Excerpt')
                                    ->rows(3),

                                RichEditor::make('content_en')
                                    ->label('Content')
                                    ->required(),
                            ]),

                        Tab::make('🇫🇷 Français')
                            ->schema([
                                TextInput::make('title_fr')
                                    ->label('Titre')
                                    ->maxLength(191),

                                Textarea::make('excerpt_fr')
                                    ->label('Extrait')
                                    ->rows(3),

                                RichEditor::make('content_fr')
                                    ->label('Contenu'),
                            ]),

                        Tab::make('🇸🇦 العربية')
                            ->schema([
                                TextInput::make('title_ar')
                                    ->label('العنوان')
                                    ->maxLength(191)
                                    ->extraAttributes(['dir' => 'rtl', 'class' => 'text-right']),

                                Textarea::make('excerpt_ar')
                                    ->label('مقتطف')
                                    ->rows(3)
                                    ->extraAttributes(['dir' => 'rtl', 'class' => 'text-right']),

                                RichEditor::make('content_ar')
                                    ->label('المحتوى'),
                            ]),
                    ]),
            ]);
    }
}
