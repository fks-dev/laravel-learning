<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->isLocal()) {
            Admin::factory()
                ->count(10)
                ->sequence(function ($sequence) {
                    return [
                        'username' => sprintf('admin_%02d', $sequence->index + 1),
                        'password' => Hash::make('admin'),
                        'mail_address' => sprintf('admin_%02d@admin', $sequence->index + 1),
                        'deleted_at' => null,
                        'created_at' => '2023-06-01 01:23:47',
                        'updated_at' => '2023-06-30 21:58:59',
                    ];
                })
                ->create();
        }

        // テスト環境でのテスト用アカウント生成
        if (app()->environment('testing')) {
            Admin::create([
                'username' => 'testAdmin',
                'password' => Hash::make('testAdmin'),
                'mail_address' => 'testAdmin@admin',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
