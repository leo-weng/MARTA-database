<?php
include 'dbinfo.php' ;
include 'logout.php';
?>

<html>
<title>Create Station</title>
<heading>Create Station</heading>
<form action="station-management.php">
    <input class="button" type="submit" name="back" value="Back to Station Management"/>
</form>

<?php
    session_start();
    if(isset($_POST['name'])) {
        $name = test_input($_POST["name"]);
        $stopID = test_input($_POST["stopID"]);
        $fare = test_input($_POST["fare"]);
        $stationType = test_input($_POST["stationType"]);
        $intersection = test_input($_POST["intersection"]);
        $status = test_input($_POST["status"]);

        @mysql_connect($host,$username,$password) or die( "Unable to connect");;
        mysql_select_db($database) or die( "Unable to select database");
        if ($stationType == "train") {
            $query = "INSERT INTO Station VALUES ('$stopID', '$name', '$fare', '$status', True)";
            $result = mysql_query ($query)  or die(mysql_error());
            header("Location: station-management.php");
        } else {
            $query = "INSERT INTO Station VALUES ('$stopID', '$name', '$fare', '$status', False)";
            $query2 = "INSERT INTO BusStationIntersection VALUES ('$stopID', '$intersection')";
            $result = mysql_query ($query)  or die(mysql_error());
            $result2 = mysql_query ($query2)  or die(mysql_error());
            header("Location: station-management.php");
        }
    }

    echo "<html>";
    echo "<head>";
    echo "</head>";
    echo "<body>";
    echo "<form action=\"\" method=\"POST\">";
    echo "<p>Station Name: \t";
    echo "<input name=\"name\" size=\"20\" maxlength=\"50\" required/>";
    echo "</p>";
    echo "<p>Stop ID: \t";
    echo "<input name=\"stopID\" size=\"20\" maxlength=\"50\" required/>";
    echo "</p>";
    echo "<p>Entry Fare: \t";
    echo "<input name=\"fare\" type=\"number\" min=\"0\" max=\"50.00\" step=\"0.01\" required/>";
    echo "</p>";
    echo "<p>Station Type:<br>";
    echo "<input type=\"radio\" name=\"stationType\" value=\"bus\" checked> Bus Station<br>";
    echo "Nearest Intersection: <input name=\"intersection\" size=\"20\" maxlength=\"255\"/><br>";
    echo "<input type=\"radio\" name=\"stationType\" value=\"train\"> Train Station<br>";
    echo "</p>";
    echo "<p>Status:<br>";
    echo "<input type=\"radio\" name=\"status\" value=\"False\" checked> Open<br>";
    echo "<input type=\"radio\" name=\"status\" value=\"True\"> Closed<br><br>";
    echo "</p>";
    echo "<input class=\"button\" type=\"submit\" name=\"create\" value=\"Create Station\" />";
    echo "</form>";
    echo "</body>";
    echo "</html>";

?>

</html>
