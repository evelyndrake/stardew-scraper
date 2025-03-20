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
<h1>Stardew Scraper</h1>
<p><i>A profit calculator for the farming game <a href='https://www.stardewvalley.net/'>Stardew Valley</a> written by <a href='https://www.github.com/evelyndrake'>Evelyn Drake</a> for CSDS285 (Linux Tools and Scripting).</i></p>
<?php
// Load the CSV file
$csvFile = fopen('data/crops_raw_data.csv', 'r');

// Check if the file was successfully opened
if ($csvFile !== false) {
    echo '<table>';

    // Read the header row
    $headers = fgetcsv($csvFile,0, ',', '"', '\\');
    if ($headers !== false) {
        $displayedColumns = [1, 4];
        echo '<tr>';
        echo '<th>Seed name</th>';
        echo '<th>Growth time</th>';
        echo '<th class="regular-price-column">Sell price (regular)</th>';
        echo '<th class="silver-price-column">Sell price (silver)</th>';
        echo '<th class="gold-price-column">Sell price (gold)</th>';
        echo '</tr>';
    }

    // Loop through the rows and generate table rows
    while (($row = fgetcsv($csvFile, 0, ',', '"', '\\')) !== false) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row[4]) . '</td>';
        echo '<td>' . htmlspecialchars($row[1]) . '</td>';
        echo '<td class="regular-price-column-row">' . htmlspecialchars($row[17]) . '</td>';
        echo '<td class="silver-price-column-row">' . htmlspecialchars($row[3]) . '</td>';
        echo '<td class="gold-price-column-row">' . htmlspecialchars($row[5]) . '</td>';
    }

    echo '</table>';
    fclose($csvFile);
} else {
    echo '<p>Unable to load the CSV file.</p>';
}
?>

</body>
</html>