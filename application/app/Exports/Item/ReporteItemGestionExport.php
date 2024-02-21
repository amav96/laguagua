<?php
namespace App\Exports\Item;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteItemGestionExport implements FromView, Responsable, ShouldAutoSize, WithTitle, WithEvents, WithStyles
{
    use Exportable;
    private $data;
    private $filename;
    private $writerType = Excel::XLSX;

    public function __construct($data, $filename)
    {
        $this->data = $data;
        $this->filename = $filename;
    }

    public function view(): View
    {
       
        return view('Excel.Item.GestionExcel')
            ->with([
                "metricas"      => $this->data["metricas"],
                "items"         => $this->data["items"],
                "parametros"    => $this->data["parametros"],
                "usuario"       => $this->data["usuario"]

            ]);
    }

    public function title(): string
    {
        return $this->filename;
    }

    public function toResponse($request)
    {
        return $this->download();
    }

    public function styles(Worksheet $sheet)
    {
       
        foreach ($this->data['items'] as $key => $item) {
            $color = $item->itemEstado->color;
            $color = str_replace('#', '', $color);
            $value = $key + 11;
            $sheet->getStyle("A{$value}")->getFill()->applyFromArray(['fillType' => 'solid', 'color' => ['rgb' => $color]]);
        }
       
    }

    public function registerEvents(): array
    {
        // $alphabet = $this->getNameFromNumber($this->data['total_columnas'] - 1);
        // $sumarRow = config('app.domain') === 'cl' ? 17 : 12;
        // $totalRow = $this->data["items"]->count() + $sumarRow;
        // $cellRange = config('app.domain') === 'cl' ? 'A14:'.$alphabet.$totalRow : 'A10:'.$alphabet.$totalRow;
      
        return [
            AfterSheet::class => function (AfterSheet $event) : void {
                $event->sheet->getDelegate()->freezePane('A1');
                $event->sheet->getDelegate()->freezePane('A2');
                $event->sheet->getDelegate()->freezePane('A3');
                $event->sheet->getDelegate()->freezePane('A4');
                $event->sheet->getDelegate()->freezePane('A5');
                $event->sheet->getDelegate()->freezePane('A6');
                $event->sheet->getDelegate()->freezePane('A7');
                $event->sheet->getDelegate()->freezePane('A8');
                $event->sheet->getDelegate()->freezePane('A9');
                
                // $event->sheet->getStyle($cellRange)->applyFromArray([
                //     'borders' => [
                //         'allBorders' => [
                //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                //             'color' => ['argb' => '000000'],
                //         ],
                //     ],
                // ]);

               
            },
        ];
    }
}