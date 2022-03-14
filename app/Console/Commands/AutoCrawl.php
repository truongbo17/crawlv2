<?php

namespace App\Console\Commands;

use App\Crawler\Crawler;
use App\Crawler\Queue\MySqlQueue;
use Illuminate\Console\Command;

class AutoCrawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $crawler = new Crawler(new MySqlQueue());
        //Chú ý về đường đẫn tương đối và đường dẫn tuyệt đối
        //Nếu site sử dụng đường đẫn tương đối thì nên thêm một root url đầy đủ => https://www.scirp.org/journal/articles.aspx
        $crawler->run('https://www.scirp.org/journal/articles.aspx');

        print "Success";
    }
}
