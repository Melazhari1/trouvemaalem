<?php

namespace App\Filament\Resources\Artisans\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ArtisanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity & Contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(191)
                            ->unique(ignoreRecord: true),

                        Select::make('categories')
                            ->label('Categories')
                            ->multiple()
                            ->relationship('categories', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Category $record) => $record->name)
                            ->preload()
                            ->required(),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(30),

                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->maxLength(30)
                            ->helperText('International format, e.g. +212612345678'),

                        FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('artisans'),
                    ]),

                Section::make('Location')
                    ->columns(2)
                    ->schema([
                        TextInput::make('city')->maxLength(100),
                        TextInput::make('lat')->label('Latitude')->numeric()->step(0.00000001),
                        TextInput::make('lng')->label('Longitude')->numeric()->step(0.00000001),
                    ]),

                Section::make('Address Lines')
                    ->description('Add one or more address lines. Each line can be translated into all three languages.')
                    ->schema([
                        Repeater::make('locations')
                            ->label('')
                            ->schema([
                                TextInput::make('en')
                                    ->label('English')
                                    ->maxLength(500),
                                TextInput::make('fr')
                                    ->label('Français')
                                    ->maxLength(500),
                                TextInput::make('ar')
                                    ->label('العربية')
                                    ->maxLength(500)
                                    ->extraAttributes(['dir' => 'rtl', 'class' => 'text-right']),
                            ])
                            ->columns(3)
                            ->addActionLabel('Add address line')
                            ->collapsible()
                            ->defaultItems(0),
                    ]),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_verified')->label('Verified artisan')->default(false),
                    ]),

                Tabs::make('Translations')
                    ->columnSpanFull()
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

                                Textarea::make('bio_en')
                                    ->label('Bio')
                                    ->rows(4),
                            ]),

                        Tab::make('🇫🇷 Français')
                            ->schema([
                                TextInput::make('name_fr')
                                    ->label('Nom')
                                    ->maxLength(191),

                                Textarea::make('bio_fr')
                                    ->label('Biographie')
                                    ->rows(4),
                            ]),

                        Tab::make('🇸🇦 العربية')
                            ->schema([
                                TextInput::make('name_ar')
                                    ->label('الاسم')
                                    ->maxLength(191)
                                    ->extraAttributes(['dir' => 'rtl', 'class' => 'text-right']),

                                Textarea::make('bio_ar')
                                    ->label('نبذة شخصية')
                                    ->rows(4)
                                    ->extraAttributes(['dir' => 'rtl', 'class' => 'text-right']),
                            ]),
                    ]),
            ]);
    }
}
