<?php

namespace Capmega\BlogScraping\Commands;

use Illuminate\Console\Command;
use Capmega\BlogScraping\Models\ScrapingData;

class Spin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sdk:spin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia un spin de los posts';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info("Iniciando Spineo");
        $posts = ScrapingData::whereNull('spin')->limit(108)->get();

        foreach ($posts as $key => $post) {
            if ($post->description) {
                $this->info("Spineando Posts {$post->id}");
                $post->spindata('high');
                sleep(10);
            }
        }
    }
}
