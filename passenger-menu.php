<?php
include 'dbinfo.php';
include 'logout.php';

session_start();
@mysql_connect($host,$username,$password) or die( "Unable to connect");
mysql_select_db($database) or die( "Unable to select database");
$user = $_SESSION['user'];
?>

<html>
<title>Passenger Menu</title>
<heading>Passenger Menu</heading>
<form action= "manage-my-breezecards.php">
    <input class="button" type="submit" name="manage" value="Manage My Breezecards"/>
</form>
</html>

<?php
    $card_query = "SELECT Breezecard.BreezecardNum, Value FROM Breezecard LEFT JOIN Conflict ON Breezecard.BreezecardNum = Conflict.BreezecardNum WHERE BelongsTo = '$user' AND Conflict.BreezecardNum IS NULL";
    $card_result = mysql_query ($card_query)  or die(mysql_error());
    $noprogress = True;

    $currcard = NULL;
    $currvalue = NULL;
    if(mysql_num_rows($card_result) >= 1) {
        if(isset($_POST['choosecard'])) {
            $currcard = $_POST['cards'];
        } else {
            $currcard = mysql_result($card_result, 0, "BreezecardNum");
        }
        echo "<form action=\"\" method=\"POST\" id=\"form\">";
        echo "<p>Breezecard: ";
        echo "<select name=\"cards\" form = \"form\">";
        for ($i = 0; $i < mysql_num_rows($card_result); $i++) {
            $cardnum = mysql_result($card_result, $i, "BreezecardNum");
            if ($currcard == $cardnum) {
                echo "<option value=\"$cardnum\" selected=\"selected\">$cardnum</option>";
                $currvalue = mysql_result($card_result, $i, "Value");
            } else {
                echo "<option value=\"$cardnum\">$cardnum</option>";
            }
        }
        echo "</select>";
        echo "<input class=\"button\" type=\"submit\" name=\"choosecard\" value=\"Choose Card\" />";
        echo "</p>";
        echo "</form>";
        echo "<p>Balance: \$$currvalue</p>";
    } else {
        echo "<p>Breezecard: ";
        echo "None Registered";
        echo "</p>";
    }

    $start_query = "SELECT StopID, Name, EnterFare FROM Station WHERE ClosedStatus = False";
    $start_result = mysql_query ($start_query)  or die(mysql_error());
    $currstartid = NULL;
    $startfare = NULL;

    echo "<form action=\"\" method=\"POST\" id=\"starttripform\">";
    echo "<p>Start Station: ";
    echo "<select name=\"startstation\" form=\"starttripform\">";
    for ($i = 0; $i < mysql_num_rows($start_result); $i++) {
        $startid = mysql_result($start_result, $i, "StopID");
        $startname = mysql_result($start_result, $i, "Name");
        $startfare = mysql_result($start_result, $i, "EnterFare");
        if ($i == 0 || $currstartid == $startid) {
            echo "<option value=\"$startid\" selected=\"selected\">$startid - $startname - $startfare</option>";
            $currstartid = $startid;
        } else {
            echo "<option value=\"$startid\">$startid - $startname - $startfare</option>";
        }
    }
    echo "</select>";
    if (empty($_GET['inprogress']) || $_GET['inprogress'] == 0) {
        echo "<input class=\"button\" type=\"submit\" name=\"starttrip\" value=\"Start Trip\" />";
        //$noprogress = True;
    }
    echo "</p></form>";
    if(isset($_POST['starttrip'])) {
        $currstartid = $_POST['startstation'];
        $tripfare_query = "SELECT EnterFare FROM Station WHERE StopID = '$currstartid'";
        $tripfare_result = mysql_query($tripfare_query) or die(mysql_error());
        $tripfare = mysql_result($tripfare_result, 0, "EnterFare");
        if ($currvalue > $tripfare) {
            $find_query = "SELECT Value FROM Breezecard WHERE BreezecardNum = '$currcard'";
            $find_result = mysql_query($find_query) or die(mysql_error());
            $currvalue = mysql_result($find_result, 0, "Value");
            $balance = $currvalue - $tripfare;
            #echo "<p>$currstartid</p>";
            $time = date("Y-m-d H:i:s");
            $addtrip_query = "INSERT INTO Trip (Tripfare, StartTime, BreezecardNum, StartsAt) VALUES ('$tripfare', '$time', '$currcard', '$currstartid')";
            $updatefunds_query = "UPDATE Breezecard SET Value = $balance WHERE BreezecardNum = $currcard";
            #echo "<p>$addtrip_query</p>";
            $addtrip_result = mysql_query ($addtrip_query)  or die(mysql_error());
            $updatefunds_result = mysql_query ($updatefunds_query)  or die(mysql_error());
            header("Location: passenger-menu.php?inprogress=1&time=" . $time . "&cardnum=" . $currcard);
        } else {
            echo "<script type='text/javascript'>alert('Not enough funds!')</script>";
        }
    } else {
        $currstartid = mysql_result($start_result, 0, "StopID");
    }

    $end_query = "SELECT StopID, Name, EnterFare FROM Station WHERE ClosedStatus = False";
    $end_result = mysql_query ($end_query)  or die(mysql_error());
    $currendid = NULL;

    echo "<form action=\"\" method=\"POST\" id=\"endtripform\">";
    echo "<p>End Station: ";
    echo "<select name=\"endstation\" form=\"endtripform\">";
    for ($i = 0; $i < mysql_num_rows($end_result); $i++) {
        $endid = mysql_result($end_result, $i, "StopID");
        $endname = mysql_result($end_result, $i, "Name");
        $endfare = mysql_result($end_result, $i, "EnterFare");
        if ($i == 0 || $currendid == $endid) {
            echo "<option value=\"$endid\" selected=\"selected\">$endid - $endname - $endfare</option>";
            $currendid = $endid;
        } else {
            echo "<option value=\"$endid\">$endid - $endname - $endfare</option>";
        }
    }
    echo "</select>";
    if (isset($_GET['inprogress']) ) {
        if ($_GET['inprogress'] == 1) {
            echo "<input class=\"button\" type=\"submit\" name=\"endtrip\" value=\"End Trip\" />";
            //$noprogress = True;
        }
    }
    echo "</p></form>";
    if(isset($_POST['endtrip'])) {
        //$inprogress = False;
        $time = $_GET['time'];
        $currcard = $_GET['cardnum'];
        $currendid = $_POST['endstation'];
        $endtrip_query = "UPDATE Trip SET EndsAt = '$currendid' WHERE StartTime = '$time' AND BreezecardNum = '$currcard'";
        #echo "<p>$endtrip_query</p>";
        #echo "<p>$currendid</p>";
        $endtrip_result = mysql_query ($endtrip_query)  or die(mysql_error());
        header("Location: passenger-menu.php?inprogress=0");
    } else {
        $currendid = mysql_result($end_result, 0, "StopID");
    }

    echo "<form action= \"view-trips.php\">";
    echo "<button class=\"button\" type=\"submit\" name=\"view\" value=\"$currcard\">View Trip History</button>";
    echo "</form>";

?>
