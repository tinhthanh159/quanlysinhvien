@extends('layouts.admin')

@section('title', 'Thống kê hệ thống')

@section('content')
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Tổng sinh viên</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $totalStudents }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Tổng giảng viên</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $totalLecturers }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Tổng học phần</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $totalCourses }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Lớp học phần</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $totalClasses }} <small class="fs-6">({{ $activeClasses }} đang hoạt
                            động)</small></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Sinh viên theo Khoa</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Khoa</th>
                                <th>Số lượng sinh viên</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($studentsPerFaculty as $faculty)
                                <tr>
                                    <td>{{ $faculty->name }}</td>
                                    <td>{{ $faculty->students_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Sinh viên theo Ngành</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Ngành</th>
                                <th>Số lượng sinh viên</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($studentsPerMajor as $major)
                                <tr>
                                    <td>{{ $major->name }}</td>
                                    <td>{{ $major->students_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
