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
            ['kode_mk' => 'IF101', 'nama_mk' => 'Pengembangan Aplikasi Web', 'sks' => 3],
            ['kode_mk' => 'IF102', 'nama_mk' => 'Basis Data', 'sks' => 3],
            ['kode_mk' => 'IF103', 'nama_mk' => 'Pemrograman Perangkat Bergerak', 'sks' => 3],
            ['kode_mk' => 'IF104', 'nama_mk' => 'Pemrograman Berorientasi Objek', 'sks' => 2],
        ];

        foreach ($data as $row) {
            // firstOrCreate supaya seeder aman dijalankan lebih dari sekali
            Course::firstOrCreate(
                ['semester_id' => $semesterId, 'kode_mk' => $row['kode_mk']],
                $row + ['semester_id' => $semesterId]
            );
        }
    }
}
