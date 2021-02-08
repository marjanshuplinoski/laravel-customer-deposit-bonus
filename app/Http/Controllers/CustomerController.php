<?php

namespace App\Http\Controllers;

use App\Jobs\DepositMoney;
use App\Jobs\WithdrawMoney;
use App\Models\Deposit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public function editCustomer(Request $request, $id)
    {
        try {
            $customer = User::findOrFail($id);

            $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'gender' => 'required|string|max:1',
                'country' => 'required|string|max:5'
            ]);

            $customer->first_name = $request->first_name;
            $customer->last_name = $request->last_name;
            $customer->gender = $request->gender;
            $customer->country = $request->country;

            return response()->json([
                "message" => "User with id of {$customer->id} has been updated",
                'Customer: ' => $customer,
                "status" => 200
            ]);
        } catch (\Exception $e) {

            return response()->json([
                "message" => "Problems occured updating the user with the id of {$customer->id}.",
                "status" => 500
            ]);
        }
    }

    public function deposit($id, $sum)
    {
        try {
            if (!is_numeric($sum) && $sum > 0) {
                return response()->json([
                    "message" => "Quantity must be numeric and above 0.",
                    "status" => 403
                ]);
            }

            $customer = User::findOrFail($id);
            $job = new DepositMoney($customer, $sum);
            $test = $this->dispatch($job);

            return response()->json([
                "message" => "{$customer->first_name} {$customer->last_name} has added {$sum}.",
                "status" => 200
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 403]);
        }
    }

    public function withdraw($id, $sum)
    {
        try {
            if (!is_numeric($sum) && $sum > 0) {
                return response()->json([
                    "message" => "Quantity must be numeric and above 0.",
                    "status" => 403
                ]);
            }

            $customer = User::findOrFail($id);
            $job = new WithdrawMoney($customer, $sum);
            $test = $this->dispatch($job);
            return response()->json([
                "message" => "{$customer->first_name} {$customer->last_name} has withdraw {$sum}.",
                "status" => 200
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 403]);
        }
    }

    public function report()
    {

        $deposits = Deposit::whereDate('created_at', '>', Carbon::today()->subDays(7))->get();
        $deposits = $this->organizeDeposits($deposits);
        return view('report', compact('deposits'));

    }

    private function organizeDeposits(Collection $deposits)
    {

        $deposits = $deposits->groupBy(function ($deposit) {
            return $deposit->created_at->format('Y-m-d');
        });

        foreach ($deposits as $country => $transfers) {
            $transfers = $transfers->groupBy(function ($deposit) {

                return $deposit->customer->country;
            });

            $transfers = $this->organizeTrasfers($transfers);

            $deposits[$country] = $transfers;
        }
        return $deposits;
    }

    private function organizeTrasfers(Collection $transfersCollection)
    {
        $usefulData = [];
        foreach ($transfersCollection as $date => $transfers) {
            $usefulData['unique_customers'] = $transfers->groupBy('user_id')->count();
            $deposits = $transfers->where('type', 'deposit');
            $withdraws = $transfers->where('type', 'withdraw');

            $usefulData['no_of_deposits'] = $deposits->count();
            $usefulData['total_deposit_amount'] = $this->sumMoney($deposits);
            $usefulData['no_of_withdraws'] = $withdraws->count();
            $usefulData['total_withdraws_amount'] = -$this->sumMoney($withdraws);

            $transfersCollection[$date] = $usefulData;
        }

        return $transfersCollection;
    }

    private function sumMoney($money)
    {
        $sum = 0;
        foreach ($money as $deposit) {
            $sum += $deposit->amount;
        }

        return $sum;
    }
}
