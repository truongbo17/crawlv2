<?php

namespace App\Crawler\Queue;

use App\Crawler\CrawlUrl;
use App\Crawler\Enum\CrawlStatus;
use DB;
use Illuminate\Support\Arr;

class MySqlQueue implements QueueInterface
{

    protected string $table = 'crawl_urls';

    public function reset(string $site, bool $keep_data = false)
    {
    }

    public function resume(string $site)
    {
    }

    public function push(CrawlUrl $crawlUrl)
    {
        if (self::exists($crawlUrl->url)) {
            return false;
        }

        $data = $crawlUrl->toArray();
        $data['data'] = json_encode($data['data']);

        $inserted = DB::table($this->table)->insertGetId($data);

        if ($inserted) {
            $crawlUrl->setId($inserted);
        }

        return $crawlUrl;
    }

    public function exists(string $url, string $site = null): bool
    {
        $db = DB::table($this->table);
        if ($site !== null) {
            $db = $db->where('site', $site);
        }
        return $db->where('url_hash', CrawlUrl::hashUrl($url))->exists();
    }

    public function findByUrl(string $url, string $site = null)
    {
    }

    public function hasPendingUrls(string|array $sites): bool
    {
        $sites = Arr::wrap($sites);

        return DB::table($this->table)
            ->whereIn('site', $sites)
            ->where('status', CrawlStatus::INIT)
            ->exists();
    }

    public function firstPendingUrl(string|array $sites): ?CrawlUrl
    {
        $sites = Arr::wrap($sites);
        $db = null;

        try {
            $db = DB::transaction(function () use ($sites) {
                $first = DB::table($this->table)
                    ->whereIn('site', $sites)
                    ->where('status', CrawlStatus::INIT)
                    ->orderBy('visited')
                    ->sharedLock()
                    ->first();
                if ($first) {
                    $crawlUrl = CrawlUrl::fromObject($first); //convert object first to instance CrawlUrl
                    self::changeProcessStatus($crawlUrl, CrawlStatus::VISITING); //change visit status

                    return $crawlUrl;
                } else {
                    return null;
                }
            });
        } catch (QueryException $exception) {
        }
        return $db;
    }

    public function changeProcessStatus(CrawlUrl $crawlUrl, $status = null)
    {
        $data = ['status' => $status ?? $crawlUrl->getStatus()];

        if ($data['status'] == CrawlStatus::VISITING) {
            $data['visited'] = DB::raw('visited + 1'); //visited = visited + 1
        }

        if ($data['status'] == CrawlStatus::DONE) {
            $data['data_status'] = $crawlUrl->getDataStatus();
            $data['data'] = json_encode($crawlUrl->getData());
        }

        DB::table($this->table)
            ->where('id', $crawlUrl->getId())
            ->update($data);
    }

    public function delay(CrawlUrl $crawlUrl)
    {
    }

    public function updateData(CrawlUrl $crawlUrl)
    {
    }
}
