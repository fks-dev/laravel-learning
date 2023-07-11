<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(app()->isLocal()) {
            Course::factory()
                ->count(10)
                ->sequence(function($sequence) {
                    return [
                        'title' => sprintf('タイトル%d', $sequence->index + 1),
                        'introduction' => Str::random(20),
                        'remarks' => Str::random(20),
                        'position' => $sequence->index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->create();
        }
    }
}
