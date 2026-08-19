<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AnomalyExcelImport implements ToArray, WithHeadingRow, WithCalculatedFormulas
{
    public function array(array $rows): array
    {
        return $rows;
    }
}
