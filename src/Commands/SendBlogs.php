<?php

namespace Capmega\BlogScraping\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Capmega\BlogScraping\Models\ScrapingUrl;
use Capmega\BlogScraping\Models\ScrapingTarget;
use Storage;
use Exception;
use App;

class SendBlogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'capmega:scraper-submit {target_seoname} {driver_name} {--categoryid1=} {--categoryid2=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'capmega:scraper-submit {target_seoname}
                                Submit content to a target
                                {target_seoname : The seoname of the target on database}
                                {driver_name    : Name of the Driver such as AdultSearch}
                                {--categoryid1  : ID of the category on the target site }
                                {--categoryid2  : ID of the category on the target site }';

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
        try{
            /*
             * Process arguments
             */
            $target_seoname = $this->argument('target_seoname');
            $driver_name    = $this->argument('driver_name');

            /*
             * Get the target according to the provided seoname
             */
            $target = ScrapingTarget::where('seoname', $target_seoname)->first();

            if(empty($target)){
                $this->error(sprintf('Provided seoname %s does not match with any target on database', $target_seoname));
                return 0;
            }

            /*
             * Setup HTTP Client with target domain
             */
            $client = new Client([
                'base_uri' => $target->domain,
                'timeout'  => 60.0,
                'verify'   => App::environment('local') ? false : true, // THIS ENABLES OR DISABLES SSL VERIFICATION
            ]);

            /*
             * Get URLS from this target
             */
    // :TODO: Filter through target meanwhile we use driver_name on command line
            $urls = ScrapingUrl::where('driver', 'like', '%'.$driver_name.'%')->get();

            if(empty($urls)){
                $this->error(sprintf('There are 0 URLs to process for this driver %s, maybe is not the correct driver name', $driver_name));
                return 0;
            }

            /*
             * Perpare variables to process
             */
            $this->info(sprintf('Starting to process: %d URLS for TARGET: %s and DRIVER: ', count($urls), $target->domain, $driver_name));
            $count        = 0;
            $progress_bar = $this->getOutput()->createProgressBar(count($urls));

            /*
             * Process each url
             */
            foreach($urls as $key => $url){
                $this->info(sprintf('Processing URL ID: %d ', $url->id));

                /*
                 * Get URL category and data
                 */
                $use_category_id = false;
                $category        = $url->category;
                $data            = $url->data;

                /*
                 * Check category info from DB
                 */
                if(empty($category)){
                    /*
                     * Since there is no data, try to gather it from command line
                     */
                    $category1_id = $this->option('categoryid1');
                    $category2_id = $this->option('categoryid2');

                    if(empty($category1_id) or empty($category2_id)){
                        $this->error(sprintf('No category data was found for URL with ID: %d and no category data was provider through command line', $url->id));
                        return 0;
                    }

                    $use_category_id = true;
                }

                /*
                 * Get URL images
                 */
                $images       = $data->images()->limit(20)->get();
                $array_images = [];
                $this->info(sprintf('Found %d IMAGES for URL ID: %d ', count($images), $url->id));

                /*
                 * Process images
                 */
                foreach($images as $key => $image){
                    $file_route = storage_path('app/scraping/') . $data->id . '/' . $image->id . '.' . $image->extension;

                    try{
                        $file_data      = fopen($file_route, 'r');
                        $array_images[] = [
                            'name'     => 'images[]',
                            'contents' => $file_data
                        ];

                    }catch(\Exception $e){
                        $this->error(sprintf('Error opening image for URL ID: %d with IMAGE ID: %d', $url->id, $image->id));
                    }
                }

                /*
                 * Prepare request array
                 */
                $request_array = [];

                /*
                 * BUILD request array
                 */
                $request_array[] = [
                    'name'     => 'name',
                    'contents' => $url->name
                ];

                if($use_category_id){
                    $request_array[] = [
                        'name'     => 'category1',
                        'contents' => $category1_id
                    ];

                    $request_array[] = [
                        'name'     => 'category2',
                        'contents' => $category2_id
                    ];

                }else{
                    $request_array[] = [
                        'name'     => 'category',
                        'contents' => $category->name
                    ];
                }

                if($data->spin_lvl){
                    $request_array[] = [
                        'name'     => 'spin_lvl',
                        'contents' => $data->spin_lvl
                    ];

                    $request_array[] = [
                        'name'     => 'description',
                        'contents' => $data->spin
                    ];

                }else{
                    $request_array[] = [
                        'name'     => 'description',
                        'contents' => $data->description
                    ];
                }

                /*
                 * Merge images
                 */
                $final_array = array_merge($request_array, $array_images);

                /*
                 * Send request
                 */
                $response = $client->request('POST', '', [
                    'multipart' => $final_array
                ]);
//dd($response);
                dd($response->getBody()->getContents());

                /*
                 * Advance bar and sleep
                 */
                $bar->advance();
                usleep(300000);
            }

            /*
             * Finish
             */
            $bar->finish();
            $this->line(sprintf('.'));

            /*
             * Show final stats
             */
            //$this->info(sprintf('Excluded %d', $excluded));
            //$this->info(sprintf('Failed %d', count($failed)));
            //$this->info(sprintf('Unpaid invoices %d', count($unpaid_invoices)));
            //$this->info(sprintf('Total Invoices Checked %d', count($invoices)));
        }catch(Exception $e){
            dump('----------------ERROR---------------');
            dd($e);
        }
    }
}
