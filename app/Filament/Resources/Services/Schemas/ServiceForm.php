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
                    ->helperText(new HtmlString(__('admin.fields.service_icon_desc') . '<br>' . __('admin.fields.keep_current')))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('services-icons')
                    ->nullable(),
            ]);
    }
}
