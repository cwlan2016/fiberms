<?php

require_once 'backend/NetworkNode.php';
require_once 'backend/CableType.php';
require_once 'backend/OpticalFiberJoin.php';
require_once 'func/CableType.php';
require_once 'func/NetworkNode.php';
require_once 'func/NetworkBoxType.php';

function isSingPoint( $point )
{
    $res = FALSE;
    if ( $point[ 'meterSign' ] != "" || $point[ 'note' ] != "" || $point[ 'NetworkNode' ] != "" )
    {
        $res = TRUE;
    }
    return $res;
}

function updCableLinePoints( $coors, $CableLine, $seqStart, $seqEnd,
        $tmpT = FALSE )
{
    $query = 'SELECT * FROM "'.tmpTable( 'CableLinePoint', $tmpT ).'" WHERE "CableLine" = '.$CableLine.' ORDER BY "sequence"';
    $res = PQuery( $query );
    if ( isSingPoint( $res[ 'rows' ][ $seqStart - 1 ] ) && isSingPoint( $res[ 'rows' ][ $seqEnd - 1 ] ) )
    {
        $query = 'DELETE FROM "'.tmpTable( 'CableLinePoint', $tmpT ).'" WHERE "CableLine" = '.$CableLine.' AND "sequence" > '.$seqStart.' AND "sequence" < '.$seqEnd;
        PQuery( $query );
        $seqDiff = count( $coors ) - ( $seqEnd - $seqStart ) - 1;
        $query = 'UPDATE "'.tmpTable( 'CableLinePoint', $tmpT ).'" SET "sequence" = ("sequence" + '.$seqDiff.')*-1 WHERE "CableLine" = '.$CableLine.' AND "sequence" >= '.$seqEnd;
        PQuery( $query );
        $query = 'UPDATE "'.tmpTable( 'CableLinePoint', $tmpT ).'" SET "sequence" = "sequence" * -1 WHERE "CableLine" = '.$CableLine.' AND "sequence" < 0';
        PQuery( $query );
        $seq = $seqStart + 1;
        $iSt = 1;
        $toMin = 1;
    }
    elseif ( isSingPoint( $res[ 'rows' ][ $seqEnd - 1 ] ) )
    {
        $query = 'DELETE FROM "'.tmpTable( 'CableLinePoint', $tmpT ).'" WHERE "CableLine" = '.$CableLine.' AND "sequence" >= '.$seqStart.' AND "sequence" < '.$seqEnd;
        PQuery( $query );
        $seqDiff = count( $coors ) - ( $seqEnd - $seqStart ) - 1;
        $query = 'UPDATE "'.tmpTable( 'CableLinePoint', $tmpT ).'" SET "sequence" = ("sequence" + '.$seqDiff.')*-1 WHERE "CableLine" = '.$CableLine.' AND "sequence" >= '.$seqEnd;
        PQuery( $query );
        $query = 'UPDATE "'.tmpTable( 'CableLinePoint', $tmpT ).'" SET "sequence" = "sequence" * -1 WHERE "CableLine" = '.$CableLine.' AND "sequence" < 0';
        PQuery( $query );
        $seq = $seqStart;
        $iSt = 0;
        $toMin = 1;
    }
    else if ( isSingPoint( $res[ 'rows' ][ $seqStart - 1 ] ) )
    {
        $query = 'DELETE FROM "'.tmpTable( 'CableLinePoint', $tmpT ).'" WHERE "CableLine" = '.$CableLine.' AND "sequence" > '.$seqStart.' AND "sequence" <= '.$seqEnd;
        PQuery( $query );
        $seqDiff = count( $coors ) - ( $seqEnd - $seqStart );
        $query = 'UPDATE "'.tmpTable( 'CableLinePoint', $tmpT ).'" SET "sequence" = ("sequence" + '.$seqDiff.')*-1 WHERE "CableLine" = '.$CableLine.' AND "sequence" >= '.$seqEnd;
        PQuery( $query );
        $query = 'UPDATE "'.tmpTable( 'CableLinePoint', $tmpT ).'" SET "sequence" = "sequence" * -1 WHERE "CableLine" = '.$CableLine.' AND "sequence" < 0';
        PQuery( $query );
        $seq = $seqStart + 1;
        $iSt = 1;
        $toMin = 0;
    }
    else
    {
        $query = 'DELETE FROM "'.tmpTable( 'CableLinePoint', $tmpT ).'" WHERE "CableLine" = '.$CableLine.' AND "sequence" >= '.$seqStart.' AND "sequence" <= '.$seqEnd;
        PQuery( $query );
        $seq = 1;
        $iSt = 0;
        $toMin = 0;
    }
    for ( $i = $iSt; $i < count( $coors ) - $toMin; $i++ )
    {
        $coor = "(".$coors[ $i ]->lon.",".$coors[ $i ]->lat.")";
        $ins[ 'OpenGIS' ] = $coor;
        $ins[ 'sequence' ] = $seq++;
        $ins[ 'CableLine' ] = $CableLine;
        $query = 'INSERT INTO "'.tmpTable( 'CableLinePoint', $tmpT ).'"'.genInsert( $ins );
        PQuery( $query );
    }
}

