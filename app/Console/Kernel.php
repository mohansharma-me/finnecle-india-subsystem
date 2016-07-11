<?php

namespace App\Console;

use App\Declaration;
use App\Donation;
use App\Draw;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Auto Drawer
        $schedule->call(function () {
            ob_start();

            $now = \Carbon\Carbon::now();
            $draw_time = $now->format("H:i");
            $date = $now->format('Y-m-d');
            echo "Draw Time : ".$draw_time."\n";
            echo "Date : ".$date."\n";
            $draws = Draw::where('draw_time', $draw_time)->get();

            foreach($draws as $draw) {

                $declaration = Declaration::where('draw_id', $draw->id)->whereDate('created_at', '=', $date)->first();
                if($declaration) { //draw already selected
                    echo $draw->draw_time.' of channel '.$draw->channel_id.' is ignored\n';
                } else { // not selected yet...
                    echo $draw->draw_time.' of channel '.$draw->channel_id.' is in process'."\n";
                    // get total donation of draw
                    $totalDonation = Donation::totalDonation($draw->id);
                    echo "Total donation: $totalDonation\n";
                    // get automatic ratio
                    $automaticRatio = $draw->automatic_ratio;
                    echo "Automatic Ratio: $automaticRatio\n";
                    $pivotAmount = $totalDonation * $automaticRatio / 100;
                    echo "Pivot Amount: $pivotAmount\n";

                    $selected_ngo_id = 0;

                    // Searching ngo wise donations and transaction/user count
                    // $subQuery = Transaction::select(
                    //     DB::raw("sum(donations.amount) as total_donations"),
                    //     DB::raw("count(transactions.id) as total_transactions"),
                    //     'donations.ngo_id'
                    // )
                    // ->whereDate('transactions.created_at','=',$date)
                    // ->where('transactions.declaration_id',0)
                    // ->where('transactions.draw_id', $draw->id)
                    // ->join('donations', 'donations.transaction_id', '=', 'transactions.id')
                    // ->groupBy('donations.ngo_id');

                    // $query = DB::table(DB::raw("({$subQuery->toSql()}) as ngo_donations"))->mergeBindings($subQuery->getQuery());
                    // $query->select("ngo_donations.*", "ngos.relative_ngo");
                    // $query->join('ngos', 'ngos.id', '=','ngo_donations.ngo_id');

                    // $query->where('total_donations','<=',$pivotAmount);
                    // $query->orderBy('ngo_donations.total_donations', 'desc');
                    // $query->orderBy('ngo_donations.total_transactions', 'desc');
                    $foundNgos = DB::select(
                        DB::raw("select * from (select a.*, (a.total_donations+ifnull(b.total_donations,0)) as final_donations,(a.total_transactions+ifnull(b.total_transactions,0)) as final_transactions  from (select ngos.relative_ngo, a.* from ngos, (select sum(donations.amount) as total_donations, count(transactions.id) as total_transactions, `donations`.`ngo_id` from `transactions` inner join `donations` on `donations`.`transaction_id` = `transactions`.`id` where date(`transactions`.`created_at`) = :createdAt1 and `transactions`.`declaration_id` = 0 and `transactions`.`draw_id` = :drawId1 group by `donations`.`ngo_id`)a where a.ngo_id = ngos.id)a left join (select ngos.relative_ngo, a.* from ngos, (select sum(donations.amount) as total_donations, count(transactions.id) as total_transactions, `donations`.`ngo_id` from `transactions` inner join `donations` on `donations`.`transaction_id` = `transactions`.`id` where date(`transactions`.`created_at`) = :createdAt and `transactions`.`declaration_id` = 0 and `transactions`.`draw_id` = :drawId group by `donations`.`ngo_id`)a where a.ngo_id = ngos.id)b on a.relative_ngo = b.ngo_id)a where final_donations <= :pivotAmount order by final_donations desc, final_transactions desc"), 
                        ['createdAt'=>$date, 'drawId'=> $draw->id,'createdAt1'=>$date, 'drawId1'=> $draw->id,'pivotAmount'=>$pivotAmount]
                    );

                    //$query->where('final_donations','<=',$pivotAmount);
                    //$query->orderBy('final_donations', 'desc');
                    //$query->orderBy('final_transactions', 'desc');

                    echo "Found NGOs:\n";
                    print_r($foundNgos);

                    if(count($foundNgos) > 0) { // found the best... or zero amount
                        echo "Found the best choice.\n";
                        foreach($foundNgos as $foundNgo) {
                            $selected_ngo_id = $foundNgo->ngo_id;
                            echo "Selected NGO ID : $selected_ngo_id\n";
                            break;
                        }
                    } else { // find closet to pivot on upper...
                        echo "Didn't found the best choice, trying to looking up-side\n";

                        $foundNgos = DB::select(
                            DB::raw("select * from (select a.*, (a.total_donations+ifnull(b.total_donations,0)) as final_donations,(a.total_transactions+ifnull(b.total_transactions,0)) as final_transactions  from (select ngos.relative_ngo, a.* from ngos, (select sum(donations.amount) as total_donations, count(transactions.id) as total_transactions, `donations`.`ngo_id` from `transactions` inner join `donations` on `donations`.`transaction_id` = `transactions`.`id` where date(`transactions`.`created_at`) = :createdAt1 and `transactions`.`declaration_id` = 0 and `transactions`.`draw_id` = :drawId1 group by `donations`.`ngo_id`)a where a.ngo_id = ngos.id and ngos.ngo_group_id > 10)a left join (select ngos.relative_ngo, a.* from ngos, (select sum(donations.amount) as total_donations, count(transactions.id) as total_transactions, `donations`.`ngo_id` from `transactions` inner join `donations` on `donations`.`transaction_id` = `transactions`.`id` where date(`transactions`.`created_at`) = :createdAt and `transactions`.`declaration_id` = 0 and `transactions`.`draw_id` = :drawId group by `donations`.`ngo_id`)a where a.ngo_id = ngos.id)b on a.relative_ngo = b.ngo_id)a where final_donations >= :pivotAmount order by final_donations, final_transactions desc"), 
                            ['createdAt'=>$date, 'drawId'=> $draw->id,'createdAt1'=>$date, 'drawId1'=> $draw->id,'pivotAmount'=>$pivotAmount]
                        );

                        if(count($foundNgos) > 0) {
                            echo "Found UpSide Choice\n";
                            foreach($foundNgos as $foundNgo) {
                                $selected_ngo_id = $foundNgo->ngo_id;
                                echo "Selected NGO ID : $selected_ngo_id\n";
                                break;
                            }
                        } else { // Didn't found anything 
                            echo "Didn't found anything\n";
                            echo "Continued with Default NGO1\n";
                            $selected_ngo_id = 1;
                        }
                    }

                    // Validate selected ngo id...
                    if($selected_ngo_id == 0) {
                        echo "Autodraw failed.\n";
                    } else {
                        $declaration = new Declaration();
                        $declaration->draw_id = $draw->id;
                        $declaration->ngo_id = $selected_ngo_id;
                        $declaration->status = 'processing';
                        $declaration->manually = 1;
                        if($declaration->save()) {
                            
                            $draw->transactions()
                                ->whereDate('created_at', '=', Carbon::parse($declaration->created_at)->toDateString())
                                ->update(array('declaration_id'=>$declaration->id));

                            $declaration->status = 'completed';
                            $declaration->update();

                            echo "AutoDraw : completed";

                        } else {
                            echo "AutoDraw : failed at last.";
                        }
                    }
                }

            }

            // store as log...
            $data = ob_get_clean();
            Storage::put('autodraw-logs/'.$now->format('Y/m/d/H/i').'.log', $data);

        })->everyMinute();
    }
}
