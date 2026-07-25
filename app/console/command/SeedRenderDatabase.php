<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeedRenderDatabase extends Command
{
    protected $signature = 'seed:render';
    protected $description = 'Seed the Render PostgreSQL database with sample data';

    public function handle()
    {
        $this->info('🌱 Seeding Render PostgreSQL Database...');
        
        // ========== SEED COURSES ==========
        $courseCount = DB::table('courses')->count();
        
        if ($courseCount == 0) {
            $this->info('📚 Seeding courses...');
            
            $courses = [
                ['course_name' => 'Computer Science', 'description' => 'Bachelor of Science in Computer Science', 'credits' => 120, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
                ['course_name' => 'Business Administration', 'description' => 'Bachelor of Business Administration', 'credits' => 120, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
                ['course_name' => 'Information Technology', 'description' => 'Bachelor of Information Technology', 'credits' => 120, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
                ['course_name' => 'Accounting', 'description' => 'Bachelor of Accounting', 'credits' => 120, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
                ['course_name' => 'Civil Engineering', 'description' => 'Bachelor of Civil Engineering', 'credits' => 150, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
                ['course_name' => 'Medicine', 'description' => 'Bachelor of Medicine and Surgery', 'credits' => 180, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
                ['course_name' => 'Law', 'description' => 'Bachelor of Laws', 'credits' => 150, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
                ['course_name' => 'Education', 'description' => 'Bachelor of Education', 'credits' => 120, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
                ['course_name' => 'Nursing', 'description' => 'Bachelor of Nursing', 'credits' => 120, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
                ['course_name' => 'Architecture', 'description' => 'Bachelor of Architecture', 'credits' => 150, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ];

            foreach ($courses as $course) {
                try {
                    DB::table('courses')->insert($course);
                } catch (\Exception $e) {
                    // Skip if duplicate
                }
            }
            
            $this->info('✅ ' . count($courses) . ' courses seeded!');
        } else {
            $this->info('📚 Courses already exist (' . $courseCount . ' found)');
        }

        // ========== GET COURSE IDs ==========
        $courseIds = DB::table('courses')->pluck('id')->toArray();
        
        if (empty($courseIds)) {
            $this->error('❌ No courses found.');
            return 1;
        }

        // ========== SEED STUDENTS ==========
        $studentCount = DB::table('students')->count();
        
        if ($studentCount == 0) {
            $this->info('👨‍🎓 Seeding students...');

            $students = [
                ['admission_number' => 'ADM-2024-001', 'first_name' => 'Collins', 'last_name' => 'Owanda', 'email' => 'collins.owanda@example.com', 'phone' => '+254712345678', 'address' => '123 Unity Road, Nairobi, Kenya', 'class_id' => null, 'course_id' => $courseIds[0] ?? 1, 'status' => 'active', 'registration_number' => 'REG-2024-001', 'created_at' => now(), 'updated_at' => now()],
                ['admission_number' => 'ADM-2024-002', 'first_name' => 'Grace', 'last_name' => 'Mwangi', 'email' => 'grace.mwangi@example.com', 'phone' => '+254723456789', 'address' => '456 Peace Avenue, Mombasa, Kenya', 'class_id' => null, 'course_id' => $courseIds[1] ?? 2, 'status' => 'active', 'registration_number' => 'REG-2024-002', 'created_at' => now(), 'updated_at' => now()],
                ['admission_number' => 'ADM-2024-003', 'first_name' => 'Peter', 'last_name' => 'Ochieng', 'email' => 'peter.ochieng@example.com', 'phone' => '+254734567890', 'address' => '789 Freedom Street, Kisumu, Kenya', 'class_id' => null, 'course_id' => $courseIds[2] ?? 3, 'status' => 'active', 'registration_number' => 'REG-2024-003', 'created_at' => now(), 'updated_at' => now()],
                ['admission_number' => 'ADM-2024-004', 'first_name' => 'Sarah', 'last_name' => 'Kariuki', 'email' => 'sarah.kariuki@example.com', 'phone' => '+254745678901', 'address' => '321 Hope Lane, Nakuru, Kenya', 'class_id' => null, 'course_id' => $courseIds[3] ?? 4, 'status' => 'active', 'registration_number' => 'REG-2024-004', 'created_at' => now(), 'updated_at' => now()],
                ['admission_number' => 'ADM-2024-005', 'first_name' => 'James', 'last_name' => 'Odhiambo', 'email' => 'james.odhiambo@example.com', 'phone' => '+254756789012', 'address' => '654 Vision Road, Eldoret, Kenya', 'class_id' => null, 'course_id' => $courseIds[4] ?? 5, 'status' => 'active', 'registration_number' => 'REG-2024-005', 'created_at' => now(), 'updated_at' => now()],
                ['admission_number' => 'ADM-2024-006', 'first_name' => 'Mary', 'last_name' => 'Akinyi', 'email' => 'mary.akinyi@example.com', 'phone' => '+254767890123', 'address' => '987 Unity Drive, Thika, Kenya', 'class_id' => null, 'course_id' => $courseIds[5] ?? 6, 'status' => 'active', 'registration_number' => 'REG-2024-006', 'created_at' => now(), 'updated_at' => now()],
                ['admission_number' => 'ADM-2024-007', 'first_name' => 'David', 'last_name' => 'Mutua', 'email' => 'david.mutua@example.com', 'phone' => '+254778901234', 'address' => '159 Progress Street, Malindi, Kenya', 'class_id' => null, 'course_id' => $courseIds[6] ?? 7, 'status' => 'active', 'registration_number' => 'REG-2024-007', 'created_at' => now(), 'updated_at' => now()],
                ['admission_number' => 'ADM-2024-008', 'first_name' => 'Lucy', 'last_name' => 'Kamau', 'email' => 'lucy.kamau@example.com', 'phone' => '+254789012345', 'address' => '753 Success Avenue, Meru, Kenya', 'class_id' => null, 'course_id' => $courseIds[7] ?? 8, 'status' => 'active', 'registration_number' => 'REG-2024-008', 'created_at' => now(), 'updated_at' => now()],
                ['admission_number' => 'ADM-2024-009', 'first_name' => 'Michael', 'last_name' => 'Omondi', 'email' => 'michael.omondi@example.com', 'phone' => '+254790123456', 'address' => '951 Innovation Road, Nyeri, Kenya', 'class_id' => null, 'course_id' => $courseIds[8] ?? 9, 'status' => 'active', 'registration_number' => 'REG-2024-009', 'created_at' => now(), 'updated_at' => now()],
                ['admission_number' => 'ADM-2024-010', 'first_name' => 'Esther', 'last_name' => 'Wanjiru', 'email' => 'esther.wanjiru@example.com', 'phone' => '+254701234567', 'address' => '357 Resilience Lane, Kitale, Kenya', 'class_id' => null, 'course_id' => $courseIds[9] ?? 10, 'status' => 'active', 'registration_number' => 'REG-2024-010', 'created_at' => now(), 'updated_at' => now()],
            ];

            foreach ($students as $student) {
                try {
                    DB::table('students')->insert($student);
                } catch (\Exception $e) {
                    // Skip if duplicate
                }
            }

            $this->info('✅ ' . count($students) . ' students seeded!');
        } else {
            $this->info('👨‍🎓 Students already exist (' . $studentCount . ' found)');
        }

        // ========== SEED FEES ==========
        $feeCount = DB::table('fees')->count();
        
        if ($feeCount < 10) {
            $this->info('💰 Seeding fees...');

            $studentIds = DB::table('students')->pluck('id')->toArray();
            
            if (empty($studentIds)) {
                $this->warn('⚠️ No students found. Skipping fees.');
                return 0;
            }

            $feeStatuses = ['paid', 'unpaid', 'partial'];
            $feeTypes = ['Tuition', 'Registration', 'Examination', 'Library', 'Sports', 'Laboratory'];
            $paymentMethods = ['Cash', 'Bank Transfer', 'Cheque', 'M-Pesa', 'Credit Card'];

            $feeCounter = 0;
            $targetFees = 10;
            
            foreach ($studentIds as $studentId) {
                if ($feeCounter >= $targetFees) break;
                
                $status = $feeStatuses[array_rand($feeStatuses)];
                $amount = rand(5000, 50000);
                $paymentDate = $status === 'paid' 
                    ? Carbon::now()->subDays(rand(1, 365))->format('Y-m-d')
                    : null;
                $dueDate = Carbon::now()->addDays(rand(1, 60))->format('Y-m-d');
                
                try {
                    DB::table('fees')->insert([
                        'student_id' => $studentId,
                        'amount' => $amount,
                        'payment_date' => $paymentDate,
                        'due_date' => $dueDate,
                        'status' => $status,
                        'term' => 'Term ' . rand(1, 3) . ' ' . date('Y'),
                        'academic_year' => date('Y'),
                        'payment_method' => $status === 'paid' ? $paymentMethods[array_rand($paymentMethods)] : null,
                        'fee_type' => $feeTypes[array_rand($feeTypes)],
                        'description' => 'Fee payment',
                        'receipt_no' => 'RCP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                        'paid_at' => $status === 'paid' ? now() : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $feeCounter++;
                } catch (\Exception $e) {
                    // Skip if error
                }
            }

            $this->info('✅ ' . $feeCounter . ' fees seeded!');
        } else {
            $this->info('💰 Fees already exist (' . $feeCount . ' found)');
        }
        
        $this->info('🎉 Database seeding completed!');
        $this->info('📊 Summary:');
        $this->info('   - Courses: ' . DB::table('courses')->count());
        $this->info('   - Students: ' . DB::table('students')->count());
        $this->info('   - Fees: ' . DB::table('fees')->count());
        
        return 0;
    }
}