<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodStock;
use Illuminate\Http\Request;

class BloodStockController extends Controller
{
    public function index()
    {
        $stocks = BloodStock::all();
        return view('admin.blood-stocks.index', compact('stocks'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bags_count' => 'required|integer|min:0',
            'status' => 'required|in:aman,menipis,kritis',
        ]);

        $stock = BloodStock::findOrFail($id);
        $stock->update([
            'bags_count' => $request->bags_count,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Stok darah ' . $stock->blood_type . ' berhasil diperbarui.');
    }
}
