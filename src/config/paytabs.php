<?php


return [

    'PAYTABS_PROFILE_ID'  => env('PAYTABS_PROFILE_ID'),
    'PAYTABS_SERVER_KEY' =>  env('PAYTABS_SERVER_KEY'),
    'PAYTABS_BASE_URL' =>   env('PAYTABS_BASE_URL', "https://secure-egypt.paytabs.com"),
    'PAYTABS_CHECKOUT_LANG' => env('PAYTABS_CHECKOUT_LANG', "AR"),
    'PAYTABS_CURRENCY' => env('PAYTABS_CURRENCY', "EGP"),
    'VERIFY_ROUTE_NAME' => env('VERIFY_ROUTE_NAME'),

];
