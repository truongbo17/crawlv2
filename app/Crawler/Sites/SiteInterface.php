<?php

namespace App\Crawler\Sites;

use Symfony\Component\DomCrawler\Crawler;

interface SiteInterface
{
    public function rootUrl(): string;

    //trả về mảng các url sẽ được sử dụng trong lần chạy đầu tiên
    public function startUrls(): array;

    //định nghĩa như nào là 1 url cần phi vào
    public function shouldCrawl(string $url);

    //định nghĩa như nào là 1 url cần lấy data
    public function shouldGetData(string $url);

    //hàm này định nghĩa viêc lấy data như thế nào? (sử dụng DomCrawler)
    public function getInfoFromCrawler(Crawler $dom_crawler, string $url = '');

    public function __toString(): string;
}
