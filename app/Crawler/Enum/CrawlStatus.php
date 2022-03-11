<?php

namespace App\Crawler\Enum;

use BenSampo\Enum\Enum;

final class CrawlStatus extends Enum
{
    public const INIT = 0;
    public const VISITING = 10;
    public const DONE = 200; // default success code
    public const FAIL = 1000; // default error code, or response code for specific error
}

