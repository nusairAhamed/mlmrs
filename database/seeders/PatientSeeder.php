<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\User;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::first()?->id ?? 1;

        $patients = [
            [
                'full_name' => 'Kamal Perera',
                'dob'       => '1985-03-12',
                'gender'    => 'male',
                'phone'     => '0771234501',
                'email'     => 'kamal.perera@gmail.com',
                'address'   => '45/A, Galle Road, Colombo 03',
            ],
            [
                'full_name' => 'Nimali Fernando',
                'dob'       => '1992-07-25',
                'gender'    => 'female',
                'phone'     => '0712345602',
                'email'     => 'nimali.fernando@yahoo.com',
                'address'   => '12, Kandy Road, Kurunegala',
            ],
            [
                'full_name' => 'Suresh Rajapaksa',
                'dob'       => '1978-11-04',
                'gender'    => 'male',
                'phone'     => '0751234503',
                'email'     => null,
                'address'   => '78, Temple Road, Gampaha',
            ],
            [
                'full_name' => 'Dilini Senanayake',
                'dob'       => '1990-05-18',
                'gender'    => 'female',
                'phone'     => '0761234504',
                'email'     => 'dilini.s@hotmail.com',
                'address'   => '23, Bauddaloka Mawatha, Colombo 07',
            ],
            [
                'full_name' => 'Chaminda Wickramasinghe',
                'dob'       => '1965-09-30',
                'gender'    => 'male',
                'phone'     => '0701234505',
                'email'     => null,
                'address'   => '5, Station Road, Matara',
            ],
            [
                'full_name' => 'Priyanka Jayawardena',
                'dob'       => '2000-02-14',
                'gender'    => 'female',
                'phone'     => '0771234506',
                'email'     => 'priyanka.jw@gmail.com',
                'address'   => '101, High Level Road, Nugegoda',
            ],
            [
                'full_name' => 'Roshan Bandara',
                'dob'       => '1955-06-22',
                'gender'    => 'male',
                'phone'     => '0712345607',
                'email'     => null,
                'address'   => '9, Raja Mawatha, Kandy',
            ],
            [
                'full_name' => 'Sandya Dissanayake',
                'dob'       => '1998-12-08',
                'gender'    => 'female',
                'phone'     => '0751234508',
                'email'     => 'sandya.d@gmail.com',
                'address'   => '67, Negombo Road, Wattala',
            ],
            [
                'full_name' => 'Lahiru Gunasekara',
                'dob'       => '1988-04-17',
                'gender'    => 'male',
                'phone'     => '0761234509',
                'email'     => 'lahiru.g@outlook.com',
                'address'   => '34, New Kandy Road, Kotte',
            ],
            [
                'full_name' => 'Madhavi Ranasinghe',
                'dob'       => '1975-08-03',
                'gender'    => 'female',
                'phone'     => '0701234510',
                'email'     => 'madhavi.r@gmail.com',
                'address'   => '15, Beach Road, Galle',
            ],
        ];

        $year = now()->format('Y');

        foreach ($patients as $i => $data) {
            if (!Patient::where('phone', $data['phone'])->exists()) {
                $code = sprintf('PAT-%s-%05d', $year, $i + 1);
                Patient::create(array_merge($data, [
                    'patient_code' => $code,
                    'created_by'   => $adminId,
                ]));
            }
        }
    }
}
