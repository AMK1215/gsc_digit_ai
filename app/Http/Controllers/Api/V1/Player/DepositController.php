<?php

namespace App\Http\Controllers\Api\V1\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DepositRequest as ApiDepositRequest;
use App\Http\Resources\Api\V1\DepositResource;
use App\Models\DepositRequest;
use App\Traits\HttpResponses;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class DepositController extends Controller
{
    use HttpResponses;

    public function deposit(ApiDepositRequest $request)
    {
        try {
            $inputs = $request->validated();

            $player = Auth::user();

            $pendingDeposit = DepositRequest::where('user_id', $player->id)->where('status', 0)->first();

            if ($pendingDeposit) {
                return $this->error('', 'ငွေသွင်းအတည်ပြုရန် ခေတ္တစောင့်ပါ', 401);
            }
            // image
            $image = $request->file('image');
            $ext = $image->getClientOriginalExtension();
            $filename = uniqid('deposit').'.'.$ext; // Generate a unique filename
            $image->move(public_path('assets/img/deposit/'), $filename); // Save the file

            $deposit = DepositRequest::create(array_merge(
                $inputs,
                [
                    'user_id' => $player->id,
                    'agent_id' => $player->agent_id,
                    'image' => $filename,
                ]
            ));

            return $this->success(new DepositResource($deposit), 'Deposit Request Success');
        } catch (Exception $e) {

            return $this->error('', $e->getMessage(), 401);
        }
    }

    private function encrypt($data, $password)
    {
        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($password);

        $salt = sha1(mt_rand());
        $saltWithPassword = hash('sha256', $password.$salt);

        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", null, $iv);

        return $msgEncryptedBundle = "$iv:$salt:$encrypted";
    }
}
