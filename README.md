# golf-heritage

Laravel REST API for mapping heritage golf courses across Indonesia. Covers location, designer history, and user reviews.

The point: heritage courses in Indonesia are poorly documented online. This collects them in one queryable place with coordinates, so you can search by province or find what's near you.

## Stack

- Laravel 11
- Sanctum for token auth
- MySQL or Postgres (SQLite works for local)

## Setup

```
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Endpoints

```
GET    /api/v1/courses
GET    /api/v1/courses/{slug}
POST   /api/v1/courses               auth
PUT    /api/v1/courses/{slug}        auth, owner
DELETE /api/v1/courses/{slug}        auth, owner
POST   /api/v1/courses/{slug}/reviews auth
DELETE /api/v1/reviews/{id}          auth, owner
```

## Search

```
GET /api/v1/courses?q=emeralda
GET /api/v1/courses?province=Bali
GET /api/v1/courses?lat=-6.2&lng=106.8&radius=50
```

The radius query uses Haversine in SQL:

```php
$haversine = '(6371 * acos(cos(radians(?)) * cos(radians(latitude))'
    . ' * cos(radians(longitude) - radians(?))'
    . ' + sin(radians(?)) * sin(radians(latitude))))';

return $query
    ->selectRaw("*, {$haversine} AS distance", [$lat, $lng, $lat])
    ->having('distance', '<=', $radiusKm)
    ->orderBy('distance');
```

Latitude, longitude, and radius are all validated and bound as parameters.

## Schema

```
courses        name, slug, city, province, lat, lng, holes, par,
               year_opened, designer, description, status
course_photos  course_id, path, caption
reviews        course_id, user_id, rating, comment
```

Composite index on `(latitude, longitude)` for the geo query, plus an index on `province`.

`reviews` has a unique constraint on `(course_id, user_id)` so one user can't stack ratings on the same course.

## Validation

`StoreCourseRequest` handles everything. Notable rules:

- lat between -90 and 90, lng between -180 and 180
- `year_opened` can't be in the future
- holes must be 9, 18, 27, or 36
- slug is auto-generated from the name if not supplied, and must be unique

Update requests drop the uniqueness check against the record being edited.

## Resources

API responses don't leak the raw model. `CourseResource` shapes the output, nests location and course details, and exposes distance only when the geo query ran.

## What's missing

- no photo upload, paths are manual
- no caching on the geo query
- no rate limiting
- no moderation on reviews
- Haversine doesn't use a spatial index, fine at this scale but not at 100k rows
