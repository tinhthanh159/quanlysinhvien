<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\MajorController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\LecturerController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseClassController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\StatisticsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Illuminate\Support\Facades\Auth::user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.statistics.index'); // Admin dashboard
    } elseif ($user->role === 'lecturer') {
        return redirect()->route('lecturer.dashboard');
    } elseif ($user->role === 'student') {
        return redirect()->route('student.dashboard');
    }
    return view('dashboard'); // Fallback
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('faculties', FacultyController::class);
    Route::resource('majors', MajorController::class);
    Route::resource('classes', ClassController::class);
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::resource('students', StudentController::class);
    Route::resource('lecturers', LecturerController::class);
    Route::resource('courses', CourseController::class);

    Route::resource('course_classes', CourseClassController::class);
    Route::post('course_classes/{courseClass}/add-student', [CourseClassController::class, 'addStudent'])->name('course_classes.add_student');
    Route::delete('course_classes/{courseClass}/remove-student/{student}', [CourseClassController::class, 'removeStudent'])->name('course_classes.remove_student');

    // Attendance Routes
    Route::get('course_classes/{courseClass}/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('course_classes/{courseClass}/attendance/create', [AttendanceController::class, 'createSession'])->name('attendance.create_session');
    Route::post('course_classes/{courseClass}/attendance', [AttendanceController::class, 'storeSession'])->name('attendance.store_session');
    Route::get('course_classes/{courseClass}/attendance/{session}', [AttendanceController::class, 'showSession'])->name('attendance.show_session');
    Route::post('course_classes/{courseClass}/attendance/{session}', [AttendanceController::class, 'updateAttendance'])->name('attendance.update_attendance');

    // Grade Routes
    Route::get('course_classes/{courseClass}/grades', [GradeController::class, 'index'])->name('grades.index');
    Route::post('course_classes/{courseClass}/grades', [GradeController::class, 'update'])->name('grades.update');
    Route::get('course_classes/{courseClass}/grades/export', [GradeController::class, 'export'])->name('grades.export');
    Route::post('course_classes/{courseClass}/grades/import', [GradeController::class, 'import'])->name('grades.import');
    Route::post('grades/{grade}/send-warning', [GradeController::class, 'sendWarning'])->name('grades.send_warning');

    // Statistics Route
    Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics.index');
});

// Lecturer Routes
Route::middleware(['auth', 'role:lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Lecturer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/schedule', [App\Http\Controllers\Lecturer\DashboardController::class, 'schedule'])->name('schedule');
    Route::get('/classes', [App\Http\Controllers\Lecturer\DashboardController::class, 'classes'])->name('classes.index');

    // Attendance
    Route::get('course_classes/{courseClass}/attendance', [App\Http\Controllers\Lecturer\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('course_classes/{courseClass}/attendance/create', [App\Http\Controllers\Lecturer\AttendanceController::class, 'createSession'])->name('attendance.create_session');
    Route::post('course_classes/{courseClass}/attendance', [App\Http\Controllers\Lecturer\AttendanceController::class, 'storeSession'])->name('attendance.store_session');
    Route::get('course_classes/{courseClass}/attendance/{session}', [App\Http\Controllers\Lecturer\AttendanceController::class, 'showSession'])->name('attendance.show_session');
    Route::post('course_classes/{courseClass}/attendance/{session}', [App\Http\Controllers\Lecturer\AttendanceController::class, 'updateAttendance'])->name('attendance.update_attendance');

    // Grades
    Route::get('course_classes/{courseClass}/grades', [App\Http\Controllers\Lecturer\GradeController::class, 'index'])->name('grades.index');
    Route::post('course_classes/{courseClass}/grades', [App\Http\Controllers\Lecturer\GradeController::class, 'update'])->name('grades.update');
    Route::get('course_classes/{courseClass}/grades/export', [App\Http\Controllers\Lecturer\GradeController::class, 'export'])->name('grades.export');
    Route::post('course_classes/{courseClass}/grades/import', [App\Http\Controllers\Lecturer\GradeController::class, 'import'])->name('grades.import');
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/schedule', [App\Http\Controllers\Student\DashboardController::class, 'schedule'])->name('schedule');
    Route::get('/grades', [App\Http\Controllers\Student\DashboardController::class, 'grades'])->name('grades');
});

Route::get('/attendance/checkin/{token}', [AttendanceController::class, 'checkin'])->middleware(['auth'])->name('attendance.checkin');

require __DIR__ . '/auth.php';
