<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RenderDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding Render PostgreSQL Database...');
        
        // ========== SEED COURSES (10 records) ==========
        $this->command->info('📚 Seeding courses...');
        
        $courses = [
            [
                'course_name' => 'Computer Science',
                'code' => 'CS101',
                'description' => 'Bachelor of Science in Computer Science - Programming, Algorithms, and Software Development',
                'credits' => 120,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_name' => 'Business Administration',
                'code' => 'BA101',
                'description' => 'Bachelor of Business Administration - Management, Marketing, and Finance',
                'credits' => 120,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_name' => 'Information Technology',
                'code' => 'IT101',
                'description' => 'Bachelor of Information Technology - Networks, Security, and Systems',
                'credits' => 120,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_name' => 'Accounting',
                'code' => 'ACC101',
                'description' => 'Bachelor of Accounting - Financial Reporting, Audit, and Taxation',
                'credits' => 120,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_name' => 'Civil Engineering',
                'code' => 'ENG101',
                'description' => 'Bachelor of Civil Engineering - Structures, Construction, and Infrastructure',
                'credits' => 150,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_name' => 'Medicine',
                'code' => 'MED101',
                'description' => 'Bachelor of Medicine and Surgery - Clinical Practice and Healthcare',
                'credits' => 180,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_name' => 'Law',
                'code' => 'LAW101',
                'description' => 'Bachelor of Laws - Legal Studies, Advocacy, and Jurisprudence',
                'credits' => 150,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_name' => 'Education',
                'code' => 'EDU101',
                'description' => 'Bachelor of Education - Teaching, Curriculum, and Educational Psychology',
                'credits' => 120,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_name' => 'Nursing',
                'code' => 'NUR101',
                'description' => 'Bachelor of Nursing - Patient Care, Health Assessment, and Clinical Practice',
                'credits' => 120,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_name' => 'Architecture',
                'code' => 'ARC101',
                'description' => 'Bachelor of Architecture - Design, Planning, and Building Construction',
                'credits' => 150,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($courses as $course) {
            $exists = DB::table('courses')->where('code', $course['code'])->exists();
            if (!$exists) {
                DB::table('courses')->insert($course);
            }
        }
        
        $this->command->info('✅ ' . count($courses) . ' courses seeded successfully!');

        // ========== GET COURSE IDs ==========
        $courseIds = DB::table('courses')->pluck('id')->toArray();
        
        if (empty($courseIds)) {
            $this->command->error('❌ No courses found. Please check your courses table.');
            return;
        }

        // ========== SEED STUDENTS (10 records) ==========
        $this->command->info('👨‍🎓 Seeding students...');

        $students = [
            [
                'admission_number' => 'ADM-2024-001',
                'first_name' => 'Collins',
                'last_name' => 'Owanda',
                'email' => 'collins.owanda@example.com',
                'phone' => '+254712345678',
                'address' => '123 Unity Road, Nairobi, Kenya',
                'class_id' => null,
                'course_id' => $courseIds[0] ?? 1,
                'status' => 'active',
                'registration_number' => 'REG-2024-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admission_number' => 'ADM-2024-002',
                'first_name' => 'Grace',
                'last_name' => 'Mwangi',
                'email' => 'grace.mwangi@example.com',
                'phone' => '+254723456789',
                'address' => '456 Peace Avenue, Mombasa, Kenya',
                'class_id' => null,
                'course_id' => $courseIds[1] ?? 2,
                'status' => 'active',
                'registration_number' => 'REG-2024-002',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admission_number' => 'ADM-2024-003',
                'first_name' => 'Peter',
                'last_name' => 'Ochieng',
                'email' => 'peter.ochieng@example.com',
                'phone' => '+254734567890',
                'address' => '789 Freedom Street, Kisumu, Kenya',
                'class_id' => null,
                'course_id' => $courseIds[2] ?? 3,
                'status' => 'active',
                'registration_number' => 'REG-2024-003',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admission_number' => 'ADM-2024-004',
                'first_name' => 'Sarah',
                'last_name' => 'Kariuki',
                'email' => 'sarah.kariuki@example.com',
                'phone' => '+254745678901',
                'address' => '321 Hope Lane, Nakuru, Kenya',
                'class_id' => null,
                'course_id' => $courseIds[3] ?? 4,
                'status' => 'active',
                'registration_number' => 'REG-2024-004',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admission_number' => 'ADM-2024-005',
                'first_name' => 'James',
                'last_name' => 'Odhiambo',
                'email' => 'james.odhiambo@example.com',
                'phone' => '+254756789012',
                'address' => '654 Vision Road, Eldoret, Kenya',
                'class_id' => null,
                'course_id' => $courseIds[4] ?? 5,
                'status' => 'active',
                'registration_number' => 'REG-2024-005',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admission_number' => 'ADM-2024-006',
                'first_name' => 'Mary',
                'last_name' => 'Akinyi',
                'email' => 'mary.akinyi@example.com',
                'phone' => '+254767890123',
                'address' => '987 Unity Drive, Thika, Kenya',
                'class_id' => null,
                'course_id' => $courseIds[5] ?? 6,
                'status' => 'active',
                'registration_number' => 'REG-2024-006',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admission_number' => 'ADM-2024-007',
                'first_name' => 'David',
                'last_name' => 'Mutua',
                'email' => 'david.mutua@example.com',
                'phone' => '+254778901234',
                'address' => '159 Progress Street, Malindi, Kenya',
                'class_id' => null,
                'course_id' => $courseIds[6] ?? 7,
                'status' => 'active',
                'registration_number' => 'REG-2024-007',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admission_number' => 'ADM-2024-008',
                'first_name' => 'Lucy',
                'last_name' => 'Kamau',
                'email' => 'lucy.kamau@example.com',
                'phone' => '+254789012345',
                'address' => '753 Success Avenue, Meru, Kenya',
                'class_id' => null,
                'course_id' => $courseIds[7] ?? 8,
                'status' => 'active',
                'registration_number' => 'REG-2024-008',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admission_number' => 'ADM-2024-009',
                'first_name' => 'Michael',
                'last_name' => 'Omondi',
                'email' => 'michael.omondi@example.com',
                'phone' => '+254790123456',
                'address' => '951 Innovation Road, Nyeri, Kenya',
                'class_id' => null,
                'course_id' => $courseIds[8] ?? 9,
                'status' => 'active',
                'registration_number' => 'REG-2024-009',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admission_number' => 'ADM-2024-010',
                'first_name' => 'Esther',
                'last_name' => 'Wanjiru',
                'email' => 'esther.wanjiru@example.com',
                'phone' => '+254701234567',
                'address' => '357 Resilience Lane, Kitale, Kenya',
                'class_id' => null,
                'course_id' => $courseIds[9] ?? 10,
                'status' => 'active',
                'registration_number' => 'REG-2024-010',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($students as $student) {
            $exists = DB::table('students')->where('admission_number', $student['admission_number'])->exists();
            if (!$exists) {
                DB::table('students')->insert($student);
            }
        }

        $this->command->info('✅ ' . count($students) . ' students seeded successfully!');

        // ========== SEED FEES (10 records) ==========
        $this->command->info('💰 Seeding fees...');

        $studentIds = DB::table('students')->pluck('id')->toArray();
        
        if (empty($studentIds)) {
            $this->command->warn('⚠️ No students found. Skipping fees.');
            return;
        }

        $feeStatuses = ['paid', 'unpaid', 'partial'];
        $feeTypes = ['Tuition', 'Registration', 'Examination', 'Library', 'Sports', 'Laboratory'];
        $paymentMethods = ['Cash', 'Bank Transfer', 'Cheque', 'M-Pesa', 'Credit Card'];

        $feeCounter = 0;
        $targetFees = 10;
        
        // Create fees for students
        foreach ($studentIds as $studentId) {
            if ($feeCounter >= $targetFees) break;
            
            $status = $feeStatuses[array_rand($feeStatuses)];
            $amount = rand(5000, 50000);
            $paymentDate = $status === 'paid' 
                ? Carbon::now()->subDays(rand(1, 365))->format('Y-m-d')
                : null;
            $dueDate = Carbon::now()->addDays(rand(1, 60))->format('Y-m-d');
            
            $feeData = [
                'student_id' => $studentId,
                'amount' => $amount,
                'payment_date' => $paymentDate,
                'due_date' => $dueDate,
                'status' => $status,
                'term' => 'Term ' . rand(1, 3) . ' ' . date('Y'),
                'academic_year' => date('Y'),
                'payment_method' => $status === 'paid' ? $paymentMethods[array_rand($paymentMethods)] : null,
                'fee_type' => $feeTypes[array_rand($feeTypes)],
                'description' => 'Fee payment for student',
                'receipt_no' => 'RCP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'paid_at' => $status === 'paid' ? now() : null,
                'mpesa_phone' => null,
                'mpesa_transaction_code' => null,
                'mpesa_checkout_request_id' => null,
                'mpesa_result_code' => null,
                'mpesa_response' => null,
                'account_reference' => null,
                'mpesa_result_desc' => null,
                'completed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            DB::table('fees')->insert($feeData);
            $feeCounter++;
        }

        // If less than 10 fees were created, add more
        if ($feeCounter < $targetFees && count($studentIds) > 0) {
            $remaining = $targetFees - $feeCounter;
            for ($i = 0; $i < $remaining; $i++) {
                $studentId = $studentIds[array_rand($studentIds)];
                $status = $feeStatuses[array_rand($feeStatuses)];
                $amount = rand(5000, 50000);
                $paymentDate = $status === 'paid' 
                    ? Carbon::now()->subDays(rand(1, 365))->format('Y-m-d')
                    : null;
                $dueDate = Carbon::now()->addDays(rand(1, 60))->format('Y-m-d');
                
                $feeData = [
                    'student_id' => $studentId,
                    'amount' => $amount,
                    'payment_date' => $paymentDate,
                    'due_date' => $dueDate,
                    'status' => $status,
                    'term' => 'Term ' . rand(1, 3) . ' ' . date('Y'),
                    'academic_year' => date('Y'),
                    'payment_method' => $status === 'paid' ? $paymentMethods[array_rand($paymentMethods)] : null,
                    'fee_type' => $feeTypes[array_rand($feeTypes)],
                    'description' => 'Additional fee payment',
                    'receipt_no' => 'RCP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'paid_at' => $status === 'paid' ? now() : null,
                    'mpesa_phone' => null,
                    'mpesa_transaction_code' => null,
                    'mpesa_checkout_request_id' => null,
                    'mpesa_result_code' => null,
                    'mpesa_response' => null,
                    'account_reference' => null,
                    'mpesa_result_desc' => null,
                    'completed_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                DB::table('fees')->insert($feeData);
                $feeCounter++;
            }
        }

        $this->command->info('✅ ' . $feeCounter . ' fees seeded successfully!');
        $this->command->info('🎉 Database seeding completed successfully!');
        
        // Show summary
        $this->command->info('📊 Summary:');
        $this->command->info('   - Courses: ' . DB::table('courses')->count());
        $this->command->info('   - Students: ' . DB::table('students')->count());
        $this->command->info('   - Fees: ' . DB::table('fees')->count());
    }
}