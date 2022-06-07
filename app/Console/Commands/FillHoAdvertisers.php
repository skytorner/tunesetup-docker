<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Advertiser;
use Illuminate\Support\Facades\DB;


class FillHoAdvertisers extends Command
{
    /**
     * We configure our FR date format
     */
    const DATE_FORMAT = 'Y-m-d\TH:i:s';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hasoffers:advertisers:fill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save all H.O Advertisers in the DB';

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
        $response = ho_call('Advertiser', 'findAll', ["id", "company"], 'desc');

        $response = json_decode($response, true);   

        foreach($response['response']['data']['data'] as $advertiser) {
            $data[] = [ 
                'adv_id'  => $advertiser['Advertiser']['id'],
                'company' => $advertiser['Advertiser']['company'],
            ];
        }
        
        $add = Advertiser::upsert( // Update or insert all in the same time
            $data,
            ['company'],
            ['adv_id']
        );

        DB::delete('DELETE FROM advertisers WHERE id NOT IN( SELECT * FROM ( SELECT MAX(n.id) FROM advertisers n GROUP BY n.adv_id) x)');

        if($add > 0) { // Error management
            $this->info('Congratulation, ' . $add . ' Advertisers was added or updated');
            return true;
        } elseif ($add == 0) {
            $this->info('No new advertiser to add');
            return true;
        } else {
            $this->error('Error : ' . var_dump($add));
            return false;
        }

        return false;
    }
}
