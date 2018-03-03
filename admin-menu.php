<style>
<?php include 'style.css'?>
</style>

<?php
include 'logout.php';
?>

<html>
<title>Admin Menu</title>
<heading>Administrator's Menu</heading>
<form action="station-management.php">
    <input class="button" type="submit" name="station-management" value="Station Management"/>
</form>

<form action="suspended-cards.php">
    <input class="button" type="submit" name="suspended-cards" value="Suspended Cards"/>
</form>

<form action="breezecard-management.php">
    <input class="button" type="submit" name="breezecard-management" value="Breezecard Management"/>
</form>

<form action="passenger-flow-report.php">
    <input class="button" type="submit" name="passenger-flow-report" value="Passenger Flow Report"/>
</form>
</html>
