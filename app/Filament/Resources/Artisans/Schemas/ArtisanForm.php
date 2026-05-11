<?php

namespace App\Filament\Resources\Artisans\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
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

                        Select::make('category_id')
                            ->label('Category')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(30),

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

                                TextInput::make('location_en')
                                    ->label('Location')
                                    ->maxLength(255),
                            ]),

                        Tab::make('🇫🇷 Français')
                            ->schema([
                                TextInput::make('name_fr')
                                    ->label('Nom')
                                    ->maxLength(191),

                                Textarea::make('bio_fr')
                                    ->label('Biographie')
                                    ->rows(4),

                                TextInput::make('location_fr')
                                    ->label('Localisation')
                                    ->maxLength(255),
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

                                TextInput::make('location_ar')
                                    ->label('الموقع')
                                    ->maxLength(255)
                                    ->extraAttributes(['dir' => 'rtl', 'class' => 'text-right']),
                            ]),
                    ]),
            ]);
    }
}
