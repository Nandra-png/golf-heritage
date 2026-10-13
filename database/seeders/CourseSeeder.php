<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $lapangan = [
            ['Emeralda Golf Club', 'Cimanggis', 'Jawa Barat', -6.3842, 106.8701, 1994, 'Nelson & Haworth', 36],
            ['Pondok Indah Golf Course', 'Jakarta Selatan', 'DKI Jakarta', -6.2667, 106.7868, 1976, 'Robert Trent Jones Jr', 36],
            ['Sentul Highland Golf Club', 'Bogor', 'Jawa Barat', -6.5683, 106.8531, 1994, 'Greg Norman', 36],
            ['Gunung Geulis Country Club', 'Bogor', 'Jawa Barat', -6.5994, 106.8897, 1989, 'Thomson & Wolveridge', 36],
            ['Bali National Golf Club', 'Nusa Dua', 'Bali', -8.7997, 115.2266, 2014, 'Nelson & Haworth', 18],
            ['Ria Bintan Golf Club', 'Bintan', 'Kepulauan Riau', 1.0931, 104.4789, 1998, 'Gary Player', 27],
            ['Damai Indah Golf BSD', 'Tangerang', 'Banten', -6.2926, 106.6478, 1992, 'Jack Nicklaus', 36],
            ['Merapi Golf Course', 'Sleman', 'DI Yogyakarta', -7.6113, 110.4459, 1995, 'Thomson & Wolveridge', 18],
            ['Ciputra Golf Surabaya', 'Surabaya', 'Jawa Timur', -7.2884, 112.6789, 1995, 'Phil Jacobs', 18],
            ['Kota Permai Golf', 'Kota Kemuning', 'Jawa Barat', -6.3221, 106.7012, 1998, 'Ross Watson', 18],
        ];

        foreach ($lapangan as $i => [$nama, $kota, $prov, $lat, $lng, $tahun, $desainer, $holes]) {
            $course = Course::create([
                'name' => $nama,
                'slug' => \Illuminate\Support\Str::slug($nama),
                'city' => $kota,
                'province' => $prov,
                'latitude' => $lat,
                'longitude' => $lng,
                'holes' => $holes,
                'par' => $holes == 18 ? 72 : 144,
                'year_opened' => $tahun,
                'designer' => $desainer,
                'description' => "lapangan {$holes} hole di {$kota}, dibuka tahun {$tahun}.",
                'status' => 'active',
            ]);

            foreach (range(1, 3) as $n) {
                $course->photos()->create([
                    'path' => "courses/{$course->slug}/hole-{$n}.jpg",
                    'caption' => "hole {$n}",
                ]);
            }
        }

        $users = User::factory(5)->create();

        foreach (Course::all() as $course) {
            foreach ($users->random(3) as $user) {
                Review::create([
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                    'rating' => rand(3, 5),
                    'comment' => 'fairwaynya kecepatan, green-nya susah dibaca',
                ]);
            }
        }
    }
}
