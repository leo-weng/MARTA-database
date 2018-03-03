<?php
include 'dbinfo.php';
include 'logout.php';
?>

<html>

<title>Passenger Flow Report</title>
<heading>Passenger Flow Report</heading>
<form action="admin-menu.php">
    <input class="button" type="submit" name="back" value="Back to Admin Menu"/>
</form>
</html>

<?php
    session_start();
    @mysql_connect($host,$username,$password) or die( "Unable to connect");;
    mysql_select_db($database) or die( "Unable to select database");

    $smalltime = date("Y-m-d\TH:i:s", mktime(0, 0, 0, 1, 1, 2017));
    $timenow = date("Y-m-d\TH:i:s");
    //$cardnum= $_GET['view'];
    $startfilter = NULL;
    $endfilter = NULL;


    $out_query = "SELECT Tripfare, StartsAt, StartTime, COUNT(StartsAt) AS OutCount FROM Trip GROUP BY StartsAt";
    if(isset($_POST['update'])) {
        $startfilter = $_POST["start"];
        if ($startfilter == NULL) {
            $startfilter = $smalltime;
        }
        $endfilter = $_POST["end"];
        if ($endfilter == NULL) {
            $endfilter = $timenow;
        }
        $out_query = "SELECT Tripfare, StartsAt, StartTime, COUNT(StartsAt) AS OutCount FROM Trip WHERE StartTime >= '$startfilter' AND StartTime <= '$endfilter' GROUP BY StartsAt";
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
    $in_query = "SELECT EndsAt, COUNT(EndsAt) AS InCount FROM Trip GROUP BY EndsAt";
    $db_query = "SELECT a.Name, StartsAt, OutCount, InCount, OutCount-InCount AS Flow, Tripfare*OutCount AS Revenue FROM ($out_query) outQuery JOIN Station AS a ON outQuery.StartsAt = a.StopID JOIN ($in_query) inQuery ON outQuery.StartsAt = inQuery.EndsAt";
    #echo"<p>$db_query</p>";
    if (isset($_GET['sort'])) {
        $sortvar = $_GET['sort'];
        $db_query = $db_query . " ORDER BY $sortvar";
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

    echo "<form action=\"\" method=\"POST\" id=\"tableform\">";

    echo "<div style=\"width: 600px; height:250px; overflow:auto;\">";
    echo "<table><tr>";
    echo "<th><a href=\"passenger-flow-report.php?sort=a.Name\">Station Name</a></th>";
    echo "<th># Passenger In</th>";
    echo "<th># Passenger Out</th>";
    echo "<th>Flow</th>";
    echo "<th>Revenue</th>";
    echo "</tr>";

    $db_result = mysql_query($db_query) or die(mysql_error());
    for ($i = 0; $i < mysql_num_rows($db_result); $i++) {
        $station = mysql_result($db_result, $i, "a.Name");
        $outcount = mysql_result($db_result, $i, "OutCount");
        $incount = mysql_result($db_result, $i, "InCount");
        $flow = mysql_result($db_result, $i, "Flow");
        $revenue = mysql_result($db_result, $i, "Revenue");;

        echo "<tr>";
        echo "<td>$station</td>";
        echo "<td>$outcount</td>";
        echo "<td>$incount</td>";
        echo "<td>$flow</td>";
        echo "<td>$revenue</td>";
        echo "</tr>";
    }
    echo "</table></div>";
?>
