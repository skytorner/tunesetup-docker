<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign as Campaign;
use Illuminate\Support\Facades\DB;

class FillHoCampaigns extends Command
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
    protected $signature = 'hasoffers:campaign:fill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save all H.O Campaign in the DB';

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
        $response = ho_call('Offer', 'findAll', ["id", "name", "offer_url", "preview_url", "status", "redirect_offer_id", "has_goals_enabled", "description", "email_instructions"], 'asc');

        $response = json_decode($response, true);   

        foreach($response['response']['data']['data'] as $campaign) {

            if(is_null($campaign['Offer']['redirect_offer_id'])) { // Prevent error
                $campaign['Offer']['redirect_offer_id'] = 0;
            }

            $data[] = [ 
                'campaign_id'         => $campaign['Offer']['id'],
                'name'                => $campaign['Offer']['name'],
                'default_offer_url'   => $campaign['Offer']['offer_url'],
                'default_preview_url' => $campaign['Offer']['preview_url'],
                'status'              => $campaign['Offer']['status'],
                'redirect_offer_id'   => $campaign['Offer']['redirect_offer_id'],
                'has_goals_enabled'   => $campaign['Offer']['has_goals_enabled'],
                'description'         => $campaign['Offer']['description'],
                'email_instructions'  => $campaign['Offer']['email_instructions']
            ];
        }
        
        $add = Campaign::upsert( // Update or insert all in the same time
            $data,
            ['name', 'default_offer_url', 'preview_url', 'status', 'redirect_offer_id', 'has_goals_enabled', 'description', 'email_instructions'],
            ['campaign_id']
        );

        DB::delete('DELETE FROM campaigns WHERE id NOT IN( SELECT * FROM ( SELECT MAX(n.id) FROM campaigns n GROUP BY n.campaign_id) x)');

        if($add > 0) { // Error management
            $this->info('Congratulation, ' . $add . ' campaigns was added or updated');
            return true;
        } elseif ($add == 0) {
            $this->info('No new campaign to add');
            return true;
        } else {
            $this->error('Error : ' . var_dump($add));
            return false;
        }

        return false;
    }
}
