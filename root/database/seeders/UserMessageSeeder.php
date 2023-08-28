<?php

namespace Database\Seeders;

use App\Models\UserMessage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class UserMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(app()->isLocal()) {
            UserMessage::factory()
                ->count(10)
                ->sequence(function($sequence) {
                    return [
                        'admin_id' => 1,
                        'user_id' => 1,
                        'title' => sprintf('メッセージ%d', $sequence->index + 1),
                        'text' => Str::random(20),
                        'draft' => null,
                        'hidden' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->create();
        }
    }
}
