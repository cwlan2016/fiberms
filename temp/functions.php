<?php
require_once("config.php");

function PQuery($query)
{
	print('2');
	print('3');
	$qcount++;
}


if($_SERVER["REQUEST_METHOD"] == 'POST')
	{
		{
			{
        	    marking, manufacturer, units, width, height, length, diameter)
		    	VALUES ('$_POST[#marking]', '$_POST[#manufacturer]', '$_POST[#units]', '$_POST[#width]', '$_POST[#height]', '$_POST[#length]', '$_POST[#diameter]');");*/
		    	$marking = $_POST['#marking'];
		    	$manufacturer = $_POST['#manufacturer'];
		    	$units = $_POST['#units'];
		    	$width = $_POST['#width'];
		    	$height = $_POST['#height'];
		    	$length = $_POST['#length'];
		    	$diameter = $_POST['#diameter'];
		    	PQuery("INSERT INTO NetworkBoxType(
        	    marking, manufacturer, units, width, height, length, diameter)
		    	VALUES ('$marking', '$manufacturer', '$units', '$width', '$height', '$length', '$diameter')");
		    	print("done");
			}
		}
	}
else print("bris!");
?>