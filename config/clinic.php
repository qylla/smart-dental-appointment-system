<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Clinic contact details
    |--------------------------------------------------------------------------
    |
    | WhatsApp number in international format (no + and no leading zero),
    | used by the floating WhatsApp contact button.
    |
    */

    'whatsapp' => env('CLINIC_WHATSAPP'),
    'phone'    => '014-915 3244',
    'location' => 'Pasir Mas, Kelantan',
    'maps_query' => 'KLINIK PERGIGIAN DR ZURAIDA PASIR MAS',

    /*
     * Starting prices (RM) from the clinic's own printed price list.
     * Only treatments on that list are here; anything missing shows
     * "price after check-up" instead of a guessed number.
     */
    'prices' => [
        'scaling'   => 89,
        'extract'   => 50,
        'filling'   => 80,
        'rootcanal' => 450,
        'whitening' => 229,
    ],

    // When the list above was last checked against the clinic's price list.
    'prices_updated' => 'September 2026',
];