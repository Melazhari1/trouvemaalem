<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Models\Artisan;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Review Content')
                    ->schema([
                        Select::make('artisan_id')
                            ->label('Artisan')
                            ->options(Artisan::all()->mapWithKeys(fn ($a) => [$a->id => $a->name]))
                            ->searchable()
                            ->required(),

                        Select::make('rating')
                            ->options([
                                1 => '1 — Poor',
                                2 => '2 — Fair',
                                3 => '3 — Good',
                                4 => '4 — Very Good',
                                5 => '5 — Excellent',
                            ])
                            ->required(),

                        Textarea::make('comment')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Submission Info')
                    ->schema([
                        Select::make('user_id')
                            ->label('User Account (optional)')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        TextInput::make('submitted_by_name')
                            ->label('Submitted By (name)')
                            ->disabled(),

                        TextInput::make('submitted_by_email')
                            ->label('Submitted By (email)')
                            ->disabled(),
                    ])
                    ->columns(3),

                Section::make('Moderation')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending'  => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),

                        Textarea::make('admin_notes')
                            ->label('Admin Notes / Rejection Reason')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
