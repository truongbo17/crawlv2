<?php

namespace App\Crawler\Storage;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StorageFile implements StorageInterface
{
    private static object|null $storageFile = null;
    private static string $disk;
    private static string|null $fileName = null;

    public function __construct(string $disk)
    {
        self::$disk = $disk;
    }

    public static function create(string $disk): object
    {
        if (self::$storageFile !== null) {
            return self::$storageFile;
        }

        self::$storageFile = new self($disk);
        return self::$storageFile;
    }

    public function put(array $data)
    {
        if (self::$fileName === null) {
            $this->init($data);
        } else {
            $this->append($data);
        }

        return ['disk' => self::$disk, 'path' => self::$fileName];
    }

    public function init($data)
    {
        self::$fileName = $this->createName();
        $data = [$data];
        Storage::disk(self::$disk)->put(self::$fileName, json_encode($data));
    }

    public function createName(): string
    {
        $time = Carbon::now();
        return 'data-' . $time->format('d-m-y-H:i:s') . '.json';
    }

    public function append($data)
    {
        $string = Storage::disk(self::$disk)->get(self::$fileName);
        $allData = json_decode($string, true);

        array_push($allData, $data);

        Storage::disk(self::$disk)->put(self::$fileName, json_encode($allData));
    }
}
