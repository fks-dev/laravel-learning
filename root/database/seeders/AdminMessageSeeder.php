<?php

namespace Database\Seeders;

use App\Models\AdminMessage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class AdminMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(app()->isLocal()) {
            AdminMessage::factory()
                ->count(30)
                ->sequence(function($sequence) {
                    return [
                        'admin_id' => 120001,
                        'user_id' => 110001,
                        'title' => sprintf('管理者からのメッセージ%d', $sequence->index + 1),
                        'text' => Str::random(20),
                        'action' => random_int(0,1),
                        'is_hidden' => 0,
                        'is_replied' => 0,
                        'reply_message_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->create();
        }

    }
}
