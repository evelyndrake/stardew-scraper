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

function calculate_xp_per_gold($xp, $growth_time, $regrowth_time, $gold_per_day): float
{
    $days_in_season = 28;

    if ($regrowth_time > 0) {
        $harvests = floor(($days_in_season - $growth_time) / $regrowth_time) + 1;
    } else {
        $harvests = floor($days_in_season / $growth_time);
    }

    $total_xp = $xp * $harvests;
    $total_gold = $gold_per_day * $days_in_season;

    if ($total_gold == 0) return INF;

    return round($total_xp / $total_gold, 5);
}

function calculate_accessibility_score($purchase_prices): int
{
    if ($purchase_prices[1] > 0) return 3; // general store
    if ($purchase_prices[0] > 0 || $purchase_prices[4] > 0 || $purchase_prices[3] > 0) return 2; // seasonal shops
    if ($purchase_prices[5] > 0) return 1; // traveling cart only
    return 0; // not purchasable
}

function calculate_profit_per_seed($production, $sell_price_regular, $purchase_price): float
{
    $profit = $production * $sell_price_regular - $purchase_price;
    $profitPerSeed = $profit / ($purchase_price ?: 1);
    return round($profitPerSeed, 5);
}


function calculate_effort_per_gold($growth_time, $regrowth_time, $gold_per_day): float
{
    // Number of harvests per season
    $days_in_season = 28;

    if ($regrowth_time > 0) {
        // Initial planting effort + (number of regrowths × lower effort per regrowth)
        $harvests = floor(($days_in_season - $growth_time) / $regrowth_time) + 1;
    } else {
        $harvests = floor($days_in_season / $growth_time);
    }

    $plant_effort = 1;
    $harvest_effort_per = $regrowth_time > 0 ? 0.5 : 1.0;
    $total_effort = $plant_effort + ($harvests * $harvest_effort_per);

    $total_gold = $gold_per_day * $days_in_season;

    if ($total_gold == 0) return INF; // Don't divide by zero

    return round($total_effort / $total_gold, 5);
}

function find_cheapest_price($prices) {
    $filtered_prices = array_filter($prices, fn($price) => is_numeric($price) && $price > 0);
    return empty($filtered_prices) ? 0 : min($filtered_prices);
}

function find_cheapest_price_name($prices): string
{
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