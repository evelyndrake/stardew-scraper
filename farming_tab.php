<?php
function displayFarmingTab($csvFile) {
    echo '<div id="farming" class="tabcontent">
    <div class="controls">
        <h4>Controls</h4>
        <label for="sortType">Sort table by:</label>
        <select id="sortType">
            <option value="name">seed name (default)</option>
            <option value="goldPerDay">gold per day</option>
            <option value="purchasePrice">cheapest purchase price</option>
            <option value="farmingXP">farming XP gained</option>
            <option value="sellPrice">sell price</option>
        </select>
        <br/>
        <label for="sellPriceType">Calculate Gold per Day based on:</label>
        <select id="sellPriceType">
            <option value="regular">regular quality crops</option>
            <option value="silver">silver quality crops</option>
            <option value="gold">gold quality crops</option>
            <option value="iridium">iridium quality crops</option>
        </select>
        <br/>
    <!--    <label for="showAllPrices">Show all sale prices:</label>-->
    <!--    <input type="checkbox" id="showAllPrices" checked="checked">-->

    </div>

    <table id="cropTable">
        <thead>
        <tr>
            <th>Seed name</th>
            <th class="gold-price-column">Gold per day</th>
            <th>Growth time</th>
            <th>Regrowth time</th>
            <th>Production per season</th>
            <th class="purchase-price-column">Cheapest purchase price</th>
            <th class="regular-price-column">Sell price (regular quality)</th>
            <th class="silver-price-column">Sell price (silver quality)</th>
            <th class="gold-price-column">Sell price (gold quality)</th>
            <th class="iridium-price-column">Sell price (iridium quality)</th>
            <th class="farming-xp-column">Farming XP</th>
        </tr>
        </thead>
        <tbody>';

    while (($row = fgetcsv($csvFile, 0, ',', '"', '\\')) !== false) {
            $seed_name = htmlspecialchars($row[4]);
            if ($seed_name === "seed") { // I was never able to figure this out, but there was an extra row that wasn't in the dataset
                continue;
            }
            $image_name = str_replace(" ", "_", $seed_name) . '.png';
            $image_path = "icons/seeds/" . $image_name;
            $growth_time = (int)htmlspecialchars($row[1]);
            $regrowth_time = (int)htmlspecialchars($row[19]);
            $sell_price_regular = (int)htmlspecialchars($row[17]);
            $sell_price_silver = (int)htmlspecialchars($row[3]);
            $sell_price_gold = (int)htmlspecialchars($row[5]);
            $sell_price_iridium = (int)htmlspecialchars($row[13]);
            $production = production_per_season($growth_time, $regrowth_time);
            $farming_xp = (int)htmlspecialchars($row[22]);
            $purchase_prices = [
                (int)htmlspecialchars($row[10]), // Egg festival
                (int)htmlspecialchars($row[7]),  // General store
                (int)htmlspecialchars($row[25]), // JojaMart
                (int)htmlspecialchars($row[8]),  // Night market
                (int)htmlspecialchars($row[21]), // Oasis
                (int)htmlspecialchars($row[12]), // Traveling cart
            ];
            $purchase_price = find_cheapest_price($purchase_prices);
            $purchase_price_name = find_cheapest_price_name($purchase_prices);
            $description = htmlspecialchars($row[6]);
            $gold_per_day = calculate_gold_per_day($production, $purchase_price, $sell_price_regular);
            $growth_time_string = $growth_time . " " . dayOrDaysString($growth_time);
            $regrowth_time_string = $regrowth_time . " " . dayOrDaysString($regrowth_time);
            if ($regrowth_time === 0) {
                $regrowth_time_string = "instant";
            }
            echo "<tr data-regular='{$sell_price_regular}' data-silver='{$sell_price_silver}' data-gold='{$sell_price_gold}' data-iridium='{$sell_price_iridium}'>";
            echo "<td><img src='{$image_path}' style='width: 32px; height: 32px; vertical-align: middle; margin-right: 5px;' title = '{$description}'> {$seed_name}</td>";
            echo "<td class='gold-price-column-row' data-goldperday='" . number_format($gold_per_day, 2) . "'>" . number_format($gold_per_day, 2) . "</td>";
            echo "<td>{$growth_time_string}</td>";
            echo "<td>{$regrowth_time_string}</td>";
            echo "<td>{$production}</td>";
            echo "<td class='purchase-price-column-row'>" . htmlspecialchars($purchase_price) . " <div class='small-text'>$purchase_price_name</div></td>";
            echo "<td class='regular-price-column-row'>" . htmlspecialchars($sell_price_regular) . "</td>";
            echo "<td class='silver-price-column-row'>" . htmlspecialchars($sell_price_silver) . "</td>";
            echo "<td class='gold-price-column-row'>" . htmlspecialchars($sell_price_gold) . "</td>";
            echo "<td class='iridium-price-column-row'>" . htmlspecialchars($sell_price_iridium) . "</td>";
            echo "<td class='farming-xp-column-row'>" . htmlspecialchars($farming_xp) . "</td>";
            echo "</tr>";
        }

    echo '</tbody></table></div>';
}
?>