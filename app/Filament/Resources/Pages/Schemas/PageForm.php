<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Get;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('admin.fields.page_info'))
                    ->schema([
                        TextInput::make('title_ar')
                            ->label(__('admin.fields.title_ar'))
                            ->required(),
                        TextInput::make('title_en')
                            ->label(__('admin.fields.title_en'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
                        RichEditor::make('content_ar')
                            ->label(__('admin.fields.content_ar'))
                            ->required()
                            ->fileAttachmentsDirectory('pages')
                            ->columnSpanFull(),
                        RichEditor::make('content_en')
                            ->label(__('admin.fields.content_en'))
                            ->fileAttachmentsDirectory('pages')
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->label(__('admin.fields.link'))
                            ->required()
                            ->unique('pages', 'slug', ignoreRecord: true)
                            ->helperText(__('admin.fields.auto_generated_slug')),
                    ]),

                Section::make(__('admin.fields.team_members'))
                    ->description(__('admin.fields.team_description'))
                    ->schema([
                        Repeater::make('team_members')
                            ->label(__('admin.fields.team_members'))
                            ->schema([
                                TextInput::make('name')->label(__('admin.fields.name'))->required(),
                                TextInput::make('position')->label(__('admin.fields.position')),
                                FileUpload::make('photo')
                                    ->label(fn($get) => match ($get('type')) {
                                        default => __('admin.fields.image'),
                                    })
                                    ->helperText('يرجى استخدام صيغ الصور المدعومة: JPG, PNG, GIF, WEBP')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->directory('team')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->downloadable()
                                    ->openable()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                            ->collapsible()
                            ->collapsed(),
                    ]),
            ]);
    }
}
