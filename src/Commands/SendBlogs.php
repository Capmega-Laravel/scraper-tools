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
    protected $signature = 'capmega:scraper-submit {target_seoname} {driver_name} {--categoryid1=} {--categoryid2=} {--categoryid3=} {--check_city=} {--keyvalues}';

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
                                {--categoryid2  : ID of the category on the target site }
                                {--categoryid3  : ID of the category on the target site }
                                {--check_city   : Seoname of the city in the urls of scraped site}
                                {--check_city   : Just a flag to send keyvalues from data table}';

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
            $category1_id   = $this->option('categoryid1');
            $category2_id   = $this->option('categoryid2');
            $category3_id   = $this->option('categoryid3');
            $check_city     = $this->option('check_city');
            $keyvalues_opt  = $this->option('keyvalues');

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
            $urls = ScrapingUrl::where('driver', 'like', '%'.$driver_name.'%')
                               ->where('status', 20); // NOT PROCESSED URLS

            if($check_city){
                $city    = '';
                $state   = '';
                $country = '';
                $zipcode = '';

                switch($check_city){
                    case 'las-vegas':
                        $city    = 'Las Vegas';
                        $state   = 'Nevada';
                        $country = 'United States';
                        $zipcode = '88901';
                        break;

                    case 'manhattan':
                        $city    = 'Manhattan';
                        $state   = 'New York';
                        $country = 'United States';
                        $zipcode = '10010';
                        break;

                    case 'miami':
                        $city    = 'Miami';
                        $state   = 'Florida';
                        $country = 'United States';
                        $zipcode = '33101';
                        break;

                    case 'los-angeles':
                        $city    = 'Los Angeles';
                        $state   = 'California';
                        $country = 'United States';
                        $zipcode = '90001';
                        break;

                    default:
                        $this->error(sprintf('Unsupported city %s', $check_city));
                        return 0;
                }

                $urls = $urls->where('url', 'like', '%'.$check_city.'%');
            }

            $urls = $urls->take(100)->get();

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
                $keyValues       = $data->keyValues;

                if($category1_id and $category2_id){
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

                if($keyvalues_opt){
                    /*
                     * Get the phone and clean it
                     */
                    $name = $keyValues->where('name', 'name')->first();

                    $request_array[] = [
                        'name'     => 'name',
                        'contents' => $name->value
                    ];

                }else{
                   $request_array[] = [
                        'name'     => 'name',
                        'contents' => $data->name
                    ];
                }

                if($use_category_id){
                    $request_array[] = [
                        'name'     => 'category1',
                        'contents' => $category1_id
                    ];

                    $request_array[] = [
                        'name'     => 'category2',
                        'contents' => $category2_id
                    ];

                    if(!empty($category3_id)){
                        $request_array[] = [
                            'name'     => 'category3',
                            'contents' => $category3_id
                        ];
                    }

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
                 * Check City
                 */
                if($check_city){
                    $request_array[] = [
                        'name'     => 'city',
                        'contents' => $city
                    ];

                    $request_array[] = [
                        'name'     => 'state',
                        'contents' => $state
                    ];

                    $request_array[] = [
                        'name'     => 'country',
                        'contents' => $country
                    ];

                    $request_array[] = [
                        'name'     => 'zipcode',
                        'contents' => $zipcode
                    ];
                }

                /*
                 * Check keyvalues
                 */
                if($keyvalues_opt){
                    /*
                     * Get the phone and clean it
                     */
                    $phone = $keyValues->where('name', 'phone')->first();
                    $phone =  str_replace('-', '', $phone->seovalue);

                    if(!empty($phone)){
                        $request_array[] = [
                            'name'     => 'phone',
                            'contents' => $phone
                        ];

                    }else{
                        /*
                         * This listing should not be created is unuseful without phone number
                         */
                        $this->error(sprintf('Skipping URL ID: %d due to no phone number included', $url->id));
                        continue;
                    }

                    /*
                     * Add price
                     */
                    $request_array[] = [
                        'name'     => 'payment_option',
                        'contents' => 'hour'
                    ];

                    $request_array[] = [
                        'name'     => 'currency',
                        'contents' => 'usd'
                    ];

                    $price = rand(185, 210);

                    $request_array[] = [
                        'name'     => 'price',
                        'contents' => $price
                    ];

                    $request_array[] = [
                        'name'     => 'username',
                        'contents' => 'scigotit'
                    ];

                    $request_array[] = [
                        'name'     => 'password',
                        'contents' => 'test123$$55'
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

                /*
                 * Parse response
                 */
                $response_contents = $response->getBody()->getContents();
                $response_data     = json_decode($response_contents, true);
dd($response_contents);
                /*
                 * Get Code
                 */
                $result_code = $response_data['result'];

                /*
                 * Check Code
                 */
                switch($result_code){
                    case 'OK':
                        $this->info(sprintf('URL ID %d sent correctly', $url->id));

                        /*
                         * Update URL status to processed
                         */
                        $url->status = 30;
                        $url->save();
                        break;

                    default:
                        $this->error(sprintf('Error while sending URL ID %d', $url->id));
dump($final_array);
dd($response_contents);
                        break;
                }

// :DEBUG: This is debug code
//dump($response_data);
//dump('------------');
//dump(json_encode($response_data));
//dump('------------');
//dd(json_decode($response_data,true));
////dd($response);
////dd($response->getBody()->getContents());

                /*
                 * Advance bar and sleep
                 */
                $progress_bar->advance();
                usleep(300000);
            }

            /*
             * Finish
             */
            $progress_bar->finish();
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
