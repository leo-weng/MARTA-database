<?php
include 'dbinfo.php';
include 'logout.php';

session_start();
@mysql_connect($host,$username,$password) or die( "Unable to connect");
mysql_select_db($database) or die( "Unable to select database");
$user = $_SESSION['user'];
?>

<html>
<title>Manage My Breezecards</title>
<heading>Manage My Breezecards</heading>
<form action="passenger-menu.php?inprogress=0">
    <input class="button" type="submit" value="Back to Passenger Menu"/>
</form>
</html>

<?php
    $card_query = "SELECT Breezecard.BreezecardNum, Value FROM Breezecard LEFT JOIN Conflict ON Breezecard.BreezecardNum = Conflict.BreezecardNum WHERE BelongsTo = '$user' AND Conflict.BreezecardNum IS NULL";
    if (isset($_GET['sort'])) {
        $sortvar = $_GET['sort'];
        $card_query = $card_query . " ORDER BY $sortvar";
    }
    $card_result = mysql_query ($card_query)  or die(mysql_error());

    echo "<form action=\"\" method=\"POST\" id=\"tableform\">";
    echo "<table><tr>";
    echo "<th>Select</th>";
    echo "<th><a href=\"manage-my-breezecards.php?sort=BreezecardNum\">Card #</a></th>";
    echo "<th><a href=\"manage-my-breezecards.php?sort=Value\">Value</a></th>";
    echo "</tr>";

    for ($i = 0; $i < mysql_num_rows($card_result); $i++) {
        echo "<tr>";
        $cardnum = mysql_result($card_result, $i, "BreezecardNum");
        $value = mysql_result($card_result, $i, "Value");
        if ($i == 0) {
            echo "<td><input type=\"radio\" name=\"choose\" value=\"$cardnum\" checked></td>";
        } else {
            echo "<td><input type=\"radio\" name=\"choose\" value=\"$cardnum\"></td>";
        }
        echo "<td>$cardnum</td>";
        echo "<td>$value</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<input class=\"button\" type=\"submit\" name=\"remove\" value=\"Remove Card\"/></p><br>";
    echo "<p>Credit Card #: ";
    echo "<input name=\"creditcard\" size=\"20\" maxlength=\"16\"/></p>";
    echo "<p>Value: ";
    echo "<input name=\"val\" value='15.00' type=\"number\" min=\"10.00\" max=\"1000.00\" step=\"0.01\"/></p>";
    echo "<p><input class=\"button\" type=\"submit\" name=\"addvalue\" value=\"Add Value to Card\"/></p><br>";
    echo "</form>";

    if(isset($_POST['remove'])) {
        $choice = $_POST['choose'];
        if(mysql_num_rows($card_result) > 1) {
            $remove_query = "UPDATE Breezecard SET BelongsTo = NULL WHERE BreezecardNum = '$choice'";
            $remove_result = mysql_query($remove_query) or die(mysql_error());
            header("Location: manage-my-breezecards.php");
        } else if(mysql_num_rows($card_result) == 1) {
            $remove_query = "UPDATE Breezecard SET BelongsTo = NULL WHERE BreezecardNum = '$choice'";
            $remove_result = mysql_query($remove_query) or die(mysql_error());
            $choice = get_new_card();
            $remove_query1 = "INSERT INTO Breezecard VALUES('$choice', 0.0, '$user')";
            $remove_result1 = mysql_query($remove_query1) or die(mysql_error());
            header("Location: manage-my-breezecards.php");
        }
    }
    if(isset($_POST['addvalue'])) {
        $hasError = False;
        $creditcard = $_POST['creditcard'];
        if (strlen($creditcard) != 16) {
            $creditErr = "Credit card number invalid<br>";
            echo "$creditErr";
            $hasError = True;
        }
        if ($hasError == False) {
            $choice = $_POST['choose'];
            $find_query = "SELECT Value FROM Breezecard WHERE BreezecardNum = '$choice'";
            $find_result = mysql_query($find_query) or die(mysql_error());
            $currval = mysql_result($find_result, 0, "Value");
            $val = $_POST['val'];
            $newval = $currval + $val;
            $val_query = "UPDATE Breezecard SET Value = '$newval' WHERE BreezecardNum = '$choice'";
            $val_result = mysql_query($val_query) or die(mysql_error());
            header("Location: manage-my-breezecards.php");
        }
    }

    echo "<form action=\"\" method=\"POST\" id=\"addcardform\">";
    echo "<input name=\"addcardnum\" size=\"20\" maxlength=\"16\"/>";
    echo "<input class=\"button\" type=\"submit\" name=\"addcard\" value=\"Add Card\"/></p>";
    echo "</form>";

    if(isset($_POST['addcard'])) {
        $hasError = False;
        $addcardnum = $_POST['addcardnum'];
        if (strlen($addcardnum) != 16) {
            $cardErr1 = "Breezecard number invalid<br>";
            echo "$cardErr1";
            $hasError = True;
        }
        if ($hasError == False) {
            $check_query = "SELECT BelongsTo FROM Breezecard WHERE BreezecardNum = '$addcardnum'";
            $check_result = mysql_query ($check_query)  or die(mysql_error());
            if (mysql_num_rows($check_result) != 0)
            {
                $olduser = mysql_result($check_result, 0, "BelongsTo");
                if ($olduser != NULL) {
                    $time = date("Y-m-d H:i:s");
                    $conflict_query = "INSERT INTO Conflict VALUES ('$user', '$addcardnum', '$time')";
                    $confict_result = mysql_query ($conflict_query)  or die(mysql_error());
                    $addcardnum = get_new_card();
                    $add_query = "INSERT INTO Breezecard VALUES('$addcardnum', 0.0, '$user')";
                } else {
                    $add_query = "UPDATE Breezecard SET BelongsTo = '$user' WHERE BreezecardNum = '$addcardnum'";
                }
            } else {
                $add_query = "INSERT INTO Breezecard VALUES('$addcardnum', 0.0, '$user')";
            }
            $add_result = mysql_query ($add_query)  or die(mysql_error());
            header("Location: manage-my-breezecards.php");
        }
    }
