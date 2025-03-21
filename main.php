<?php
include 'data_functions.php';
$csvFile = fopen('data/crops_raw_data.csv', 'r');
function dayOrDaysString($number): string
{
    return $number === 1 ? "day" : "days";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Stardew Scraper</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        <?php include 'style.css'; ?>
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
<h1>Stardew Calculator</h1>
<div>
<p><i>A profit calculator for the farming game <a href='https://www.stardewvalley.net/'>Stardew Valley</a> written by <a href="https://www.github.com/evelyndrake">Evelyn Drake</a>
        for CSDS285 (Linux Tools and Scripting). View the source code <a href="https://github.com/evelyndrake/stardew-scraper">here.</a></i></i></p>
</div>
<hr/>
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
    <tbody>
    <?php
    while (($row = fgetcsv($csvFile, 0, ',', '"', '\\')) !== false) {
        $seed_name = htmlspecialchars($row[4]);
        if ($seed_name === "seed") { // I was never able to figure this out, but there was an extra row that wasn't in the dataset
            continue;
        }
        $growth_time = (int)htmlspecialchars($row[1]);
        $regrowth_time = (int)htmlspecialchars($row[19]);
        $sell_price_regular = (int)htmlspecialchars($row[17]);
        $sell_price_silver = (int)htmlspecialchars($row[3]);
        $sell_price_gold = (int)htmlspecialchars($row[5]);
        $sell_price_iridium = (int)htmlspecialchars($row[13]);
        $production = production_per_season($growth_time, $regrowth_time);
        $farming_xp = (int)htmlspecialchars($row[22]);
        $purchase_prices = [
            (int)htmlspecialchars($row[10]),
            (int)htmlspecialchars($row[7]),
            (int)htmlspecialchars($row[25]),
            (int)htmlspecialchars($row[8]),
            (int)htmlspecialchars($row[21]),
            (int)htmlspecialchars($row[12]),
        ];
        $purchase_price = find_cheapest_price($purchase_prices);
        $gold_per_day = calculate_gold_per_day($production, $purchase_price, $sell_price_regular);
        $growth_time_string = $growth_time . " " . dayOrDaysString($growth_time);
        $regrowth_time_string = $regrowth_time . " " . dayOrDaysString($regrowth_time);
        if ($regrowth_time === 0) {
            $regrowth_time_string = "instant";
        }
        echo "<tr data-regular='{$sell_price_regular}' data-silver='{$sell_price_silver}' data-gold='{$sell_price_gold} ' data-iridium='{$sell_price_iridium}'>";
        echo "<td>{$seed_name}</td>";
        echo "<td class='gold-price-column-row' data-goldperday='" . number_format($gold_per_day, 2) . "'>" . number_format($gold_per_day, 2) . "</td>";
        echo "<td>{$growth_time_string}</td>";
        echo "<td>{$regrowth_time_string}</td>";
        echo "<td>{$production}</td>";
        echo "<td class='purchase-price-column-row'>" . htmlspecialchars($purchase_price) . "</td>";
        echo "<td class='regular-price-column-row'>" . htmlspecialchars($sell_price_regular) . "</td>";
        echo "<td class='silver-price-column-row'>" . htmlspecialchars($sell_price_silver) . "</td>";
        echo "<td class='gold-price-column-row'>" . htmlspecialchars($sell_price_gold) . "</td>";
        echo "<td class='iridium-price-column-row'>" . htmlspecialchars($sell_price_iridium) . "</td>";
        echo "<td class='farming-xp-column-row'>" . htmlspecialchars($farming_xp) . "</td>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>


<script>
    // Initally, sort alphabetically by seed name
    let table = document.getElementById("cropTable").getElementsByTagName('tbody')[0];
    let rows = Array.from(table.getElementsByTagName("tr"));
    rows.sort((a, b) => {
        let nameA = a.cells[0].textContent.toLowerCase();
        let nameB = b.cells[0].textContent.toLowerCase();
        return nameA.localeCompare(nameB);
    });
    table.innerHTML = "";
    rows.forEach(row => table.appendChild(row));
    // Event listeners for controls
    // Calculate gold per day based on selected sell price type
    document.getElementById('sellPriceType').addEventListener('change', function() {
        let selectedType = this.value;
        let rows = document.querySelectorAll("#cropTable tbody tr");

        rows.forEach(row => {
            let production = parseFloat(row.cells[4].textContent) || 0;
            let purchasePrice = parseFloat(row.cells[5].textContent) || 0;
            let sellPrice = parseFloat(row.dataset[selectedType]) || 0;

            let goldPerDay = (production > 0 && purchasePrice !== null) ?
                (((production * sellPrice) - purchasePrice) / 28).toFixed(2) :
                "0.00";

            row.cells[1].textContent = goldPerDay;
        });
    });
    function updateSalePrices() {

    }
    // Show all sale prices if checkbox is checked, otherwise just show selected quality
    // CLEAN THIS UP!
    // document.getElementById('showAllPrices').addEventListener('change', function() {
    //     let showAll = this.checked;
    //     let rows = document.querySelectorAll("#cropTable tbody tr");
    //     let headerRow = document.querySelector("#cropTable thead tr");
    //     rows.forEach(row => {
    //         row.cells[6].style.display = showAll ? "" : "none";
    //         row.cells[7].style.display = showAll ? "" : "none";
    //         row.cells[8].style.display = showAll ? "" : "none";
    //         row.cells[9].style.display = showAll ? "" : "none";
    //         headerRow.cells[6].style.display = showAll ? "" : "none";
    //         headerRow.cells[7].style.display = showAll ? "" : "none";
    //         headerRow.cells[8].style.display = showAll ? "" : "none";
    //         headerRow.cells[9].style.display = showAll ? "" : "none";
    //
    //     });
    //     if (!showAll) {
    //         let selectedType = document.getElementById('sellPriceType').value;
    //         rows.forEach(row => {
    //             row.cells[6].style.display = selectedType === 'regular' ? "" : "none";
    //             row.cells[7].style.display = selectedType === 'silver' ? "" : "none";
    //             row.cells[8].style.display = selectedType === 'gold' ? "" : "none";
    //             row.cells[9].style.display = selectedType === 'iridium' ? "" : "none";
    //             headerRow.cells[6].style.display = selectedType === 'regular' ? "" : "none";
    //             headerRow.cells[7].style.display = selectedType === 'silver' ? "" : "none";
    //             headerRow.cells[8].style.display = selectedType === 'gold' ? "" : "none";
    //             headerRow.cells[9].style.display = selectedType === 'iridium' ? "" : "none";
    //         });
    //     }
    // });
    // Sort table depending on selected sort type
    document.getElementById('sortType').addEventListener('change', function() {
        let sortType = this.value;
        let table = document.getElementById("cropTable").getElementsByTagName('tbody')[0];
        let rows = Array.from(table.getElementsByTagName("tr"));

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
                let priceA = parseFloat(a.cells[5].textContent) || 0;
                let priceB = parseFloat(b.cells[5].textContent) || 0;
                return priceA - priceB; // Sort in ascending order
            } else if (sortType === 'farmingXP') {
                let xpA = parseFloat(a.cells[9].textContent) || 0;
                let xpB = parseFloat(b.cells[9].textContent) || 0;
                return xpB - xpA; // Sort in descending order
            } else if (sortType === 'sellPrice') {
                let sellA = parseFloat(a.cells[6].textContent) || 0;
                let sellB = parseFloat(b.cells[6].textContent) || 0;
                return sellB - sellA; // Sort in descending order
            }
        });

        table.innerHTML = "";
        rows.forEach(row => table.appendChild(row));
    });
</script>
<?php


fclose($csvFile);
?>

</body>
</html>