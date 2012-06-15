<?php
require_once("backend/NetworkBoxType.php");
require_once("backend/NetworkNode.php");

function NetworkBoxType_Check($marking,$manufacturer,$units,$width,$height,$length,$diameter) {	$result = 1;
	/* ����� �������� */
	return $result;
}

function NetworkBoxType_Mod($id,$marking,$manufacturer,$units,$width,$height,$length,$diameter) {	if (NetworkBoxType_Check($marking,$manufacturer,$units,$width,$height,$length,$diameter) == 0) {		return 0;
	}
	$upd['marking'] = $marking;
	$upd['manufacturer'] = $manufacturer;
	$upd['units'] = $units;
	$upd['width'] = $width;
	$upd['height'] = $height;
	$upd['length'] = $length;
	$upd['diameter'] = $diameter;
	$wr['id'] = $id;
  	$res = NetworkBoxType_UPDATE($upd,$wr);
	if (isset($res['error'])) {
  		return $res;
  	}
  	return 1;
}

function NetworkBoxType_Add($marking,$manufacturer,$units,$width,$height,$length,$diameter) {	if (NetworkBoxType_Check($marking,$manufacturer,$units,$width,$height,$length,$diameter) == 0) {
		return 0;
	}	$ins['marking'] = $marking;
	$ins['manufacturer'] = $manufacturer;
	$ins['units'] = $units;
	$ins['width'] = $width;
	$ins['height'] = $height;
	$ins['length'] = $length;
	$ins['diameter'] = $diameter;
	$res = NetworkBoxType_INSERT($ins);
	if (isset($res['error'])) {
  		return $res;
  	}
	return 1;
}

function GetNetworkBoxTypeInfo($NetworkBoxTypeId) {	$wr['id'] = $_GET['boxtypeid'];
    $res = NetworkBoxType_SELECT('',$wr);
    $result['NetworkBoxType'] = $res;
    unset($wr);
    $wr['NetworkBoxType'] = $NetworkBoxTypeId;
    $res2 = NetworkBox_SELECT('',$wr);
    $result['NetworkBoxType']['NetworkBoxCount'] = $res2['count'];
    return $result;
}

function NetworkBox_Check($BoxTypeId,$InvNum) {	$result = 1;
	/* ����� �������� */
	return $result;
}

function NetworkBox_Mod($id,$BoxTypeId,$InvNum) {	if (NetworkBox_Check($BoxTypeId,$InvNum) == 0) {		return 0;
	}	$upd['inventoryNumber'] = $InvNum;
   	$upd['NetworkBoxType'] = $BoxTypeId;
   	$wr['id'] = $id;
   	NetworkBox_UPDATE($upd,$wr);
   	return 1;
}

function NetworkBox_Add($BoxTypeId,$InvNum) {	if (NetworkBox_Check($BoxTypeId,$InvNum) == 0) {
		return 0;
	}
	$ins['NetworkBoxType'] = $BoxTypeId;
	$ins['inventoryNumber'] = $InvNum;
	NetworkBox_INSERT($ins);
	return 1;
}

function GetNetworkBoxInfo($NetworkBoxId) {	$wr['id'] = $NetworkBoxId;
   	$res = NetworkBox_SELECT(0,$wr);
   	$result['NetworkBox'] = $res;
   	unset($wr);
	$wr['id'] = $res['rows'][0]['NetworkBoxType'];
	$res2 = NetworkBoxType_SELECT('',$wr);
	$result['NetworkBox']['rows'][0]['NetworkBoxType'] = $res2['rows'][0];
	unset($wr);
	$wr['NetworkBox'] = $NetworkBoxId;
	$res3 = NetworkNode_SELECT('','',$wr);
	$result['NetworkBox']['rows'][0]['NetworkNode'] = $res3['rows'][0];
	return $result;
}

?>