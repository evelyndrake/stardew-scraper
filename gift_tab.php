<?php
function displayGiftTab($csvFile) {
echo '<div id="gifts" class="tabcontent" style="display: none">
    <div class="controls">
        <h4>Controls</h4>
        <input id="searchGifts" class="search-box" type="text" placeholder="Search gift name...">
        </br>
        <input id="searchPeople" class="search-box" type="text" placeholder="Search characters...">
    </div>
    <table id="giftTable">
        <thead>
        <tr>
            <th>Gift Name</th>
            <th class="gold-price-column">Characters Who Love</th>
            <th>Difficulty to Acquire</th>
            <th>How to Acquire</th>
        </tr>
        </thead>
        <tbody>';
        while (($row = fgetcsv($csvFile, 0, ',', '"', '\\')) !== false) {
            $gift_name = $row[1] ?? "";
            $characters_love = $row[2] ?? ""; // Characters who love this gift
            $difficulty_to_acquire = $row[6] ?? ""; // Difficulty to acquire this gift
            $image_name = str_replace(" ", "_", $gift_name) . '.png';
            // Remove ' in image name
            $image_name = str_replace("'", "", $image_name);
            $image_path = "icons/gifts/" . $image_name;
            $how_to_acquire = $row[7] ?? ""; // How to acquire this gift
            // Skip first row
            if ($row[0]) {
                continue;
            }
            echo "<tr>";
            echo "<td><img src='{$image_path}' style='width: 32px; height: 32px; vertical-align: middle; margin-right: 5px;'><a class='listName' target='_blank' href='https://stardewvalleywiki.com/$gift_name'>{$gift_name}</a></td>";
            // Display characters who love this gift
            if (!empty($characters_love)) {
                $characters_love = str_replace(", ", ", ", $characters_love); // Format the string
                echo "<td class='gold-price-column-row'>{$characters_love}</td>";
            } else {
                echo "<td>(No characters love this gift)</td>";
            }
            echo "<td>{$difficulty_to_acquire}</td>";
            if (!empty($how_to_acquire)) {
                // Format the string to be more readable
                $how_to_acquire = str_replace(", ", ", ", $how_to_acquire); // Format the string
                echo "<td>{$how_to_acquire}</td>";
            } else {
                echo "<td>(No information on how to acquire this gift)</td>";
            }
            echo "</tr>";

        }
    echo '</tbody></table></div></div>';
        echo "<script>
        document.getElementById('searchGifts').addEventListener('input', function() {
            let searchTerm = this.value.toLowerCase();
            let rows = document.querySelectorAll('#giftTable tbody tr');
            rows.forEach(row => {
                let giftName = row.cells[0].textContent.toLowerCase();
                if (giftName.includes(searchTerm)) {
                    row.style.display = ''; // Show row
                } else {
                    row.style.display = 'none'; // Hide row
                }
            });
        });
        document.getElementById('searchPeople').addEventListener('input', function() {
            let searchTerm = this.value.toLowerCase();
            let rows = document.querySelectorAll('#giftTable tbody tr');
            rows.forEach(row => {
                let personName = row.cells[1].textContent.toLowerCase();
                if (personName.includes(searchTerm)) {
                    row.style.display = ''; // Show row
                } else {
                    row.style.display = 'none'; // Hide row
                }
            });
        })</script>";
}
?>