<?php

namespace App\Crawler;

use App\Crawler\Enum\CrawlStatus;
use App\Crawler\Enum\DataStatus;
use App\Crawler\Sites\SiteInterface;
use App\Crawler\Sites\SiteManager;
use App\Crawler\Storage\StorageFile;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

class CrawlUrl
{
    protected ?int $id = null;

    protected int $visited = 0;

    protected int $data_status = DataStatus::INIT;

    protected array|null $data_file = [];

    protected int $status = CrawlStatus::INIT;

    protected function __construct(protected SiteInterface $site, public UriInterface $url, public ?UriInterface $foundOnUrl = null)
    {
    }

    public static function fromObject(object $object)
    {
        $site = (new SiteManager())->getSiteConfig($object->site);
        $url = new Uri($object->url);
        $foundOnUrl = $object->parent ? new Uri($object->parent) : null;

        $instance = self::create($site, $url, $foundOnUrl, $object->id);

        $instance->status = $object->status;
        $instance->data_status = $object->data_status;
        $instance->data_file = is_array($object->data_file) ? $object->data_file : json_decode($object->data_file);
        $instance->visited = $object->visited;

        return $instance;
    }

    public static function create(SiteInterface $site, UriInterface $url, ?UriInterface $foundOnUrl = null, $id = null)
    {
        $static = new static($site, $url, $foundOnUrl);

        if ($id !== null) {
            $static->setId($id);
        }

        return $static;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getDataStatus()
    {
        return $this->data_status;
    }

    public function getDataFile()
    {
        return $this->data_file;
    }

    public function setDataFile(array $data_file)
    {
        $this->data_file = $data_file;
        if (!empty($data_file)) {
            $this->data_status = DataStatus::HAS_DATA;
        } else {
            $this->data_status = DataStatus::NO_DATA;
        }
    }

    public function toArray()
    {
        $data = ['url' => $this->url, 'site' => $this->site, 'url_hash' => self::hashUrl($this->url), 'parent' => $this->foundOnUrl, 'status' => $this->status, 'data_status' => $this->data_status, 'data_file' => $this->data_file, 'visited' => $this->visited];

        if ($this->id) {
            $data['id'] = $this->id;
        }

        return $data;
    }

    public static function hashUrl(string $url, string $algo = 'sha256'): string
    {
        $url = preg_replace("/^(https?)?:\/\//", "", $url);
        return hash($algo, $url);
    }

    public function saveDataInFile(array $data)
    {
        $storageFile = StorageFile::create(config('crawler.disk'));
        return $storageFile->put($data);
    }
}
