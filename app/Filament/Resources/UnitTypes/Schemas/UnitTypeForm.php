<?php

namespace App\Filament\Resources\UnitTypes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class UnitTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name_ar')
                    ->label(__('admin.fields.name_ar'))
                    ->required(),
                TextInput::make('name_en')
                    ->label(__('admin.fields.name_en'))
                    ->required(),
                FileUpload::make('icon')
                    ->label(__('admin.fields.icon'))
                    ->helperText('يرجى استخدام صيغ الصور المدعومة: JPG, PNG, GIF, WEBP')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->disk('public')
                    ->visibility('public')
                    ->directory('unit-types')
                    ->downloadable()
                    ->openable()
                    ->nullable(),
            ]);
    }
}
