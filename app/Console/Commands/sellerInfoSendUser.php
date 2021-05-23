<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Route;
use Carbon\Carbon;
use App\Jobs\sendSellerInfotoBuyer;
use App\User;

class sellerInfoSendUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minute:alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seller available alert to user';

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
        // \Log::info('minute:failquestion');
        $this->index();
    }

    public function index()
    {
        $users = User::where('user_type', 'customer')->groupBy('id')->orderBy('id', 'desc')->get(['id', 'area', 'email']);
        foreach ($users as $user => $data) {
            $dats = Route::where('area_id', $data->area)->where('day', Carbon::now()->add(1, 'day')->format('d'))->groupBy('seller_id')->with('findSeller')->get(['seller_id']);
            $seller_name = '';
            foreach ($dats as $i => $routedatas) {
                if (!empty($routedatas->findSeller)) {
                    $seller_name .= ',' . $routedatas->findSeller->name;
                }
            }
            $seller_name = ltrim($seller_name, ",");
            if ($seller_name != '') {
                sendSellerInfotoBuyer::dispatch($seller_name, $data->email);
            }
        }
    }
}
