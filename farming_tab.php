<?php
function displayFarmingTab($csvFile)
{echo '<div id="farming" class="tabcontent">
    <div class="controls">
        <h4>Controls</h4>
        <label for="sortType">Sort crops by</label>
        <select id="sortType">
            <option value="name">seed name (default)</option>
            <option value="goldPerDay">gold per day</option>
            <option value="purchasePrice">cheapest purchase price</option>
            <option value="farmingXP">farming XP gained</option>
            <option value="sellPrice">sell price</option>
            <option value="effortPerGold">effort per gold</option>
            <option value="XPperGold">XP per gold</option>
            <option value="accessibilityScore">accessibility score</option>
            <option value="profitPerSeed">profit per seed</option>
        </select>
        <br/>
        <label for="sellPriceType">Calculate Gold per Day based on</label>
        <select id="sellPriceType">
            <option value="regular">regular quality crops</option>
            <option value="silver">silver quality crops</option>
            <option value="gold">gold quality crops</option>
            <option value="iridium">iridium quality crops</option>
        </select>
        </br>
        <label for="numberOfSeeds">Number of seeds</label>
        <input type="number" id="numberOfSeeds" value="1" min="1" max="9999"></input>
        <br/>
         <input id="searchCrop" class="search-box" type="text" placeholder="Search crop name...">
        <div class="notes-section">
            <h4 class="notes-toggle" style="margin-top: 0.3em" onclick="toggleNotes()">▶ Notes (click to expand)</h4>
            <div id="notesContent" style="display: none; margin-left: 3px;">
                    <strong>Gold per day</strong>: (Production × Sell Price − Purchase Price) / 28
                    </br>
                    <strong>Effort per gold</strong>: (Total harvests × Time per harvest) / Total Gold
                    </br>
                    <strong>XP per gold</strong>: Total Farming XP / Total Gold Earned
                    </br>
                    <strong>Accessibility score</strong>: Based on how easily seeds can be obtained
                    <ul style="margin-top=0em">
                        <li>3 - Available at the general store</li>
                        <li>2 - Available at a seasonal store</li>
                        <li>1 - Only available at traveling cart</li>
                        <li>0 - Available by special means only (see wiki page by clicking item name)</li>
                    </ul>
                    <strong>Profit per seed</strong>: (Production × Sell Price) − Purchase Price
            </div>
        </div>
    </div>

    <table id="cropTable">
        <thead>
        <tr>
            <th>Seed name</th>
            <th class="gold-price-column">Gold per day</th>
            <th class="summer-column">Effort per gold</th>
            <th class="fall-column">XP per gold</th>
            <th class="winter-column">Accessibility score</th>
            <th class="spring-column">Profit per seed</th>
            <th>Growth time</th>
            <th>Regrowth time</th>
            <th>Production per season</th>
            <th class="purchase-price-column">Cheapest purchase price</th>
            <th class="regular-price-column">Sell price (regular)</th>
            <th class="silver-price-column">Sell price (silver)</th>
            <th class="gold-price-column">Sell price (gold)</th>
            <th class="iridium-price-column">Sell price (iridium)</th>
            <th class="farming-xp-column">Farming XP</th>
        </tr>
        </thead>
    <tbody>';
        while (($row = fgetcsv($csvFile, 0, ',', '"', '\\')) !== false) {
                $seed_name = htmlspecialchars($row[4]);
                if ($seed_name === "seed" || $seed_name === "Ancient Seeds") { // Skip the header row
                        // Also skip ancient seeds because they're missing data
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
                $effort_per_gold = calculate_effort_per_gold($growth_time, $regrowth_time, $gold_per_day);
                if ($effort_per_gold === INF) {
                        $effort_per_gold = "No data";
                } else {
                        $effort_per_gold = number_format($effort_per_gold, 3);
                }
                $xp_per_gold = calculate_xp_per_gold($farming_xp, $growth_time, $regrowth_time, $gold_per_day);
                $accessibility_score = calculate_accessibility_score($purchase_prices);
                $profit_per_seed = calculate_profit_per_seed($production, $sell_price_regular, $purchase_price);
                echo "<tr title='{$description}' data-regular='{$sell_price_regular}' data-silver='{$sell_price_silver}' data-gold='{$sell_price_gold}' data-iridium='{$sell_price_iridium}'>";
                echo "<td><img src='{$image_path}' style='width: 32px; height: 32px; vertical-align: middle; margin-right: 5px;' title='{$description}'> <a class='listName' target='_blank' href='https://stardewvalleywiki.com/$seed_name'>{$seed_name}</a></td>";
                echo "<td class='gold-price-column-row' data-goldperday='" . number_format($gold_per_day, 2) . "'>" . number_format($gold_per_day, 2) . "</td>";
                echo "<td class='summer-column-row'>" . $effort_per_gold . "</td>";
                echo "<td class='fall-column-row'>" . number_format($xp_per_gold, 3) . "</td>";
                echo "<td class='winter-column-row'>" . $accessibility_score . "</td>";
                echo "<td class='spring-column-row'>" . number_format($profit_per_seed, 0) . "</td>";
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
        echo "<script>
            let table = document.getElementById('cropTable').getElementsByTagName('tbody')[0];
            let rows = Array.from(table.getElementsByTagName('tr'));
            rows.sort((a, b) => {
                let nameA = a.cells[0].textContent.toLowerCase();
                let nameB = b.cells[0].textContent.toLowerCase();
                return nameA.localeCompare(nameB);
            });
            table.innerHTML = '';
            rows.forEach(row => table.appendChild(row));
            // Calculate gold per day based on selected sell price type
            document.getElementById('sellPriceType').addEventListener('change', function() {
                let selectedType = this.value;
                let rows = document.querySelectorAll('#cropTable tbody tr');
        
                rows.forEach(row => {
                    let production = parseFloat(row.cells[4].textContent) || 0;
                    let purchasePrice = parseFloat(row.cells[5].textContent) || 0;
                    let sellPrice = parseFloat(row.dataset[selectedType]) || 0;
        
                    let goldPerDay = (production > 0 && purchasePrice !== null) ?
                        (((production * sellPrice) - purchasePrice) / 28).toFixed(2) :
                        '0.00';
        
                    row.cells[1].textContent = goldPerDay;
                });
            });
            // Sort table depending on selected sort type
            document.getElementById('sortType').addEventListener('change', function() {
                let sortType = this.value;
                let table = document.getElementById('cropTable').getElementsByTagName('tbody')[0];
                let rows = Array.from(table.getElementsByTagName('tr'));
        
                rows.sort((a, b) => {
                    if (sortType === 'name') {
                        let nameA = a.cells[0].textContent.toLowerCase();
                        let nameB = b.cells[0].textContent.toLowerCase();
                        return nameA.localeCompare(nameB);
                    } else if (sortType === 'goldPerDay') {
                        let goldA = parseFloat(a.cells[1].dataset.goldperday) || 0;
                        let goldB = parseFloat(b.cells[1].dataset.goldperday) || 0;
                        return goldB - goldA; // Sort in descending order
                    } else if (sortType === 'purchasePrice') {
                        let priceA = parseFloat(a.cells[9].textContent) || 0;
                        let priceB = parseFloat(b.cells[9].textContent) || 0;
                        return priceA - priceB; // Sort in ascending order
                    } else if (sortType === 'farmingXP') {
                        let xpA = parseFloat(a.cells[14].textContent) || 0;
                        let xpB = parseFloat(b.cells[14].textContent) || 0;
                        return xpB - xpA; // Sort in descending order
                    } else if (sortType === 'sellPrice') {
                        let sellA = parseFloat(a.cells[10].textContent) || 0;
                        let sellB = parseFloat(b.cells[10].textContent) || 0;
                        return sellB - sellA; // Sort in descending order
                    } else if (sortType === 'effortPerGold') {
                        let effortA = parseFloat(a.cells[2].textContent) || 0;
                        let effortB = parseFloat(b.cells[2].textContent) || 0;
                        return effortA - effortB; // Sort in ascending order
                    } else if (sortType === 'XPperGold') {
                        let scoreA = parseFloat(a.cells[3].textContent) || 0;
                        let scoreB = parseFloat(b.cells[3].textContent) || 0;
                        return scoreB - scoreA; // Sort in descending order
                    } else if (sortType === 'accessibilityScore') {
                        let scoreA = parseFloat(a.cells[4].textContent) || 0;
                        let scoreB = parseFloat(b.cells[4].textContent) || 0;
                        return scoreB - scoreA; // Sort in descending order
                    }  else if (sortType === 'profitPerSeed') {
                        let profitA = parseFloat(a.cells[5].textContent) || 0;
                        let profitB = parseFloat(b.cells[5].textContent) || 0;
                        return profitB - profitA; // Sort in descending order
                    }
                });
                table.innerHTML = '';
                rows.forEach(row => table.appendChild(row));
            });
            // Recalculate gold per day based on number of seeds
            document.getElementById('numberOfSeeds').addEventListener('change', function() {
                let numberOfSeeds = parseInt(this.value) || 1;
                let rows = document.querySelectorAll('#cropTable tbody tr');
                let selectedType = document.getElementById('sellPriceType').value;
                console.log('test');
                rows.forEach(row => {
                    let production = parseFloat(row.cells[4].textContent) || 0;
                    let purchasePrice = parseFloat(row.cells[5].textContent) || 0;
                    let sellPrice = parseFloat(row.dataset[selectedType]) || 0;
        
                    let goldPerDay = (production > 0 && purchasePrice !== null) ?
                        (((production * sellPrice) - purchasePrice) / 28).toFixed(2) :
                        '0.00';
        
                    row.cells[1].textContent = (goldPerDay * numberOfSeeds).toFixed(2);
                });
            });
            // Search crop table
            document.getElementById('searchCrop').addEventListener('input', function() {
            let searchTerm = this.value.toLowerCase();
            let rows = document.querySelectorAll('#cropTable tbody tr');
            rows.forEach(row => {
                let cropName = row.cells[0].textContent.toLowerCase();
                if (cropName.includes(searchTerm)) {
                    row.style.display = ''; // Show row
                } else {
                    row.style.display = 'none'; // Hide row
                }
            });
        })
        function toggleNotes() {
            const notes = document.getElementById('notesContent');
            const header = document.querySelector('.notes-toggle');
        if (notes.style.display === 'none') {
            notes.style.display = 'block';
            header.innerHTML = '▼ Notes (click to collapse)';
        } else {
            notes.style.display = 'none';
            header.innerHTML = '▶ Notes (click to expand)';
        }
        }
        </script>";
}
?>
