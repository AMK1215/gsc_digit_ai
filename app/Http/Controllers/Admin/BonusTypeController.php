<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BonusType;
use Illuminate\Http\Request;

class BonusTypeController extends Controller
{
    public function get()
    {
        $data = BonusType::all();

        return view('admin.bonus.type', compact('data'));
    }
}
