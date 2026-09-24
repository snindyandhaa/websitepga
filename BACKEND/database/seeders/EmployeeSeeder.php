<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = base_path("database/data/employees.csv");

        if (!file_exists($filePath)) {
            $this->command->error("File employees.csv tidak ditemukan di database/data/");
            return;
        }

        $csvFile = fopen($filePath, "r");
        $firstline = true;

        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            // Lewati baris pertama (header)
            if (!$firstline) {
                DB::table('employees')->insert([
                    'name'            => $data[0] ?? null,
                    'nik'             => $data[1] ?? null,
                    'mutation_date'   => !empty($data[2]) ? date('Y-m-d', strtotime($data[2])) : null,
                    'on_to_mutation'  => $data[3] ?? null,
                    'ktp'             => $data[4] ?? null,
                    'level'           => $data[5] ?? null,
                    'status'          => $data[6] ?? null,
                    'division'        => $data[7] ?? null,
                    'cost_center'     => $data[8] ?? null,
                    'department'      => $data[9] ?? null,
                    'position'        => $data[10] ?? null,
                    'last_promotion'  => !empty($data[11]) ? date('Y-m-d', strtotime($data[11])) : null,
                    'birth_date'      => !empty($data[12]) ? date('Y-m-d', strtotime($data[12])) : null,
                    'gender'          => $data[13] ?? null,
                    'join_date'       => !empty($data[14]) ? date('Y-m-d', strtotime($data[14])) : null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}