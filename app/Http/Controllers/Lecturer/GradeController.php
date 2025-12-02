<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\GradesExport;
use App\Imports\GradesImport;
use Maatwebsite\Excel\Facades\Excel;

class GradeController extends Controller
{
    private function checkOwnership(CourseClass $courseClass)
    {
        if ($courseClass->lecturer_id !== Auth::user()->lecturer->id) {
            abort(403, 'Bạn không có quyền truy cập lớp học phần này.');
        }
    }

    public function index(CourseClass $courseClass)
    {
        $this->checkOwnership($courseClass);
        $courseClass->load(['students', 'grades']);

        foreach ($courseClass->students as $student) {
            Grade::firstOrCreate(
                [
                    'course_class_id' => $courseClass->id,
                    'student_id' => $student->id,
                ],
                [
                    'attendance_score' => 0,
                    'midterm_score' => 0,
                    'final_score' => 0,
                    'total_score' => 0,
                    'status' => 'active',
                ]
            );
        }

        $grades = Grade::where('course_class_id', $courseClass->id)
            ->with('student')
            ->get();

        return view('lecturer.grades.index', compact('courseClass', 'grades'));
    }

    public function update(Request $request, CourseClass $courseClass)
    {
        $this->checkOwnership($courseClass);
        $data = $request->grades;

        foreach ($data as $studentId => $scores) {
            $attendance = $scores['attendance_score'] ?? 0;
            $midterm = $scores['midterm_score'] ?? 0;
            $final = $scores['final_score'] ?? 0;

            $total = ($attendance * 0.1) + ($midterm * 0.3) + ($final * 0.6);

            Grade::updateOrCreate(
                [
                    'course_class_id' => $courseClass->id,
                    'student_id' => $studentId,
                ],
                [
                    'attendance_score' => $attendance,
                    'midterm_score' => $midterm,
                    'final_score' => $final,
                    'total_score' => $total,
                ]
            );
        }

        return back()->with('success', 'Cập nhật điểm thành công');
    }
    public function export(CourseClass $courseClass)
    {
        $this->checkOwnership($courseClass);
        return Excel::download(new GradesExport($courseClass->id), 'bang_diem_' . $courseClass->course->code . '.xlsx');
    }

    public function import(Request $request, CourseClass $courseClass)
    {
        $this->checkOwnership($courseClass);
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new GradesImport($courseClass->id), $request->file('file'));

        return back()->with('success', 'Nhập điểm từ file Excel thành công!');
    }
}
