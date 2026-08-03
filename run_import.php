<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$path = '/home/transcend/Downloads/users.csv';

if (!file_exists($path)) {
    echo "File not found at: {$path}\n";
    exit;
}

echo "Starting direct CSV import...\n";

$file = fopen($path, 'r');
$header = fgetcsv($file);
$batch = [];
$count = 0;

while (($row = fgetcsv($file)) !== false) {
    $data = array_combine($header, $row);

    $rawAccountType = isset($data['account_type']) ? (int) $data['account_type'] : 2;
    $mappedAccountType = match ($rawAccountType) {
        3, 4 => 1,
        default => $rawAccountType,
    };

    $username = !empty($data['username']) ? $data['username'] : 'user_' . Str::random(8);
    $email = !empty($data['email']) ? $data['email'] : 'noemail_' . Str::random(8) . '@example.com';
    $phone = !empty($data['phone']) ? $data['phone'] : null;

    $batch[] = [
        'tenant_id' => 1,
        'uuid' => (string) Str::uuid(),
        'username' => $username,
        'account_type' => $mappedAccountType,
        'account_status' => isset($data['account_status']) ? (int) $data['account_status'] : 0,
        'phone' => $phone,
        'photo' => $data['photo'] ?? null,
        'email' => $email,
        'require_login_mfa' => filter_var($data['require_login_mfa'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'email_verified_at' => !empty($data['email_verified_at']) ? $data['email_verified_at'] : null,
        'email_verification' => filter_var($data['email_verification'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'email_verification_code' => $data['email_verification_code'] ?? null,
        'email_verification_code_expires_at' => !empty($data['email_verification_code_expires_at']) ? $data['email_verification_code_expires_at'] : null,
        'admin_privilege' => filter_var($data['admin_privilege'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'password' => $data['password'],
        'remember_token' => $data['remember_token'] ?? null,
        'created_at' => $data['created_at'] ?? now(),
        'updated_at' => $data['updated_at'] ?? now(),
    ];

    $count++;

    if (count($batch) >= 500) {
        DB::table('users')->insertOrIgnore($batch);
        $batch = [];
    }
}

if (!empty($batch)) {
    DB::table('users')->insertOrIgnore($batch);
}

fclose($file);
echo "Successfully imported {$count} users!\n";
