<?php
include 'dbinfo.php' ;
include 'logout.php';
?>

<html>
<title>Registration</title>
<heading>Registration Page</heading>

<?php
    session_start();

    if(isset($_POST['user']))  {
        $hasError = False;
        $allErrors = "";
        $user = test_input($_POST["user"]);
        //if (!preg_match("/^[a-zA-Z]*$/",$user)) {
            //$usernameErr = "Only letters allowed<br>";
            //echo "$usernameErr";
            //$hasError = True;
        //}
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format\\n";
            $allErrors = $allErrors . $emailErr;
            $hasError = True;
        }
        $pass = test_input($_POST["pass"]);
        $confirmpass = test_input($_POST["confirmpass"]);
        if (strlen($pass) < 8) {
            $passErr1 = "Password length less than 8\\n";
            $allErrors = $allErrors . $passErr1;
            $hasError = True;
        } else if ($pass != $confirmpass) {
            $passErr2 = "Password and Confirm Password do not match\\n";
            $allErrors = $allErrors . $passErr2;
            $hasError = True;
        }
        $cardnum = test_input($_POST["cardnum"]);
        $card = test_input($_POST["card"]);
        if ($card == "existingcard" && strlen($cardnum) != 16) {
            $cardErr1 = "Breezecard number invalid\\n";
            $allErrors = $allErrors . $cardErr1;
            $hasError = True;
        }



        @mysql_connect($host,$username,$password) or die( "Unable to connect");;
        mysql_select_db($database) or die( "Unable to select database");
        #do card things
        $final_query = "";
        if ($hasError == False) {
            //$pass = md5($pass);
            $md5pass = md5($pass);

            $user_query1 = "INSERT INTO User VALUES ('$user', '$md5pass', False)";
            $user_query2 = "INSERT INTO Passenger VALUES ('$user', '$email')";
            $user_result1 = mysql_query($user_query1) or die(mysql_error());
            $user_result2 = mysql_query($user_query2) or die(mysql_error());

            if ($card == "newcard") {
                $cardnum = get_new_card();
                $final_query = "INSERT INTO Breezecard VALUES('$cardnum', 0.0, '$user')";
            } else {
                $card_query = "SELECT BelongsTo FROM Breezecard WHERE BreezecardNum = '$cardnum'";
                $card_result = mysql_query ($card_query)  or die(mysql_error());
                if (mysql_num_rows($card_result) != 0)
                {
                    $olduser = mysql_result($card_result, 0, "BelongsTo");
                    if ($olduser != NULL) {
                        $time = date("Y-m-d H:i:s");
                        $conflict_query = "INSERT INTO Conflict VALUES ('$user', '$cardnum', '$time')";
                        $confict_result = mysql_query ($conflict_query)  or die(mysql_error());
                        $cardnum = get_new_card();
                        $final_query = "INSERT INTO Breezecard VALUES('$cardnum', 0.0, '$user')";
                    } else {
                        $final_query = "UPDATE Breezecard SET BelongsTo = '$user' WHERE BreezecardNum = '$cardnum'";
                    }
                } else {
                    $final_query = "INSERT INTO Breezecard VALUES('$cardnum', 0.0, '$user')";
                }
            }
            $final_result = mysql_query ($final_query)  or die(mysql_error());
            header('Location: index.php');
        }
        else {
            echo "<script type='text/javascript'>alert('$allErrors')</script>";
        }


    }



    echo "<html>";
    echo "<head>";
    echo "</head>";
    echo "<body>";
    echo "<form action=\"\" method=\"POST\">";
    echo "<p>Username: \t";
    echo "<input name=\"user\" size=\"20\" maxlength=\"50\" required/>";
    echo "</p>";
    echo "<p>Email Address: \t";
    echo "<input name=\"email\" size=\"20\" maxlength=\"50\" required/>";
    echo "</p>";
    echo "<p>Password: \t";
    echo "<input type=\"password\" name=\"pass\" size=\"20\" maxlength=\"50\" required/>";
    echo "</p>";
    echo "<p>Confirm Password: \t";
    echo "<input type=\"password\" name=\"confirmpass\" size=\"20\" maxlength=\"50\" required/>";
    echo "</p>";
    echo "<div class=\"temp\"><input type=\"radio\" name=\"card\" value=\"existingcard\" checked> Use Existing Breezecard<br>";
    echo "<p>Card Number: \t";
    echo "<input name=\"cardnum\" size=\"16\" maxlength=\"16\"/>";
    echo "</p>";
    echo "<input type=\"radio\" name=\"card\" value=\"newcard\"> Get A New Breezecard<br></div>";
    echo "<input class=\"button\" type=\"submit\" name=\"create\" value=\"Create An Account\" />";
    echo "</form>";
    echo "</body>";
    echo "</html>";

?>

</html>
