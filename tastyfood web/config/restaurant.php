<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Restaurant Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration is sourced from the database (settings table) when
    | available, allowing admins to update values from the panel. Falls back
    | to environment variables defined in .env.
    |
    */

    'name' => env('RESTAURANT_NAME', 'Padang Rice'),

    'address' => env('RESTAURANT_ADDRESS', 'Jl. Terusan Mars Utara III No.8D, Manjahlega, Kec. Bandung Kidul, Kota Bandung, Jawa Barat 40267, Indonesia'),

    // Restaurant location coordinates
    'latitude' => env('RESTAURANT_LATITUDE', -6.9475),
    'longitude' => env('RESTAURANT_LONGITUDE', 107.6191),

    // Delivery settings
    'delivery_radius_km' => env('DELIVERY_RADIUS_KM', 10),
    'delivery_fee_per_km' => env('DELIVERY_FEE_PER_KM', 5000),
    'min_delivery_fee' => env('MIN_DELIVERY_FEE', 5000),
    'free_delivery_min_order' => env('FREE_DELIVERY_MIN_ORDER', 100000),
];
