<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class CrawlUrl extends Model
{
    use CrudTrait;

    protected $table = 'crawl_urls';
    protected $guarded = ['id'];
}
