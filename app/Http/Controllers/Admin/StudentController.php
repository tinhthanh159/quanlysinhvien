<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Major;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['class', 'major', 'faculty']);
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('full_name', 'like', "%$search%")
                ->orWhere('student_code', 'like', "%$search%");
        }
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }
        $students = $query->paginate(10);
        $classes = Classes::where('status', 'active')->get();
        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $faculties = Faculty::where('status', 'active')->get();
        $majors = Major::where('status', 'active')->get();
        $classes = Classes::where('status', 'active')->get();
        return view('admin.students.create', compact('faculties', 'majors', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_code' => 'required|unique:students,student_code',
            'full_name' => 'required',
            'email' => 'required|email|unique:students,email|unique:users,email',
            'class_id' => 'required|exists:classes,id',
            'major_id' => 'required|exists:majors,id',
            'faculty_id' => 'required|exists:faculties,id',
            'status' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            // Create User
            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'username' => $request->student_code,
                'password' => Hash::make($request->student_code), // Default password is student code
                'role' => 'student',
            ]);

            // Create Student
            Student::create([
                'user_id' => $user->id,
                'student_code' => $request->student_code,
                'full_name' => $request->full_name,
                'gender' => $request->gender,
                'dob' => $request->dob,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'class_id' => $request->class_id,
                'major_id' => $request->major_id,
                'faculty_id' => $request->faculty_id,
                'status' => $request->status,
            ]);
        });

        return redirect()->route('admin.students.index')->with('success', 'Thêm sinh viên thành công');
    }

    public function edit(Student $student)
    {
        $faculties = Faculty::where('status', 'active')->get();
        $majors = Major::where('status', 'active')->get();
        $classes = Classes::where('status', 'active')->get();
        return view('admin.students.edit', compact('student', 'faculties', 'majors', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'student_code' => 'required|unique:students,student_code,' . $student->id,
            'full_name' => 'required',
            'email' => 'required|email|unique:students,email,' . $student->id . '|unique:users,email,' . $student->user_id,
            'class_id' => 'required|exists:classes,id',
            'major_id' => 'required|exists:majors,id',
            'faculty_id' => 'required|exists:faculties,id',
            'status' => 'required',
        ]);

        DB::transaction(function () use ($request, $student) {
            // Update User
            if ($student->user) {
                $student->user->update([
                    'name' => $request->full_name,
                    'email' => $request->email,
                    'username' => $request->student_code,
                ]);
            }

            // Update Student
            $student->update([
                'student_code' => $request->student_code,
                'full_name' => $request->full_name,
                'gender' => $request->gender,
                'dob' => $request->dob,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'class_id' => $request->class_id,
                'major_id' => $request->major_id,
                'faculty_id' => $request->faculty_id,
                'status' => $request->status,
            ]);
        });

        return redirect()->route('admin.students.index')->with('success', 'Cập nhật sinh viên thành công');
    }

    public function destroy(Student $student)
    {
        if ($student->user) {
            $student->user->delete();
        }
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Xóa sinh viên thành công');
    }
    public function export()
    {
        return Excel::download(new StudentsExport, 'danh_sach_sinh_vien.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));
            return back()->with('success', 'Nhập sinh viên từ file Excel thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi nhập file: ' . $e->getMessage());
        }
    }
}
