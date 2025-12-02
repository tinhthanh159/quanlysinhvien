@extends('layouts.student')

@section('title', 'Lịch học')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h4 class="mb-0 text-primary"><i class="fas fa-calendar-alt me-2"></i>Thời khóa biểu</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered text-center mb-0 align-middle">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th style="width: 100px;">Buổi / Tiết</th>
                            <th>Thứ 2</th>
                            <th>Thứ 3</th>
                            <th>Thứ 4</th>
                            <th>Thứ 5</th>
                            <th>Thứ 6</th>
                            <th>Thứ 7</th>
                            <th>Chủ nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Morning Session (Periods 1-5) -->
                        <tr class="table-secondary">
                            <td colspan="8" class="fw-bold text-start ps-3 text-uppercase">Buổi Sáng</td>
                        </tr>
                        @for ($i = 1; $i <= 5; $i++)
                            <tr>
                                <td class="fw-bold bg-light">Tiết {{ $i }}</td>
                                @foreach (['2', '3', '4', '5', '6', '7', 'CN'] as $day)
                                    @php
                                        $class = $classes->first(function ($c) use ($day, $i) {
                                            return $c->day_of_week == $day &&
                                                $i >= $c->period_from &&
                                                $i <= $c->period_to;
                                        });

                                        $isStart = $class && $class->period_from == $i;
                                        $isContinuation = $class && $class->period_from < $i;
                                    @endphp

                                    @if ($isStart)
                                        <td rowspan="{{ $class->period_to - $class->period_from + 1 }}"
                                            class="bg-primary bg-opacity-10 border-primary align-middle">
                                            <div
                                                class="p-2 rounded border border-primary bg-white shadow-sm h-100 d-flex flex-column justify-content-center">
                                                <strong class="text-primary d-block">{{ $class->course->name }}</strong>
                                                <small class="text-muted d-block">{{ $class->course->code }}</small>
                                                <div class="mt-1">
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $class->classroom }}
                                                    </span>
                                                </div>
                                                <small class="text-muted mt-1 d-block">
                                                    (Tiết {{ $class->period_from }} - {{ $class->period_to }})
                                                </small>
                                            </div>
                                        </td>
                                    @elseif (!$isContinuation)
                                        <td></td>
                                    @endif
                                @endforeach
                            </tr>
                        @endfor

                        <!-- Afternoon Session (Periods 6-10) -->
                        <tr class="table-secondary">
                            <td colspan="8" class="fw-bold text-start ps-3 text-uppercase">Buổi Chiều</td>
                        </tr>
                        @for ($i = 6; $i <= 10; $i++)
                            <tr>
                                <td class="fw-bold bg-light">Tiết {{ $i }}</td>
                                @foreach (['2', '3', '4', '5', '6', '7', 'CN'] as $day)
                                    @php
                                        $class = $classes->first(function ($c) use ($day, $i) {
                                            return $c->day_of_week == $day &&
                                                $i >= $c->period_from &&
                                                $i <= $c->period_to;
                                        });

                                        $isStart = $class && $class->period_from == $i;
                                        $isContinuation = $class && $class->period_from < $i;
                                    @endphp

                                    @if ($isStart)
                                        <td rowspan="{{ $class->period_to - $class->period_from + 1 }}"
                                            class="bg-primary bg-opacity-10 border-primary align-middle">
                                            <div
                                                class="p-2 rounded border border-primary bg-white shadow-sm h-100 d-flex flex-column justify-content-center">
                                                <strong class="text-primary d-block">{{ $class->course->name }}</strong>
                                                <small class="text-muted d-block">{{ $class->course->code }}</small>
                                                <div class="mt-1">
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $class->classroom }}
                                                    </span>
                                                </div>
                                                <small class="text-muted mt-1 d-block">
                                                    (Tiết {{ $class->period_from }} - {{ $class->period_to }})
                                                </small>
                                            </div>
                                        </td>
                                    @elseif (!$isContinuation)
                                        <td></td>
                                    @endif
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
