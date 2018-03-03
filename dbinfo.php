<style>
<?php include 'style.css'?>
</style>

<html>
<link href="https://fonts.googleapis.com/css?family=Arvo|Open+Sans|Roboto" rel="stylesheet">
</html>

<?php
$username = 'cs4400_Group_106';
$password = 'by8u50gY';
$host = 'academic-mysql.cc.gatech.edu';
$database = 'cs4400_Group_106';

date_default_timezone_set('America/New_York');

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function get_new_card() {
    $doagain = False;
    $newcardnum = 0;
    do {
        $doagain = False;

        $randNumberLength = 16;  // length of your giant random number
        $newnum = NULL;
        for ($i = 0; $i < $randNumberLength; $i++) {
            $newnum .= rand(0, 9);  // add random number to growing giant random number
        }

        $newcardnum = $newnum;
        $card_query = "SELECT BreezecardNum FROM Breezecard WHERE BreezecardNum = '$newnum'";
        $card_result = mysql_query ($card_query)  or die(mysql_error());
        if (mysql_num_rows($card_result) != 0)
        {
            $doagain = True;
        }
    } while ($doagain == True);
    return $newcardnum;
}
?>
