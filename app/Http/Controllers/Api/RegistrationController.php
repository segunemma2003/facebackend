<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'organization' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'dietary_requirements' => 'nullable|string',
            'ticket_type' => 'required|in:standard,vip,corporate',
            'event_date' => 'required|date',
        ]);

        // Generate unique reference number
        do {
            $referenceNumber = 'FGR24-' . str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (Registration::where('reference_number', $referenceNumber)->exists());

        // Calculate amount based on ticket type
        $amount = match($validated['ticket_type']) {
            'standard' => 250,
            'vip' => 450,
            'corporate' => 1800,
        };

        $registration = Registration::create([
            ...$validated,
            'reference_number' => $referenceNumber,
            'amount' => $amount,
            'status' => 'confirmed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration completed successfully',
            'data' => [
                'id' => $registration->id,
                'reference_number' => $registration->reference_number,
                'full_name' => $registration->full_name,
                'email' => $registration->email,
                'ticket_type' => $registration->ticket_type,
                'amount' => $registration->amount,
                'status' => $registration->status,
                'event_date' => $registration->event_date->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }

    public function show(Request $request): JsonResponse
    {
        $request->validate([
            'reference_number' => 'required|string',
            'email' => 'required|email',
        ]);

        $registration = Registration::where('reference_number', $request->reference_number)
            ->where('email', $request->email)
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Registration not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $registration->id,
                'reference_number' => $registration->reference_number,
                'full_name' => $registration->full_name,
                'email' => $registration->email,
                'phone' => $registration->phone,
                'organization' => $registration->organization,
                'country' => $registration->country,
                'city' => $registration->city,
                'dietary_requirements' => $registration->dietary_requirements,
                'ticket_type' => $registration->ticket_type,
                'amount' => $registration->amount,
                'status' => $registration->status,
                'event_date' => $registration->event_date->format('Y-m-d H:i:s'),
            ]
        ]);
    }
}
