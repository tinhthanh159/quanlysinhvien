<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseClass;
use App\Models\Grade;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('profile.edit')->with('error', 'Vui lòng cập nhật thông tin sinh viên.');
        }

        // Get enrolled classes
        $enrolledClasses = $student->courseClasses()
            ->wherePivot('status', 'active') // Assuming pivot has status or we check class status
            ->where('course_classes.status', 'active')
            ->with('course', 'lecturer')
            ->get();

        // Get today's schedule
        $today = now();
        $dayOfWeekMap = [
            0 => 'CN',
            1 => '2',
            2 => '3',
            3 => '4',
            4 => '5',
            5 => '6',
            6 => '7'
        ];
        $currentDay = $dayOfWeekMap[$today->dayOfWeek];

        $todayClasses = $enrolledClasses->filter(function ($class) use ($currentDay) {
            return $class->day_of_week == $currentDay;
        });

        return view('student.dashboard', compact('student', 'enrolledClasses', 'todayClasses'));
    }

    public function schedule()
    {
        $user = Auth::user();
        $student = $user->student;

        $classes = $student->courseClasses()
            ->where('course_classes.status', 'active')
            ->orderBy('day_of_week')
            ->orderBy('period_from')
            ->get();

        return view('student.schedule', compact('classes'));
    }

    public function grades()
    {
        $user = Auth::user();
        $student = $user->student;

        $grades = Grade::where('student_id', $student->id)
            ->with(['courseClass.course'])
            ->get();

        $cumulativeGPA = $student->calculateCumulativeGPA();

        return view('student.grades', compact('grades', 'cumulativeGPA'));
    }
}
