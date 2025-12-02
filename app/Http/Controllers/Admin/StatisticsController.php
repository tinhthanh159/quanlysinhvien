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

        return view('admin.statistics.index', compact(
            'totalStudents',
            'totalLecturers',
            'totalCourses',
            'totalClasses',
            'activeClasses',
            'studentsPerFaculty',
            'studentsPerMajor'
        ));
    }
}
