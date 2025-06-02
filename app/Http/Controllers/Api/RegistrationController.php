<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Registrations",
 *     description="API Endpoints for Event Registrations"
 * )
 */
class RegistrationController extends Controller
{
      /**
     * @OA\Post(
     *      path="/api/v1/registrations",
     *      operationId="createRegistration",
     *      tags={"Registrations"},
     *      summary="Create a new registration",
     *      description="Register for the awards event",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"first_name","last_name","email","phone","country","city","ticket_type","event_date"},
     *              @OA\Property(property="first_name", type="string", example="John"),
     *              @OA\Property(property="last_name", type="string", example="Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="phone", type="string", example="+1234567890"),
     *              @OA\Property(property="organization", type="string", example="Tech Corp"),
     *              @OA\Property(property="country", type="string", example="United States"),
     *              @OA\Property(property="city", type="string", example="New York"),
     *              @OA\Property(property="dietary_requirements", type="string", example="Vegetarian"),
     *              @OA\Property(property="ticket_type", type="string", enum={"standard", "vip", "corporate"}, example="standard"),
     *              @OA\Property(property="event_date", type="string", format="date", example="2024-12-15")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Registration created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Registration completed successfully"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="reference_number", type="string", example="FGR24-123456"),
     *                  @OA\Property(property="full_name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", example="john@example.com"),
     *                  @OA\Property(property="ticket_type", type="string", example="standard"),
     *                  @OA\Property(property="amount", type="number", format="float", example=250.00),
     *                  @OA\Property(property="status", type="string", example="confirmed"),
     *                  @OA\Property(property="event_date", type="string", format="date-time")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
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

     /**
     * @OA\Post(
     *      path="/api/v1/registrations/lookup",
     *      operationId="lookupRegistration",
     *      tags={"Registrations"},
     *      summary="Look up registration",
     *      description="Find registration by reference number and email",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"reference_number","email"},
     *              @OA\Property(property="reference_number", type="string", example="FGR24-123456"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Registration found",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="reference_number", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="organization", type="string"),
     *                  @OA\Property(property="country", type="string"),
     *                  @OA\Property(property="city", type="string"),
     *                  @OA\Property(property="dietary_requirements", type="string"),
     *                  @OA\Property(property="ticket_type", type="string"),
     *                  @OA\Property(property="amount", type="number", format="float"),
     *                  @OA\Property(property="status", type="string"),
     *                  @OA\Property(property="event_date", type="string", format="date-time")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Registration not found"
     *      )
     * )
     */
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
