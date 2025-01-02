<?php

namespace App\Helpers;

class NotaHelper
{
    public static function generatePrintNota($id)
    {
        $date = date('Y/m');
        return sprintf("NOTA/%s/%06d", $date, $id);
    }
}
