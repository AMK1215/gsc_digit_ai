<?php

namespace App\Http\Controllers\Api\V1\Bank;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BankRequest;
use App\Http\Resources\Api\V1\BankResource;
use App\Http\Resources\BonusResource;
use App\Models\Admin\Bank;
use App\Models\Bonus;
use App\Models\UserBank;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    use HttpResponses;

    public function banks()
    {
        $banks = Bank::agentPlayer()->where('status', 1)->get();

        return $this->success(BankResource::collection($banks), 'Banks retrieved successfully');
    }

    public function bonusLog()
    {
        $bonus = Bonus::where('user_id', Auth::id())->latest()->paginate(10);

        return $this->success(BonusResource::collection($bonus), 'Bonus retrieved successfully');
    }
}
