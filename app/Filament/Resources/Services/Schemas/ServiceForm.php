<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('title_ar')
                    ->label(__('admin.fields.title_ar'))
                    ->required(),
                TextInput::make('title_en')
                    ->label(__('admin.fields.title_en'))
                    ->required(),
                RichEditor::make('content_ar')
                    ->label(__('admin.fields.content_ar'))
                    ->required()
                    ->fileAttachmentsDirectory('services')
                    ->columnSpanFull(),
                RichEditor::make('content_en')
                    ->label(__('admin.fields.content_en'))
                    ->required()
                    ->fileAttachmentsDirectory('services')
                    ->columnSpanFull(),
                FileUpload::make('icon')
                    ->label(__('admin.fields.icon'))
                    ->helperText('يرجى استخدام صيغ الصور المدعومة: JPG, PNG, GIF, WEBP')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->disk('public')
                    ->visibility('public')
                    ->directory('services-icons')
                    ->downloadable()
                    ->openable()
                    ->nullable(),
            ]);
    }
}
