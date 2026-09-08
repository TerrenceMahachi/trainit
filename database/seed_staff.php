<?php
require __DIR__ . '/../bootstrap.php';
use App\Models\User;
use App\Models\Login;
use App\Models\Staffprofile;

// Ensure User 1 has a Staff Profile
$adminUser = User::find(1);
if ($adminUser) {
    $existing = Staffprofile::where('user', 1);
    if (empty($existing)) {
        $p = new Staffprofile();
        $p->user = 1;
        $p->employee_number = 'TRN-001';
        $p->job_title = 'System Administrator';
        $p->department = 'Information Technology';
        $p->nature_of_employment = 'ORDINARY';
        $p->employee_type = 'Executive';
        $p->date_of_employment = '2026-08-01';
        $p->station = 'Harare HQ';
        $p->employment_status = 'active';
        $p->title = 'Mr';
        $p->first_name = 'System';
        $p->surname = 'Administrator';
        $p->work_email = 'admin@trainit.co.zw';
        $p->town = 'HARARE';
        $p->region = 'HRE';
        $p->reg_by = 1;
        $p->save();
        echo "Created staff profile for User 1\n";
    }
}

// Seed Staff from P4 Example if not present
$staffData = [
    [
        'email' => 'hchibvura@trainit.co.zw',
        'name' => 'Happymore Chibvura',
        'role' => 1,
        'title' => 'MR',
        'first_name' => 'HAPPYMORE',
        'other_names' => '',
        'surname' => 'CHIBVURA',
        'national_id' => '34-107299A34',
        'ssr_number' => '9612194AA',
        'dob' => '1993-11-16',
        'job_title' => 'FOUNDER',
        'department' => 'Executive',
        'station' => 'Harare HQ',
        'date_emp' => '2024-06-07',
        'emp_number' => 'TRN-002',
        'street' => 'NKWISI GARDENS',
        'suburb' => 'TYNWALD',
        'town' => 'HARARE',
        'region' => 'HRE'
    ],
    [
        'email' => 'tmahachi@trainit.co.zw',
        'name' => 'Terrence Mahachi',
        'role' => 1,
        'title' => 'MR',
        'first_name' => 'TERRENCE',
        'other_names' => '',
        'surname' => 'MAHACHI',
        'national_id' => '63-1267948M07',
        'ssr_number' => '6738625AA',
        'dob' => '1986-07-15',
        'drivers_licence' => '12842JC',
        'passport' => 'AE671950',
        'job_title' => 'HEAD OF IT',
        'department' => 'Information Technology',
        'station' => 'Harare HQ',
        'date_emp' => '2026-08-01',
        'emp_number' => 'TRN-003',
        'street' => 'Darling Close',
        'suburb' => 'Hatfield',
        'town' => 'HARARE',
        'region' => 'HRE'
    ],
    [
        'email' => 'mchapisa@trainit.co.zw',
        'name' => 'Michael Ngaakudzwe Chapisa',
        'role' => 1,
        'title' => 'MR',
        'first_name' => 'MICHAEL',
        'other_names' => 'NGAAKUDZWE',
        'surname' => 'CHAPISA',
        'national_id' => '63-2317565A42',
        'ssr_number' => '4786738AA',
        'dob' => '2003-03-17',
        'drivers_licence' => 'AA00109163',
        'passport' => 'FN991839',
        'job_title' => 'ASSISTANT HEAD OF IT',
        'department' => 'Information Technology',
        'station' => 'Harare HQ',
        'date_emp' => '2026-08-01',
        'emp_number' => 'TRN-004',
        'street' => 'Fidelity Life Park',
        'suburb' => 'Manresa',
        'town' => 'HARARE',
        'region' => 'HRE'
    ]
];

foreach ($staffData as $s) {
    $existing = User::where('email', $s['email']);
    if (empty($existing)) {
        $u = new User();
        $u->name = $s['name'];
        $u->email = $s['email'];
        $u->role = $s['role'];
        $u->reg_by = 1;
        $u->status = 1;
        $u->save();

        $l = new Login();
        $l->user = $u->iD;
        $l->reg_by = 1;
        $l->password = password_hash('Trainit2026!', PASSWORD_BCRYPT);
        $l->status = 1;
        $l->save();

        $p = new Staffprofile();
        $p->user = $u->iD;
        $p->employee_number = $s['emp_number'];
        $p->job_title = $s['job_title'];
        $p->department = $s['department'];
        $p->nature_of_employment = 'ORDINARY';
        $p->employee_type = 'Employee';
        $p->date_of_employment = $s['date_emp'];
        $p->station = $s['station'];
        $p->employment_status = 'active';
        $p->title = $s['title'];
        $p->first_name = $s['first_name'];
        $p->other_names = $s['other_names'];
        $p->surname = $s['surname'];
        $p->national_id_number = $s['national_id'];
        $p->ssr_number = $s['ssr_number'];
        $p->drivers_licence_number = $s['drivers_licence'] ?? null;
        $p->passport_number = $s['passport'] ?? null;
        $p->date_of_birth = $s['dob'];
        $p->street_name = $s['street'];
        $p->suburb = $s['suburb'];
        $p->town = $s['town'];
        $p->region = $s['region'];
        $p->country = 'Zimbabwe';
        $p->work_email = $s['email'];
        $p->reg_by = 1;
        $p->save();

        echo "Seeded staff record for {$s['name']} ({$s['email']})\n";
    } else {
        echo "User already exists for {$s['email']}\n";
    }
}
