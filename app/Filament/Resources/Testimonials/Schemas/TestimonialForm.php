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
            \Filament\Forms\Components\Placeholder::make('user_link')
                ->label(__('admin.resources.user'))
                ->content(fn($record) => $record?->user ? new \Illuminate\Support\HtmlString('<a href="' . \App\Filament\Resources\Users\UserResource::getUrl('edit', ['record' => $record->user_id]) . '" class="text-primary-600 hover:underline font-bold">' . $record->user->name . '</a>') : '-'),
            Select::make('user_id')
                ->label(__('admin.resources.user'))
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->live()
                ->hidden(fn($record) => $record !== null)
                ->afterStateUpdated(fn($state, $set) => $set('name', \App\Models\User::find($state)?->name)),

            \Filament\Forms\Components\Placeholder::make('user_email')
                ->label(__('admin.fields.email'))
                ->content(fn($record) => $record?->user?->email ?? '-'),
            \Filament\Forms\Components\Placeholder::make('user_phone')
                ->label(__('admin.fields.phone'))
                ->content(fn($record) => $record?->user?->phone ?? '-'),

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
                ->helperText('يرجى استخدام صيغ الصور المدعومة: JPG, PNG, GIF, WEBP, JFIF')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jfif'])
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
