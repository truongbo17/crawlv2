<?php

namespace App\Crawler\Browsers;

interface BrowserInterface{
    /**
     * get html
     * @param string $url
     *
     * return mixed
     * */
    public function getHtml(string $url);
}
