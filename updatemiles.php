<!-- php debugging 
http://www.netbeans.org/kb/docs/php/debugging.html#sampleDebuggingSession  -->

<?php

include("Utilsmiles.php");
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
set_time_limit(800);  // max seconds to run.

LogIn();


/*
DropAndCreate();

LoadOldMileage();
ShowDBStatus();
LoadDetailMileage(2010, 10, 12);
ShowDBStatus();
LoadDetailMileage(2009, 15, 16);
ShowDBStatus();

LoadDetailMileage(2008, 17, 18);
ShowDBStatus();
LoadDetailMileage(2007, 19, 20);
ShowDBStatus();

LoadDetailMileage(2006, 21,22);
ShowDBStatus();
LoadDetailMileage(2005, 23,23);
ShowDBStatus();

LoadDetailMileage(2004, 25,0);
ShowDBStatus();
*/


/*
DeleteYear("Alice", 2004);
LoadDetailMileage(2004, 25,0);
ShowDBStatus();

DeleteYear("Alice", 2005);
DeleteYear("Frank", 2005);
LoadDetailMileage(2005, 23,23);
ShowDBStatus();

DeleteYear("Alice", 2006);
DeleteYear("Frank", 2006);
LoadDetailMileage(2006, 21,22);
ShowDBStatus();

DeleteYear("Alice", 2007);
DeleteYear("Frank", 2007);
LoadDetailMileage(2007, 19, 20);
ShowDBStatus();

DeleteYear("Alice", 2008);
DeleteYear("Frank", 2008);
LoadDetailMileage(2008, 17, 18);
ShowDBStatus();

DeleteYear("Alice", 2009);
DeleteYear("Frank", 2009);
LoadDetailMileage(2009, 15, 16);
ShowDBStatus();
*/

/*
DeleteYear("Alice", 2010);
DeleteYear("Frank", 2010);
LoadDetailMileage(2010, 10, 12);
ShowDBStatus();
DeleteYear("Alice", 2011);
DeleteYear("Frank", 2011);
*/

for ($i = 2004 ; $i < 2013 ; $i++)
{
  UpdateWeeks('Frank', $i);
  UpdateWeeks('Alice', $i);
}
ShowDBStatus();


?>
