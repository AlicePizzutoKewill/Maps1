<?php
require_once("Utilsmiles.php");
if(isset($_POST['amiles']))
{
  postMiles();
}

function getMiles($name, $year)
{
  LogIn();
  $query = "select sum(miles) as totalMiles from Mileage where name = '$name' and year = '$year'";
  $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  if($row=mysql_fetch_assoc($result))
  {
    return $row['totalMiles'];
  }
  return 'not found';
}

function postMiles()
{

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
set_time_limit(800);  // max seconds to run.

LogIn();
$amiles=$_POST['amiles'];
$fmiles=$_POST['fmiles'];
$date = $_POST['date'];
$month = $_POST['month'];
$year = $_POST['year'];
if (isset($_POST['a_replace']))
$a_replace = true;
else
$a_replace = false;
if (isset($_POST['f_replace']))
$f_replace = true;
else
$f_replace = false;

// format the date
$newdate = $year.'/'.$month.'/'.$date;

$query="select count(*) from Mileage";
$result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $before = mysql_result($result, 0);


if ($amiles > 0 || $a_replace == true)
{
  addSpecific("Alice", $a_replace, $newdate, $year, $amiles, '');
}

if ($fmiles > 0 || $f_replace == true)
{
  addSpecific("Frank", $f_replace, $newdate, $year, $fmiles, '');
}

$query="select count(*) from Mileage";
  $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $after = mysql_result($result, 0);

$updateOK = 1;

if(isset($_POST['addalice']))
  header("Location: http://sandbox1.alicefrank.com/index.php?option=com_content&view=article&id=19&Itemid=26");
else if(isset($_POST['addfrank']))
  header("Location: http://sandbox1.alicefrank.com/index.php?option=com_content&view=article&id=18&Itemid=22");
else
  header("Location: ./addmiles.php");
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
  <head>
    <title>Add miles to database</title>
  </head>
  <body>
    <hr />
    <center>
      <form method="post" action="addmiles.php" >
        <p />
        <br />
        <br />Alice:&nbsp;<input type="text" name="amiles" value="0" />
        &nbsp;&nbsp;Replace existing value?<input type="checkbox" name="a_replace" />
        &nbsp;&nbsp;
        (Current <?php echo getMiles('alice', '2014');?>)
        <br />
        <br />Frank: <input type="text" name="fmiles" value="0" />
        &nbsp;&nbsp;Replace existing value?<input type="checkbox" name="f_replace" />
        &nbsp;&nbsp;
        (Current <?php echo getMiles('frank', '2014');?>)
        <br /><br /><br />Date<input type="text" id="month" name="month" value="" />
        &nbsp;<input type="text" id="date" name="date" value="" />
        &nbsp;<input type="text" id="year" name="year" value="" />
        <br />
        <p />

        <input type="submit" value="Add miles, go to Alice's chart" name="addalice"/>
        <p /><input type="submit" value="Add miles, go to Frank's chart" name="addfrank"/>
        <p /><input type="submit" value="Add miles, stay on this page"/>
      </form>
      <hr />

      <script language="javascript" type="text/javascript">
        function getDate()
        {
        var dt = new Date();
        var y  = dt.getYear() + 1900;
        var m = dt.getMonth() + 1;  // because month starts at zero...
        var d = dt.getDate();
        document.getElementById('month').value= m;
        document.getElementById('date').value= d;
        document.getElementById('year').value= y;
        }

        getDate();
      </script>
    </center>
  </body>
</html>
