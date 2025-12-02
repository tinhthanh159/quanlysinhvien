<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\CourseClass;
use Carbon\Carbon;

class CourseClassSeeder extends Seeder
{
    public function run()
    {
        $courses = Course::all();
        $lecturers = Lecturer::all();
        $students = Student::all();

        if ($courses->isEmpty() || $lecturers->isEmpty() || $students->isEmpty()) {
            return;
        }

        foreach ($courses as $course) {
            // Create 2 classes for each course
            for ($i = 1; $i <= 2; $i++) {
                $lecturer = $lecturers->random();
                $startDate = Carbon::now()->subMonths(rand(0, 4));
                $endDate = $startDate->copy()->addMonths(4);

                $session = rand(0, 1) ? 'morning' : 'afternoon';
                // Use course credits as duration, clamped between 2 and 5
                $duration = max(2, min(5, $course->number_of_credits));

                if ($session === 'morning') {
                    // Morning: periods 1-5
                    $maxStart = 5 - $duration + 1;
                    $periodFrom = rand(1, $maxStart);
                    $periodTo = $periodFrom + $duration - 1;
                } else {
                    // Afternoon: periods 6-10
                    $maxStart = 10 - $duration + 1;
                    $periodFrom = rand(6, $maxStart);
                    $periodTo = $periodFrom + $duration - 1;
                }

                $courseClass = CourseClass::create([
                    'course_id' => $course->id,
                    'lecturer_id' => $lecturer->id,
                    'classroom' => 'P' . rand(100, 500),
                    'semester' => 1,
                    'school_year' => '2025-2026',
                    'day_of_week' => rand(2, 7), // Mon-Sat
                    'period_from' => $periodFrom,
                    'period_to' => $periodTo,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $endDate->isPast() ? 'completed' : 'active',
                ]);

                // Enroll random students (10-20 students per class)
                $enrolledStudents = $students->random(rand(10, 20));
                foreach ($enrolledStudents as $student) {
                    $courseClass->students()->attach($student->id, ['enrolled_at' => now(), 'status' => 'enrolled']);
                }
            }
        }
    }
}
