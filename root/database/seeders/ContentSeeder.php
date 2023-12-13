<?php

namespace Database\Seeders;

use App\Models\Content;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->isLocal()) {
            Content::factory()
                ->count(20)
                ->sequence(function ($sequence) {
                    return [
                        'course_id'          => 190000 + random_int(1, 10),
                        'admin_id'           => 120000 + random_int(1, 10),
                        'title'              => sprintf('コンテンツ%d', $sequence->index + 1),
                        'youtube_video_id'   => 'nkqdhXSwEVs',
                        'remarks'            => sprintf('コンテンツ%d', $sequence->index + 1),
                        'is_public'          => random_int(0, 1),
                        'position'           => $sequence->index + 1,
                        'deleted_at'         => null,
                        'created_at'         => '2022-12-30 11:22:33',
                        'updated_at'         => '2022-12-31 23:58:59',
                    ];
                })
                ->create();
        }
    }
}
