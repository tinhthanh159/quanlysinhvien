<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['code' => 'INT101', 'name' => 'Nhập môn Lập trình', 'number_of_credits' => 3, 'theory_hours' => 30, 'practice_hours' => 30],
            ['code' => 'INT102', 'name' => 'Cấu trúc dữ liệu và Giải thuật', 'number_of_credits' => 4, 'theory_hours' => 45, 'practice_hours' => 30],
            ['code' => 'INT103', 'name' => 'Cơ sở dữ liệu', 'number_of_credits' => 3, 'theory_hours' => 30, 'practice_hours' => 30],
            ['code' => 'INT104', 'name' => 'Lập trình Web', 'number_of_credits' => 4, 'theory_hours' => 30, 'practice_hours' => 60],
            ['code' => 'INT105', 'name' => 'Mạng máy tính', 'number_of_credits' => 3, 'theory_hours' => 30, 'practice_hours' => 30],

            ['code' => 'ECO101', 'name' => 'Kinh tế vi mô', 'number_of_credits' => 3, 'theory_hours' => 45, 'practice_hours' => 0],
            ['code' => 'ECO102', 'name' => 'Kinh tế vĩ mô', 'number_of_credits' => 3, 'theory_hours' => 45, 'practice_hours' => 0],
            ['code' => 'ECO103', 'name' => 'Nguyên lý kế toán', 'number_of_credits' => 3, 'theory_hours' => 30, 'practice_hours' => 30],

            ['code' => 'ENG101', 'name' => 'Tiếng Anh cơ bản 1', 'number_of_credits' => 3, 'theory_hours' => 30, 'practice_hours' => 30],
            ['code' => 'ENG102', 'name' => 'Tiếng Anh cơ bản 2', 'number_of_credits' => 3, 'theory_hours' => 30, 'practice_hours' => 30],
        ];

        foreach ($courses as $course) {
            Course::firstOrCreate(['code' => $course['code']], $course);
        }
    }
}
