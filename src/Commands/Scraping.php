<?php
namespace Sdkconsultoria\BlogScraping\Commands;

use Illuminate\Console\Command;
use Sdkconsultoria\BlogScraping\Models\{ScrapingUrl};

class Scraping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sdk:scraping
        {--driver= : el branch del cual vas a hacer deploy}
        {--resource= : el branch del cual vas a hacer deploy}
        {--url= : el branch del cual vas a hacer deploy}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hace scraping de los sitios configurados';

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
        foreach (config('scraping.drivers') as $driver => $value) {
            $urls = ScrapingUrl::where('driver', $driver)->get();
            if ($urls) {
                $this->info('Buscando en el driver: ' . $driver);
                $search = new $driver();
                $search->getData();
            }
        }
    }
}
