<?php

namespace App\Crawler\Sites;

use App\Crawler\Exception\SiteNotFoundException;
use App\Crawler\Sites\Resource\Eprints;
use App\Crawler\Sites\Resource\EprintsWhiterose;
use App\Crawler\Sites\Resource\Job123;
use App\Crawler\Sites\Resource\Journals;
use App\Crawler\Sites\Resource\ScripOrg;

final class SiteManager
{
    protected static array $sites = [
        'https://123job.vn' => Job123::class,
        'https://journals.plos.org' => Journals::class,
        'https://www.scirp.org/journal/articles.aspx' => ScripOrg::class,
        'http://eprints.lse.ac.uk/view/year/' => Eprints::class,
        'https://eprints.whiterose.ac.uk/view/year/' => EprintsWhiterose::class,
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
