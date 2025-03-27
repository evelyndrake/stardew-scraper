<?php
function displayFishingTab($csvFile) {
echo '<div id="fishing" class="tabcontent" style="display: none">
    <div class="controls">
            <h4>Controls</h4>
            Fish data is from <a href="https://www.reddit.com/r/StardewValley/comments/4ayu8b/ultimate_fish_guide_spreadsheet_edition_which/">this spreadsheet</a>.
            <br/>
            
            <label for="sortTypeFishing">Sort fish by</label>
            <select id="sortTypeFishing">
                <option value="name">fish name (default)</option>
                <option value="difficulty">difficulty</option>
                <option value="sellPrice">sell price</option>
            </select>
            
            <br/>
            <label for="locationFilter">Only show fish from</label>
            <select id="locationFilter">
                <option value="all">All locations (default)</option>
                <option value="river">River</option>
                <option value="ocean">Ocean</option>
                <option value="nightMarket">Night Market</option>
                <option value="gingerIsland">Ginger Island</option>
                <option value="mountainLake">Mountain Lake</option>
                <option value="secretWoods">Secret Woods</option>
                <option value="witch\'sSwamp">Witch\'s Swamp</option>
                <option value="waterfalls">Waterfalls</option>
                <option value="mines">Mines</option>
                <option value="forestPond">Forest Pond</option>
                <option value="sewers">Sewers</option>
                <option value="desert">Desert</option>
                <option value="mutantBugLair">Mutant Bug Lair</option>
            </select>
            <br/>
            <input id="searchFish" class="search-box" type="text" placeholder="Search fish name...">
            
            
    
        </div>
        <table id="fishTable">
            <thead>
            <tr>
                <th>Fish Name</th>
                <th class="gold-price-column">Location</th>
                <th>Time</th>
                <th>Weather</th>
                <th>Difficulty</th>
                <th class="spring-column">Spring</th>
                <th class="summer-column">Summer</th>
                <th class="fall-column">Fall</th>
                <th class="winter-column">Winter</th>
                <th class="regular-price-column">Sell price (regular quality)</th>
                <th class="silver-price-column">Sell price (silver quality)</th>
                <th class="gold-price-column">Sell price (gold quality)</th>
                <th class="iridium-price-column">Sell price (iridium quality)</th>
            </tr>
            </thead>
            <tbody>';
            while (($row = fgetcsv($csvFile, 0, ',', '"', '\\')) !== false) {
                // Skip first row
                if ($row[0] === "Fish") {
                    continue;
                }

                $fish_name = htmlspecialchars($row[0]);
                $image_name = str_replace(" ", "_", $fish_name) . '.png';
                $image_path = "icons/fish/" . $image_name;

                $location = htmlspecialchars($row[11]);
                $time = htmlspecialchars($row[12]);
                $spring = htmlspecialchars($row[6]);
                $summer = htmlspecialchars($row[7]);
                $fall = htmlspecialchars($row[8]);
                $winter = htmlspecialchars($row[9]);
                $weather = htmlspecialchars($row[10]);
                $difficulty = htmlspecialchars($row[13]);
                $sell_price_regular = (int)htmlspecialchars($row[2]);
                $sell_price_silver = (int)htmlspecialchars($row[3]);
                $sell_price_gold = (int)htmlspecialchars($row[4]);
                $sell_price_iridium = (int)htmlspecialchars($row[5]);

                echo "<tr>";
                echo "<td><img src='{$image_path}' style='width: 32px; height: 32px; vertical-align: middle; margin-right: 5px;'> <a class='ListName' target='_blank' href='https://stardewvalleywiki.com/$fish_name'>{$fish_name}</a></td>";
                echo "<td class='gold-price-column-row'>{$location}</td>";
                echo "<td>{$time}</td>";
                echo "<td>{$weather}</td>";
                echo "<td>{$difficulty}</td>";
                if ($spring === "Yes") {
                    $spring = "<b>✔</b>";
                } else {
                    $spring = "x";
                }
                if ($summer === "Yes") {
                    $summer = "<b>✔</b>";
                } else {
                    $summer = "x";
                }
                if ($fall === "Yes") {
                    $fall = "<b>✔</b>";
                } else {
                    $fall = "x";
                }
                if ($winter === "Yes") {
                    $winter = "<b>✔</b>";
                } else {
                    $winter = "x";
                }
                echo "<td class='spring-column-row'>{$spring}</td>";
                echo "<td class='summer-column-row'>{$summer}</td>";
                echo "<td class='fall-column-row'>{$fall}</td>";
                echo "<td class='winter-column-row'>{$winter}</td>";

                echo "<td class='regular-price-column-row'>" . htmlspecialchars($sell_price_regular) . "</td>";
                echo "<td class='silver-price-column-row'>" . htmlspecialchars($sell_price_silver) . "</td>";
                echo "<td class='gold-price-column-row'>" . htmlspecialchars($sell_price_gold) . "</td>";
                echo "<td class='iridium-price-column-row'>" . htmlspecialchars($sell_price_iridium) . "</td>";
                echo "</tr>";
            }
        echo '</tbody></table></div></div>';
        echo "<script>
        document.getElementById('sortTypeFishing').addEventListener('change', function() {
            let sortType = this.value;
            let table = document.getElementById('fishTable').getElementsByTagName('tbody')[0];
            // Make visible
            table.style.display = 'table-row-group';
            let rows = Array.from(table.getElementsByTagName('tr'));
    
            rows.sort((a, b) => {
                if (sortType === 'name') {
                    let nameA = a.cells[0].textContent.toLowerCase();
                    let nameB = b.cells[0].textContent.toLowerCase();
                    return nameA.localeCompare(nameB);
                } else if (sortType === 'difficulty')
                {
                    // Order (easy, medium, hard, very hard, extreme)
                    let difficultyOrder = {
                        'Very Easy': 0,
                        'Easy': 1,
                        'Medium': 2,
                        'Hard': 3,
                        'Very Hard': 4,
                        'Extreme': 5
                    }
                    let difficultyA = difficultyOrder[a.cells[4].textContent] || 0;
                    let difficultyB = difficultyOrder[b.cells[4].textContent] || 0;
                    return difficultyA - difficultyB; // Sort in ascending order
                } else if (sortType === 'sellPrice') {
                    let sellA = parseFloat(a.cells[11].textContent) || 0;
                    let sellB = parseFloat(b.cells[11].textContent) || 0;
                    return sellA - sellB; // Sort in descending order
                }
            });
            table.innerHTML = '';
            rows.forEach(row => table.appendChild(row));
        });
        // Filter fishing table depending on location
        document.getElementById('locationFilter').addEventListener('change', function() {
            let selectedLocation = this.value.toLowerCase().replaceAll(' ','');
            let rows = document.querySelectorAll('#fishTable tbody tr');
            rows.forEach(row => {
                let location = row.cells[1].textContent.toLowerCase().replaceAll(' ','');
                if (selectedLocation === 'all' || location.includes(selectedLocation)) {
                    row.style.display = ''; // Show row
                } else {
                    row.style.display = 'none'; // Hide row
                }
            });
        });
        // Search fish table
        document.getElementById('searchFish').addEventListener('input', function() {
            let searchTerm = this.value.toLowerCase();
            let rows = document.querySelectorAll('#fishTable tbody tr');
            rows.forEach(row => {
                let fishName = row.cells[0].textContent.toLowerCase();
                if (fishName.includes(searchTerm)) {
                    row.style.display = ''; // Show row
                } else {
                    row.style.display = 'none'; // Hide row
                }
            });
        })
        </script>
        ";
}
?>