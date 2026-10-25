<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UksHotline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UksHotlineController extends Controller
{
    /**
     * Tampilkan halaman pengaturan Hotline & Siaga UKS di Backoffice.
     */
    public function index(): View
    {
        $hotline = UksHotline::first();
        if (! $hotline) {
            $hotline = UksHotline::create([
                'phone_number' => '083872506817',
                'label' => 'Hotline UKS',
                'service_name' => 'Piket Medis & P3K Sekolah',
                'officer_name' => 'Tim Relawan PMR Wira SMAN 1 Ciawi',
                'operational_hours' => 'Senin - Jumat (07.00 - 16.00 WIB)',
                'location' => 'Ruang UKS SMAN 1 Ciawi',
                'is_active' => true,
                'status_text' => 'Siaga di Jam Sekolah',
                'description' => 'Layanan cepat tanggap pertolongan pertama (P3K), penanganan siswa sakit/pingsan saat upacara/KBM, serta evakuasi medis darurat di lingkungan SMAN 1 Ciawi.',
                'whatsapp_message' => 'Halo Petugas Piket Medis PMR SMAN 1 Ciawi, saya membutuhkan bantuan medis/pertolongan pertama di lokasi: ',
            ]);
        }

        return view('admin.uks-hotline.index', compact('hotline'));
    }

    /**
     * Simpan perubahan pengaturan Hotline & Siaga UKS.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone_number' => 'required|string|max:30',
            'label' => 'required|string|max:50',
            'service_name' => 'required|string|max:100',
            'officer_name' => 'nullable|string|max:100',
            'operational_hours' => 'required|string|max:100',
            'location' => 'required|string|max:100',
            'status_text' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
            'whatsapp_message' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $hotline = UksHotline::first();
        if ($hotline) {
            $hotline->update($validated);
        } else {
            UksHotline::create($validated);
        }

        return redirect()->route('admin.uks-hotline.index')->with('success', 'Pengaturan Hotline & Siaga Piket Medis UKS berhasil disimpan.');
    }

    /**
     * Ubah status siaga secara cepat (Aktif / Tutup).
     */
    public function toggleStatus(): RedirectResponse
    {
        $hotline = UksHotline::first();
        if ($hotline) {
            $hotline->update([
                'is_active' => ! $hotline->is_active,
            ]);
            $statusLabel = $hotline->is_active ? 'diaktifkan (Siaga)' : 'dinonaktifkan (Tutup)';

            return redirect()->route('admin.uks-hotline.index')->with('success', "Status Hotline UKS berhasil {$statusLabel}.");
        }

        return redirect()->route('admin.uks-hotline.index')->with('info', 'Data hotline belum tersedia.');
    }
}
