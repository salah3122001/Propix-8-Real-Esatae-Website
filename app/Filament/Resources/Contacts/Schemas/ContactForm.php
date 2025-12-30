<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->label(__('admin.fields.name'))->required(),
                TextInput::make('email')->email()->label(__('admin.fields.email'))->required(),
                TextInput::make('phone')->label(__('admin.fields.phone')),
                TextInput::make('address')->label(__('admin.fields.address')),
                \Filament\Forms\Components\Select::make('unit_id')
                    ->label(__('admin.resources.unit'))
                    ->relationship('unit', 'title_' . app()->getLocale())
                    ->disabled(),
                \Filament\Forms\Components\Select::make('seller_id')
                    ->label(__('admin.resources.user'))
                    ->relationship('seller', 'name')
                    ->disabled(),
                Textarea::make('message')->label(__('admin.fields.message'))->required(),
            ]);
    }
}
