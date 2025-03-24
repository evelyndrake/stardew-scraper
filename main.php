<?php
include 'data_functions.php';
include 'farming_tab.php';
include 'fishing_tab.php';
$csvFile = fopen('data/crops_raw_data.csv', 'r');
$fishFile = fopen('data/fish_raw_data.csv', 'r');

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
<div class="tab">
    <button class="tablinks" onclick="openTab(event, 'farming')"><img src='icons/tabs/Watering_Can.png' style='width: 32px; height: 32px; vertical-align: middle; margin-right: 5px;'>Farming</button>
    <button class="tablinks" onclick="openTab(event, 'fishing')"><img src='icons/tabs/Fiberglass_Rod.png' style='width: 32px; height: 32px; vertical-align: middle; margin-right: 5px;'>Fishing</button>
</div>
<?php
displayFarmingTab($csvFile);
displayFishingTab($fishFile);
?>


<script>
    // Tab functionality
    function openTab(evt, cityName) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }
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
        openTab(event, 'farming'); // Ensure the farming tab is open after sorting
    });
</script>
<?php


fclose($csvFile);
?>

</body>
</html>