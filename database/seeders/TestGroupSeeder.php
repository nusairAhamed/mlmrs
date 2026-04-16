<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TestCategory;
use App\Models\TestGroup;
use App\Models\Test;

class TestGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            // Hematology
            [
                'category' => 'Hematology',
                'name'     => 'Full Blood Count (FBC)',
                'price'    => 500.00,
                'tests'    => [
                    'Haemoglobin (Hb)',
                    'Red Blood Cells (RBC)',
                    'White Blood Cells (WBC)',
                    'Platelets',
                    'Haematocrit (PCV)',
                    'Mean Corpuscular Volume (MCV)',
                    'Mean Corpuscular Haemoglobin (MCH)',
                    'Mean Corpuscular Hb Concentration (MCHC)',
                    'Neutrophils',
                    'Lymphocytes',
                    'Monocytes',
                    'Eosinophils',
                    'Basophils',
                ],
            ],
            [
                'category' => 'Hematology',
                'name'     => 'Erythrocyte Sedimentation Rate (ESR)',
                'price'    => 250.00,
                'tests'    => [
                    'Erythrocyte Sedimentation Rate (ESR)',
                ],
            ],

            // Biochemistry
            [
                'category' => 'Biochemistry',
                'name'     => 'Liver Function Tests (LFT)',
                'price'    => 1200.00,
                'tests'    => [
                    'Total Bilirubin',
                    'Direct Bilirubin',
                    'Indirect Bilirubin',
                    'Alanine Aminotransferase (ALT/SGPT)',
                    'Aspartate Aminotransferase (AST/SGOT)',
                    'Alkaline Phosphatase (ALP)',
                    'Gamma-Glutamyl Transferase (GGT)',
                    'Total Protein',
                    'Albumin',
                ],
            ],
            [
                'category' => 'Biochemistry',
                'name'     => 'Renal Function Tests (RFT)',
                'price'    => 900.00,
                'tests'    => [
                    'Blood Urea Nitrogen (BUN)',
                    'Serum Creatinine',
                    'Uric Acid',
                    'eGFR',
                ],
            ],
            [
                'category' => 'Biochemistry',
                'name'     => 'Lipid Profile',
                'price'    => 1000.00,
                'tests'    => [
                    'Total Cholesterol',
                    'LDL Cholesterol',
                    'HDL Cholesterol',
                    'Triglycerides',
                    'VLDL Cholesterol',
                ],
            ],
            [
                'category' => 'Biochemistry',
                'name'     => 'Fasting Blood Sugar',
                'price'    => 300.00,
                'tests'    => [
                    'Fasting Blood Sugar (FBS)',
                ],
            ],
            [
                'category' => 'Biochemistry',
                'name'     => 'Random Blood Sugar',
                'price'    => 300.00,
                'tests'    => [
                    'Random Blood Sugar (RBS)',
                ],
            ],
            [
                'category' => 'Biochemistry',
                'name'     => 'HbA1c (Glycated Haemoglobin)',
                'price'    => 800.00,
                'tests'    => [
                    'HbA1c',
                ],
            ],
            [
                'category' => 'Biochemistry',
                'name'     => 'Electrolytes Panel',
                'price'    => 950.00,
                'tests'    => [
                    'Sodium (Na⁺)',
                    'Potassium (K⁺)',
                    'Chloride (Cl⁻)',
                    'Bicarbonate (HCO₃⁻)',
                    'Serum Calcium',
                    'Serum Magnesium',
                    'Serum Phosphorus',
                ],
            ],

            // Hormones & Endocrinology
            [
                'category' => 'Hormones & Endocrinology',
                'name'     => 'Thyroid Function Tests (TFT)',
                'price'    => 1500.00,
                'tests'    => [
                    'Thyroid Stimulating Hormone (TSH)',
                    'Free Triiodothyronine (Free T3)',
                    'Free Thyroxine (Free T4)',
                    'Total Triiodothyronine (T3)',
                    'Total Thyroxine (T4)',
                ],
            ],

            // Coagulation
            [
                'category' => 'Coagulation',
                'name'     => 'Coagulation Profile',
                'price'    => 1100.00,
                'tests'    => [
                    'Prothrombin Time (PT)',
                    'Activated Partial Thromboplastin Time (APTT)',
                    'International Normalised Ratio (INR)',
                    'Fibrinogen',
                ],
            ],

            // Immunology & Serology
            [
                'category' => 'Immunology & Serology',
                'name'     => 'Hepatitis Screening Panel',
                'price'    => 2000.00,
                'tests'    => [
                    'HBsAg (Hepatitis B Surface Antigen)',
                    'Anti-HCV (Hepatitis C Antibody)',
                ],
            ],
            [
                'category' => 'Immunology & Serology',
                'name'     => 'HIV Screening',
                'price'    => 1500.00,
                'tests'    => [
                    'HIV 1 & 2 Antibody',
                ],
            ],
            [
                'category' => 'Immunology & Serology',
                'name'     => 'Widal Test (Typhoid)',
                'price'    => 700.00,
                'tests'    => [
                    'Widal Test – Salmonella Typhi O',
                    'Widal Test – Salmonella Typhi H',
                ],
            ],
            [
                'category' => 'Immunology & Serology',
                'name'     => 'VDRL (Syphilis)',
                'price'    => 600.00,
                'tests'    => [
                    'VDRL (Syphilis Screening)',
                ],
            ],
            [
                'category' => 'Immunology & Serology',
                'name'     => 'Inflammatory Markers',
                'price'    => 800.00,
                'tests'    => [
                    'C-Reactive Protein (CRP)',
                    'Erythrocyte Sedimentation Rate (ESR)',
                    'Rheumatoid Factor (RF)',
                ],
            ],

            // Urinalysis
            [
                'category' => 'Urinalysis',
                'name'     => 'Urine Full Report (UFR)',
                'price'    => 400.00,
                'tests'    => [
                    'Urine Colour',
                    'Urine Clarity',
                    'Urine pH',
                    'Urine Specific Gravity',
                    'Urine Protein',
                    'Urine Glucose',
                    'Urine Ketones',
                    'Urine Bilirubin',
                    'Urine RBCs',
                    'Urine WBCs',
                    'Urine Epithelial Cells',
                    'Urine Casts',
                ],
            ],
        ];

        foreach ($groups as $data) {
            $category = TestCategory::where('name', $data['category'])->first();

            $group = TestGroup::firstOrCreate(
                ['name' => $data['name']],
                [
                    'category_id' => $category?->id,
                    'price'       => $data['price'],
                    'status'      => 'active',
                ]
            );

            // Attach tests (sync without detaching to be idempotent)
            $testIds = Test::whereIn('name', $data['tests'])->pluck('id');
            $group->tests()->syncWithoutDetaching($testIds);
        }
    }
}
