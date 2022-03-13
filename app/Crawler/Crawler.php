<?php

namespace App\Crawler;

use App\Crawler\Browsers\Gluzze;
use App\Crawler\Enum\CrawlStatus;
use App\Crawler\Queue\QueueInterface;
use App\Crawler\Sites\SiteInterface;
use App\Crawler\Sites\SiteManager;
use App\Libs\PhpUri;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Vuh\CliEcho\CliEcho;

class Crawler
{
    public function __construct(protected QueueInterface $queue)
    {
    }

    /*
     * run crawl
     * @param string $site
     *
     * */
    public function run(string $site)
    {
        $site = SiteManager::getSiteConfig($site);
        $this->init($site);

        while ($this->queue->hasPendingUrls($site)) {
            //get first
            $crawl_url = $this->queue->firstPendingUrl($site);
            if (empty($crawl_url)) continue;

            //GET HTML
            try {
                $mytime = Carbon::now();
                CliEcho::infonl("Goto [$crawl_url->url] - Time : " . $mytime->toDateTimeString());
                $html = (new Gluzze())->getHtml($crawl_url->url); //get html using gluzze
            } catch (GuzzleException $exception) {
                CliEcho::errornl($exception->getMessage());
                if (in_array($exception->getCode(), config('crawler.should_retry_status_codes'))) {
                    $crawl_url->setStatus(CrawlStatus::INIT); //status 0 => return crawl
                } else {
                    $crawl_url->setStatus(CrawlStatus::FAIL);
                }
            }

            //START CRAWL
            try {
                //Using Sysfony/Crawler
                $dom_crawler = new DomCrawler($html);

                if ($site->shouldGetData($crawl_url->url)) {
                    $data = $site->getInfoFromCrawler($dom_crawler, $crawl_url->url); //get data

                    $data_file = $crawl_url->saveDataInFile($data); //save data in file storage

                    $crawl_url->setDataFile($data_file); //set data
                    dump($crawl_url->getDataFile());
                }
                $crawl_url->setStatus(CrawlStatus::DONE); //set status for instance
                $this->queue->changeProcessStatus($crawl_url, $crawl_url->getStatus()); //set status in database

                $urls = $this->getAllUrl($site, $dom_crawler); //get all url of dom url current

                foreach ($urls as $url) {
                    if (!$site->shouldCrawl($url)) {
                        continue;
                    }
                    $crawl_url = CrawlUrl::create($site, new Uri($url), $crawl_url->url); //new instance url
                    $this->queue->push($crawl_url); //push all url to database => pending url crawl
                }
            } catch (Exception $exception) {
                CliEcho::errornl('Fail : ' . $exception->getMessage());
                $crawl_url->setStatus(CrawlStatus::FAIL);
            }
        }
    }

    public function init(SiteInterface $site)
    {
        foreach ($site->startUrls() as $url) {
            $crawl_url = CrawlUrl::create($site, new Uri($url));
            if ($this->queue->push($crawl_url)) {
                //push success
                CliEcho::successnl("[$site] Added $crawl_url->url");
            }
        }
    }

    public function getAllUrl(SiteInterface $site, DomCrawler $dom_crawler)
    {
        $urls_selector = $dom_crawler->filter('a');
        $urls = [];
        foreach ($urls_selector as $item) {
            $item = $item->getAttribute('href');
            $item = PhpUri::parse($site)->join($item); //return full url include domain
            $urls[] = $item;
        }

        return array_unique($urls);
    }
}
