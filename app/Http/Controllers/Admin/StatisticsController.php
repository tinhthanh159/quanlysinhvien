<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalLecturers = Lecturer::count();
        $totalCourses = Course::count();
        $totalClasses = CourseClass::count();
        $activeClasses = CourseClass::where('status', 'active')->count();

        // Students per Faculty
        $studentsPerFaculty = Faculty::withCount('students')->get();

        // Students per Major
        $studentsPerMajor = Major::withCount('students')->get();

        // Calculate Top 10 Students per Major
        // We fetch all students with necessary relations first to minimize DB queries
        // or we can iterate majors and fetch students for each if the dataset is huge.
        // For reasonable size, eager loading is fine.
        $majors = Major::with(['students' => function ($query) {
            $query->with(['grades.courseClass.course', 'class', 'faculty', 'user']);
        }])->get();

        $performanceStats = [
            'excellent' => 0, // >= 3.6
            'very_good' => 0, // 3.2 - < 3.6
            'good' => 0,      // 2.5 - < 3.2
            'average' => 0,   // 2.0 - < 2.5
            'weak' => 0       // < 2.0
        ];

        $topStudentsByMajor = [];

        foreach ($majors as $major) {
            // Process students for this major to calculate GPA
            $majorStudents = $major->students->map(function ($student) use (&$performanceStats) {
                $gpa = $student->calculateCumulativeGPA();
                $student->gpa = $gpa;

                // Update global stats
                if ($gpa >= 3.6) $performanceStats['excellent']++;
                elseif ($gpa >= 3.2) $performanceStats['very_good']++;
                elseif ($gpa >= 2.5) $performanceStats['good']++;
                elseif ($gpa >= 2.0) $performanceStats['average']++;
                else $performanceStats['weak']++;

                return $student;
            });

            // Sort and take top 10 for this major
            // must use values() to reset keys so the @foreach index in view starts at 0
            $topStudentsByMajor[$major->name] = $majorStudents
                ->where('gpa', '>', 0)
                ->sortByDesc('gpa')
                ->values()
                ->take(10);
        }

        return view('admin.statistics.index', compact(
            'totalStudents',
            'totalLecturers',
            'totalCourses',
            'totalClasses',
            'activeClasses',
            'studentsPerFaculty',
            'studentsPerMajor',
            'performanceStats',
            'topStudentsByMajor'
        ));
    }
}
