<?php
include 'dbinfo.php';
include 'logout.php';
?>

<html>
<title>Breezecard Management</title>
<heading>Breezecard Management</heading>
<form action="admin-menu.php">
    <input class="button" type="submit" name="back" value="Back to Admin Menu"/>
</form>
</html>

<?php
    session_start();
    @mysql_connect($host,$username,$password) or die( "Unable to connect");
    mysql_select_db($database) or die( "Unable to select database");

    $owner_filter = NULL;
    $cardnum_filter = NULL;
    $minval = 0.00;
    $maxval = 1000.00;
    $hasError = False;
    $db_query = "SELECT Breezecard.BreezecardNum, Value, BelongsTo, Conflict.Username AS ConflictUser FROM Breezecard LEFT JOIN Conflict ON Breezecard.BreezecardNum = Conflict.BreezecardNum";
    if(isset($_POST['update'])) {
        $owner_filter = test_input($_POST["owner_filter"]);
        $cardnum_filter = test_input($_POST["cardnum_filter"]);
        if ($cardnum_filter != NULL && strlen($cardnum_filter) != 16) {
            $cardErr1 = "Breezecard number invalid<br>";
            echo "$cardErr1";
            $hasError = True;
        }
        $minval = test_input($_POST["minval"]);
        $maxval = test_input($_POST["maxval"]);

        if ($hasError == False) {
            if ($owner_filter != NULL) {
                $db_query = $db_query . " WHERE BelongsTo = '" . $owner_filter . "' AND ";
            } else {
                $db_query = $db_query . " WHERE BelongsTo = BelongsTo AND ";
            }
            if ($cardnum_filter != NULL) {
                $db_query = $db_query . "Breezecard.BreezecardNum = '" . $cardnum_filter . "' AND ";
            } else {
                $db_query = $db_query . "Breezecard.BreezecardNum = Breezecard.BreezecardNum AND ";
            }
            if ($minval != NULL) {
                $db_query = $db_query . "Value >= '" . $minval . "' AND ";
            } else {
                $db_query = $db_query . "Value >= '0.00' AND ";
            }
            if ($maxval != NULL) {
                $db_query = $db_query . "Value <= '" . $maxval . "'";
            }else {
                $db_query = $db_query . "Value <= '1000.00'";
            }
        }
    }
    if(isset($_POST['update_value'])) {
        $cardnum = $_POST["choose"];
        $newvalue = test_input($_POST["change_value"]);
        $update_query = "UPDATE Breezecard SET Value = '$newvalue' WHERE BreezecardNum = '$cardnum'";
        $update_result = mysql_query($update_query) or die(mysql_error());
    } else if(isset($_POST['update_owner'])) {
        $cardnum = $_POST["choose"];
        $newowner = test_input($_POST["change_owner"]);
        $update_query = "UPDATE Breezecard SET BelongsTo = '$newowner' WHERE BreezecardNum = '$cardnum'";
        $update_result = mysql_query($update_query) or die(mysql_error());
    }
    #echo "<p>$db_query</p>";
    $db_result = mysql_query($db_query) or die(mysql_error());

    echo "<form action=\"\" method=\"POST\" id=\"tableform\">";
    echo "<div style=\"width: 400px; height:250px; overflow:auto;\">";
    echo "<table><tr>";
    echo "<th>Select</th>";
    echo "<th>Card #</th>";
    echo "<th>Value</th>";
    echo "<th>Owner</th>";
    echo "</tr>";

    for ($i = 0; $i < mysql_num_rows($db_result); $i++) {
        $cardnum = mysql_result($db_result, $i, "Breezecard.BreezecardNum");
        $value = mysql_result($db_result, $i, "Value");
        $owner = mysql_result($db_result, $i, "BelongsTo");
        $conflict = mysql_result($db_result, $i, "ConflictUser");

        echo "<tr>";
        if ($i == 0) {
            echo "<td><input type=\"radio\" name=\"choose\" value=\"$cardnum\" checked></td>";
        } else {
            echo "<td><input type=\"radio\" name=\"choose\" value=\"$cardnum\"></td>";
        }
        echo "<td>$cardnum</td>";
        echo "<td>$value</td>";
        if ($conflict != NULL) {
            echo "<td>Suspended</td>";
        } else {
            echo "<td>$owner</td>";
        }
        echo "</tr>";
    }
    echo "</table></div>";
    echo "<p><input name=\"change_value\" type=\"number\" min=\"0\" max=\"1000\" step=\"0.01\"/>";
    echo "<input class=\"button\" type=\"submit\" name=\"update_value\" value=\"Update Value\"/></p>";
    echo "<p><input name=\"change_owner\" size=\"20\" maxlength=\"50\"/>";
    echo "<input class=\"button\" type=\"submit\" name=\"update_owner\" value=\"Update Owner\"/></p>";
    echo "</form>";

    echo "<br><p>Filters:</p>";
    echo "<form action=\"\" method=\"POST\" id=\"filter\">";
    echo "<p>Owner: ";
    echo "<input name=\"owner_filter\" value='$owner_filter' size=\"20\" maxlength=\"50\"/>";
    echo "</p>";
    echo "<p>Card #: ";
    echo "<input name=\"cardnum_filter\" value='$cardnum_filter' size=\"20\" maxlength=\"16\"/>";
    echo "</p>";
    echo "<p>Value Between : ";
    echo "<input name=\"minval\" value='$minval' type=\"number\" min=\"0\" max=\"999.99\" step=\"0.01\"/> and ";
    echo "<input name=\"maxval\" value='$maxval' type=\"number\" min=\"0.01\" max=\"1000.00\" step=\"0.01\"/>";
    echo "</p>";
    echo "<input class=\"button\" type=\"submit\" name=\"update\" value=\"Update Filter\"/>";
    echo "</form>";
?>
