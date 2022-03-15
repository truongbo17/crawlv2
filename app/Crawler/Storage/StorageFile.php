<?php

namespace App\Crawler\Storage;

use Illuminate\Support\Facades\Storage;
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

    public function put(array $data)
    {
        $data = [$data];
        Storage::disk($this->disk)->put($this->createName(), json_encode($data));

        return ['disk' => $this->disk, 'path' => $this->createName()];
    }

    public function createName(): string
    {
        $time = Carbon::now();
        return md5('data-' . $time->format('d-m-y-H:i:s')) . '.json';
    }
}
