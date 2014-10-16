<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
set_time_limit(120);  // max seconds to run.

// get file from user
$fileList = $_POST['AllKML'];
$files = explode(",",$fileList);

if (count($files) == 0)
{
  echo "<br> No files found to process.";
  return;
}

$user="cyclingroutes";
$password="cycling14";
$database="cyclingroutes";
$hostname="alicefrankcom.ipagemysql.com";
mysql_connect($hostname,$user,$password) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
@mysql_select_db($database) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

// maintenance here
/*
$queries = Array();
$query='Select * from Locations';
$result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$numRows = mysql_num_rows($result);
$rowsRead = 0;
$rowsChanged = 0;
$max = 20000;
//echo "<br> rows returned: ", $numRows;
while (($row=mysql_fetch_assoc($result)) && $rowsRead < $max)
{
	$rowsRead ++;
	$lat = $row['latitude'];
	$lon = $row['longitude'];
	$town = $row['townName'];
	$id = $row['id'];

//	echo "<br> record ", $lat, $lon, $town;
	$newTown = $town;
	if ($newTown != null)
	{
		$townParts = explode(',', $newTown);
		if (count($townParts) > 1)
		{
			if (trim($townParts[1]) == "Town Of")
			{
				$newTown = $townParts[0];
				$query="UPDATE Locations SET townName = '$newTown' WHERE latitude = $lat AND longitude = $lon";
				$queries[] = $query;
			}
		}
		if ($newTown == '"Hollis')
		{
			$newTown = "Hollis";
			$query="UPDATE Locations SET townName = '$newTown' WHERE latitude = $lat AND longitude = $lon";
			$queries[] = $query;
		}
	}
}

foreach($queries as $query)
{
	$result = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$rowsChanged ++;
}


echo "<br> Read: ", $rowsRead;
echo "<br> Written: ", $rowsChanged;

// end of maintenance
*/


// ExtendedData node coming in here
function AddTownNames($node)
{
	global $xml;
	global $townNames;

	$newTowns = $node->addChild("Towns");
	foreach($townNames as $thisName)
	{
		$newTowns->addChild("Town", $thisName);
	}
}

function StoreTownNames($node)
{
	// processing the coordinates node here //
  global $xml;
	global $townNames;

  $text = (string)$node;
	$textArray = explode(' ', $text);
	foreach ($textArray as $thisLoc)
	{
		$locNbrs = explode(',', $thisLoc);
		if (count($locNbrs) == 3)
		{
			$lat = round((float)$locNbrs[1], 3);
			$lon = round((float)$locNbrs[0], 3);
			$query = "SELECT townName, stateName from Locations where latitude = $lat and longitude = $lon";
$results = mysql_query($query) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
			while($row=mysql_fetch_assoc($results)) 
			{
          $thisTown = $row['townName'];
          $thisState = $row['stateName'];
          if ($thisTown == NULL || $thisState == NULL)
            break;

          $newName = "$thisTown, $thisState";
          $found = false;
					foreach($townNames as $thisName)
					{
						if ($newName == $thisName)
						{
							$found = true;
							break;
						}
					}
					if (false == $found)
					{
						$townNames[count($townNames)] = $newName;
					}
  			}
		}
	}
}

function FoundTowns()
{
  global $xml;
	global $townNames;

  $nodes = $xml->xpath('//Towns');
	foreach($nodes as $node)
	{
		echo "<br> found towns";
		return true;
	}
	return false;
}

function getTextFromDocument() 
{
  global $xml;
	global $townNames;

  $nodes = $xml->xpath('//coordinates');
  foreach ($nodes as $node)
  {
    StoreTownNames($node);
  }
  
  $nodes = $xml->xpath('//ExtendedData');
  foreach ($nodes as $node)
  {
    AddTownNames($node);
  }
}



$townNames = Array();
$counter = 0;
foreach($files as $fileName)
{
  // clear the array out for the next set
  $townNames = Array();
  if ($fileName == "")
    continue;
echo"<br>file: $fileName";
  $xmlFile = "MapFiles/$fileName";
  $xmlFileO = "MapFilesUpdated/$fileName";
  $xml = simplexml_load_string(str_replace('<kml xmlns="http://earth.google.com/kml/2.2">',
											   '<kml>',
											   file_get_contents($xmlFile)));

  if (file_exists($xmlFileO))
  {
    continue;
  }

//  $xmlOut->formatOutput = true;

	  getTextFromDocument();

    file_put_contents($xmlFileO, $xml->asXML());
    $counter ++;
}
echo "<br>Files processed: $counter";

?>