function addCableLinePoint( $coors, $CableType, $length, $name, $comment,
        $tmpT = FALSE )
{
    if ( $length == "" )
    {
        $ins[ 'length' ] = "NULL";
    }
    else
    {
        $ins[ 'length' ] = floatval(str_replace(',', '.', $length)) * 100;
    }
    $ins[ 'CableType' ] = $CableType;
    $ins[ 'name' ] = $name;
    $ins[ 'comment' ] = $comment;
    $query = 'INSERT INTO "'.tmpTable( 'CableLine', $tmpT ).'"'.genInsert( $ins ).' RETURNING id';
    $res = PQuery( $query );
    $CableLine = $res[ 'rows' ][ 0 ][ 'id' ];
    $seq = 1;
    unset( $ins );
    for ( $i = 0; $i < count( $coors ); $i++ )
    {
        $coor = "(".$coors[ $i ]->lon.",".$coors[ $i ]->lat.")";
        $ins[ 'OpenGIS' ] = $coor;
        $ins[ 'sequence' ] = $seq++;
        $ins[ 'CableLine' ] = $CableLine;
        $query = 'INSERT INTO "'.tmpTable( 'CableLinePoint', $tmpT ).'"'.genInsert( $ins );
        PQuery( $query );
    }
    CableLine_AddOpticalFiberForAll();
}

function addSingPoint( $coors, $CableLineId, $networkNode, $apartment,
        $building, $meterSign, $note )
{
    $OpenGIS = "(".$coors[ 0 ]->lon.",".$coors[ 0 ]->lat.")";
    $wr[ 'CableLine' ] = $CableLineId;
    $wr[ 'OpenGIS' ] = $OpenGIS;
    if ( $meterSign == "" )
    {
        $meterSign = "NULL";
    }
    if ( $note == "" )
    {
        $note = "NULL";
    }
    if ( $networkNode != "" )
    {
        $query = 'SELECT "OpenGIS" FROM "'.tmpTable( 'NetworkNode', TRUE ).
                '" WHERE id='.$networkNode;
        $res = PQuery( $query );
        $networkNodeGIS = $res[ 'rows' ][ 0 ][ 'OpenGIS' ];
        $upd[ 'OpenGIS' ] = "NULL";
        $upd[ 'NetworkNode' ] = $networkNode;
    }
    $upd[ 'meterSign' ] = $meterSign;
    $upd[ 'note' ] = $note;
    $upd[ 'Apartment' ] = $apartment;
    $upd[ 'Building' ] = $building;
    $upd[ 'SettlementGeoSpatial' ] = "NULL";
    CableLinePoint_UPDATE( $upd, $wr, TRUE );
}

