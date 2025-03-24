<?php
function production_per_season($growth_time, $regrowth_time): int
{
    if ($growth_time == 0) return 0;
    $actual_day = 1;
    $production = 0;
    $first_harvest = false;

    while ($actual_day < 29) {
        if (!$first_harvest) {
            $actual_day += $growth_time;
            $first_harvest = true;
        } else {
            $actual_day += $regrowth_time ?: $growth_time;
        }
        if ($actual_day < 29) $production += 1;
    }
    return $production;
}

function find_cheapest_price($prices) {
    $filtered_prices = array_filter($prices, fn($price) => is_numeric($price) && $price > 0);
    return empty($filtered_prices) ? 0 : min($filtered_prices);
}

function find_cheapest_price_name($prices) {
    $filtered_prices = array_filter($prices, fn($price) => is_numeric($price) && $price > 0);
    if (empty($filtered_prices)) return "(No data)";
    // Find the index of the minimum price
    $min_index = array_keys($prices, min($filtered_prices))[0];
    switch ($min_index) {
        case 0: return "(Egg festival)";
        case 1: return "(General store)";
        case 2: return "(JojaMart)";
        case 3: return "(Night market)";
        case 4: return "(Oasis)";
        case 5: return "(Traveling cart)";
        default: return "(No data)";
    }
}

function calculate_gold_per_day($production, $purchase_price, $sell_price) {
    return ($production > 0 && $purchase_price !== null) ? (($production * $sell_price) - $purchase_price) / 28 : 0;
}

function dayOrDaysString($number): string
{
    return $number === 1 ? "day" : "days";
}
?>