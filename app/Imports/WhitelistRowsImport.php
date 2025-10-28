<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WhitelistRowsImport implements ToArray, WithHeadingRow
{
    public function array(array $array)
    {
        return $array; // handled in controller via Excel::toArray
    }
}


