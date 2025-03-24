<?php
function displayFishingTab($csvFile) {
echo '<div id="fishing" class="tabcontent" style="display: none">
    <div class="controls">
            <h4>Controls</h4>
            Fish data is from <a href="https://www.reddit.com/r/StardewValley/comments/4ayu8b/ultimate_fish_guide_spreadsheet_edition_which/">this spreadsheet</a>.
            <br/>
            <label for="sortTypeFishing">Sort table by:</label>
            <select id="sortTypeFishing">
                <option value="name">fish name (default)</option>
                <option value="difficulty">difficulty</option>
                <option value="sellPrice">sell price</option>
            </select>
            <br/>
        <!--    <label for="showAllPrices">Show all sale prices:</label>-->
        <!--    <input type="checkbox" id="showAllPrices" checked="checked">-->
    
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
                echo "<td><img src='{$image_path}' style='width: 32px; height: 32px; vertical-align: middle; margin-right: 5px;'> {$fish_name}</td>";
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
}
?>