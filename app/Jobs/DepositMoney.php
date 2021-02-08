<?php

namespace App\Jobs;

use App\Models\Bonus;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DepositMoney implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $customer;
    protected $sum;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $customer, $sum)
    {
        $this->customer = $customer;
        $this->sum = $sum;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $deposits = $this->customer->deposits()->where('type', 'deposit')->count();
            if ($deposits != 0) {
                $deposits++;
                if (($deposits % 3) === 0) {
                    $this->addBonus($this->customer, $this->sum);
                }
            }
            $newDeposit = new Deposit();
            $newDeposit->user_id = $this->customer->id;
            $newDeposit->amount = $this->sum;
            $newDeposit->type = 'deposit';
            $newDeposit->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function addBonus($customer, $sum)
    {
        $bonus = $sum * $customer->bonus / 100;
        $newBonus = new Bonus();
        $newBonus->user_id = $customer->id;
        $newBonus->amount = $bonus;
        $newBonus->save();
        return true;
    }
}
