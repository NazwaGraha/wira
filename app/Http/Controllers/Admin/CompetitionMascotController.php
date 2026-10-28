<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CompetitionMascotController extends Controller
{
    /**
     * Tampilkan halaman kelola maskot untuk seluruh tingkatan dan kategori.
     */
    public function index(Request $request)
    {
        $levels = ['Mula', 'Madya', 'Wira'];
        $categories = ['Putra', 'Putri', 'Umum'];
        $activeLevel = $request->query('level', 'Mula');
        if (! in_array($activeLevel, $levels)) {
            $activeLevel = 'Mula';
        }

        $mascots = [];
        $mascotDir = public_path('images/mascot');

        foreach ($levels as $level) {
            $mascots[$level] = [];
            foreach ($categories as $cat) {
                $primaryName = "{$level}{$cat}.png";
                $primaryPath = $mascotDir.'/'.$primaryName;

                $lvlKey = strtolower($level);
                $subPath = $mascotDir."/{$lvlKey}/Kategori_{$cat}.png";
                $flatPath = $mascotDir.'/pmr_'.$lvlKey.'_'.strtolower($cat).'.png';

                $resolvedPath = null;
                $resolvedRelative = "images/mascot/{$primaryName}";

                if (file_exists($primaryPath)) {
                    $resolvedPath = $primaryPath;
                    $resolvedRelative = "images/mascot/{$primaryName}";
                } elseif (file_exists($subPath)) {
                    $resolvedPath = $subPath;
                    $resolvedRelative = "images/mascot/{$lvlKey}/Kategori_{$cat}.png";
                } elseif (file_exists($flatPath)) {
                    $resolvedPath = $flatPath;
                    $resolvedRelative = 'images/mascot/pmr_'.$lvlKey.'_'.strtolower($cat).'.png';
                }

                $info = [
                    'level' => $level,
                    'category' => $cat,
                    'file_name' => $primaryName,
                    'exists' => $resolvedPath !== null,
                    'url' => $resolvedPath ? asset($resolvedRelative).'?v='.filemtime($resolvedPath) : null,
                    'size' => $resolvedPath ? round(filesize($resolvedPath) / 1024, 1) : null,
                    'dimensions' => null,
                    'updated_at' => $resolvedPath ? date('d M Y, H:i', filemtime($resolvedPath)) : null,
                ];

                if ($resolvedPath && @getimagesize($resolvedPath)) {
                    $dims = @getimagesize($resolvedPath);
                    $info['dimensions'] = ($dims[0] ?? 0).' x '.($dims[1] ?? 0).' px';
                }

                $mascots[$level][$cat] = $info;
            }
        }

        return view('admin.competition.mascots.index', compact('mascots', 'levels', 'categories', 'activeLevel'));
    }

    /**
     * Upload dan perbarui gambar maskot.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'level' => 'required|in:Mula,Madya,Wira',
            'gender_category' => 'required|in:Putra,Putri,Umum',
            'mascot_image' => 'required|image|mimes:png,jpg,jpeg,webp|max:5120',
        ], [
            'mascot_image.required' => 'Pilih file gambar maskot terlebih dahulu.',
            'mascot_image.image' => 'File harus berupa gambar.',
            'mascot_image.mimes' => 'Format gambar yang didukung: PNG, JPG, JPEG, WEBP.',
            'mascot_image.max' => 'Ukuran file gambar maksimal 5 MB.',
        ]);

        $level = $validated['level'];
        $gender = $validated['gender_category'];
        $file = $request->file('mascot_image');

        $mascotDir = public_path('images/mascot');
        File::ensureDirectoryExists($mascotDir, 0755, true);

        // 1. Simpan target utama (e.g. public/images/mascot/MulaPutra.png)
        $primaryName = "{$level}{$gender}.png";
        $primaryPath = $mascotDir.'/'.$primaryName;
        $file->move($mascotDir, $primaryName);

        // 2. Sinkronkan ke subfolder level (e.g. public/images/mascot/mula/Kategori_Putra.png)
        $subFolder = $mascotDir.'/'.strtolower($level);
        File::ensureDirectoryExists($subFolder, 0755, true);
        @copy($primaryPath, $subFolder."/Kategori_{$gender}.png");
        @copy($primaryPath, $subFolder."/Kategori {$gender}.png");

        // 3. Sinkronkan ke flat naming (e.g. public/images/mascot/pmr_mula_putra.png)
        $flatName = 'pmr_'.strtolower($level).'_'.strtolower($gender).'.png';
        @copy($primaryPath, $mascotDir.'/'.$flatName);

        return redirect()->route('admin.competition-mascots.index', ['level' => $level])
            ->with('success', "Gambar Maskot PMR {$level} Kategori {$gender} berhasil diperbarui!");
    }
}
