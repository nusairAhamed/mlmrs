<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TestCategory;

class TestCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Hematology',
            'Biochemistry',
            'Immunology & Serology',
            'Hormones & Endocrinology',
            'Coagulation',
            'Urinalysis',
            'Microbiology',
        ];

        foreach ($categories as $name) {
            TestCategory::firstOrCreate(['name' => $name]);
        }
    }
}
