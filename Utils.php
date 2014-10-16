<?php

function LogIn()
{
  //***  Connect to databases ***//
  $current_minute = date('i');
  $db_user_active = (int)($current_minute/5);

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


//   Drop and create geoElevations
//   data source http://latlontoelevation.com/dem_consume.aspx



if (isset($_POST['DropLoc']))
{
    //   Drop and create both databases:  Locations and KmlFilesComplete
    $query="DROP TABLE IF EXISTS Locations";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    $query="CREATE TABLE Locations (
    id int(8) NOT NULL auto_increment,
    latitude Decimal(9,5) NOT NULL,
    longitude Decimal(9,5) NOT NULL,
    locationTownID int(8) references LocationTowns.id,
    PRIMARY KEY (id))";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

    $query="create index idxLat on Locations(latitude, longitude)";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

    $query="DROP TABLE IF EXISTS LocationTowns";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    $query="CREATE TABLE LocationTowns (
    id int(8) NOT NULL auto_increment,
    lat Decimal(9,3) NOT NULL,
    lon Decimal(9,3) NOT NULL,
    townName varChar(30),
    stateName varChar(2),
    PRIMARY KEY (id))";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

    $query="create index idxLat on LocationTowns(lat, lon)";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

    $query="DROP TABLE IF EXISTS KmlFilesComplete";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    $query="CREATE TABLE KmlFilesComplete (
    id int(8) NOT NULL auto_increment,
    fileName varChar(255),
    fileTime datetime NOT NULL,
    PRIMARY KEY (id))";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

}


if (isset($_POST['DropEle']))
{
    //   Drop and create both databases:  Elevations and GpxFilesProcessed
    $query="DROP TABLE IF EXISTS Elevations";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    $query="CREATE TABLE Elevations(
    id int(8) NOT NULL auto_increment,
    eleLocID int(8) not null references Locations(id),
    elevation decimal(9,2) NOT NULL,
    locTime datetime NOT NULL,
    PRIMARY KEY (id))";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

    $query="create index idxLat on Elevations(elevation, locTime)";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());


    // based on file names - just records a file name & date
    // each time the elevations is updated for that file.
    //   Create table GpxFilesProcessed
    $query="DROP TABLE IF EXISTS GpxFilesProcessed";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    $query="CREATE TABLE GpxFilesProcessed(
    id int(8) NOT NULL auto_increment,
    fileName varChar(255),
    fileTime datetime NOT NULL,
    PRIMARY KEY (id))";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

}


if (isset($_POST['DropGeo']))
{
    $query="DROP TABLE IF EXISTS geoElevations";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    $query="CREATE TABLE geoElevations (
    id int(8) NOT NULL auto_increment,
    geoLocID int(8) unique not null references Locations(id),
    geoElevation Decimal(9,2) NOT NULL,
    PRIMARY KEY (id))";
    mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

}

}


function ShowDBStatus()
{
  $query="select count(*) from Locations";
  $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $Locs = mysql_result($result, 0);
  echo "<hr/>Locations DB contains $Locs records";

  $query="select count(*) from LocationTowns";
  $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $Towns = mysql_result($result, 0);
  echo "<br/>LocationTowns DB contains $Towns records";

  $query="select count(*) from Elevations";
  $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $Eles = mysql_result($result, 0);
  echo "<br/>Elevations DB contains $Eles records";

  $query="select count(*) from geoElevations";
  $result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $Eles = mysql_result($result, 0);
  echo "<br/>geoElevations DB contains $Eles records";

}
?>
