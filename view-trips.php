<?php
include 'dbinfo.php';
include 'logout.php';
?>

<html>
<title>View Trips</title>
<heading>View Trips</heading>
<form action="passenger-menu.php?inprogress=0">
    <input class="button" type="submit" value="Back to Passenger Menu"/>
</form>
</html>

<?php
    session_start();
    @mysql_connect($host,$username,$password) or die( "Unable to connect");
    mysql_select_db($database) or die( "Unable to select database");


    $smalltime = date("Y-m-d\TH:i:s", mktime(0, 0, 0, 1, 1, 2017));
    $timenow = date("Y-m-d\TH:i:s");
    $cardnum= $_GET['view'];
    $startfilter = NULL;
    $endfilter = NULL;

    if(isset($_POST['update'])) {
        $startfilter = $_POST["start"];
        if ($startfilter == NULL) {
            $startfilter = $smalltime;
        }
        $endfilter = $_POST["end"];
        if ($endfilter == NULL) {
            $endfilter = $timenow;
        }
        $trip_query = "SELECT Tripfare, StartTime, a.Name, b.Name, BreezecardNum FROM Trip JOIN Station AS a ON Trip.StartsAt = a.StopId JOIN Station AS b ON Trip.EndsAt = b.StopId  WHERE Breezecardnum = '$cardnum' AND StartTime >= '$startfilter' AND StartTime <= '$endfilter'";
    } else {
        $trip_query = "SELECT Tripfare, StartTime, a.Name, b.Name, BreezecardNum FROM Trip JOIN Station AS a ON Trip.StartsAt = a.StopId JOIN Station AS b ON Trip.EndsAt = b.StopId WHERE Breezecardnum = '$cardnum'";
    }

    if(isset($_POST['start'])) {
        $startfilter = $_POST["start"];
    } else {
        $startfilter = $smalltime;
    }
    if(isset($_POST['end'])) {
        $endfilter = $_POST["end"];
    } else {
        $endfilter = $timenow;
    }

    echo "<form action=\"\" method=\"POST\" id=\"filter\">";
    echo "<p>Start Time: ";
    echo "<input name=\"start\" type=\"datetime-local\" value='$startfilter'/>";
    echo "</p>";
    echo "<p>End Time: ";
    echo "<input name=\"end\" type=\"datetime-local\" value='$endfilter'/>";
    echo "</p>";
    echo "<input class=\"button\" type=\"submit\" name=\"update\" value=\"Update\"/>";
    echo "</form>";

    if (isset($_GET['sort'])) {
        $sortvar = $_GET['sort'];
        $trip_query = $trip_query . " ORDER BY $sortvar";

    }
    $trip_result = mysql_query($trip_query) or die(mysql_error());

    echo "<form action=\"\" method=\"POST\" id=\"tableform\">";
    echo "<table><tr>";
    echo "<th><a href=\"view-trips.php?sort=StartTime&view=$cardnum\">Time</a></th>";
    echo "<th>Source</th>";
    echo "<th>Destination</th>";
    echo "<th>Fare Paid</th>";
    echo "<th>Card #</th>";
    echo "</tr>";

    for ($i = 0; $i < mysql_num_rows($trip_result); $i++) {
        $fare = mysql_result($trip_result, $i, "Tripfare");
        $starttime = mysql_result($trip_result, $i, "StartTime");
        $startsAt = mysql_result($trip_result, $i, "a.Name");
        $endsAt = mysql_result($trip_result, $i, "b.Name");

        echo "<tr>";
        echo "<td>$starttime</td>";
        echo "<td>$startsAt</td>";
        echo "<td>$endsAt</td>";
        echo "<td>$fare</td>";
        echo "<td>$cardnum</td>";
        echo "</tr>";
    }
    echo "</table>";
?>
