<?php

namespace App\Crawler\Sites\Resource;

use App\Crawler\CrawlUrl;
use App\Crawler\Sites\SiteAbstract;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class EprintsWhiterose extends SiteAbstract
{
    public function startUrls(): array
    {
        // TODO: Implement startUrls() method.
        return ['https://eprints.whiterose.ac.uk/view/year/'];
    }

    public function shouldCrawl(string $url)
    {
        return preg_match("/^https:\/\/eprints\.whiterose\.ac\.uk\/view\/year/", $url);
    }

    public function shouldGetData(string $url)
    {
        return preg_match("/^https:\/\/eprints\.whiterose\.ac\.uk\/[0-9]+\/$/", $url);
    }

    public function getInfoFromCrawler(DomCrawler $dom_crawler, string $url = '')
    {
        $title = $dom_crawler->filter('title')->text();
        $content = $dom_crawler->filter('p.abstract')->last()->text();
        $author = $dom_crawler->filter('ul.creators li span.person_name')->each(function (DomCrawler $node) {
            return $node->text();
        });

        $downloadLink = ($dom_crawler->filter('p.filename > a')->count() > 0) ? $dom_crawler->filter('p.filename > a    ')->attr('href') : "";

        return compact('title', 'content', 'author', 'downloadLink');
    }

    public function configUrlCrawl(string $url, CrawlUrl $crawlUrl)
    {
        if ($url == './') {
            return false;
        }

        if (substr($url, 0, 1) == '/') {
            $url = $this->rootUrl() . $url;
        } else if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            $array = explode('/', $crawlUrl->url);
            array_pop($array);
            $url = implode('/', $array) . '/' . $url;
        }

        return $url;
    }
}
