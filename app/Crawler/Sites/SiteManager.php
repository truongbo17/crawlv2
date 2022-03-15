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
    //Site phải là URL gốc để tránh trường hợp xảy ra lỗi với đường dẫn tương đối
    protected static array $sites = [
        'https://123job.vn' => Job123::class,
        'https://journals.plos.org' => Journals::class,
        'https://www.scirp.org' => ScripOrg::class,
        'http://eprints.lse.ac.uk' => Eprints::class,
        'https://eprints.whiterose.ac.uk' => EprintsWhiterose::class,
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
