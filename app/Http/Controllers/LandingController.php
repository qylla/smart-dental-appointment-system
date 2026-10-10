<?php

namespace App\Http\Controllers;

use App\Services\SlotService;
use Illuminate\View\View;

/**
 * Public landing page. Only needs one piece of live data: the next
 * open slot for the hero chip, so it never shows a made-up time.
 */
class LandingController extends Controller
{
    public function __invoke(SlotService $slots): View
    {
        return view('landing', [
            'nextSlot' => $slots->nextAvailable(),
        ]);
    }
}