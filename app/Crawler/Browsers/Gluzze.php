<?php

namespace App\Crawler\Browsers;

use GuzzleHttp\Client;

class Gluzze implements BrowserInterface{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(config('crawler.browsers.guzzle')); //new client with config
    }

    public function getHtml(string $url)
    {
        $response = $this->client->get($url); //Sending request GET to URL Crawl
        $html = $response->getBody()->getContents(); //Get all HTML from body

        return $html;
    }
}
