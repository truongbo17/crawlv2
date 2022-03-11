## CrawlerV2

* Build according to <a href="https://gitlab.com/giahao9899/any_crawl_2021">giahao9899/any_crawl_2021</a>
* Description :  
  * Crawl data from website , same as dredging algorithm all url from current url .
  * Stop when no url match then condition
  
## Use

* php artisan crawl:auto


## Config
* Change url in App\Console\Commands\AutoCrawl.php
* Add config site in App\Crawler\Sites\Resource
  * startUrls() trả về mảng các url sẽ được sử dụng trong lần chạy đầu tiên
  * shouldCrawl() định nghĩa như nào là 1 url cần phi vào
  * shouldGetData() định nghĩa như nào là 1 url cần lấy data
  * getInfoFromCrawler() hàm này định nghĩa viêc lấy data như thế nào? (sử dụng DomCrawler)
* Add site match in App\Crawler\Sites\SiteManager.php