function deleteSingPoint( $coors, $tmpT = FALSE )
{
    $OpenGIS = "(".$coors[ 0 ]->lon.",".$coors[ 0 ]->lat.")";
    $upd[ 'meterSign' ] = "NULL";
    $upd[ 'note' ] = "NULL";
    $wr[ 'OpenGIS' ] = $OpenGIS;
    $query = 'UPDATE "'.tmpTable( 'CableLinePoint', $tmpT ).'" SET '.genUpdate( $upd ).genWhere( $wr );
    PQuery( $query );
}

function deleteCableLine( $CableLineId, $tmpT = FALSE )
{
/*    $wr[ 'CableLine' ] = $CableLineId;
    $query = 'DELETE FROM "'.tmpTable( 'CableLinePoint', $tmpT ).'"'.genWhere( $wr );
    PQuery( $query );
    $query = 'DELETE FROM "'.tmpTable( 'OpticalFiber', $tmpT ).'"'.genWhere( $wr );
    PQuery( $query );
    unset( $wr );*/
    $wr[ 'id' ] = $CableLineId;
    CableLine_DELETE($wr, $tmpT);
//    $query = 'DELETE FROM "'.tmpTable( 'CableLine', $tmpT ).'"'.genWhere( $wr );
//    PQuery( $query );
}

function addNode( $coors, $name, $NetworkBoxId, $note, $SettlementGeoSpatial,
        $building, $apartment, $place, $tmpT = FALSE )
{
    $OpenGIS = "(".$coors[ 0 ]->lon.",".$coors[ 0 ]->lat.")";
    $apartment = "NULL";
    $building = "NULL";
    $SettlementGeoSpatial = "NULL";
    NetworkNode_Add( $name, $NetworkBoxId, $note, $OpenGIS,
            $SettlementGeoSpatial, $building, $apartment, $place, $tmpT );
}

function deleteNode( $coors, $tmpT = FALSE )
{
    $OpenGIS = "(".$coors[ 0 ]->lon.",".$coors[ 0 ]->lat.")";
    $wr[ 'OpenGIS' ] = $OpenGIS;
    $query = 'SELECT id FROM "'.tmpTable( 'NetworkNode', $tmpT ).'"'.genWhere( $wr );
    $res = PQuery( $query );
    $NetworkNodeId = $res[ 'rows' ][ 0 ][ 'id' ];
    unset( $wr );
    $wr[ 'id' ] = (int)$NetworkNodeId;
    NetworkNode_DELETE( $wr, $tmpT );
}

function moveNode( $coors, $tmpT = FALSE )
{
    $OpenGIS = "(".$coors[ 0 ]->lon.",".$coors[ 0 ]->lat.")";
    $wr[ 'id' ] = $coors[ 0 ]->id;
    $upd[ 'OpenGIS' ] = $OpenGIS;
    $query = 'UPDATE "'.tmpTable( 'NetworkNode', $tmpT ).'" SET'.genUpdate( $upd ).genWhere( $wr );
    PQuery( $query );
}

function addNetworkBox( $networkBoxType, $invNum, $tmpT = FALSE )
{
    if ( $invNum == "" )
    {
        $invNum = "NULL";
    }
    $wr[ 'inventoryNumber' ] = $invNum;
    $query = 'SELECT * FROM "'.tmpTable( 'NetworkBox', $tmpT ).'" '
            .genWhere( $wr );
    $res = PQuery( $query );
    if ($res['count']) {
       $NetworkBoxId['error'] = 'Duplicate!';
       return $NetworkBoxId;
    }
    $ins[ 'NetworkBoxType' ] = $networkBoxType;
    $ins[ 'inventoryNumber' ] = $invNum;
    $query = 'INSERT INTO "'.tmpTable( 'NetworkBox', $tmpT ).'" '
            .genInsert( $ins ).' RETURNING id';
    $res = PQuery( $query );
    $NetworkBoxId = [];
    if (isset($res['error'])) {
        $NetworkBoxId['error'] = $res['error'];
    } else {
        $NetworkBoxId['id'] = $res[ 'rows' ][ 0 ][ 'id' ];
    }
    return $NetworkBoxId;
}

