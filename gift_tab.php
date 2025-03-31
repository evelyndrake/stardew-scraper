<?php
function displayGiftTab($csvFile) {
echo '<div id="gifts" class="tabcontent" style="display: none">
    <div class="controls">
        <h4>Controls</h4>
    </div>
    <table id="giftTable">
        <thead>
        <tr>
            <th>Gift Name</th>
            <th>Characters Who Love</th>
            <th>Difficulty to Acquire</th>
        </tr>
        </thead>
        <tbody>';
        while (($row = fgetcsv($csvFile, 0, ',', '"', '\\')) !== false) {
            $gift_name = $row[1] ?? "";
            $characters_love = $row[2] ?? ""; // Characters who love this gift
            $difficulty_to_acquire = $row[6] ?? ""; // Difficulty to acquire this gift
            // Skip first row
            if ($row[0]) {
                continue;
            }
            echo "<tr>";
            echo "<td>{$gift_name}</td>";
            // Display characters who love this gift
            if (!empty($characters_love)) {
                $characters_love = str_replace(", ", ", ", $characters_love); // Format the string
                echo "<td>{$characters_love}</td>";
            } else {
                echo "<td>(No characters love this gift)</td>";
            }
            echo "<td>{$difficulty_to_acquire}</td>";
            echo "</tr>";

        }
    echo '</tbody></table></div></div>';
}
?>