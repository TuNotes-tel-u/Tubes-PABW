<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // pakai semester pertama yang ada di tabel semesters
        $semesterId = DB::table('semesters')->value('id');

        if (! $semesterId) {
            $this->command?->warn('Tabel semesters masih kosong. Isi dulu, lalu jalankan ulang seeder ini.');
            return;
        }

        $data = [
            ['code' => 'IF101', 'name' => 'Pengembangan Aplikasi Web', 'sks' => 3],
            ['code' => 'IF102', 'name' => 'Basis Data', 'sks' => 3],
            ['code' => 'IF103', 'name' => 'Pemrograman Perangkat Bergerak', 'sks' => 3],
            ['code' => 'IF104', 'name' => 'Pemrograman Berorientasi Objek', 'sks' => 2],
        ];

        foreach ($data as $row) {
            // firstOrCreate supaya seeder aman dijalankan lebih dari sekali
            Course::firstOrCreate(['code' => $row['code']], $row + ['semester_id' => $semesterId]);
        }
    }
}
