<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\TestReferenceRange;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        $tests = [

            // ── HEMATOLOGY ────────────────────────────────────────────────
            [
                'name' => 'Haemoglobin (Hb)',
                'unit' => 'g/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'male',   'age_min' => 18, 'age_max' => null, 'ref_min' => 13.5, 'ref_max' => 17.5],
                    ['gender' => 'female', 'age_min' => 18, 'age_max' => null, 'ref_min' => 12.0, 'ref_max' => 15.5],
                    ['gender' => 'any',    'age_min' => 0,  'age_max' => 17,   'ref_min' => 11.5, 'ref_max' => 15.5],
                ],
            ],
            [
                'name' => 'Red Blood Cells (RBC)',
                'unit' => 'x10⁶/µL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'male',   'age_min' => 18, 'age_max' => null, 'ref_min' => 4.5,  'ref_max' => 5.9],
                    ['gender' => 'female', 'age_min' => 18, 'age_max' => null, 'ref_min' => 4.0,  'ref_max' => 5.2],
                    ['gender' => 'any',    'age_min' => 0,  'age_max' => 17,   'ref_min' => 4.0,  'ref_max' => 5.5],
                ],
            ],
            [
                'name' => 'White Blood Cells (WBC)',
                'unit' => 'x10³/µL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 4.0, 'ref_max' => 11.0],
                ],
            ],
            [
                'name' => 'Platelets',
                'unit' => 'x10³/µL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 150, 'ref_max' => 400],
                ],
            ],
            [
                'name' => 'Haematocrit (PCV)',
                'unit' => '%',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'male',   'age_min' => 18, 'age_max' => null, 'ref_min' => 41, 'ref_max' => 53],
                    ['gender' => 'female', 'age_min' => 18, 'age_max' => null, 'ref_min' => 36, 'ref_max' => 46],
                    ['gender' => 'any',    'age_min' => 0,  'age_max' => 17,   'ref_min' => 35, 'ref_max' => 47],
                ],
            ],
            [
                'name' => 'Mean Corpuscular Volume (MCV)',
                'unit' => 'fL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 80, 'ref_max' => 100],
                ],
            ],
            [
                'name' => 'Mean Corpuscular Haemoglobin (MCH)',
                'unit' => 'pg',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 27, 'ref_max' => 33],
                ],
            ],
            [
                'name' => 'Mean Corpuscular Hb Concentration (MCHC)',
                'unit' => 'g/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 32, 'ref_max' => 36],
                ],
            ],
            [
                'name' => 'Neutrophils',
                'unit' => '%',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 50, 'ref_max' => 70],
                ],
            ],
            [
                'name' => 'Lymphocytes',
                'unit' => '%',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 20, 'ref_max' => 40],
                ],
            ],
            [
                'name' => 'Monocytes',
                'unit' => '%',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 2, 'ref_max' => 8],
                ],
            ],
            [
                'name' => 'Eosinophils',
                'unit' => '%',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 1, 'ref_max' => 4],
                ],
            ],
            [
                'name' => 'Basophils',
                'unit' => '%',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0, 'ref_max' => 1],
                ],
            ],
            [
                'name' => 'Erythrocyte Sedimentation Rate (ESR)',
                'unit' => 'mm/hr',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'male',   'age_min' => null, 'age_max' => null, 'ref_min' => 0, 'ref_max' => 15],
                    ['gender' => 'female', 'age_min' => null, 'age_max' => null, 'ref_min' => 0, 'ref_max' => 20],
                ],
            ],

            // ── BIOCHEMISTRY – Liver Function ─────────────────────────────
            [
                'name' => 'Total Bilirubin',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0.2, 'ref_max' => 1.2],
                ],
            ],
            [
                'name' => 'Direct Bilirubin',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0.0, 'ref_max' => 0.3],
                ],
            ],
            [
                'name' => 'Indirect Bilirubin',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0.1, 'ref_max' => 0.9],
                ],
            ],
            [
                'name' => 'Alanine Aminotransferase (ALT/SGPT)',
                'unit' => 'U/L',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'male',   'age_min' => 18, 'age_max' => null, 'ref_min' => 7,  'ref_max' => 56],
                    ['gender' => 'female', 'age_min' => 18, 'age_max' => null, 'ref_min' => 7,  'ref_max' => 45],
                    ['gender' => 'any',    'age_min' => 0,  'age_max' => 17,   'ref_min' => 7,  'ref_max' => 45],
                ],
            ],
            [
                'name' => 'Aspartate Aminotransferase (AST/SGOT)',
                'unit' => 'U/L',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 10, 'ref_max' => 40],
                ],
            ],
            [
                'name' => 'Alkaline Phosphatase (ALP)',
                'unit' => 'U/L',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => 18, 'age_max' => null, 'ref_min' => 44,  'ref_max' => 147],
                    ['gender' => 'any', 'age_min' => 0,  'age_max' => 17,   'ref_min' => 100, 'ref_max' => 390],
                ],
            ],
            [
                'name' => 'Gamma-Glutamyl Transferase (GGT)',
                'unit' => 'U/L',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'male',   'age_min' => null, 'age_max' => null, 'ref_min' => 8,  'ref_max' => 61],
                    ['gender' => 'female', 'age_min' => null, 'age_max' => null, 'ref_min' => 5,  'ref_max' => 36],
                ],
            ],
            [
                'name' => 'Total Protein',
                'unit' => 'g/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 6.0, 'ref_max' => 8.3],
                ],
            ],
            [
                'name' => 'Albumin',
                'unit' => 'g/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 3.5, 'ref_max' => 5.0],
                ],
            ],

            // ── BIOCHEMISTRY – Renal Function ─────────────────────────────
            [
                'name' => 'Blood Urea Nitrogen (BUN)',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 7, 'ref_max' => 20],
                ],
            ],
            [
                'name' => 'Serum Creatinine',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'male',   'age_min' => 18, 'age_max' => null, 'ref_min' => 0.74, 'ref_max' => 1.35],
                    ['gender' => 'female', 'age_min' => 18, 'age_max' => null, 'ref_min' => 0.59, 'ref_max' => 1.04],
                    ['gender' => 'any',    'age_min' => 0,  'age_max' => 17,   'ref_min' => 0.30, 'ref_max' => 0.70],
                ],
            ],
            [
                'name' => 'Uric Acid',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'male',   'age_min' => 18, 'age_max' => null, 'ref_min' => 3.5, 'ref_max' => 7.2],
                    ['gender' => 'female', 'age_min' => 18, 'age_max' => null, 'ref_min' => 2.6, 'ref_max' => 6.0],
                ],
            ],
            [
                'name' => 'eGFR',
                'unit' => 'mL/min/1.73m²',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 60, 'ref_max' => 120],
                ],
            ],

            // ── BIOCHEMISTRY – Lipid Profile ──────────────────────────────
            [
                'name' => 'Total Cholesterol',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0, 'ref_max' => 200],
                ],
            ],
            [
                'name' => 'LDL Cholesterol',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0, 'ref_max' => 100],
                ],
            ],
            [
                'name' => 'HDL Cholesterol',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'male',   'age_min' => null, 'age_max' => null, 'ref_min' => 40, 'ref_max' => 60],
                    ['gender' => 'female', 'age_min' => null, 'age_max' => null, 'ref_min' => 50, 'ref_max' => 80],
                ],
            ],
            [
                'name' => 'Triglycerides',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0, 'ref_max' => 150],
                ],
            ],
            [
                'name' => 'VLDL Cholesterol',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 5, 'ref_max' => 40],
                ],
            ],

            // ── BIOCHEMISTRY – Blood Glucose ──────────────────────────────
            [
                'name' => 'Fasting Blood Sugar (FBS)',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 70, 'ref_max' => 100],
                ],
            ],
            [
                'name' => 'Random Blood Sugar (RBS)',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 70, 'ref_max' => 140],
                ],
            ],
            [
                'name' => 'HbA1c',
                'unit' => '%',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 4.0, 'ref_max' => 5.7],
                ],
            ],

            // ── BIOCHEMISTRY – Electrolytes ───────────────────────────────
            [
                'name' => 'Sodium (Na⁺)',
                'unit' => 'mEq/L',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 136, 'ref_max' => 145],
                ],
            ],
            [
                'name' => 'Potassium (K⁺)',
                'unit' => 'mEq/L',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 3.5, 'ref_max' => 5.1],
                ],
            ],
            [
                'name' => 'Chloride (Cl⁻)',
                'unit' => 'mEq/L',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 98, 'ref_max' => 107],
                ],
            ],
            [
                'name' => 'Bicarbonate (HCO₃⁻)',
                'unit' => 'mEq/L',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 22, 'ref_max' => 29],
                ],
            ],
            [
                'name' => 'Serum Calcium',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 8.5, 'ref_max' => 10.5],
                ],
            ],
            [
                'name' => 'Serum Magnesium',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 1.7, 'ref_max' => 2.2],
                ],
            ],
            [
                'name' => 'Serum Phosphorus',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 2.5, 'ref_max' => 4.5],
                ],
            ],

            // ── HORMONES & ENDOCRINOLOGY ──────────────────────────────────
            [
                'name' => 'Thyroid Stimulating Hormone (TSH)',
                'unit' => 'µIU/mL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => 18, 'age_max' => null, 'ref_min' => 0.40, 'ref_max' => 4.00],
                    ['gender' => 'any', 'age_min' => 0,  'age_max' => 17,   'ref_min' => 0.70, 'ref_max' => 6.40],
                ],
            ],
            [
                'name' => 'Free Triiodothyronine (Free T3)',
                'unit' => 'pg/mL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 2.3, 'ref_max' => 4.2],
                ],
            ],
            [
                'name' => 'Free Thyroxine (Free T4)',
                'unit' => 'ng/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0.8, 'ref_max' => 1.8],
                ],
            ],
            [
                'name' => 'Total Triiodothyronine (T3)',
                'unit' => 'ng/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 80, 'ref_max' => 200],
                ],
            ],
            [
                'name' => 'Total Thyroxine (T4)',
                'unit' => 'µg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 5.1, 'ref_max' => 14.1],
                ],
            ],

            // ── COAGULATION ───────────────────────────────────────────────
            [
                'name' => 'Prothrombin Time (PT)',
                'unit' => 'seconds',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 11.0, 'ref_max' => 13.5],
                ],
            ],
            [
                'name' => 'Activated Partial Thromboplastin Time (APTT)',
                'unit' => 'seconds',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 25, 'ref_max' => 35],
                ],
            ],
            [
                'name' => 'International Normalised Ratio (INR)',
                'unit' => null,
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0.8, 'ref_max' => 1.2],
                ],
            ],
            [
                'name' => 'Fibrinogen',
                'unit' => 'mg/dL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 200, 'ref_max' => 400],
                ],
            ],

            // ── IMMUNOLOGY & SEROLOGY (text results) ──────────────────────
            [
                'name' => 'HIV 1 & 2 Antibody',
                'unit' => null,
                'sample_type' => 'blood',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'HBsAg (Hepatitis B Surface Antigen)',
                'unit' => null,
                'sample_type' => 'blood',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'Anti-HCV (Hepatitis C Antibody)',
                'unit' => null,
                'sample_type' => 'blood',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'VDRL (Syphilis Screening)',
                'unit' => null,
                'sample_type' => 'blood',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'Widal Test – Salmonella Typhi O',
                'unit' => null,
                'sample_type' => 'blood',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'Widal Test – Salmonella Typhi H',
                'unit' => null,
                'sample_type' => 'blood',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'C-Reactive Protein (CRP)',
                'unit' => 'mg/L',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0, 'ref_max' => 5],
                ],
            ],
            [
                'name' => 'Rheumatoid Factor (RF)',
                'unit' => 'IU/mL',
                'sample_type' => 'blood',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0, 'ref_max' => 14],
                ],
            ],

            // ── URINALYSIS ────────────────────────────────────────────────
            [
                'name' => 'Urine Colour',
                'unit' => null,
                'sample_type' => 'urine',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'Urine Clarity',
                'unit' => null,
                'sample_type' => 'urine',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'Urine pH',
                'unit' => null,
                'sample_type' => 'urine',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 4.5, 'ref_max' => 8.0],
                ],
            ],
            [
                'name' => 'Urine Specific Gravity',
                'unit' => null,
                'sample_type' => 'urine',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 1.001, 'ref_max' => 1.030],
                ],
            ],
            [
                'name' => 'Urine Protein',
                'unit' => null,
                'sample_type' => 'urine',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'Urine Glucose',
                'unit' => null,
                'sample_type' => 'urine',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'Urine Ketones',
                'unit' => null,
                'sample_type' => 'urine',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'Urine Bilirubin',
                'unit' => null,
                'sample_type' => 'urine',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'Urine RBCs',
                'unit' => '/HPF',
                'sample_type' => 'urine',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0, 'ref_max' => 2],
                ],
            ],
            [
                'name' => 'Urine WBCs',
                'unit' => '/HPF',
                'sample_type' => 'urine',
                'data_type' => 'numeric',
                'status' => 'active',
                'ranges' => [
                    ['gender' => 'any', 'age_min' => null, 'age_max' => null, 'ref_min' => 0, 'ref_max' => 5],
                ],
            ],
            [
                'name' => 'Urine Epithelial Cells',
                'unit' => null,
                'sample_type' => 'urine',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
            [
                'name' => 'Urine Casts',
                'unit' => null,
                'sample_type' => 'urine',
                'data_type' => 'text',
                'status' => 'active',
                'ranges' => [],
            ],
        ];

        foreach ($tests as $data) {
            $ranges = $data['ranges'];
            unset($data['ranges']);

            $test = Test::firstOrCreate(['name' => $data['name']], $data);

            if ($test->wasRecentlyCreated && count($ranges) > 0) {
                $test->ranges()->createMany($ranges);
            }
        }
    }
}
