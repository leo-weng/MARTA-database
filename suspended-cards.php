<?php
include 'dbinfo.php';
include 'logout.php';
?>

<html>
<title>Suspended Cards</title>
<heading>Suspended Cards</heading>

<form action="admin-menu.php">
    <input class="button" type="submit" name="back" value="Back to Admin Menu"/>
</form>
</html>

<?php
    session_start();
    @mysql_connect($host,$username,$password) or die( "Unable to connect");;
    mysql_select_db($database) or die( "Unable to select database");


    $db_query = "SELECT Conflict.BreezecardNum, Conflict.Username AS NewOwner, DateTime, Breezecard.BelongsTo AS OldOwner FROM Conflict JOIN Breezecard WHERE Conflict.BreezecardNum = Breezecard.BreezecardNum";
    if (isset($_GET['sort'])) {
        $sortvar = $_GET['sort'];
        $db_query = $db_query . " ORDER BY $sortvar";
    }
    $db_result = mysql_query($db_query) or die(mysql_error());
    echo "<form action=\"\" method=\"POST\" id=\"changeform\">";


    echo "<div style=\"width: 600px; height:250px; overflow:auto;\">";
    echo "<table><tr>";
    echo "<th>Select</th>";
    echo "<th><a href=\"suspended-cards.php?sort=BreezecardNum\">Card #</a></th>";
    echo "<th>New Owner</th>";
    echo "<th><a href=\"suspended-cards.php?sort=DateTime\">Date Suspended</a></th>";
    echo "<th>Previous Owner</th>";
    echo "</tr>";

    for ($i = 0; $i < mysql_num_rows($db_result); $i++) {
        $cardnum = mysql_result($db_result, $i, "BreezecardNum");
        $newowner = mysql_result($db_result, $i, "NewOwner");
        $datetime = mysql_result($db_result, $i, "DateTime");
        $oldowner = mysql_result($db_result, $i, "OldOwner");

        echo "<tr>";
        if ($i == 0) {
            echo "<td><input type=\"radio\" name=\"choose\" value=\"$cardnum\" checked></td>";
        } else {
            echo "<td><input type=\"radio\" name=\"choose\" value=\"$cardnum\"></td>";
        }
        echo "<td>$cardnum</td>";
        echo "<td>$newowner</td>";
        echo "<td>$datetime</td>";
        echo "<td>$oldowner</td>";
        echo "</tr>";
    }
    echo "</table></div>";

    echo "<br><input type=\"radio\" name=\"assign\" value=\"assign_to_new\" checked>Assign to New<br>";
    echo "<input type=\"radio\" name=\"assign\" value=\"assign-to_old\">Assign to Old<br>";
    echo "<input class=\"button\" type=\"submit\" name=\"update\" value=\"Update Changes\"/>";
    echo "</form>";

    if(isset($_POST['update'])) {
        $cardnum = $_POST["choose"];
        $assign = $_POST["assign"];
        $card_query = "SELECT Conflict.BreezecardNum, Conflict.Username AS NewOwner, DateTime, Breezecard.BelongsTo AS OldOwner FROM Conflict JOIN Breezecard WHERE Conflict.BreezecardNum = Breezecard.BreezecardNum AND Conflict.BreezecardNum = '$cardnum'";
        $card_result = mysql_query($card_query) or die(mysql_error());
        $cardnum = mysql_result($card_result, 0, "BreezecardNum");
        $newowner = mysql_result($card_result, 0, "NewOwner");
        $datetime = mysql_result($card_result, 0, "DateTime");
        $oldowner = mysql_result($card_result, 0, "OldOwner");

        $update_result = "";
        $delete_result = "";
        $new_card_result = "";
        if ($assign == "assign_to_new") {
            $update_query = "UPDATE Breezecard SET BelongsTo = '$oldowner' WHERE BreezecardNum = '$cardnum'";
            $delete_query = "DELETE FROM Conflict WHERE BreezecardNum = '$cardnum'";
            $new_cardnum = get_new_card();
            $new_card_query = "INSERT INTO Breezecard VALUES('$new_cardnum', 0.0, '$newowner')";
        } else {
            $update_query = "UPDATE Breezecard SET BelongsTo = '$newowner' WHERE BreezecardNum = '$cardnum'";
            $delete_query = "DELETE FROM Conflict WHERE BreezecardNum = '$cardnum'";
            $new_cardnum = get_new_card();
            $new_card_query = "INSERT INTO Breezecard VALUES('$new_cardnum', 0.0, '$oldowner')";
        }
        $update_result = mysql_query($update_query) or die(mysql_error());
        $delete_result = mysql_query($delete_query) or die(mysql_error());
        $new_card_result = mysql_query($new_card_query) or die(mysql_error());
        header('Location: suspended-cards.php');
    }
?>
