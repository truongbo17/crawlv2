<?php

namespace App\Crawler\Sites\Resource;

use App\Crawler\Sites\SiteAbstract;
use App\Libs\PhpUri;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class ScripOrg extends SiteAbstract
{
    private array $authorName = [];

    public function startUrls(): array
    {
        // TODO: Implement startUrls() method.
        return ['https://www.scirp.org/journal/articles.aspx'];
    }

    public function shouldCrawl(string $url)
    {
        return preg_match("/^https:\/\/www\.scirp\.org\/journal/", $url);
    }

    public function shouldGetData(string $url)
    {
        return preg_match("/^https:\/\/www\.scirp\.org\/journal\/paperinformation.aspx\?paperid=/", $url);
    }

    public function getInfoFromCrawler(DomCrawler $dom_crawler, string $url = '')
    {
        $title = $dom_crawler->filter('title')->text();
        $abstract = $dom_crawler->filter('div.articles_main')->filter('div')->eq(3)->filter('p')->eq(1)->text();

        //download link
        $downloadLink = $dom_crawler->filter('div.articles_main')->filter('div')->eq(2)->filter('a')->eq(1)->attr('href');
        $downloadLink = $this->downloadLink($downloadLink);

        //keyword
        $keyword = $dom_crawler->filter('div#JournalInfor_div_showkeywords')->text();
        $keyword = $this->keyword($keyword);

        //author
        $dom_crawler->filter('div.articles_main')->filter('div')->eq(2)->each(function (DomCrawler $node, $i) {

            preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $node->html(), $result);

            $linkAuthors = []; //link author
            foreach ($result['href'] as $value) {
                if (!preg_match("/authors/", $value)) continue;
                array_push($linkAuthors, $value);
            }

            $this->handleLinkAuthor($linkAuthors);
        });
        $authorName = $this->authorName;

        return compact('title', 'abstract', 'downloadLink', 'keyword', 'authorName');
    }

    public function downloadLink(string $downloadLink): string
    {
        return PhpUri::parse($this->rootUrl())->join($downloadLink);
    }

    public function keyword(string $keyword)
    {
        return explode(",", $keyword);
    }

    public function handleLinkAuthor(array $linkAuthors)
    {
        $authorName = [];

        foreach ($linkAuthors as $linkAuthor) {
            $string = substr($linkAuthor, 25, -35);

            $array = array_filter(explode("+", $string));
            $name = implode(" ", $array);

            $authorName[] = $name;
        }
        $this->authorName = $authorName;
    }
}
