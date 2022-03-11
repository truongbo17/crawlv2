<?php

namespace App\Crawler\Queue;

use App\Crawler\CrawlUrl;

interface QueueInterface
{
    public function reset(string $site, bool $keep_data = false);

    public function resume(string $site);

    public function push(CrawlUrl $crawlUrl);

    public function exists(string $url): bool;

    public function findByUrl(string $url, string $site = null);

    public function hasPendingUrls(string|array $sites): bool;

    public function firstPendingUrl(string|array $sites): ?CrawlUrl;

    public function delay(CrawlUrl $crawlUrl);

    public function updateData(CrawlUrl $crawlUrl);
}
