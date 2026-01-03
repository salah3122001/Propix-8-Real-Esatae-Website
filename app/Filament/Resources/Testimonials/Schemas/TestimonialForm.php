<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('user_id')
                ->label(__('admin.resources.user'))
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(fn($state, $set) => $set('name', \App\Models\User::find($state)?->name)),

            TextInput::make('name')
                ->label(__('admin.fields.name'))
                ->helperText(__('admin.fields.auto_generated_name'))
                ->required(),

            TextInput::make('position')
                ->label(__('admin.fields.position')),

            Textarea::make('content')
                ->label(__('admin.fields.content'))
                ->required()
                ->minLength(10)
                ->maxLength(500)
                ->columnSpanFull(),

            FileUpload::make('image')
                ->label(__('admin.fields.image'))
                ->helperText('يرجى استخدام صيغ الصور المدعومة: JPG, PNG, GIF, WEBP')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                ->directory('testimonials')
                ->disk('public')
                ->visibility('public')
                ->downloadable()
                ->openable(),

            Toggle::make('status')
                ->label(__('admin.fields.active_site'))
                ->default(true),
        ]);
    }
}
