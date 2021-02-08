<?php

namespace App\Jobs;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WithdrawMoney implements ShouldQueue
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
            $deposits = $this->customer->deposits()->where('type', 'deposit')->sum('amount');
            $withdraws = $this->customer->deposits()->where('type', 'withdraw')->sum('amount');

            $available_balance = $deposits - $withdraws;
            if ($this->sum > $available_balance)
            {
                return false;
            }
            $newDeposit = new Deposit();
            $newDeposit->user_id = $this->customer->id;
            $newDeposit->amount = $this->sum;
            $newDeposit->type = 'withdraw';
            $newDeposit->save();
                return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
