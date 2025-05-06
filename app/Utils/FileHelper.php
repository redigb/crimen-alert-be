<?php

namespace App\Utils;

class FileHelper
{
    public static function generateFilename($file, $pre)
    {
        $currentDateTime = date('YmdHis');
        $extension = $file->getClientOriginalExtension();
        $newFilename = $pre . '_' . $currentDateTime . '.' . $extension;
        return $newFilename;
    }
}
