<?php

require_once "auth.php";
require_once "smarty.php";
require_once "func/FiberSplice.php";
require_once "design_func.php";

global $smarty;

$spliceId = pg_escape_string( $_GET[ 'spliceId' ] );
$fiberId = pg_escape_string( $_GET[ 'fiberId' ] );

$trace_res = trace( $spliceId, $fiberId );
print_r( $trace_res );
$traceArr = array( );
$tbl = '<table border="1" id="contable">';
$tbl .= '<tr><td>Имя</td><td>Волокно</td><td>Примечание</td><td>Кассета</td></tr>';
for ( $i = 0; $i < count( $trace_res ); $i++ )
{
    /* $CableLine = $trace_res[ $i ][ 'CableLine' ];
      $CableLineName = $trace_res[ $i ][ 'cl_name' ];
      $fiber = $trace_res[ $i ][ 'fiber' ];
      $fiberNote = $trace_res[ $i ][ 'note' ];
      $NetworkNode = $trace_res[ $i ][ 'NetworkNode' ];
      $NetworkNodeName = $trace_res[ $i ][ 'nn_name' ];
      $organizer = $trace_res[ $i ][ 'FiberSpliceOrganizer' ];
      $traceArr[ ] = '<a href="CableLine.php?mode=charac&cablelineid='
      .$CableLine.'">'.$CableLineName.'</a>';
      $traceArr[ ] = $fiber;
      $traceArr[ ] = $fiberNote;
      $traceArr[ ] = '<a href="NetworkNodes.php?mode=charac&nodeid='.$NetworkNode.'">'
      .$NetworkNodeName.'</a>';
      $traceArr[ ] = $organizer; */
    if ( $trace_res[ $i ][ 'isNode' ] == 0 )
    {
        $tbl .= '<tr>';
        $CableLine = $trace_res[ $i ][ 'CableLine' ];
        $CableLineName = $trace_res[ $i ][ 'cl_name' ];
        $fiber = $trace_res[ $i ][ 'fiber' ];
        $fiberNote = $trace_res[ $i ][ 'note' ];
        $tbl .= '<td>Линия:<a href="CableLine.php?mode=charac&cablelineid='.$CableLine.'">'
                .$CableLineName.'</a></td>';
        $tbl .= '<td>'.$fiber.'</td>';
        $tbl .= '<td>'.$fiberNote.'</td>';
        $tbl .= '<td>-</td>';
        $tbl .= '</tr>';
    }
    else
    {
        $tbl .= '<tr>';
        $NetworkNode = $trace_res[ $i ][ 'NetworkNode' ];
        $NetworkNodeName = $trace_res[ $i ][ 'nn_name' ];
        $organizer = $trace_res[ $i ][ 'FiberSpliceOrganizer' ];
        $tbl .= '<td>Узел: <a href="NetworkNodes.php?mode=charac&nodeid='.$NetworkNode.'">'
                .$NetworkNodeName.'</a></td>';
        $tbl .= '<td>-</td><td>-</td>';
        $tbl .= '<td>'.$organizer.'</td>';
        $tbl .= '</tr>';
    }
}
$tbl .= '</table>';
//$smarty->assign( "data", $traceArr );
$smarty->assign( "tbl", $tbl );
$smarty->display( 'Tracing.tpl' );
?>
