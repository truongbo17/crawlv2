<?php

namespace App\Crawler\Storage;

interface StorageInterface
{
    public function put(array $data, int $id);
}
