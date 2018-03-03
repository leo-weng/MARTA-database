<?php
include 'dbinfo.php';
## STATUS DIDNT CHANGE
?>

<html>
<title>Station Management</title>
<heading>Station Management</heading>
<form action="admin-menu.php">
    <input class="button" type="submit" name="back" value="Back to Admin Menu"/>
</form>
</html>

<?php
    session_start();
    @mysql_connect($host,$username,$password) or die( "Unable to connect");;
    mysql_select_db($database) or die( "Unable to select database");
    $db_query = "SELECT * FROM Station";

    if (isset($_GET['sort'])) {
        $sortvar = $_GET['sort'];
        $db_query = $db_query . " ORDER BY $sortvar";
    }
    $db_result = mysql_query($db_query) or die(mysql_error());


    echo "<div style=\"width: 400px; height:250px; overflow:auto;\">";
    echo "<table><tr>";
    echo "<th><a href=\"station-management.php?sort=Name\">Station Name</a></th>";
    echo "<th><a href=\"station-management.php?sort=StopID\">Stop ID</a></th>";
    echo "<th><a href=\"station-management.php?sort=EnterFare\">Fare</a></th>";
    echo "<th><a href=\"station-management.php?sort=ClosedStatus\">Status</a></th>";
    echo "<th><a href=\"station-management.php?sort=IsTrain\">Bus or Train</a></th>";
    echo "</tr>";

    for ($i = 0; $i < mysql_num_rows($db_result); $i++) {
        $stopID = mysql_result($db_result, $i, "StopID");
        $name = mysql_result($db_result, $i, "Name");
        $fare = mysql_result($db_result, $i, "EnterFare");
        $status = mysql_result($db_result, $i, "ClosedStatus");
        $istrain = mysql_result($db_result, $i, "IsTrain");

        echo "<tr>";
        echo "<td><a href=\"station-detail.php?name=$name&stopID=$stopID&fare=$fare&status=$status&istrain=$istrain\">$name</a></td>";
        echo "<td>$stopID</td>";
        echo "<td>$fare</td>";
        if ($status == False) {
            echo "<td>Open</td>";
        } else {
            echo "<td>Closed</td>";
        }
        if ($istrain == True) {
            echo "<td>Train</td>";
        } else {
            echo "<td>Bus</td>";
        }
        echo "</tr>";
    }
    echo "</table></div>";
?>

<html>
    <form action="create-station.php">
        <input class="button" type="submit" name="create_station" value="Create A New Station"/>
    </form>
</html>
