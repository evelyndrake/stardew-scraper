<?php
include 'data_functions.php';
include 'farming_tab.php';
include 'fishing_tab.php';
include 'about_tab.php';
include 'gift_tab.php';
$cropFile = fopen('data/crops_raw_data.csv', 'r');
$fishFile = fopen('data/fish_raw_data.csv', 'r');
$giftFile = fopen('data/gifts_raw_data.csv', 'r');

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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

</head>
<body>
<h1>Stardew Calculator</h1>
<div>
<p><i>A profit calculator for the farming game <a href='https://www.stardewvalley.net/'>Stardew Valley</a>.</i></i></p>
</div>
<hr/>
<div class="tab">
    <button class="tablinks" onclick="openTab(event, 'farming')"><img src='icons/tabs/Watering_Can.png' style='width: 32px; height: 32px; vertical-align: middle; margin-right: 5px;'>Farming</button>
    <button class="tablinks" onclick="openTab(event, 'fishing')"><img src='icons/tabs/Fiberglass_Rod.png' style='width: 32px; height: 32px; vertical-align: middle; margin-right: 5px;'>Fishing</button>
    <button class="tablinks" onclick="openTab(event, 'gifts')"><img src='icons/tabs/Heart.png' style='width: 32px; height: 32px; vertical-align: middle; margin-right: 5px;'>Gifts</button>
    <button class="tablinks" onclick="openTab(event, 'about')"><img src='icons/fish/Energy.png' style='width: 32px; height: 32px; vertical-align: middle; margin-right: 5px;'>About</button>
</div>
<?php
displayFarmingTab($cropFile);
displayFishingTab($fishFile);
displayGiftTab($giftFile);
displayAboutTab();
?>


<script>
    // Tab functionality
    function openTab(evt, tabName) {
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
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }
    openTab(event, 'farming');
</script>
<?php


fclose($cropFile);
fclose($fishFile);
fclose($giftFile);
?>

</body>
</html>