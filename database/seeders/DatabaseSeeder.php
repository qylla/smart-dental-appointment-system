<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\SymptomCheck;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        /* ---------------- Staff accounts ---------------- */

        $admin = User::create([
            'name'     => 'Clinic Front Desk',
            'email'    => 'admin@kpzdental.my',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '014-9153244',
        ]);

        $doctor = User::create([
            'name'           => 'Dr. Zuraida',
            'email'          => 'doctor@kpzdental.my',
            'password'       => Hash::make('password'),
            'role'           => 'doctor',
            'phone'          => '014-9153245',
            'specialisation' => 'General Dentistry',
        ]);

        /* ---------------- Patients ---------------- */

        $patients = collect([
            ['name' => 'Aisyah Rahman',   'email' => 'aisyah@example.com',  'phone' => '012-3456789'],
            ['name' => 'Daniel Tan',      'email' => 'daniel@example.com',  'phone' => '013-2345678'],
            ['name' => 'Siti Nurhaliza',  'email' => 'siti@example.com',    'phone' => '011-3456789'],
            ['name' => 'Kumar Selvam',    'email' => 'kumar@example.com',   'phone' => '016-4567890'],
            ['name' => 'Farah Iskandar',  'email' => 'farah@example.com',   'phone' => '017-5678901'],
        ])->map(fn ($p) => User::create([
            'name'     => $p['name'],
            'email'    => $p['email'],
            'password' => Hash::make('password'),
            'role'     => 'patient',
            'phone'    => $p['phone'],
        ]));

        /* ---------------- Today's appointments ---------------- */

        $cases = [
            [
                'patient'  => $patients[0],
                'time'     => '09:00',
                'symptoms' => ['Toothache', 'Tooth sensitivity'],
                'desc'     => 'Sakit bila minum air sejuk, dah 3 hari. Tak teruk sangat tapi menganggu masa makan.',
                'severity' => 'routine',
                'verdict'  => 'Routine — regular slot is fine',
                'advice'   => 'Sensitivity with cold triggers, no swelling reported.',
                'status'   => 'completed',
            ],
            [
                'patient'  => $patients[1],
                'time'     => '10:00',
                'symptoms' => ['Broken / knocked-out tooth', 'Severe pain'],
                'desc'     => 'Fell off my bike this morning and chipped my front tooth badly. Pain is sharp and constant.',
                'severity' => 'critical',
                'verdict'  => 'Critical — see as soon as possible',
                'advice'   => 'Trauma with fracture and severe pain. Flagged for earliest available slot.',
                'status'   => 'pending',
            ],
            [
                'patient'  => $patients[2],
                'time'     => '11:00',
                'symptoms' => ['Just a check-up'],
                'desc'     => 'Nak buat pemeriksaan rutin je, dah lebih 6 bulan tak datang.',
                'severity' => 'routine',
                'verdict'  => 'Routine — regular slot is fine',
                'advice'   => 'No active complaints. Standard recall appointment.',
                'status'   => 'confirmed',
            ],
            [
                'patient'  => $patients[3],
                'time'     => '14:00',
                'symptoms' => ['Swollen gums', 'Bleeding gums'],
                'desc'     => 'Gusi bengkak sebelah kanan bawah, berdarah bila berus gigi. Dah seminggu makin teruk.',
                'severity' => 'urgent',
                'verdict'  => 'Urgent — best seen within a day or two',
                'advice'   => 'Swelling with persistent bleeding over a week. Possible periodontal infection.',
                'status'   => 'confirmed',
            ],
            [
                'patient'  => $patients[4],
                'time'     => '15:00',
                'symptoms' => ['Bleeding gums'],
                'desc'     => 'Gusi berdarah setiap kali berus, especially belakang. No pain though.',
                'severity' => 'urgent',
                'verdict'  => 'Urgent — best seen within a day or two',
                'advice'   => 'Persistent bleeding without pain. Likely gingivitis, needs assessment.',
                'status'   => 'pending',
            ],
        ];

        foreach ($cases as $case) {
            $check = SymptomCheck::create([
                'user_id'     => $case['patient']->id,
                'symptoms'    => $case['symptoms'],
                'description' => $case['desc'],
                'severity'    => $case['severity'],
                'verdict'     => $case['verdict'],
                'advice'      => $case['advice'],
            ]);

            $appointment = Appointment::create([
                'user_id'          => $case['patient']->id,
                'doctor_id'        => $doctor->id,
                'symptom_check_id' => $check->id,
                'appointment_date' => today(),
                'appointment_time' => $case['time'],
                'status'           => $case['status'],
                'urgency'          => $case['severity'],
            ]);

            if ($case['status'] === 'completed') {
                Consultation::create([
                    'appointment_id' => $appointment->id,
                    'doctor_id'      => $doctor->id,
                    'diagnosis'      => 'Dentine hypersensitivity, tooth 24',
                    'treatment'      => 'Desensitising varnish applied. Advised sensitive-teeth toothpaste.',
                    'followup'       => '6m',
                    'outcome'        => 'resolved',
                ]);
            }
        }

        /* ---------------- Past visits, for patient history ---------------- */

        $history = [
            ['patient' => $patients[0], 'days' => 220, 'time' => '10:00', 'diag' => 'Mild gingivitis',            'treat' => 'Scaling and polishing. Advised better flossing.',        'out' => 'resolved'],
            ['patient' => $patients[0], 'days' => 380, 'time' => '11:00', 'diag' => 'Occlusal caries, tooth 26',  'treat' => 'Composite filling placed. No complications.',            'out' => 'resolved'],
            ['patient' => $patients[3], 'days' => 90,  'time' => '09:00', 'diag' => 'Moderate periodontitis',     'treat' => 'Deep scaling, lower right quadrant. Pockets 4-5mm.',     'out' => 'ongoing'],
            ['patient' => $patients[3], 'days' => 200, 'time' => '14:00', 'diag' => 'Gingival inflammation',      'treat' => 'Chlorhexidine mouthwash prescribed.',                    'out' => 'resolved'],
            ['patient' => $patients[2], 'days' => 150, 'time' => '15:00', 'diag' => 'Irreversible pulpitis',      'treat' => 'Root canal treatment completed. Crown placed.',          'out' => 'resolved'],
        ];

        foreach ($history as $h) {
            $appointment = Appointment::create([
                'user_id'          => $h['patient']->id,
                'doctor_id'        => $doctor->id,
                'appointment_date' => today()->subDays($h['days']),
                'appointment_time' => $h['time'],
                'status'           => 'completed',
                'urgency'          => 'routine',
            ]);

            Consultation::create([
                'appointment_id' => $appointment->id,
                'doctor_id'      => $doctor->id,
                'diagnosis'      => $h['diag'],
                'treatment'      => $h['treat'],
                'followup'       => 'none',
                'outcome'        => $h['out'],
            ]);
        }

        $this->command->info('Seeded: 1 admin, 1 doctor, 5 patients, 10 appointments.');
        $this->command->info('Login with any account using password: password');
    }
}