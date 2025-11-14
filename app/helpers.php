<?php

// app/helpers.php

if (!function_exists('get_currency_symbol')) {
    function get_currency_symbol($currency = null)
    {
        $currency = $currency ?? config('app.currency', 'USD');
        
        $symbols = [
            'USD' => '$',
            'SYP' => 'sy',
            'EUR' => '€',
            'GBP' => '£',
            'NGN' => '₦',
            'KES' => 'KSh',
            'GHS' => 'GH₵',
            'ZAR' => 'R',
            'INR' => '₹',
            'PKR' => '₨',
            'BDT' => '৳',
            // Add more currencies as needed
        ];
        
        return $symbols[$currency] ?? 'sy';
    }
}
if (!function_exists('format_currency')) {
    function format_currency($amount, $decimals = 2, $currency = null)
    {
        $symbol = get_currency_symbol($currency);
        $formatted = number_format($amount, $decimals);
        
        return $symbol . $formatted;
    }
}
