<?php

namespace App\Crawler\Sites;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;

abstract class SiteAbstract implements SiteInterface
{
    public function __construct(
        public string $root_url
    )
    {
    }

    public function rootUrl(): string
    {
        return $this->root_url;
    }

    public function shouldCrawl(string $url)
    {
        return true;
    }

    public function shouldGetData(string $url)
    {
        return true;
    }

    public function getInfoFromCrawler(DomCrawler $dom_crawler, string $url = '')
    {
        $title = $dom_crawler->filterXPath('//title')->text();

        return compact('title');
    }

    public function __toString(): string
    {
        return $this->root_url;
    }
}
