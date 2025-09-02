<?php

namespace App\Exports;

use App\Models\Page;
use App\Services\Analytics\AnalyticsService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Collection;

class AnalyticsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithColumnFormatting
{
    private Page $page;
    private string $period;
    private AnalyticsService $analyticsService;

    public function __construct(Page $page, string $period)
    {
        $this->page = $page;
        $this->period = $period;
        $this->analyticsService = app(AnalyticsService::class);
    }

    public function collection(): Collection
    {
        return $this->analyticsService->generateExportData($this->page, $this->period);
    }

    public function headings(): array
    {
        return [
            'Date & Time',
            'Page Title',
            'Visitor ID',
            'IP Address',
            'Country',
            'City',
            'Device Type',
            'Browser',
            'Operating System',
            'Referrer',
            'Session ID',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row['Date'],
            $row['Page Title'],
            $this->anonymizeVisitorId($row['Visitor ID']),
            $this->anonymizeIp($row['IP Address']),
            $row['Country'] ?? 'Unknown',
            $row['City'] ?? 'Unknown',
            $row['Device'] ?? 'Unknown',
            $row['Browser'] ?? 'Unknown',
            $row['Platform'] ?? 'Unknown',
            $row['Referrer'] ?? 'Direct',
            $row['Session ID'],
            'Viewed',
        ];
    }

    public function title(): string
    {
        return "Analytics - {$this->page->title}";
    }

    public function styles(Worksheet $sheet): void
    {
        // Header styles
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2D3748']],
        ]);

        // Auto-size columns
        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Alternate row colors
        $sheet->getStyle('A2:L' . ($sheet->getHighestRow()))
            ->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setRGB('F7FAFC');

        $sheet->setAutoFilter('A1:L1');
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }

    private function anonymizeVisitorId(string $visitorId): string
    {
        return substr($visitorId, 0, 8) . '...' . substr($visitorId, -4);
    }

    private function anonymizeIp(?string $ip): string
    {
        if (!$ip) return 'N/A';

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return preg_replace('/\.\d+$/', '.xxx', $ip);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return preg_replace('/:[^:]+$/', ':xxxx', $ip);
        }

        return 'Invalid IP';
    }
}
