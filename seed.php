<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Seeding database...\n";

// Insert Courses
$courses = [
    ['course_name' => 'Computer Science', 'description' => 'Bachelor of Science in Computer Science', 'credits' => 120, 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['course_name' => 'Business Administration', 'description' => 'Bachelor of Business Administration', 'credits' => 120, 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['course_name' => 'Information Technology', 'description' => 'Bachelor of Information Technology', 'credits' => 120, 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['course_name' => 'Accounting', 'description' => 'Bachelor of Accounting', 'credits' => 120, 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['course_name' => 'Civil Engineering', 'description' => 'Bachelor of Civil Engineering', 'credits' => 150, 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
];

foreach ($courses as $course) {
    DB::table('courses')->insert($course);
}
echo "Courses inserted!\n";

// Get course IDs
$courseIds = DB::table('courses')->pluck('id')->toArray();

// Insert Students
$students = [
    ['admission_number' => 'ADM-2024-001', 'first_name' => 'Collins', 'last_name' => 'Owanda', 'email' => 'collins@example.com', 'phone' => '0712345678', 'address' => 'Nairobi', 'course_id' => $courseIds[0], 'status' => 'active', 'registration_number' => 'REG-2024-001', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['admission_number' => 'ADM-2024-002', 'first_name' => 'Grace', 'last_name' => 'Mwangi', 'email' => 'grace@example.com', 'phone' => '0723456789', 'address' => 'Mombasa', 'course_id' => $courseIds[1], 'status' => 'active', 'registration_number' => 'REG-2024-002', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['admission_number' => 'ADM-2024-003', 'first_name' => 'Peter', 'last_name' => 'Ochieng', 'email' => 'peter@example.com', 'phone' => '0734567890', 'address' => 'Kisumu', 'course_id' => $courseIds[2], 'status' => 'active', 'registration_number' => 'REG-2024-003', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['admission_number' => 'ADM-2024-004', 'first_name' => 'Sarah', 'last_name' => 'Kariuki', 'email' => 'sarah@example.com', 'phone' => '0745678901', 'address' => 'Nakuru', 'course_id' => $courseIds[3], 'status' => 'active', 'registration_number' => 'REG-2024-004', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['admission_number' => 'ADM-2024-005', 'first_name' => 'James', 'last_name' => 'Odhiambo', 'email' => 'james@example.com', 'phone' => '0756789012', 'address' => 'Eldoret', 'course_id' => $courseIds[4], 'status' => 'active', 'registration_number' => 'REG-2024-005', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
];

foreach ($students as $student) {
    DB::table('students')->insert($student);
}
echo "Students inserted!\n";

// Get student IDs
$studentIds = DB::table('students')->pluck('id')->toArray();

// Insert Fees
$fees = [
    ['student_id' => $studentIds[0], 'amount' => 25000, 'payment_date' => date('Y-m-d', strtotime('-5 days')), 'due_date' => date('Y-m-d', strtotime('+30 days')), 'status' => 'paid', 'term' => 'Term 1 2024', 'academic_year' => '2024', 'payment_method' => 'M-Pesa', 'fee_type' => 'Tuition', 'receipt_no' => 'RCP-001', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['student_id' => $studentIds[1], 'amount' => 15000, 'payment_date' => null, 'due_date' => date('Y-m-d', strtotime('+45 days')), 'status' => 'pending', 'term' => 'Term 1 2024', 'academic_year' => '2024', 'payment_method' => null, 'fee_type' => 'Registration', 'receipt_no' => 'RCP-002', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['student_id' => $studentIds[2], 'amount' => 12000, 'payment_date' => null, 'due_date' => date('Y-m-d', strtotime('-10 days')), 'status' => 'overdue', 'term' => 'Term 1 2024', 'academic_year' => '2024', 'payment_method' => null, 'fee_type' => 'Library', 'receipt_no' => 'RCP-003', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['student_id' => $studentIds[3], 'amount' => 5000, 'payment_date' => date('Y-m-d', strtotime('-15 days')), 'due_date' => date('Y-m-d', strtotime('+15 days')), 'status' => 'paid', 'term' => 'Term 1 2024', 'academic_year' => '2024', 'payment_method' => 'Cash', 'fee_type' => 'Examination', 'receipt_no' => 'RCP-004', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['student_id' => $studentIds[4], 'amount' => 8000, 'payment_date' => date('Y-m-d', strtotime('-3 days')), 'due_date' => date('Y-m-d', strtotime('+60 days')), 'status' => 'paid', 'term' => 'Term 1 2024', 'academic_year' => '2024', 'payment_method' => 'Bank Transfer', 'fee_type' => 'Sports', 'receipt_no' => 'RCP-005', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
];

foreach ($fees as $fee) {
    DB::table('fees')->insert($fee);
}
echo "Fees inserted!\n";

echo "✅ Database seeded successfully!\n";