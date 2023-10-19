<?php

namespace Database\Seeders;

use App\Models\UsersGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(app()->isLocal()) {
            UsersGroup::factory()
                ->count(10)
                ->sequence(function($sequence) {
                    return [
                        'group_id'   => $sequence->index + 1,
                        'user_id'    => $sequence->index + 1,
                        'deleted_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->create();
        }
    }
}
