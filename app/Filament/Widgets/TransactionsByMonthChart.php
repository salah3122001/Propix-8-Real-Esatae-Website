<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class TransactionsByMonthChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('admin.widgets.monthly_transactions');
    }

    protected function getData(): array
    {
        $transactions = Transaction::all()->groupBy(fn($t) => $t->created_at->format('Y-m'));

        $labels = $transactions->keys()->toArray();
        $data = $transactions->map(fn($month) => count($month))->values()->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => __('admin.resources.transactions'),
                    'data' => $data,
                    'backgroundColor' => '#4f46e5',
                    'borderColor' => '#4f46e5',
                ],
            ],
        ];
    }


    protected function getType(): string
    {
        return 'bar';
    }
}
