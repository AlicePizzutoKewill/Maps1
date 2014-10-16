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
