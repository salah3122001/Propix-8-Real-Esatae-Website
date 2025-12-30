<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('user_id')
                    ->label(__('admin.resources.user'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('unit_id')
                    ->relationship('unit', 'title_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->{'title_' . app()->getLocale()} ?? $record->title_ar)
                    ->label(__('admin.resources.unit'))
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('rating')
                    ->label(__('admin.fields.rating'))
                    ->options([
                        '1' => '1', 
                        '2' => '2', 
                        '3' => '3', 
                        '4' => '4', 
                        '5' => '5'
                    ])
                    ->required(),

                Textarea::make('comment')->label(__('admin.fields.comment')),

            ]);
    }
}
