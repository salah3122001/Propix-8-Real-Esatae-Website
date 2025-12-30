<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('user_id')->relationship('user', 'name')->label(__('admin.resources.user'))->required(),
                Select::make('unit_id')
                    ->relationship('unit', 'title_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->{'title_' . app()->getLocale()} ?? $record->title_ar)
                    ->label(__('admin.resources.unit'))
                    ->required(),
                TextInput::make('amount')->label(__('admin.fields.amount'))->numeric()->required(),
                Select::make('payment_status')->label(__('admin.fields.payment_status'))->options([
                    'pending' => __('admin.fields.statuses.pending'),
                    'paid' => __('admin.fields.statuses.paid'),
                    'failed' => __('admin.fields.statuses.failed'),
                ])->required(),
                TextInput::make('transaction_ref')->label(__('admin.fields.transaction_ref')),
            ]);
    }
}
