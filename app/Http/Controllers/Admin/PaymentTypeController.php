<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentType;
use App\Models\UserPayment;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentTypeController extends Controller
{
    use ImageUpload;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentTypes = PaymentType::orderBy('id', 'DESC')->get();

        return view('admin.paymentType.index', compact('paymentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.paymentType.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'required',
        ]);
        $filename = $this->handleImageUpload($request->image, 'paymentType');

        PaymentType::create([
            'name' => $request->name,
            'image' => $filename,
        ]);

        return redirect(route('admin.paymentType.index'))->with('success', 'New Payment Type Added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $paymentType = PaymentType::where('id', $id)->first();

        return view('admin.paymentType.edit', compact('paymentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentType = PaymentType::findOrFail($id);
        $this->handleImageDelete($paymentType->image, 'paymentType');
        $filename = $this->handleImageUpload($request->image, 'paymentType');

        $paymentType->update([
            'name' => $request->name,
            'image' => $filename,
        ]);

        return redirect()->route('admin.paymentType.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentType = PaymentType::find($id);

        $this->handleImageDelete($paymentType->image, 'paymentType');
        $paymentType->delete();

        return redirect()->route('admin.paymentType.index');
    }
}
