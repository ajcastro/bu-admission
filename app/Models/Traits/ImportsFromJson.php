<?php

namespace App\Models\Traits;

trait ImportsFromJson
{
    public static function importFromJson($filepath)
    {
        $rows = json_decode(file_get_contents($filepath), true);

        foreach ($rows as $row) {
            static::create($row);
        }
    }
}
