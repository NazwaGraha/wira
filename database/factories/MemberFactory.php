<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nri' => '2425'.fake()->unique()->numerify('#####'),
            'name' => fake()->name(),
            'position' => 'Anggota',
            'address' => fake()->address(),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->date('Y-m-d', '2008-01-01'),
            'gender' => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'class_grade' => fake()->randomElement(['X-A', 'XI-IPA-1', 'XII-IPS-2']),
            'entry_year' => (int) date('Y'),
            'member_status' => 'Aktif',
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
