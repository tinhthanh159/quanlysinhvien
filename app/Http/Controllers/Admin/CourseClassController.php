<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Http\Request;

class CourseClassController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseClass::with(['course', 'lecturer']);
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('course', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%");
            });
        }
        $courseClasses = $query->paginate(10);
        return view('admin.course_classes.index', compact('courseClasses'));
    }

    public function create()
    {
        $courses = Course::where('status', 'active')->get();
        $lecturers = Lecturer::where('status', 'working')->get();
        return view('admin.course_classes.create', compact('courses', 'lecturers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'classroom' => 'required',
            'semester' => 'required',
            'school_year' => 'required',
            'day_of_week' => 'required',
            'period_from' => 'required|integer',
            'period_to' => 'required|integer|gte:period_from',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        CourseClass::create($request->all());
        return redirect()->route('admin.course_classes.index')->with('success', 'Mở lớp học phần thành công');
    }

    public function show(CourseClass $courseClass)
    {
        $courseClass->load(['course', 'lecturer', 'students.class']);
        return view('admin.course_classes.show', compact('courseClass'));
    }

    public function edit(CourseClass $courseClass)
    {
        $courses = Course::where('status', 'active')->get();
        $lecturers = Lecturer::where('status', 'working')->get();
        return view('admin.course_classes.edit', compact('courseClass', 'courses', 'lecturers'));
    }

    public function update(Request $request, CourseClass $courseClass)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'classroom' => 'required',
            'semester' => 'required',
            'school_year' => 'required',
            'day_of_week' => 'required',
            'period_from' => 'required|integer',
            'period_to' => 'required|integer|gte:period_from',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        $courseClass->update($request->all());
        return redirect()->route('admin.course_classes.index')->with('success', 'Cập nhật lớp học phần thành công');
    }

    public function destroy(CourseClass $courseClass)
    {
        $courseClass->delete();
        return redirect()->route('admin.course_classes.index')->with('success', 'Xóa lớp học phần thành công');
    }

    public function addStudent(Request $request, CourseClass $courseClass)
    {
        $request->validate([
            'student_code' => 'required|exists:students,student_code',
        ]);

        $student = Student::where('student_code', $request->student_code)->first();

        // Check if student is already enrolled
        if ($courseClass->students()->where('student_id', $student->id)->exists()) {
            return back()->with('error', 'Sinh viên này đã có trong lớp');
        }

        $courseClass->students()->attach($student->id, ['enrolled_at' => now()]);
        return back()->with('success', 'Thêm sinh viên thành công');
    }

    public function removeStudent(CourseClass $courseClass, Student $student)
    {
        $courseClass->students()->detach($student->id);
        return back()->with('success', 'Xóa sinh viên khỏi lớp thành công');
    }
}
