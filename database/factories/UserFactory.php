<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Tạo mã sinh viên ngẫu nhiên với định dạng SV + số
        $studentCode = 'SV' . str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT);
        
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->email,
            'email_verified_at' => now(),
            'password' => '$2y$10$9NHtqgF3yCucIt6Y3rAoBeIVwf3XCvvDDnLkYhml/TkHgZNuU/2aG', //123456789
            'profile_img' => 'default-profile.png',
            'is_admin' => $this->faker->randomElement([0, 1]),
            'remember_token' => Str::random(10),
            // Thêm các trường mới
            'class' => $this->faker->randomElement(['10A1', '10A2', '11A1', '11A2', '12A1', '12A2']), // Tạo lớp ngẫu nhiên
            'student_code' => $studentCode, // Sử dụng mã sinh viên đã tạo
            'phone' => $this->faker->numerify('0#########'), // Tạo số điện thoại Việt Nam 10 số
        ];
    }
}