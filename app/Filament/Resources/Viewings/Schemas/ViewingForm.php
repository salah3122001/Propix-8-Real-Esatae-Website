<?php

namespace App\Filament\Resources\Viewings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class ViewingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('unit_id')
                    ->relationship('unit', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                DatePicker::make('date')
                    ->required(),
                TimePicker::make('time')
                    ->required(),
                Select::make('status')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'reschedule_admin' => 'Reschedule (Admin)',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Textarea::make('user_message')
                    ->label('User Message')
                    ->helperText('Message from user when proposing new time')
                    ->columnSpanFull(),
            ]);
    }
}
