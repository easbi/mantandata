<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AlokasiPetugasTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'assignment_id',
            'kode_wilayah',
            'nama_wilayah',
            'ppl_id',
            'ppl_nama',
            'pml_id',
            'pml_nama',
            'taskforce_id',
            'taskforce_nama',
        ];
    }
}
