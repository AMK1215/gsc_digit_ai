<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Enums\TransactionName;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRequest;
use App\Http\Requests\TransferLogRequest;
use App\Models\Admin\TransferLog;
use App\Models\User;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private const AGENT_ROLE = 3;

    private const SUB_AGENT_ROLE = 6;

    public function index()
    {
        abort_if(
            Gate::denies('agent_index'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        $users = User::query()->agent()->get();

        return view('admin.agent.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(
            Gate::denies('agent_create'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        $agent_name = $this->generateRandomString();
        $referralCode = $this->generateReferralCode();

        return view('admin.agent.create', compact('agent_name', 'referralCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AgentRequest $request)
    {
        abort_if(
            Gate::denies('agent_create'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        $master = Auth::user();
        $inputs = $request->validated();
        if (isset($inputs['amount']) && $inputs['amount'] > $master->balanceFloat) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient balance for transfer.',
            ]);
        }

        $userPrepare = array_merge(
            $inputs,
            [
                'password' => Hash::make($inputs['password']),
                'agent_id' => Auth()->user()->id,
                'type' => UserType::Admin,
            ]
        );

        $agent = User::create($userPrepare);
        if ($request->sub_agent) {
            $agent->roles()->sync(self::SUB_AGENT_ROLE);
        } else {
            $agent->roles()->sync(self::AGENT_ROLE);
        }

        if (isset($inputs['amount'])) {
            app(WalletService::class)->transfer($master, $agent, $inputs['amount'], TransactionName::CreditTransfer, ['agent_id' => Auth::id()]);
        }

        return redirect()->back()
            ->with('success', 'Agent created successfully')
            ->with('password', $request->password)
            ->with('username', $agent->user_name);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort_if(
            Gate::denies('agent_show'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        $user_detail = User::find($id);

        return view('admin.agent.show', compact('user_detail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        abort_if(
            Gate::denies('agent_edit') || ! $this->ifChildOfParent(request()->user()->id, $id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        $agent = User::find($id);

        return view('admin.agent.edit', compact('agent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        abort_if(
            Gate::denies('agent_edit'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        $request->validate([
            'referral_code' => [
                'required',
                Rule::unique('users')->ignore($id),
            ],
        ]);

        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'referral_code' => $request->referral_code,
        ]);

        return redirect()->back()
            ->with('success', 'Agent Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function getCashIn(string $id)
    {
        abort_if(
            Gate::denies('make_transfer'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        $agent = User::find($id);

        return view('admin.agent.cash_in', compact('agent'));
    }

    public function getCashOut(string $id)
    {
        abort_if(
            Gate::denies('make_transfer'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        // Assuming $id is the user ID
        $agent = User::findOrFail($id);

        return view('admin.agent.cash_out', compact('agent'));
    }

    public function makeCashIn(TransferLogRequest $request, $id)
    {

        abort_if(
            Gate::denies('make_transfer') || ! $this->ifChildOfParent(request()->user()->id, $id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        try {
            $inputs = $request->validated();
            $agent = User::findOrFail($id);
            $admin = Auth::user();

            $cashIn = $inputs['amount'];
            if ($cashIn > $admin->balanceFloat) {
                throw new \Exception('You do not have enough balance to transfer!');
            }

            // Transfer money
            app(WalletService::class)->transfer($admin, $agent, $request->validated('amount'), TransactionName::CreditTransfer, ['note' => $request->note ?? '', 'agent_id' => Auth::id()]);

            return redirect()->back()->with('success', 'Money fill request submitted successfully!');
        } catch (Exception $e) {

            session()->flash('error', $e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function makeCashOut(TransferLogRequest $request, string $id)
    {

        abort_if(
            Gate::denies('make_transfer') || ! $this->ifChildOfParent(request()->user()->id, $id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        try {
            $inputs = $request->validated();

            $agent = User::findOrFail($id);
            $admin = Auth::user();
            $cashOut = $inputs['amount'];

            if ($cashOut > $agent->balanceFloat) {

                return redirect()->back()->with('error', 'You do not have enough balance to transfer!');
            }

            // Transfer money
            app(WalletService::class)->transfer($agent, $admin, $request->validated('amount'), TransactionName::DebitTransfer, ['note' => $request->note ?? '', 'agent_id' => Auth::id()]);

            return redirect()->back()->with('success', 'Money fill request submitted successfully!');
        } catch (Exception $e) {

            session()->flash('error', $e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Money fill request submitted successfully!');
    }

    public function getTransferDetail($id)
    {
        abort_if(
            Gate::denies('make_transfer') || ! $this->ifChildOfParent(request()->user()->id, $id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        $transfer_detail = TransferLog::where('from_user_id', $id)
            ->orWhere('to_user_id', $id)
            ->get();

        return view('admin.agent.transfer_detail', compact('transfer_detail'));
    }

    private function generateRandomString()
    {
        $randomNumber = mt_rand(10000000, 99999999);

        return 'MKA'.$randomNumber;
    }

    public function banAgent($id)
    {
        abort_if(
            ! $this->ifChildOfParent(request()->user()->id, $id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        $user = User::find($id);
        $user->update(['status' => $user->status == 1 ? 0 : 1]);
        if (Auth::check() && Auth::id() == $id) {
            Auth::logout();
        }

        return redirect()->back()->with(
            'success',
            'User '.($user->status == 1 ? 'activated' : 'banned').' successfully'
        );
    }

    public function getChangePassword($id)
    {
        $agent = User::find($id);

        return view('admin.agent.change_password', compact('agent'));
    }

    public function makeChangePassword($id, Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $agent = User::find($id);
        $agent->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()
            ->with('success', 'Agent Change Password successfully')
            ->with('password', $request->password)
            ->with('username', $agent->user_name);
    }

    private function generateReferralCode($length = 5)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
