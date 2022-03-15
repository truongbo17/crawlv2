<?php

namespace App\Crawler\Sites\Resource;

use App\Crawler\CrawlUrl;
use App\Crawler\Sites\SiteAbstract;
use App\Libs\PhpUri;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class Eprints extends SiteAbstract
{
    public function startUrls(): array
    {
        // TODO: Implement startUrls() method.
        return ['http://eprints.lse.ac.uk/view/year/2023.html'];
    }

    public function shouldCrawl(string $url)
    {
        return preg_match("/^http:\/\/eprints\.lse\.ac\.uk\/view\/year/", $url);
    }

    public function shouldGetData(string $url)
    {
        return preg_match("/^http:\/\/eprints\.lse\.ac\.uk\/[0-9]+\//", $url);
    }

    public function getInfoFromCrawler(DomCrawler $dom_crawler, string $url = '')
    {
        $title = $dom_crawler->filter('.ep_tm_pagetitle')->text();
        $content = $dom_crawler->filter('.ep_summary_content_main > p')->last()->text();
        $author = $dom_crawler->filter('.ep_summary_content_main .person_name')->text();
        $downloadLink = ($dom_crawler->filter('a.ep_document_link')->count() > 0) ? $dom_crawler->filter('a.ep_document_link')->attr('href') : "";

        return compact('title', 'content', 'author', 'downloadLink');
    }

    public function configUrlCrawl(string $url, CrawlUrl $crawlUrl)
    {
        if ($url == './' || preg_match("/\#group/", $url)) {
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
