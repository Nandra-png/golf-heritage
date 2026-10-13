<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::query()->withCount('reviews');

        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('designer', 'like', "%{$q}%");
            });
        }

        if ($province = $request->query('province')) {
            $query->where('province', $province);
        }

        if ($request->filled(['lat', 'lng'])) {
            $request->validate([
                'lat' => ['numeric', 'between:-90,90'],
                'lng' => ['numeric', 'between:-180,180'],
                'radius' => ['nullable', 'numeric', 'min:1', 'max:500'],
            ]);
            $query->near(
                (float) $request->query('lat'),
                (float) $request->query('lng'),
                (float) $request->query('radius', 50)
            );
        }

        $courses = $query->paginate(20)->withQueryString();

        return response()->json([
            'data' => CourseResource::collection($courses->items()),
            'meta' => [
                'total' => $courses->total(),
                'page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
            ],
        ]);
    }

    public function show(Course $course): JsonResponse
    {
        $course->load('photos', 'reviews.user:id,name');

        return response()->json([
            'data' => new CourseResource($course),
            'rating' => $course->average_rating,
        ]);
    }

    public function store(StoreCourseRequest $request): JsonResponse
    {
        $course = Course::create($request->validated());

        return response()->json(['data' => new CourseResource($course)], 201);
    }

    public function update(UpdateCourseRequest $request, Course $course): JsonResponse
    {
        $this->authorize('update', $course);
        $course->update($request->validated());

        return response()->json(['data' => new CourseResource($course)]);
    }

    public function destroy(Course $course): JsonResponse
    {
        $this->authorize('delete', $course);
        $course->delete();

        return response()->json(null, 204);
    }
}
