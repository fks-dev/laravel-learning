<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CourseSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            GroupSeeder::class,
            ContentSeeder::class,
<<<<<<< HEAD
            GroupsCourseSeeder::class,
            UsersGroupSeeder::class,
=======
            UserMessageSeeder::class,
            AdminMessageSeeder::class,
>>>>>>> 55b996f1f4bd4d732cae686d8f566cba503725c7
        ]);
    }
}
