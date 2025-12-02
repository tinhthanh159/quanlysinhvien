@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2>Xin chào, Quản trị viên!</h2>
                <p class="mb-0">Chào mừng trở lại hệ thống quản lý đào tạo. Dưới đây là tổng quan hệ thống.</p>
            </div>
            <div class="col-md-4 text-end d-none d-md-block">
                <i class="fas fa-cogs fa-4x" style="opacity: 0.3;"></i>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="stats-icon bg-primary-light text-white">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ \App\Models\Student::count() }}</h3>
                    <p>Sinh viên</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="stats-icon bg-success text-white">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ \App\Models\Lecturer::count() }}</h3>
                    <p>Giảng viên</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="stats-icon bg-info text-white">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ \App\Models\Course::count() }}</h3>
                    <p>Học phần</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="stats-icon bg-warning text-white">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ \App\Models\CourseClass::count() }}</h3>
                    <p>Lớp học phần</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line"></i>
                    <h5 class="mb-0">Hoạt động gần đây</h5>
                </div>
                <div class="card-body text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/data-analysis-8867285-7265561.png"
                        alt="Analytics" style="width: 200px; opacity: 0.8;">
                    <p class="text-muted mt-3">Hệ thống đang hoạt động ổn định.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-link"></i>
                    <h5 class="mb-0">Truy cập nhanh</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.students.create') }}" class="btn btn-outline-primary text-start">
                            <i class="fas fa-plus-circle me-2"></i> Thêm sinh viên mới
                        </a>
                        <a href="{{ route('admin.course_classes.create') }}" class="btn btn-outline-primary text-start">
                            <i class="fas fa-plus-circle me-2"></i> Mở lớp học phần
                        </a>
                        <a href="{{ route('admin.statistics.index') }}" class="btn btn-outline-primary text-start">
                            <i class="fas fa-chart-bar me-2"></i> Xem báo cáo thống kê
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
