<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Route;
use Carbon\Carbon;
use App\Jobs\sendFailquestionEmail;
use App\Jobs\sendRouteShedual;
use App\User;
use Illuminate\Support\Facades\DB;

class routeShedular extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minute:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seller staff send time table for Route';

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
        $users = Route::where('day',Carbon::now()->add(1, 'day')->format('l'))->groupBy('user_id')-> orderBy('user_id', 'desc')->get();

        foreach ($users as $user => $data) {
            $user_ids = explode(',',$data->user_id);

            foreach($user_ids as $user_id){
                $route_name = '';
                $routedata = DB::table("routes")
                    ->select("routes.*")
                    ->where('day',Carbon::now()->add(1, 'day')->format('l'))
                    ->whereRaw("find_in_set('".$user_id."',routes.user_id)")
                    ->get();

                foreach ($routedata as $i => $routedatas) {
                    $route_name .= ','. $routedatas->name;
                }

                $route_name = ltrim($route_name,",");
                $user = User::where('id',$user_id)->first(['email']);

                if($user->email){
                    sendRouteShedual::dispatch($route_name, $user->email);
                }
            }
        }
    }
}
