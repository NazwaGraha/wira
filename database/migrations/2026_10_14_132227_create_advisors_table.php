<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('advisors', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 50)->nullable()->unique();
            $table->string('name');
            $table->string('position')->default('Pembina PMR');
            $table->string('title_degree')->nullable(); // misal: S.Pd, M.Pd
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->text('motto')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->string('photo')->nullable();
            $table->unsignedSmallInteger('order_position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Migrasikan otomatis data pembina lama dari tabel members jika ada
        if (Schema::hasTable('members')) {
            try {
                $pembinaMembers = DB::table('members')
                    ->where(function ($q) {
                        $q->where('position', 'like', '%pembina%')
                            ->orWhereNotNull('nip');
                    })
                    ->get();

                foreach ($pembinaMembers as $pm) {
                    DB::table('advisors')->insert([
                        'nip' => $pm->nip ?? null,
                        'name' => $pm->name,
                        'position' => $pm->position ?: 'Pembina PMR',
                        'gender' => $pm->gender ?? null,
                        'birth_place' => $pm->birth_place ?? null,
                        'birth_date' => $pm->birth_date ?? null,
                        'address' => $pm->address ?? null,
                        'phone' => $pm->phone ?? null,
                        'email' => $pm->email ?? null,
                        'password' => $pm->password ?? null,
                        'motto' => $pm->motto ?? null,
                        'vision' => $pm->vision ?? null,
                        'mission' => $pm->mission ?? null,
                        'photo' => $pm->photo ?? null,
                        'order_position' => 1,
                        'is_active' => $pm->is_active ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (Throwable $e) {
                // Ignore jika data members belum ada / kosong
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advisors');
    }
};
