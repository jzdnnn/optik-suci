<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class PembukuanHarian extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Pembukuan Harian';
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.admin.pages.pembukuan-harian';

    public ?string $startDate = null;
    public ?string $endDate = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('viewAny_laporan_keuangan') ?? false;
    }

    public function mount(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->endOfMonth()->toDateString();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('startDate')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->live(),
                DatePicker::make('endDate')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->live(),
            ])
            ->columns(2);
    }

    protected function getViewData(): array
    {
        $report = ReportController::getPembukuanHarianData($this->startDate, $this->endDate);

        return [
            'reportData' => $report['data'],
            'totals'     => $report['totals'],
            'printUrl'   => route('pembukuan-harian.print', [
                'start_date' => $this->startDate,
                'end_date'   => $this->endDate,
            ]),
        ];
    }
}
