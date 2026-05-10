<?php

namespace App\Filament\Resources\ContactSubmissions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Submission Details')
                    ->schema([
                        TextInput::make('name')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('email')
                            ->email()
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('subject')
                            ->disabled()
                            ->columnSpanFull(),

                        Textarea::make('message')
                            ->rows(8)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Status')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'new'     => 'New',
                                'read'    => 'Read',
                                'replied' => 'Replied',
                            ])
                            ->required(),

                        TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled(),

                        TextInput::make('created_at')
                            ->label('Received At')
                            ->disabled(),
                    ])
                    ->columns(3),
            ]);
    }
}