function divCableLine( $coors, $CableLineId, $nodeInfo, $tmpT = FALSE )
{

    $OpenGIS = "(".$coors[ 0 ]->lon.",".$coors[ 0 ]->lat.")";
    $wr[ 'id' ] = $CableLineId;
    $res = CableLine_SELECT( 0, $wr, $tmpT );
    $CableLine = $res[ 'rows' ][ 0 ];
    unset( $wr );

    $name = $nodeInfo[ 'name' ];
    $NetworkBoxId = $nodeInfo[ 'NetworkBoxId' ];
    $note = $nodeInfo[ 'note' ];
    $place = $nodeInfo[ 'place' ];
    $SettlementGeoSpatial = "NULL";
    $building = "NULL";
    $apartment = "NULL";
    PQuery( "BEGIN WORK;" );
    $res = NetworkNode_Add( $name, $NetworkBoxId, $note, $OpenGIS,
            $SettlementGeoSpatial, $building, $apartment, $place, $tmpT );
    if (isset($res['error'])) {
	PQuery( "ROLLBACK WORK;" );
	return array('error' => $res['error']);
    }
    $NetworkNodeId = $res[ 'rows' ][ 0 ][ 'id' ];

    $ins[ 'CableType' ] = $CableLine [ 'CableType' ];
    if ( $CableLine[ 'length' ] != "" )
    {
        $ins[ 'length' ] = $CableLine[ 'length' ];
    }
    else
    {
        $ins[ 'length' ] = "NULL";
    }
    $ins[ 'name' ] = $CableLine[ 'name' ].'_'.$nodeInfo[ 'name' ];
    $ins[ 'comment' ] = $CableLine[ 'comment' ];
    $res = CableLine_INSERT( $ins, $tmpT );
    if (isset($res['error'])) {
	PQuery( "ROLLBACK WORK;" );
	return array('error' => $res['error']);
    }
    $NCableLineId = $res[ 'rows' ][ 0 ][ 'id' ];

    $wr[ 'CableLine' ] = $CableLineId;
    $query = 'SELECT * FROM "'.tmpTable( 'CableLinePoint', $tmpT ).'"'.genWhere( $wr ).
            'ORDER BY "sequence"';
    $res2 = PQuery( $query );
    $CableLinePoints = $res2[ 'rows' ];
    for ( $i = 0; $i < count( $CableLinePoints ); $i++ )
    {
        $point = $CableLinePoints[ $i ];
        if ( $point[ 'OpenGIS' ] == $OpenGIS )
        {
            $seq = $point[ 'sequence' ];
            $wr1 = array( 'sequence' => array( 'val' => $seq, 'sign' => '>'),
			  'CableLine' => $CableLineId
			);
            CableLinePoint_DELETE($wr1, $tmpT);
            $wr[ 'sequence' ] = $seq;
            $upd[ 'OpenGIS' ] = "NULL";
            $upd[ 'NetworkNode' ] = $NetworkNodeId;
            CableLinePoint_UPDATE( $upd, $wr, $tmpT );
            break;
        }
    }
    $seq = 1;
    for ( $j = $i; $j < count( $CableLinePoints ); $j++ )
    {
        $point = $CableLinePoints[ $j ];
        $ins = array();
        foreach ( $point as $key => $value )
        {
            if ( $key != "id" && $key != "sequence" )
            {
                if ( $value != "" )
                {
                    $ins[ $key ] = $value;
                }
                else
                {
                    $ins[ $key ] = "NULL";
                }
            }
        }
        $ins[ 'CableLine' ] = $NCableLineId;
        $ins[ 'sequence' ] = $seq++;
        if ( $j == $i )
        {
            $ins[ 'NetworkNode' ] = $NetworkNodeId;
            $ins[ 'OpenGIS' ] = "NULL";
        }
        CableLinePoint_INSERT( $ins, $tmpT );
    }
    if (isset(end($CableLinePoints)['NetworkNode']))
        OpticalFiberJoin_replaceCableLine( $CableLine['id'], $NCableLineId, end($CableLinePoints)['NetworkNode'], $tmpT );
    $res = PQuery( "COMMIT WORK;" );
    return array('error' => (isset($res['error'])) ? $res['error'] : false );
}

