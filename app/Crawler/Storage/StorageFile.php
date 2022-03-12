<?php

namespace App\Crawler\Storage;

use Excel;
use App\Exports\DataExport;
use Carbon\Carbon;

class StorageFile implements StorageInterface
{
    private static object|null $storageFile = null;
    private string $disk;

    public function __construct(string $disk)
    {
        $this->disk = $disk;
    }

    public static function create(string $disk): object
    {
        if (self::$storageFile !== null) {
            return self::$storageFile;
        }

        self::$storageFile = new self($disk);
        return self::$storageFile;
    }

    public function put(array $data = null)
    {
        $data = [$data];
        $exportData = new DataExport($data);

        $array_key = array_keys($data[0]);
        $exportData->setArrayKey($array_key);

        Excel::store($exportData, $this->createName(), 'data');
    }

    public function createName()
    {
        $time = Carbon::now();
        return 'data-' . $time->format('d-m-y-H:i:s') . '.xlsx';
    }
}
