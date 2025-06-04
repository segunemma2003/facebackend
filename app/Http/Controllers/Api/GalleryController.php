<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GalleryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = GalleryEvent::with('images');

        if ($request->has('year')) {
            $query->byYear($request->year);
        }

        if ($request->boolean('featured_only')) {
            $query->featured();
        }

        $events = $query->orderBy('event_date', 'desc')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'location' => $event->location,
                    'event_date' => $event->event_date->format('Y-m-d'),
                    'date' => $event->event_date->format('F j, Y'),
                    'description' => $event->description,
                    'attendees' => $event->attendees,
                    'highlights' => $event->highlights,
                    'year' => $event->year,
                    'is_featured' => $event->is_featured,
                    'image_count' => $event->images->count(),
                    'featured_image' => $event->images->first()?->image_url, // Uses accessor
                    'images' => $event->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image_url' => $image->image_url, // Uses accessor
                            'caption' => $image->caption,
                            'sort_order' => $image->sort_order,
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function show(GalleryEvent $galleryEvent): JsonResponse
    {
        $galleryEvent->load('images');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $galleryEvent->id,
                'title' => $galleryEvent->title,
                'location' => $galleryEvent->location,
                'event_date' => $galleryEvent->event_date->format('Y-m-d'),
                'date' => $galleryEvent->event_date->format('F j, Y'),
                'description' => $galleryEvent->description,
                'attendees' => $galleryEvent->attendees,
                'highlights' => $galleryEvent->highlights,
                'year' => $galleryEvent->year,
                'is_featured' => $galleryEvent->is_featured,
                'images' => $galleryEvent->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image_url' => $image->image_url, // Uses accessor
                        'caption' => $image->caption,
                        'sort_order' => $image->sort_order,
                    ];
                }),
            ]
        ]);
    }

    public function years(): JsonResponse
    {
        $years = GalleryEvent::selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json([
            'success' => true,
            'data' => $years
        ]);
    }
}