function divCableLine1( $coors, $CableLineId, $nodeInfo, $tmpT = FALSE )
{
    $OpenGIS = "(".$coors[ 0 ]->lon.",".$coors[ 0 ]->lat.")";
    $wr[ 'id' ] = $CableLineId;
    $res = CableLine_SELECT( 0, $wr, $tmpT );
    $CableLine = $res[ 'rows' ][ 0 ];
    unset( $wr );
    $wr[ 'CableLine' ] = $CableLineId;
    $query = 'SELECT * FROM "'.tmpTable( 'CableLinePoint', $tmpT ).'"'.genWhere( $wr ).
            'ORDER BY "sequence"';
    $res2 = PQuery( $query );
    $CableLinePoints = $res2[ 'rows' ];
    for ( $i = 0; $i < count( $CableLinePoints ); $i++ )
    {
        $point = $CableLinePoints[ $i ];
        if ( $point[ 'OpenGIS' ] == $OpenGIS )
        {
            $name = $nodeInfo[ 'name' ];
            $NetworkBoxId = $nodeInfo[ 'NetworkBoxId' ];
            $note = $nodeInfo[ 'note' ];
            $SettlementGeoSpatial = "NULL";
            $building = $nodeInfo[ 'building' ];
            $apartment = $nodeInfo[ 'apartment' ];
            $res3 = NetworkNode_Add( $name, $NetworkBoxId, $note, $OpenGIS,
                    $SettlementGeoSpatial, $building, $apartment, $tmpT );
            $NetworkNodeId = $res3[ 'rows' ][ 0 ][ 'id' ];

            $seq = $point[ 'sequence' ];
            $wr[ 'sequence' ] = $seq;
            $upd[ 'OpenGIS' ] = "NULL";
            $upd[ 'NetworkNode' ] = $NetworkNodeId;
            CableLinePoint_UPDATE( $upd, $wr, $tmpT );
            $wr1 = array( 'sequence' => array( 'val' => $seq, 'sign' => '>'));
            error_log(genWhere($wr1));

	    $ins = array();
	    $ins[ 'CableType' ] = $CableLine [ 'CableType' ];
	    $ins[ 'name' ] = $CableLine[ 'name' ]."_div";
	    $ins[ 'comment' ] = $CableLine[ 'comment' ];
	    $query = 'INSERT INTO "'.tmpTable( 'CableLine', $tmpT ).'"'.genInsert( $ins ).' RETURNING id';
	    error_log($query);
	    $res4 = PQuery( $query );
	    $NCableLineId = $res4[ 'rows' ][ 0 ][ 'id' ];

	    $ins = array();
	    foreach ( $point as $key => $value )
	    {
		if ( $key != "id" && $key != "sequence" )
		{
		    if ( $value != "" )
		{
		        $ins[ $key ] = $value;
		    }
		    else
		    {
		        $ins[ $key ] = "NULL";
		    }
		}
	    }
	    $ins[ 'CableLine' ] = $NCableLineId;
	    $ins[ 'sequence' ] = 1;
	    $ins[ 'OpenGIS' ] = 'NULL';
	    $ins[ 'NetworkNode' ] = $NetworkNodeId;
	    $query = 'INSERT INTO "'.tmpTable( 'CableLinePoint', $tmpT ).'"'.genInsert( $ins );
	    error_log($query);
	    PQuery( $query );
	    $query = 'UPDATE "'.tmpTable( 'CableLinePoint', $tmpT ).
		    '" SET "CableLine" = '.$NCableLineId.', sequence = sequence + 1 - '.$seq.
		    ' WHERE "sequence" > '.$seq.' AND "CableLine" = '.$CableLineId;
	    error_log($query);
	    PQuery( $query );
	    break;
	}
    }
}

