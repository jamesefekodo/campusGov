<?php

// require __DIR__.'/vendor/autoload.php';

// $app = require_once __DIR__.'/bootstrap/app.php';
// $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
// $kernel->bootstrap();

// use Illuminate\Support\Facades\DB;

// $path = '/home/transcend/Downloads/student_personal_profiles.csv';

// if (!file_exists($path)) {
//     echo "File not found at: {$path}\n";
//     exit;
// }

// echo "Starting direct student-to-member profile import and transformation...\n";

// $file = fopen($path, 'r');
// $header = fgetcsv($file);
// $batch = [];
// $count = 0;

// while (($row = fgetcsv($file)) !== false) {
//     $data = array_combine($header, $row);

//     // 1. Map verification status (e.g., 'verified' -> true, anything else -> false)
//     $verificationStatus = strtolower($data['verification_status'] ?? 'unverified');
//     $isVerified = in_array($verificationStatus, ['verified', 'active', '1', 'true']);

//     // 2. Safeguard foreign keys or unique strings referencing users table
//     $username = $data['username'] ?? null;
//     $phone1 = $data['phone1'] ?? null;
//     $email1 = $data['email1'] ?? null;

//     if (empty($username) || empty($phone1) || empty($email1)) {
//         continue; // Skip rows lacking required user relational mappings
//     }

//     $batch[] = [
//         'tenant_id' => 1, // Explicitly assigning tenant id 1
//         'username' => $username,
//         'phone1' => $phone1,
//         'email1' => $email1,
//         'phone2' => $data['phone2'] ?? null,
//         'email2' => $data['email2'] ?? null,
//         'first_name' => $data['first_name'] ?? null,
//         'middle_name' => $data['middle_name'] ?? null,
//         'last_name' => $data['last_name'] ?? null,
//         'gender' => $data['gender'] ?? null,
//         'dob' => !empty($data['dob']) ? $data['dob'] : null,
//         'nationality' => $data['nationality'] ?? null,
//         'state_of_origin' => $data['state_of_origin'] ?? null,
//         'lga_of_origin' => $data['lga_of_origin'] ?? null,
//         'address1' => $data['address1'] ?? null,
//         'address2' => $data['address2'] ?? null,
//         'is_verified' => $isVerified,
//         'verified_by' => $data['verified_by'] ?? null, // Will map to username if present in users table
//         'verified_at' => $isVerified ? ($data['updated_at'] ?? now()) : null,
//         'created_at' => $data['created_at'] ?? now(),
//         'updated_at' => $data['updated_at'] ?? now(),
//     ];

//     $count++;

//     if (count($batch) >= 500) {
//         DB::table('member_personal_profiles')->insertOrIgnore($batch);
//         $batch = [];
//     }
// }

// if (!empty($batch)) {
//     DB::table('member_personal_profiles')->insertOrIgnore($batch);
// }

// fclose($file);
// echo "Successfully imported and transformed {$count} member profiles!\n";







require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$path = '/home/transcend/Downloads/student_academic_info.csv';

if (!file_exists($path)) {
    echo "File not found at: {$path}\n";
    exit;
}

echo "Starting direct student-to-member academic info import (ignoring department & session)... \n";

// Disable foreign key checks temporarily to bypass constraint silence drops
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

$file = fopen($path, 'r');
$header = fgetcsv($file);
$batch = [];
$count = 0;

$defaultFaculty = DB::table('faculties')->value('slug');

while (($row = fgetcsv($file)) !== false) {
    if (count($header) !== count($row)) {
        continue; // Skip malformed rows
    }

    $data = array_combine($header, $row);

    $username = $data['username'] ?? null;
    if (empty($username)) {
        continue;
    }

    $verificationStatus = strtolower($data['verification_status'] ?? 'unverified');
    $isVerified = in_array($verificationStatus, ['verified', 'active', '1', 'true']);
    $academicStatus = !empty($data['affiliation_status']) ? strtolower($data['affiliation_status']) : 'student';

    $batch[] = [
        'tenant_id' => 1,
        'username' => $username,
        'academic_status' => $academicStatus,
        'program' => $data['current_program'] ?? null,
        'program_type' => null,
        'faculty' => $defaultFaculty,
        'department' => null, // Ignored
        'admission_session' => null, // Left blank as requested
        'graduation_session' => null, // Left blank as requested
        'matric_number' => $data['nd_matric_number'] ?? $data['hnd_matric_number'] ?? null,
        'is_verified' => $isVerified,
        'verified_by' => null,
        'verified_at' => $isVerified ? ($data['updated_at'] ?? now()) : null,
        'created_at' => $data['created_at'] ?? now(),
        'updated_at' => $data['updated_at'] ?? now(),
    ];

    $count++;

    if (count($batch) >= 500) {
        DB::table('member_academic_infos')->insertOrIgnore($batch);
        $batch = [];
    }
}

if (!empty($batch)) {
    DB::table('member_academic_infos')->insertOrIgnore($batch);
}

fclose($file);

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Successfully imported and inserted {$count} member academic records!\n";
