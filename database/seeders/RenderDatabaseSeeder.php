<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Students;
use App\Models\Fee;
use Illuminate\Database\Seeder;
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
                'name' => 'Computer Science',
                'code' => 'CS101',
                'description' => 'Bachelor of Science in Computer Science - Programming, Algorithms, and Software Development',
                'duration' => '4 years',
                'fee' => 55000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Business Administration',
                'code' => 'BA101',
                'description' => 'Bachelor of Business Administration - Management, Marketing, and Finance',
                'duration' => '4 years',
                'fee' => 45000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT101',
                'description' => 'Bachelor of Information Technology - Networks, Security, and Systems',
                'duration' => '4 years',
                'fee' => 48000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Accounting',
                'code' => 'ACC101',
                'description' => 'Bachelor of Accounting - Financial Reporting, Audit, and Taxation',
                'duration' => '4 years',
                'fee' => 42000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Civil Engineering',
                'code' => 'ENG101',
                'description' => 'Bachelor of Civil Engineering - Structures, Construction, and Infrastructure',
                'duration' => '5 years',
                'fee' => 58000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Medicine',
                'code' => 'MED101',
                'description' => 'Bachelor of Medicine and Surgery - Clinical Practice and Healthcare',
                'duration' => '6 years',
                'fee' => 65000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Law',
                'code' => 'LAW101',
                'description' => 'Bachelor of Laws - Legal Studies, Advocacy, and Jurisprudence',
                'duration' => '5 years',
                'fee' => 52000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Education',
                'code' => 'EDU101',
                'description' => 'Bachelor of Education - Teaching, Curriculum, and Educational Psychology',
                'duration' => '4 years',
                'fee' => 38000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nursing',
                'code' => 'NUR101',
                'description' => 'Bachelor of Nursing - Patient Care, Health Assessment, and Clinical Practice',
                'duration' => '4 years',
                'fee' => 48000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Architecture',
                'code' => 'ARC101',
                'description' => 'Bachelor of Architecture - Design, Planning, and Building Construction',
                'duration' => '5 years',
                'fee' => 54000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($courses as $courseData) {
            Course::updateOrCreate(
                ['code' => $courseData['code']],
                $courseData
            );
        }
        
        $this->command->info('✅ ' . count($courses) . ' courses seeded successfully!');

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
                'course_id' => 1,
                'status' => 'active',
                'registration_number' => 'REG-2024-001',
            ],
            [
                'admission_number' => 'ADM-2024-002',
                'first_name' => 'Grace',
                'last_name' => 'Mwangi',
                'email' => 'grace.mwangi@example.com',
                'phone' => '+254723456789',
                'address' => '456 Peace Avenue, Mombasa, Kenya',
                'course_id' => 2,
                'status' => 'active',
                'registration_number' => 'REG-2024-002',
            ],
            [
                'admission_number' => 'ADM-2024-003',
                'first_name' => 'Peter',
                'last_name' => 'Ochieng',
                'email' => 'peter.ochieng@example.com',
                'phone' => '+254734567890',
                'address' => '789 Freedom Street, Kisumu, Kenya',
                'course_id' => 3,
                'status' => 'active',
                'registration_number' => 'REG-2024-003',
            ],
            [
                'admission_number' => 'ADM-2024-004',
                'first_name' => 'Sarah',
                'last_name' => 'Kariuki',
                'email' => 'sarah.kariuki@example.com',
                'phone' => '+254745678901',
                'address' => '321 Hope Lane, Nakuru, Kenya',
                'course_id' => 4,
                'status' => 'active',
                'registration_number' => 'REG-2024-004',
            ],
            [
                'admission_number' => 'ADM-2024-005',
                'first_name' => 'James',
                'last_name' => 'Odhiambo',
                'email' => 'james.odhiambo@example.com',
                'phone' => '+254756789012',
                'address' => '654 Vision Road, Eldoret, Kenya',
                'course_id' => 5,
                'status' => 'active',
                'registration_number' => 'REG-2024-005',
            ],
            [
                'admission_number' => 'ADM-2024-006',
                'first_name' => 'Mary',
                'last_name' => 'Akinyi',
                'email' => 'mary.akinyi@example.com',
                'phone' => '+254767890123',
                'address' => '987 Unity Drive, Thika, Kenya',
                'course_id' => 6,
                'status' => 'active',
                'registration_number' => 'REG-2024-006',
            ],
            [
                'admission_number' => 'ADM-2024-007',
                'first_name' => 'David',
                'last_name' => 'Mutua',
                'email' => 'david.mutua@example.com',
                'phone' => '+254778901234',
                'address' => '159 Progress Street, Malindi, Kenya',
                'course_id' => 7,
                'status' => 'active',
                'registration_number' => 'REG-2024-007',
            ],
            [
                'admission_number' => 'ADM-2024-008',
                'first_name' => 'Lucy',
                'last_name' => 'Kamau',
                'email' => 'lucy.kamau@example.com',
                'phone' => '+254789012345',
                'address' => '753 Success Avenue, Meru, Kenya',
                'course_id' => 8,
                'status' => 'active',
                'registration_number' => 'REG-2024-008',
            ],
            [
                'admission_number' => 'ADM-2024-009',
                'first_name' => 'Michael',
                'last_name' => 'Omondi',
                'email' => 'michael.omondi@example.com',
                'phone' => '+254790123456',
                'address' => '951 Innovation Road, Nyeri, Kenya',
                'course_id' => 9,
                'status' => 'active',
                'registration_number' => 'REG-2024-009',
            ],
            [
                'admission_number' => 'ADM-2024-010',
                'first_name' => 'Esther',
                'last_name' => 'Wanjiru',
                'email' => 'esther.wanjiru@example.com',
                'phone' => '+254701234567',
                'address' => '357 Resilience Lane, Kitale, Kenya',
                'course_id' => 10,
                'status' => 'active',
                'registration_number' => 'REG-2024-010',
            ],
        ];

        foreach ($students as $studentData) {
            Students::updateOrCreate(
                ['admission_number' => $studentData['admission_number']],
                $studentData
            );
        }

        $this->command->info('✅ ' . count($students) . ' students seeded successfully!');

        // ========== SEED FEES (10 records) ==========
        $this->command->info('💰 Seeding fees...');

        $students = Students::all();
        
        if ($students->count() < 10) {
            $this->command->warn('⚠️ Less than 10 students found. Creating fees for available students.');
        }

        $feeStatuses = ['paid', 'pending', 'overdue'];
        $feeTypes = ['Tuition', 'Registration', 'Examination', 'Library', 'Sports', 'Laboratory'];
        $paymentMethods = ['Cash', 'Bank Transfer', 'Cheque', 'M-Pesa', 'Credit Card'];

        $feeCounter = 0;
        
        foreach ($students as $student) {
            // Create 1-2 fees per student
            $numFees = rand(1, 2);
            
            for ($i = 0; $i < $numFees && $feeCounter < 10; $i++) {
                $status = $feeStatuses[array_rand($feeStatuses)];
                $amount = rand(5000, 50000);
                $paymentDate = $status === 'paid' 
                    ? Carbon::now()->subDays(rand(1, 365)) 
                    : null;
                $dueDate = Carbon::now()->addDays(rand(1, 60));
                
                Fee::create([
                    'student_id' => $student->id,
                    'amount' => $amount,
                    'payment_date' => $paymentDate,
                    'due_date' => $dueDate,
                    'status' => $status,
                    'fee_type' => $feeTypes[array_rand($feeTypes)],
                    'payment_method' => $status === 'paid' ? $paymentMethods[array_rand($paymentMethods)] : null,
                    'receipt_no' => 'RCP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'description' => 'Fee payment for ' . $student->first_name . ' ' . $student->last_name,
                    'term' => 'Term ' . rand(1, 3) . ' ' . date('Y'),
                    'academic_year' => date('Y'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $feeCounter++;
            }
            
            if ($feeCounter >= 10) {
                break;
            }
        }

        // If less than 10 fees were created, add more
        if ($feeCounter < 10 && $students->count() > 0) {
            $remaining = 10 - $feeCounter;
            for ($i = 0; $i < $remaining; $i++) {
                $student = $students->random();
                $status = $feeStatuses[array_rand($feeStatuses)];
                $amount = rand(5000, 50000);
                $paymentDate = $status === 'paid' 
                    ? Carbon::now()->subDays(rand(1, 365)) 
                    : null;
                $dueDate = Carbon::now()->addDays(rand(1, 60));
                
                Fee::create([
                    'student_id' => $student->id,
                    'amount' => $amount,
                    'payment_date' => $paymentDate,
                    'due_date' => $dueDate,
                    'status' => $status,
                    'fee_type' => $feeTypes[array_rand($feeTypes)],
                    'payment_method' => $status === 'paid' ? $paymentMethods[array_rand($paymentMethods)] : null,
                    'receipt_no' => 'RCP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'description' => 'Additional fee payment',
                    'term' => 'Term ' . rand(1, 3) . ' ' . date('Y'),
                    'academic_year' => date('Y'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $feeCounter++;
            }
        }

        $this->command->info('✅ ' . $feeCounter . ' fees seeded successfully!');
        $this->command->info('🎉 Database seeding completed successfully!');
    }
}