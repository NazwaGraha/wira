<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackgroundMusic;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class BackgroundMusicController extends Controller
{
    /**
     * Stream berkas audio dengan header HTTP audio yang benar dan aman dari blokir web server.
     */
    public function streamAudio()
    {
        self::ensureTableExists();

        $music = BackgroundMusic::first();
        $path = null;

        if ($music && $music->audio_path) {
            $candidate = public_path(ltrim($music->audio_path, '/'));
            if (file_exists($candidate)) {
                $path = $candidate;
            }
        }

        if (! $path) {
            if (file_exists(public_path('audio/mars-pmi.mp3'))) {
                $path = public_path('audio/mars-pmi.mp3');
            } elseif (file_exists(public_path('audio/mars-pmi.m4a'))) {
                $path = public_path('audio/mars-pmi.m4a');
            }
        }

        if (! $path || ! file_exists($path)) {
            abort(404, 'Berkas audio Mars PMI tidak ditemukan.');
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'mp3' => 'audio/mpeg',
            'm4a', 'mp4' => 'audio/mp4',
            'ogg' => 'audio/ogg',
            'wav' => 'audio/wav',
            default => 'audio/mp4',
        };

        return response()->file($path, [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    /**
     * Tampilkan halaman pengelolaan musik latar (Mars PMI).
     */
    public function index()
    {
        self::ensureTableExists();

        $music = BackgroundMusic::first();
        if (! $music) {
            $music = self::seedDefault();
        }

        return view('admin.background-music.index', compact('music'));
    }

    /**
     * Perbarui pengaturan musik latar dan berkas audio.
     */
    public function update(Request $request)
    {
        self::ensureTableExists();

        $music = BackgroundMusic::first();
        if (! $music) {
            $music = self::seedDefault();
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'audio_file' => 'nullable|file|mimes:mp3,m4a,wav,ogg,aac,mp4|max:25600', // max 25MB
            'default_volume' => 'required|integer|min:5|max:100',
            'lyrics' => 'nullable|string',
        ]);

        $data = [
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? 'Ciptaan: Mochtar H. S. • PMR Wira SMAN 1 Ciawi',
            'is_active' => $request->has('is_active'),
            'autoplay_on_first_interaction' => $request->has('autoplay_on_first_interaction'),
            'loop' => $request->has('loop'),
            'default_volume' => (int) $validated['default_volume'],
            'lyrics' => $validated['lyrics'] ?? self::defaultLyrics(),
        ];

        // Jika ada unggahan berkas audio baru
        if ($request->hasFile('audio_file')) {
            $file = $request->file('audio_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $fileName = 'mars-pmi-custom-'.time().'.'.$extension;

            // Simpan langsung ke public/audio agar mudah dibaca server tanpa masalah symlink storage
            $destinationPath = public_path('audio');
            if (! File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Hapus berkas kustom lama jika ada
            if ($music->audio_path && ! str_contains($music->audio_path, 'mars-pmi.m4a')) {
                $oldPath = public_path(ltrim($music->audio_path, '/'));
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $file->move($destinationPath, $fileName);
            $data['audio_path'] = '/audio/'.$fileName;
        }

        $music->update($data);

        return redirect()->route('admin.background-music.index')
            ->with('success', 'Pengaturan musik latar Mars PMI berhasil disimpan dan diperbarui!');
    }

    /**
     * Kembalikan audio ke berkas default resmi Mars PMI.
     */
    public function resetDefault()
    {
        self::ensureTableExists();

        $music = BackgroundMusic::first();
        if ($music) {
            // Hapus custom audio jika ada
            if ($music->audio_path && ! str_contains($music->audio_path, 'mars-pmi.m4a')) {
                $oldPath = public_path(ltrim($music->audio_path, '/'));
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $music->update([
                'title' => 'Mars Palang Merah Indonesia',
                'subtitle' => 'Ciptaan: Mochtar H. S. • PMR Wira SMAN 1 Ciawi',
                'audio_path' => null, // reset ke default /audio/mars-pmi.m4a
                'lyrics' => self::defaultLyrics(),
            ]);
        }

        return redirect()->route('admin.background-music.index')
            ->with('success', 'Audio musik latar telah dikembalikan ke berkas resmi Mars PMI bawaan.');
    }

    /**
     * Teks lirik standar Mars PMI.
     */
    public static function defaultLyrics(): string
    {
        return "Palang Merah Indonesia\nSumber kasih umat manusia\nWarisan luhur, nusa dan bangsa\nWujud nyata pengayom Pancasila\n\nGerak juangnya ke seluruh nusa\nMendarmabaktikan bagi ampera\nTunaikan tugas suci tujuan PMI\nDi persada bunda pertiwi\n\nUntuk umat manusia\nDi seluruh dunia\nPMI menghantarkan jasa";
    }

    /**
     * Buat data default jika tabel kosong.
     */
    public static function seedDefault(): BackgroundMusic
    {
        return BackgroundMusic::create([
            'title' => 'Mars Palang Merah Indonesia',
            'subtitle' => 'Ciptaan: Mochtar H. S. • PMR Wira SMAN 1 Ciawi',
            'audio_path' => null,
            'is_active' => true,
            'autoplay_on_first_interaction' => true,
            'default_volume' => 30,
            'loop' => true,
            'lyrics' => self::defaultLyrics(),
        ]);
    }

    /**
     * Self-healing migration agar website di server hosting tidak pernah error 500.
     */
    public static function ensureTableExists(): void
    {
        if (! Schema::hasTable('background_musics')) {
            Schema::create('background_musics', function (Blueprint $table) {
                $table->id();
                $table->string('title')->default('Mars Palang Merah Indonesia');
                $table->string('subtitle')->nullable()->default('Ciptaan: Mochtar H. S. • PMR Wira SMAN 1 Ciawi');
                $table->string('audio_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('autoplay_on_first_interaction')->default(true);
                $table->unsignedTinyInteger('default_volume')->default(30);
                $table->boolean('loop')->default(true);
                $table->text('lyrics')->nullable();
                $table->timestamps();
            });

            try {
                self::seedDefault();
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }
}
