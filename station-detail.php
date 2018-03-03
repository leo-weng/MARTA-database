<?php
include 'dbinfo.php';
include 'logout.php';
?>

<html>
<title>Station Detail</title>
<heading>Station Detail</heading>
<form action="station-management.php">
    <input class="button" type="submit" name="back" value="Back to Station Management"/>
</form>
</html>

<?php
    session_start();
    @mysql_connect($host,$username,$password) or die( "Unable to connect");;
    mysql_select_db($database) or die( "Unable to select database");

    $name = $_GET['name'];
    $stopID = $_GET['stopID'];
    $fare = $_GET['fare'];
    $status = $_GET['status'];
    $istrain = $_GET['istrain'];
    $intersection = "";
    if ($istrain == False) {
        $intersection_query = "SELECT Intersection FROM BusStationIntersection WHERE StopID = '$stopID'";
        $intersection = mysql_query($intersection_query) or die(mysql_error());
    }
    if(isset($_POST['update'])) {
        $newfare = test_input($_POST["fare"]);
        $newstatus= test_input($_POST["newstatus"]);
        if ($newfare != $fare) {
            $fare_query = "UPDATE Station SET EnterFare = '$newfare' WHERE StopID = '$stopID'";
            $fare_result = mysql_query($fare_query) or die(mysql_error());
        }
        #NOT WORKING RIGHT NOW
        if ($newstatus != $status) {
            $status_query = "UPDATE Station SET ClosedStatus = '$newstatus' WHERE StopID = '$stopID'";
            $status_result = mysql_query($status_query) or die(mysql_error());
        }
        header('Location: station-management.php');
    }


    echo "<p>$name</p>";
    echo "<p>Stop ID: $stopID</p>";
    echo "<form action=\"\" method=\"POST\" id=\"changeform\">";
    echo "<p>Fare: ";
    echo "<input name=\"fare\" value=$fare type=\"number\" min=\"0\" max=\"50.00\" step=\"0.01\" required/>";
    echo "</p>";

    if ($istrain == False) {
        $intersection = mysql_result($intersection, 0, "Intersection");
        echo "<p>Nearest Intersection: $intersection</p><br>";
    }
    echo "<p><select name=\"newstatus\" form = \"changeform\">";
    if ($status == True) {
        echo "<option value=\"0\">Open</option>";
        echo "<option value=\"1\" selected=\"selected\">Closed</option>";
    } else {
        echo "<option value=\"0\" selected=\"selected\">Open</option>";
        echo "<option value=\"1\">Closed</option>";
    }
    echo "</select></p>";
    echo "<input class=\"button\" type=\"submit\" name=\"update\" value=\"Update Values\" />";
    echo "</form>";

?>
