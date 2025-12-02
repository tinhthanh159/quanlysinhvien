<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
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
        $sessions = $courseClass->attendanceSessions()->orderBy('session_date', 'desc')->get();
        // Reuse Admin view or create a new one. Reusing for now but we need to ensure routes in view are dynamic or we pass a flag.
        // Actually, the admin view uses named routes like 'admin.attendance.create_session'.
        // We should probably duplicate the view to 'lecturer.attendance.index' and update routes.
        return view('lecturer.attendance.index', compact('courseClass', 'sessions'));
    }

    public function createSession(CourseClass $courseClass)
    {
        $this->checkOwnership($courseClass);
        return view('lecturer.attendance.create_session', compact('courseClass'));
    }

    public function storeSession(Request $request, CourseClass $courseClass)
    {
        $this->checkOwnership($courseClass);
        $request->validate([
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $session = AttendanceSession::create([
            'course_class_id' => $courseClass->id,
            'session_date' => $request->session_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'qr_code_token' => Str::random(32),
            'status' => 'open',
        ]);

        foreach ($courseClass->students as $student) {
            Attendance::create([
                'course_class_id' => $courseClass->id,
                'student_id' => $student->id,
                'attendance_session_id' => $session->id,
                'status' => 'absent',
            ]);
        }

        return redirect()->route('lecturer.attendance.index', $courseClass)->with('success', 'Tạo buổi điểm danh thành công');
    }

    public function showSession(CourseClass $courseClass, AttendanceSession $session)
    {
        $this->checkOwnership($courseClass);
        $session->load(['attendances.student']);
        $qrCode = QrCode::size(200)->generate(route('attendance.checkin', ['token' => $session->qr_code_token]));

        return view('lecturer.attendance.show_session', compact('courseClass', 'session', 'qrCode'));
    }

    public function updateAttendance(Request $request, CourseClass $courseClass, AttendanceSession $session)
    {
        $this->checkOwnership($courseClass);
        $data = $request->attendance;

        foreach ($data as $studentId => $status) {
            Attendance::where('attendance_session_id', $session->id)
                ->where('student_id', $studentId)
                ->update(['status' => $status]);
        }

        return back()->with('success', 'Cập nhật điểm danh thành công');
    }
}
