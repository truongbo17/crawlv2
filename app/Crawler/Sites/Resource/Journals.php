<?php

namespace App\Crawler\Sites\Resource;

use App\Crawler\Sites\SiteAbstract;
use App\Libs\PhpUri;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

//https://journals.plos.org/
class Journals extends SiteAbstract
{
    public function startUrls(): array
    {
        // TODO: Implement startUrls() method.
        return ['https://journals.plos.org/plosone/browse?page=2'];
    }

    public function shouldCrawl(string $url)
    {
        return preg_match("/^https:\/\/journals\.plos\.org\/plosone/", $url);
    }

    public function shouldGetData(string $url)
    {
        return preg_match("/^https:\/\/journals\.plos\.org\/plosone\/article\?id=/", $url);
    }

    public function getInfoFromCrawler(DomCrawler $dom_crawler, string $url = '')
    {
        $title = $dom_crawler->filter('title')->text();
        $author = $dom_crawler->filter('div.title-authors ul.author-list a.author-name')->each(function (DomCrawler $node, $i) {
            return $node->text();
        });
        $abstract = $dom_crawler->filter('div.abstract-content p')->text();
        $description = $dom_crawler->filter('div#section1')->text();

        $downloadLink = $dom_crawler->filter('div.dload-pdf a')->attr('href');
        $downloadLink = PhpUri::parse($this->rootUrl())->join($downloadLink);

        return compact('title', 'author', 'abstract', 'description', 'downloadLink');
    }
}
