<?php

namespace App\Crawler\Browsers;

interface BrowserInterface{
    /**
     * Get Html
     * @param string $url
     * @return mixed
     */
    public function getHtml(string $url);
}
