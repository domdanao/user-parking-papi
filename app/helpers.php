<?php

if (!function_exists('money')) {
    /**
     * Format a number as Philippine Peso
     *
     * @param int|float $amount
     * @return string
     */
    function money($amount): string
    {
        return '₱' . number_format($amount / 100, 2);
    }
}
