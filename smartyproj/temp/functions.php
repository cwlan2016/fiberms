<?php

function PQuery($query)
{
	require "config.php";
	$result = pg_query($connection, $query) or die(pg_last_error($connection));
	return $result;
}


if($_SERVER["REQUEST_METHOD"] == 'POST')
	{
			{
		    	$manufacturer = $_POST['manufacturer'];
		    	$units = $_POST['units'];
		    	$width = $_POST['width'];
		    	$height = $_POST['height'];
		    	$length = $_POST['length'];
		    	$diameter = $_POST['diameter'];
		    	PQuery('INSERT INTO "NetworkBoxType" (marking, manufacturer, units, width, height, length, diameter) VALUES (\''.$marking.'\', \''.$manufacturer.'\', '.$units.', '.$width.', '.$height.', '.$length.', '.$diameter.')');
		    	print("Добавлено!
		    	<br><a href=\"index.php\">Назад</a>");
			}
		else
		if ($_POST['mode'] == 1)
			{
    			print("<select name=\"networkboxtypes\" onChange=\"javascript: gettypeboxinfo(document.boxtypevalue.networkboxtypes.value,2); getcurrentscripts(document.boxtypevalue.networkboxtypes.value,3)\">");
				print("<option selected=\"true\">Выберите нужное</option>");
				while ($mybox = pg_fetch_array($res)) {
				print("<option value=\"".$mybox['id']."\">".$mybox['marking']."</option>");
				}
				print("</select>");
				//print("<script type=\"text/javascript\">var a=2; var b=3; alert(a+b);</script>");
			}
		else
		if ($_POST['mode'] == 2)
			{
				$res = PQuery('SELECT COUNT(*) AS count FROM "NetworkBox" WHERE "NetworkBoxType"='.$boxtypeid);
				while ($row = pg_fetch_array($res)) {
					$boxtypeid = $row['count'];
				}
				$res = PQuery('SELECT * FROM "NetworkBoxType" WHERE id='.$boxtypeid.';');

//				print($boxtypeid);
				$smarty->assign("marking","1");
				$smarty->display('networkbox.tpl');
			}
		if ($_POST['mode'] == 3)
			{
				$boxtypeid = $_POST['boxtypeid'];
				$res = PQuery('SELECT * FROM "NetworkBoxType" WHERE id='.$boxtypeid.';');
				/*while ($boxrow = pg_fetch_array($res)) {
					print("&nbsp;<script type=\"text/javascript\">
					var marking = \"".$boxrow['marking']."\";
					var manufacturer = \"".$boxrow['manufacturer']."\";
					var units = \"".$boxrow['units']."\";
					var width = \"".$boxrow['width']."\";
					var height = \"".$boxrow['height']."\";
					var length = \"".$boxrow['length']."\";
					var diameter = \"".$boxrow['diameter']."\";
					</script>");
				}//\");*/
				$smarty->assign("marking","Fred Irving Johnathan Bradley Peppergill",true);
				$smarty->display('networkbox.tpl');
			}
	}
else print("bris!");
/*var marking = \"".$boxrow['marking']."\";
						   var manufacturer = \"".$boxrow['manufacturer']."\";
						   var units = \"".$boxrow['units']."\";
						   var width = \"".$boxrow['width']."\";
						   var height = \"".$boxrow['height']."\";
						   var length = \"".$boxrow['length']."\";
						   var diameter = \"".$boxrow['diameter']."\";
						   setvalues(marking,manufacturer,units,width,height,length,diameter);
						   */
?>