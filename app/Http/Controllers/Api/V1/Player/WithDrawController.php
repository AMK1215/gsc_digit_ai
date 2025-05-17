<?php

namespace App\Http\Controllers\Api\V1\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WithdrawRequest;
use App\Http\Resources\Api\V1\WithdrawResource;
use App\Models\WithDrawRequest as ModelsWithDrawRequest;
use App\Services\ApiService;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WithDrawController extends Controller
{
    use HttpResponses;

    public function withdraw(WithdrawRequest $request)
    {
        $inputs = $request->validated();
        $player = Auth::user();

        if ($player->balanceFloat < $inputs['amount']) {
            return $this->error('', ['amount' => 'Insufficient balance'], 422);
        }

        $withdraw = ModelsWithDrawRequest::create(array_merge(
            $inputs,
            [
                'payment_type_id' => $request->bank_id,
                'user_id' => $player->id,
                'agent_id' => $player->agent_id,
            ]
        ));

        return $this->success(new WithdrawResource($withdraw), 'Withdraw Request Success');
    }
}
