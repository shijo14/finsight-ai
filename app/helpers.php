<?php

if (! function_exists('format_money')) {
    /**
     * Format an amount with the app's currency symbol (Indian Rupees).
     */
    function format_money(float|int|string|null $amount, int $decimals = 2): string
    {
        return 'Rs ' . number_format((float) $amount, $decimals);
    }
}
