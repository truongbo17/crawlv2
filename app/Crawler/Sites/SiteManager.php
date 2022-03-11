<?php

namespace App\Crawler\Sites;

use App\Crawler\Exception\SiteNotFoundException;
use App\Crawler\Sites\Resource\Job123;
use App\Crawler\Sites\Resource\Journals;

final class SiteManager
{
    protected static array $sites = [
        'https://123job.vn' => Job123::class,
        'https://journals.plos.org/plosone/browse?page=2' => Journals::class,
    ];

    /**
     * @throw SiteNotFoundException
     * */
    public static function getSiteConfig(string $name)
    {
        if (empty(self::$sites[$name])) {
            throw new SiteNotFoundException('Can not find match with name : ' . $name);
        }

        return new self::$sites[$name]($name);
    }
}
