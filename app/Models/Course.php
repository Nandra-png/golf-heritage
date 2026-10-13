<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'city',
        'province',
        'latitude',
        'longitude',
        'holes',
        'par',
        'year_opened',
        'designer',
        'description',
        'status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'holes' => 'integer',
        'par' => 'integer',
        'year_opened' => 'integer',
    ];

    public function photos()
    {
        return $this->hasMany(CoursePhoto::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeNear($query, float $lat, float $lng, float $radiusKm)
    {
        $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(latitude))'
            . ' * cos(radians(longitude) - radians(?))'
            . ' + sin(radians(?)) * sin(radians(latitude))))';

        return $query
            ->selectRaw("*, {$haversine} AS distance", [$lat, $lng, $lat])
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');
    }

    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg === null ? null : round((float) $avg, 1);
    }
}
