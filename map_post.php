<?php

require_once "backend/functions.php";
require_once "backend/map.php";

ini_set('display_errors', false);

if ( $_SERVER[ "REQUEST_METHOD" ] == 'POST' )
{
    if (isset($_POST[ 'coors' ])) {
        $obj = json_decode( $_POST[ 'coors' ] );
        $coors = isset($obj->{'coorArr'}) ? $obj->{'coorArr'} : NULL;
        $CableLineId = isset($obj->{'CableLineId'}) ? (int)$obj->{'CableLineId'} : NULL;
    } else {
        $coors = $CableLineId = NULL;
    }
    $uId = (isset($_POST[ 'userId' ])) ? (int)$_POST[ 'userId' ] : NULL;
    $_SESSION[ 'user_id' ] = $uId;
    if ( $_POST[ 'mode' ] == "updCableLine" )
    {
        $seqStart = $obj->{'seqStart'};
        $seqEnd = $obj->{'seqEnd'};
        updCableLinePoints( $coors, $CableLineId, $seqStart, $seqEnd, TRUE );
        setTmpMapLastEdit();
    }
    elseif ( $_POST[ 'mode' ] == "addCableLine" )
    {
        $length = $obj->{'length'};
        $name = $obj->{'name'};
        $comment = $obj->{'comment'};
        $CableType = $obj->{'CableType'};
        addCableLinePoint( $coors, $CableType, $length, $name, $comment, TRUE );
        setTmpMapLastEdit();
    }
    elseif ( $_POST[ 'mode' ] == "addSingPoint" )
    {
        require_once ( "func/CableType.php" );

        /*$apartment = $obj->{'apartment'};
        $building = $obj->{'building'};*/
        $meterSign = $obj->{'meterSign'};
        $note = $obj->{'note'};
        $networkNode = isset($obj->{'networkNode'}) ? $obj->{'networkNode'} : "";
        addSingPoint( $coors, $CableLineId, $networkNode, "NULL", "NULL",
                $meterSign, $note );
        setTmpMapLastEdit();
    }
    elseif ( $_POST[ 'mode' ] == "deleteSingPoint" )
    {
        deleteSingPoint( $coors, TRUE );
        setTmpMapLastEdit();
    }
    elseif ( $_POST[ 'mode' ] == "deleteCableLine" )
    {
        deleteCableLine( $CableLineId, TRUE );
        setTmpMapLastEdit();
    }
    elseif ( $_POST[ 'mode' ] == "addNode" )
    {
        $NetworkBoxId = $obj->{'NetworkBoxId'};
        $apartment = NULL;
        $building = NULL;
        $note = $obj->{'note'};
        $name = $obj->{'name'};
        $place = $obj->{'place'};
        addNode( $coors, $name, $NetworkBoxId, $note, NULL,
                $building, $apartment, $place, TRUE );
        setTmpMapLastEdit();
    }
    elseif ( $_POST[ 'mode' ] == "deleteNode" )
    {
        deleteNode( $coors, TRUE );
        setTmpMapLastEdit();
    }
    elseif ( $_POST[ 'mode' ] == "moveNode" )
    {
        moveNode( $coors, TRUE );
        setTmpMapLastEdit();
    }
    elseif ( $_POST[ 'mode' ] == "divCableLine" )
    {
        $nodeInfo[ 'name' ] = $obj->{'name'};
        $nodeInfo[ 'NetworkBoxId' ] = $obj->{'NetworkBoxId'};
        /*$nodeInfo[ 'apartment' ] = $obj->{'apartment'};
        $nodeInfo[ 'building' ] = $obj->{'building'};*/
        $nodeInfo[ 'note' ] = $obj->{'note'};
        $nodeInfo[ 'place' ] = $obj->{'place'};
        $ret = divCableLine( $coors, $CableLineId, $nodeInfo, TRUE );
        if ($ret['error']) {
            print json_encode($ret);
        } else {
            setTmpMapLastEdit();
            print json_encode( array( "error" => false ) );
        }
    }
    elseif ( $_POST[ 'mode' ] == "addNetworkBox" )
    {
        $networkBoxType = $obj->{'networkBoxType'};
        $invNum = $obj->{'invNum'};
        $boxId = addNetworkBox( $networkBoxType, $invNum, TRUE );
        if (isset($boxId['id'])) {
           $result = array( "NetworkBoxId" => $boxId['id'] );
        } else {
           $result = array( "error" => (isset($boxId['error'])) ? $boxId['error'] : NULL );
        }
        setTmpMapLastEdit();
        setMapUserActivity( $uId );
        print json_encode( $result );
        die();
    }
    elseif ( $_POST[ 'mode' ] == "save" )
    {
        $result = saveTmpData();
        if (defined($result['error'])) {
            print json_encode( array( "error" => $result['error'] ) );
        } else {
            print json_encode( array( "error" => false ) );
        }
    }
    elseif ( $_POST[ 'mode' ] == "cancel" )
    {
        setMapLastEdit();
        $result = checkData();
        print json_encode( $result );
    }
    setMapUserActivity( $uId );
}
?>