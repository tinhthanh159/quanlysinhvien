@extends('layouts.student')

@section('title', 'Dashboard Sinh viên')

@section('content')
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2>Xin chào, {{ $student->full_name }}!</h2>
                <p class="mb-0">Mã SV: <strong>{{ $student->student_code }}</strong> | Lớp:
                    {{ $student->class->name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-4 text-end d-none d-md-block">
                <i class="fas fa-user-graduate fa-4x" style="opacity: 0.3;"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-calendar-day"></i>
                    <h5 class="mb-0">Lịch học hôm nay</h5>
                </div>
                <div class="card-body p-0">
                    @if ($todayClasses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Tiết</th>
                                        <th>Học phần</th>
                                        <th>Giảng viên</th>
                                        <th>Phòng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($todayClasses as $class)
                                        <tr>
                                            <td><span class="badge bg-primary">{{ $class->period_from }} -
                                                    {{ $class->period_to }}</span></td>
                                            <td>
                                                <strong>{{ $class->course->name }}</strong><br>
                                                <small class="text-muted">{{ $class->course->code }}</small>
                                            </td>
                                            <td>{{ $class->lecturer->full_name }}</td>
                                            <td>{{ $class->classroom }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-mug-hot fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Hôm nay không có lịch học. Thư giãn nhé!</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-book"></i>
                    <h5 class="mb-0">Các lớp đang học</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Mã HP</th>
                                    <th>Tên học phần</th>
                                    <th>Giảng viên</th>
                                    <th>Lịch học</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($enrolledClasses as $class)
                                    <tr>
                                        <td>{{ $class->course->code }}</td>
                                        <td>{{ $class->course->name }}</td>
                                        <td>{{ $class->lecturer->full_name }}</td>
                                        <td>Thứ {{ $class->day_of_week }}, Tiết
                                            {{ $class->period_from }}-{{ $class->period_to }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Academic Status Card -->
            <div class="card mb-4">
                <div class="card-header bg-info">
                    <i class="fas fa-graduation-cap"></i>
                    <h5 class="mb-0">Kết quả học tập</h5>
                </div>
                <div class="card-body text-center py-4">
                    @php
                        $gpa = $student->calculateCumulativeGPA();
                    @endphp
                    <div class="mb-3">
                        <span class="display-3 fw-bold {{ $gpa < 2.0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($gpa, 2) }}
                        </span>
                        <span class="text-muted fs-5">/ 4.0</span>
                    </div>
                    <p class="text-muted mb-0">Điểm trung bình tích lũy (GPA)</p>

                    @if ($gpa < 2.0 && $gpa > 0)
                        <div class="alert alert-danger mt-4 mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Cảnh báo học vụ!</strong><br>
                            GPA dưới 2.0. Vui lòng gặp cố vấn học tập.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-bullhorn"></i>
                    <h5 class="mb-0">Thông báo</h5>
                </div>
                <div class="card-body text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7265556.png"
                        alt="No Data" style="width: 150px; opacity: 0.7;">
                    <p class="text-muted mt-3">Chưa có thông báo mới.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
