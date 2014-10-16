<?php

function LogIn()
{
  //***  Connect to databases ***//
  $current_minute = date('i');
  $db_user_active = (int)($current_minute/5) +1;

  $user="cyclingroutes" . $db_user_active;
  $password="cycling14";
  $database="cyclingroutes";
  $hostname="alicefrankcom.ipagemysql.com";
  mysql_connect($hostname,$user,$password) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  @mysql_select_db($database) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

}

function DropAndCreate()
{
//***  Drop and recreate database tables if requested ***//


//   Drop and create Mileage

//if (isset($_POST['DropMiles']))
{
    //   Drop and create Mileage table
    $query="DROP TABLE IF EXISTS Mileage";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    $query="CREATE TABLE Mileage (
    id int(8) NOT NULL auto_increment,
    monthday datetime,
    year int(4) NOT NULL,
    name varChar(32),
    miles decimal(6,1),
    weektotal decimal(6,1),
    elevation decimal(6,1),
    PRIMARY KEY (id))";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

}



}

// from 1993 on - these records contain only the year, no date
function LoadOldMileage()
{
  $lines = file("miles.csv");

  $firstTime = true;
  foreach($lines as $line)
  {
    if ($firstTime)
    {
      $firstTime = false;
      continue;
    }
    $index = 0;
    $fields = explode(",", $line);
    if (count($fields) < 2)
    {
      // done
      continue;
    }
    // figure out the date field.
    $year = $fields[0];
    $fmile = $fields[1];
    $amile = $fields[2];
  
    $query="Insert into Mileage values('', 
      null, $year, 'Frank', $fmile, null)";

    $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

    $query="Insert into Mileage values('', 
        null, $year, 'Alice', $amile, null)";

    $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

  }
}

function LoadDetailMileage($year, $acol, $fcol)
{
  $lines = file("2010miles.csv");

  $nbrLines = 0;
  $atotal = 0;
  $ftotal = 0;
  foreach($lines as $line)
  {
    $nbrLines ++;
    if ($nbrLines < 15 )
    {
      continue;
    }
    $index = 0;
    $fields = explode(",", $line);
    if (count($fields) < 2)
    {
      continue;
    }
    if ($fields[1] == '')
    {
    // summary lines
      continue;
    }
    

//    $year = 2010;
    $date = $year . '/' . $fields[1];
    $amile = $fields[$acol] - $atotal;
    $atotal = $fields[$acol];
    
    $fmile = $fields[$fcol] - $ftotal;
    $ftotal = $fields[$fcol];

    if ($fmile > 0)
    {
      $query="Insert into Mileage values('', 
        '$date', $year, 'Frank', $fmile, null)";
   
       echo"<br>$query";
        $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    }

    if ($amile > 0)
    {
      $query="Insert into Mileage values('', 
          '$date', $year, 'Alice', $amile, null)";

        $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    }

  }
}

function addSpecific($name, $replace, $date, $year, $miles, $elevation)
{
  
  if ($replace == true)
  {
    $query = "update Mileage set miles=$miles where monthday = '$date' and name='$name'";
  }
  else
  {
    $query = "Insert into Mileage set monthday = '$date', year=$year, name='$name', miles=$miles";
    if ($elevation != null)
    {
      $query .=", elevation=$elevation";
    }
  }
  
//    echo"<br> $query";

      $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
      UpdateWeeks($name, $year);
 
 }

function updateWeeks($name, $year)
{

// set weeklys to zero before we start.
    $query = "update Mileage set weektotal = 0 where name = '$name' and year = $year";
      $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

    // go through the year, read every record, by date.
    // if not Sunday, add to accum.
    // if Sunday, update record, reset accum.
    $query = "select monthday, miles from Mileage where name = '$name' and year = $year order by monthday";
      $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

  $runningtotal = 0;
    $total = 0;
    $nextMonday = 0;
    $lastActiveDate = 0;
while($row=mysql_fetch_assoc($result))
{
  if ($row['miles'] < 0.5)
  {
  // put in very small miles to get the graph to appear correct.  Ignore those rows here.
  continue;
  }
    $date = $row['monthday'];
    $arraydate = explode('-', $date);
    $intDate = strtotime($arraydate[1].'/'.$arraydate[2].'/'.$arraydate[0]);
    if ($date >= $nextMonday)
    {
      if ($total > 0)
      {
        // process last week's mileage
        $weekMileage = $total;
        $query = "update Mileage set weektotal = $weekMileage where monthday = '$lastActiveDate' and name='$name'";
        mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
        $total = 0;
      }

      // get the date of next Monday -
      $nextMonday = date("Y-m-d", strtotime('next monday', $intDate)); 
      $intMonday = strtotime($nextMonday);
      $intSunday = $intMonday - 1;
      $Sunday = date("Y-m-d", $intSunday);
    }
    $runningtotal += $row['miles'];
      $query = "update Mileage set runningtotal = $runningtotal where monthday = '$date' and name='$name'";
      mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

//    echo "<br>" . $date  . " mileage " . $row['miles'];
    $total += $row["miles"];
    $lastActiveDate = $date;
    
    
  }
//    echo "<br> date is $date, nextMonday is " . $nextMonday;
    // process last week's mileage
    $weekMileage = $total;
//    echo "<br>**** total is " . $weekMileage . " for week ending " . $Sunday . ", last active date is " . $lastActiveDate ;

    $query = "update Mileage set weektotal = $weekMileage where monthday = '$lastActiveDate' and name='$name'";
//    echo "<br>query is $query";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

    $total = 0;

mysql_free_result($result);


}

function ShowDBStatus()
{
  $query="select count(*) from Mileage";
  $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $Mileage = mysql_result($result, 0);
  echo "<hr/>Mileage DB contains $Mileage records";

}
?>
