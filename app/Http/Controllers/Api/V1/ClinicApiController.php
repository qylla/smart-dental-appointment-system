<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ClinicSetting;
use App\Services\SlotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public, read-only REST API (version 1).
 *
 * Gives clinic information and live slot availability as JSON, for example
 * for a future mobile app or a partner website. It only exposes what the
 * landing page already shows: no patient data, no dentist names, and
 * nothing can be booked or changed through it.
 *
 * Availability comes from the same SlotService as the website, so the API
 * and the booking page always agree.
 */
class ClinicApiController extends Controller
{
    public function __construct(private SlotService $slots)
    {
    }

    /**
     * GET /api/v1
     * A small index so anyone opening the API can see what's available.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'name'      => 'KPZ Dentalcare API',
            'version'   => 'v1',
            'endpoints' => [
                ['method' => 'GET', 'path' => '/api/v1/clinic',     'description' => 'Clinic details, opening hours and starting prices'],
                ['method' => 'GET', 'path' => '/api/v1/slots',      'description' => 'Availability for the next 14 days. Filters: ?date=YYYY-MM-DD, ?available=1'],
                ['method' => 'GET', 'path' => '/api/v1/slots/next', 'description' => 'The earliest slot that can be booked right now'],
            ],
        ]);
    }

    /**
     * GET /api/v1/clinic
     */
    public function clinic(): JsonResponse
    {
        $slots = collect(ClinicSetting::get('slots'));
        $day   = $slots->filter(fn ($t) => SlotService::sessionOf($t) === 'day')->values();
        $night = $slots->filter(fn ($t) => SlotService::sessionOf($t) === 'night')->values();

        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // Treatment names in English, whatever language the visitor picked on the site.
        $prices = collect(config('clinic.prices'))->map(fn ($amount, $key) => [
            'treatment' => trans("site.treat.{$key}_name", [], 'en'),
            'from'      => $amount,
            'currency'  => 'MYR',
        ])->values();

        return response()->json([
            'data' => [
                'name'         => 'Klinik Pergigian Dr. Zuraida (KPZ Dentalcare)',
                'location'     => config('clinic.location'),
                'phone'        => config('clinic.phone'),
                'whatsapp'     => config('clinic.whatsapp') ? 'https://wa.me/' . config('clinic.whatsapp') : null,
                'open_days'    => collect(ClinicSetting::get('open_days'))->sort()->map(fn ($d) => $dayNames[$d])->values(),
                'slot_minutes' => 30,
                'sessions'     => [
                    'day'   => $day->isEmpty()   ? null : ['first_slot' => $day->first(),   'last_slot' => $day->last()],
                    'night' => $night->isEmpty() ? null : ['first_slot' => $night->first(), 'last_slot' => $night->last()],
                ],
                'prices'         => $prices,
                'prices_updated' => config('clinic.prices_updated'),
                'prices_note'    => 'Starting prices only. The final cost depends on the dentist\'s examination.',
            ],
        ]);
    }

    /**
     * GET /api/v1/slots
     *
     * Optional filters:
     *   ?date=2026-10-01  one day only (404 if the clinic isn't open that day)
     *   ?available=1      leave out slots that can't be booked
     */
    public function slots(Request $request): JsonResponse
    {
        $request->validate([
            'date'      => ['nullable', 'date_format:Y-m-d'],
            'available' => ['nullable', 'boolean'],
        ]);

        $days = collect($this->slots->availability());

        if ($request->filled('date')) {
            $days = $days->where('date', $request->date)->values();

            if ($days->isEmpty()) {
                return response()->json([
                    'message' => 'The clinic has no bookable hours on that date. Dates must be within the next '
                        . SlotService::DAYS_AHEAD . ' days, on a day the clinic is open.',
                ], 404);
            }
        }

        $onlyAvailable = $request->boolean('available');

        $data = $days->map(function ($day) use ($onlyAvailable) {
            $slots = collect($day['slots'])->map(fn ($s) => [
                'time'      => $s['time'],
                'label'     => $s['label'],
                'session'   => SlotService::sessionOf($s['time']),
                'available' => ! $s['booked'],
                // Why it can't be booked, so an app can explain it.
                'reason'    => ! $s['booked'] ? null : ($s['past'] ? 'past' : ($s['closed'] ? 'closed' : 'full')),
            ]);

            if ($onlyAvailable) {
                $slots = $slots->where('available', true)->values();
            }

            return [
                'date'            => $day['date'],
                'day'             => $day['dow'],
                'label'           => $day['label'],
                'full'            => $day['full'],
                'available_count' => collect($day['slots'])->where('booked', false)->count(),
                'slots'           => $slots->values(),
            ];
        })->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'days'         => $data->count(),
                'days_ahead'   => SlotService::DAYS_AHEAD,
                'timezone'     => config('app.timezone'),
                'generated_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * GET /api/v1/slots/next
     */
    public function nextSlot(): JsonResponse
    {
        $next = $this->slots->nextAvailable();

        return response()->json([
            'data' => $next ? [
                'date'      => $next['date']->toDateString(),
                'time'      => $next['date']->format('H:i'),
                'label'     => $next['label'],
                'starts_at' => $next['date']->toIso8601String(),
            ] : null,
            'meta' => [
                // null data is a valid answer: the next fortnight is full.
                'message' => $next ? null : 'No free slots in the next ' . SlotService::DAYS_AHEAD . ' days.',
            ],
        ]);
    }
}