function checkSession()
{
    $user_res = getCurrUserInfo();
    $user = $user_res[ 'rows' ][ 0 ][ 'id' ];
    $query = 'SELECT * FROM "MapSessions"
                WHERE "LastAction" + INTERVAL \'30 MINUTES\' > NOW()';
    $res = PQuery( $query );
    $sesUserId = $res[ 'rows' ][ 0 ][ 'UserId' ];
    if ($res[ 'count' ] > 0) {
        return $user == $sesUserId ? TRUE : FALSE;
    } else {
        checkData();
        return TRUE;
    }
    return $res[ 'count' ] > 0 ? $user == $sesUserId ? TRUE : FALSE  : TRUE;
}

function checkData()
{
    $res = PQuery( 'BEGIN WORK; LOCK "MapSettings";' );
    if (isset($res['error'])) {
        return array('error' => $res['error']);
    }
    $query = 'SELECT * FROM "MapSettings"
                WHERE "LastChangedMap" >= "LastChangedTmpMap"';
    $res = PQuery( $query );
    if (isset($res['error'])) {
	return array('error' => $res['error']);
    }
    if ( $res[ 'count' ] == 1 )
    {
        $res = dropTmpTables(true);
        $res = createTmpTables(true);
        setTmpMapLastEdit();
    } else {
        $res = createTmpTables(true);
    }
    $res = PQuery( 'COMMIT WORK' );
    return array('error' => isset($res['error']) ? $res['error'] : false );
}

function setTmpMapLastEdit()
{
    $query = 'UPDATE "MapSettings" SET "LastChangedTmpMap" = NOW()';
    PQuery( $query );
}

function setMapLastEdit()
{
    $query = 'UPDATE "MapSettings" SET "LastChangedMap" = NOW()';
    PQuery( $query );
}

function setMapUserActivity( $userId = -1 )
{
    if ( $userId == -1 )
    {
        $user_res = getCurrUserInfo();
        $user = $user_res[ 'rows' ][ 0 ][ 'id' ];
    }
    else
    {
        $user = $userId;
    }
    $query = 'BEGIN;
                DELETE FROM "MapSessions" WHERE "UserId" = '.$user.';
                INSERT INTO "MapSessions" ("UserId", "LastAction")
                VALUES ('.$user.', NOW());
                COMMIT;';
    PQuery( $query );
}

function finishMapSession( $userId = -1 )
{
    if ( $userId == -1 )
    {
        $user_res = getCurrUserInfo();
        $user = $user_res[ 'rows' ][ 0 ][ 'id' ];
    }
    else
    {
        $user = $userId;
    }
    $wr[ 'UserId' ] = $user;
    $query = 'UPDATE "MapSessions"
                SET "LastAction" = "LastAction" - INTERVAL \'30 MINUTES\' '.genWhere( $wr );
    PQuery( $query );
}

function saveTmpData()
{
    $query = 'BEGIN; LOCK "MapSettings";';
    $tables = getTables();
    $tbl_del = "";
    $ins = "";
    for ( $i = 0; $i < count( $tables ); $i++ )
    {
        $table = $tables[ $i ];
        $tmpT = tmpTable( $table, TRUE );
        if ( strlen( $tbl_del ) > 0 )
        {
            $tbl_del .= ', ';
        }
        $tbl_del .= '"'.$table.'"';
        $ins .= ' INSERT INTO "'.$table.'" SELECT * FROM "'.$tmpT.'";';
    }
    $query .= ' TRUNCATE '.$tbl_del.' CASCADE;'.$ins;
    $query .= ' COMMIT;';
    $res = PQuery( $query );
    if (!isset($res['error'])) {
        $res = setMapLastEdit();
    }
    if (!isset($res['error'])) {
        $res = CheckData();
    }
    return $res;
}

?>