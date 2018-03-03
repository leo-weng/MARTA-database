<?php
include 'dbinfo.php' ;
?>

<html>
<title>Index</title>
<heading>Login Page</heading>

<?php
    session_start();
    if(isset($_POST['pass']))  {
        $user = $_POST['user'];
        $_SESSION['user'] = $user;
        $pass = $_POST['pass'];
        $pass = md5($pass);

        @mysql_connect($host,$username,$password) or die( "Unable to connect");
        mysql_select_db($database) or die( "Unable to select database");
        
        $sql_query = "SELECT Username, Password, IsAdmin FROM User WHERE Username = '$user'";
        $result = mysql_query ($sql_query)  or die(mysql_error());

        if(mysql_num_rows($result) == 1) {
            $realpass = mysql_result($result, 0, "Password");
            //$realpass = md5($realpass);
            $isadmin = mysql_result($result, 0, "IsAdmin");
             if ($realpass == $pass) {
                 if ($isadmin == 1) {
                     header('Location: admin-menu.php');
                 } else {
                     header('Location: passenger-menu.php?inprogress=0');
                 }
             } else {
                 $err = 'Login Failed';
             }
        } else{
            $err = 'Login Failed' ;
        }
        echo "$err";
        mysql_close();
    }

    echo "<html>";
    echo "<head>";
    echo "</head>";
    echo "<body>";
    echo "<form action=\"\" method=\"POST\">";
    echo "<p>Username: ";
    echo "<input name=\"user\" size=\"20\" maxlength=\"50\"/>";
    echo "</p>";
    echo "<p>Password: ";
    echo "<input type=\"password\" name=\"pass\" size=\"20\" maxlength=\"50\"/>";
    echo "</p>";
    echo "<input class=\"button\" type=\"submit\" name=\"login\" value=\"Login\" />";
    echo "</form>";
    echo "</body>";
    echo "</html>";


?>

<form action="registration.php">
    <input class="button" type="submit" name="register" value="Register"/>
</form>


</html>
