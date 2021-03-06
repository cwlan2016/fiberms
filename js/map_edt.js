var map;
var drawControls, selectedFeature;
var lineLayer, lineLayer_halo, layerNodes, layerCableLinePoints, layerNodeNames,
        selectSingPointLayer, addNodeLayer;
var CableLine_arr = { };
var notyInformation, notyQuestion, notyError;
var panel, navigationCon, editCableCon, drawCableCon, deleteCableLineCon,
        addSingPointCon, deleteSingPointCon, addNodeCon, deleteNodeCon,
        divCableLineCon, saveCon, cancelCon;
var coor;
var CableLineEdtInfo = { };
var jsonInsertCoor;
var cableTypeArr, nodesArr, networkBoxesArr, freePointsArr, networkBoxTypesArr;
var mapCr = true;
var selectSingPoint = false, selectDeleteSingPointMode = false,
        selectDeleteCableLineMode = false, selectDeleteNodeMode = false,
        divLineMode = false;
var selectLineControl, selectSingPointControl,
        selectDeleteSingPointControl, selectDeleteCableLineControl;
var selectedCableLineId;
var selectedLineAddSingPoint;
var j = 0, j2 = 0;
var CableLine_Points_count = Array();
var nodesLabels_Count = 0;
var nodesLabels_arr = Array();
var style_arr = Array( {
    // Стиль линии (0 волокон)
    strokeColor: 'blue',
    //strokeOpacity: 5.5
    strokeWidth: 2
},
{
    // Стиль линии (1-8 волокон)
    strokeColor: 'white',
    //strokeOpacity: 5.5
    strokeWidth: 3//2
},
{
    // Стиль линии (9-24 волокон)
    strokeColor: 'white',
    //strokeOpacity: 5.5
    strokeWidth: 5
},
{
    // Стиль линии (25+ волокон)
    strokeColor: 'white',
    //strokeOpacity: 5.5
    strokeWidth: 7
} );

function disableControls() {
    selectSingPointControl.deactivate();
    selectSingPoint = false;
    selectDeleteSingPointControl.deactivate();
    selectDeleteSingPointMode = false;
    selectDeleteCableLineControl.deactivate();
    selectDeleteCableLineMode = false;
    selectLineControl.deactivate();
    selectSingPoint = false;
    //addNodeControl.deactivate();
    deleteNodeControl.deactivate();
    selectDeleteNodeMode = false;
    selectSingPointLayer.destroyFeatures();
    divLineMode = false;
}

function getData() {
    getCableTypes(); // получаем типы кабелей            
    getNodes(); // получаем узлы
    getNetworkBoxes(); // получаем ящики
    getNetworkBoxTypes(); // получаем типы ящиков

}

function getCableTypes() {
    function fillArr( data ) {
        cableTypesObj = JSON.parse( data );
        cableTypeArr = [ ];
        for ( var i = 0; i < cableTypesObj.CableTypes.length; i++ ) {
            cableTypeArr[i] = [ ];
            cableTypeArr[i][0] = cableTypesObj.CableTypes[i].id;
            cableTypeArr[i][1] = cableTypesObj.CableTypes[i].marking;
        }
    }
    $.get( 'getLayers_edt.php?mode=GetCableTypes',
            fillArr );
}

function getNodes() {
    function fillArr( data ) {
        nodesArrObj = JSON.parse( data );
        nodesArr = [ ];
        for ( var i = 0; i < nodesArrObj.Nodes.length; i++ ) {
            nodesArr[i] = [ ];
            nodesArr[i][0] = nodesArrObj.Nodes[i].id;
            nodesArr[i][1] = nodesArrObj.Nodes[i].name;
        }
    }
    $.get( 'getLayers_edt.php?mode=GetNodes',
            fillArr );
}

function getNetworkBoxes() {
    function fillArr( data ) {
        var networkBoxesObj = JSON.parse( data );
        networkBoxesArr = [ ];
        for ( var i = 0; i < networkBoxesObj.Boxes.length; i++ ) {
            networkBoxesArr[i] = [ ];
            networkBoxesArr[i][0] = networkBoxesObj.Boxes[i].id;
            networkBoxesArr[i][1] = networkBoxesObj.Boxes[i].inventoryNumber;
        }
    }
    $.get( 'getLayers_edt.php?mode=GetNetworkBoxes',
            fillArr );
}

function getNetworkBoxTypes() {
    function fillArr( data ) {
        var networkBoxTypesObj = JSON.parse( data );
        networkBoxTypesArr = [ ];
        for ( var i = 0; i < networkBoxTypesObj.BoxTypes.length; i++ ) {
            networkBoxTypesArr[i] = [ ];
            networkBoxTypesArr[i][0] = networkBoxTypesObj.BoxTypes[i].id;
            networkBoxTypesArr[i][1] = networkBoxTypesObj.BoxTypes[i].marking;
        }
    }
    $.get( 'getLayers_edt.php?mode=GetNetworkBoxTypes',
            fillArr );
}

function refreshAllLayers() {
    lineLayer_halo.destroyFeatures();
    lineLayer.destroyFeatures();
    layerNodeNames.destroyFeatures();
    j = 0;
    nodesLabels_Count = 0;
    CableLineEdtInfo = { };
    CableLine_arr = { };
    CableLine_Points_count = Array();
    getData();
    GetXMLFile(
            "getLayers_edt.php?mode=GetCableLines",
            parseCableLineXML );
    if ( typeof notyInformation !== "undefined" ) {
        notyInformation.close();
    }
    setTimeout( function() {
        layerNodes.refresh( { force: true } );
        layerCableLinePoints.refresh( { force: true } );
    }, 2500 );
}

function addPoint( lon, lat, title, ident, layr ) {
    var ttt = new OpenLayers.LonLat( parseFloat( lon ),
            parseFloat(
                    lat ) );
    ttt.transform( new OpenLayers.Projection(
            "EPSG:4326" ),
            new OpenLayers.Projection( "EPSG:900913" ) );
    for ( var k = 0; k < layr.features.length; k++ ) {
        if ( layr.features[k].attributes.PointId == ident ) {
            layr.features[k].move( ttt );
            layr.features[k].attributes.label = title;
            return false;
        }
    }
    var point0 = new OpenLayers.Geometry.Point( parseFloat(
            lon ),
            parseFloat( lat ) );
    point0.transform( new OpenLayers.Projection(
            "EPSG:4326" ),
            new OpenLayers.Projection( "EPSG:900913" ) );
    layr.addFeatures( new OpenLayers.Feature.Vector(
            point0,
            { label: title, name: title, PointId: ident } ) );
}

function init() {
    map = new OpenLayers.Map( {
        div: "map",
        projection: new OpenLayers.Projection(
                "EPSG:4326" ),
        displayProjection: new OpenLayers.Projection(
                "EPSG:4326" ),
        controls: [
            new OpenLayers.Control.MousePosition()
        ],
        units: "m"/*,
         allOverlays: true*/
    } );

    var ghyb = new OpenLayers.Layer.Google(
            "Google Hybrid",
            { type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20 }
    );

    var gsat = new OpenLayers.Layer.Google(
            "Google Спутник",
            { type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 20 }
    );
    map.addLayers( [ ghyb, gsat ] );

    var localLayer = new OpenLayers.Layer.OSM(
            "Локальна карта",
            "map/tiles/${z}/${x}/${y}.png",
            { numZoomLevels: 19,
                alpha: false,
                isBaseLayer: true,
                attribution: "",
            } );
    map.addLayer( localLayer );

    var osm = new OpenLayers.Layer.OSM();
    map.addLayers( [ osm ] );

    // рисуем типо гало :)
    lineLayer_halo = new OpenLayers.Layer.Vector(
            "Кабельные линии (гало)" );
    map.addLayer( lineLayer_halo );

    lineLayer = new OpenLayers.Layer.Vector(
            "Кабельные линии" );
    map.addLayer( lineLayer );
    lineLayer.events.on( {
        "featureselected": selectDeleteCableLine
    } );
    selectDeleteCableLineControl = new OpenLayers.Control.SelectFeature(
            [ lineLayer ] );
    map.addControl( selectDeleteCableLineControl );

    var styleMarkersLabels = new OpenLayers.Style( // стили для надписей узлов
            {
                strokeWidth: 2,
                labelYOffset: 10,
                label: "${label}",
                fontColor: '#faa',
                fontSize: 11,
                fontFamily: "Arial",
                fontWeight: "bold",
                labelOutlineColor: "black",
                labelOutlineWidth: 3
            } );

    layerNodes = new OpenLayers.Layer.Vector(
            "Узлы",
            {
                strategies: [ new OpenLayers.Strategy.BBOX(
                            { resFactor: 1.1 } ) ],
                protocol: new OpenLayers.Protocol.HTTP(
                        {
                            url: "getLayers_edt.php?mode=GetNodesMarkers",
                            format: new OpenLayers.Format.Text()
                        } )
            } );
    map.addLayer( layerNodes );
    layerNodes.events.on( {
        "featureselected": selectDeleteNode,
        "afterfeaturemodified": moveFeature
    } );
    deleteNodeControl = new OpenLayers.Control.SelectFeature(
            [ layerNodes ] );
    map.addControl( deleteNodeControl );

    layerCableLinePoints = new OpenLayers.Layer.Vector(
            "Особые точки линии",
            {
                minScale: 7000,
                strategies: [ new OpenLayers.Strategy.BBOX(
                            { resFactor: 1.1 } ) ],
                protocol: new OpenLayers.Protocol.HTTP(
                        {
                            url: "getLayers_edt.php?mode=GetSingularCableLinePoints",
                            format: new OpenLayers.Format.Text()
                        } )
            } );
    layerCableLinePoints.events.on( {
        "featureselected": selectDeleteSingPoint
    } );
    selectDeleteSingPointControl = new OpenLayers.Control.SelectFeature(
            [ layerCableLinePoints ] );
    map.addControl( selectDeleteSingPointControl );

    map.addLayer(
            layerCableLinePoints );

    layerNodeNames = new OpenLayers.Layer.Vector(
            "Узлы (надписи)",
            {
                minScale: 7000,
                styleMap: new OpenLayers.StyleMap(
                        { "default": styleMarkersLabels,
                            "select": { pointRadius: 20 }
                        } )
            } );
    map.addLayer( layerNodeNames );

    lineLayer.events.on( {
        "afterfeaturemodified": updCableLine,
        "beforefeaturemodified": function() {
            notyInformation.close();
            showInformation( 'topCenter',
                    'Щелкните в любом месте для завершения редактирования' );
            setTimeout( function() {
                notyInformation.close();
            }, 5000 );
        },
        "featureselected": getFreeSingPoint//getSingPoints
    } );
    selectLineControl = new OpenLayers.Control.SelectFeature(
            [ lineLayer ] );
    map.addControl( selectLineControl );
    //selectLineControl.activate();

    addCableLineLayer = new OpenLayers.Layer.Vector(
            "AddLineLayer" );
    map.addLayer(
            addCableLineLayer );
    addCableLineLayer.events.on( {
        "featureadded": addCableLine
    } );

    selectSingPointLayer = new OpenLayers.Layer.Vector(
            "SelectSingPointLayer" );
    map.addLayer(
            selectSingPointLayer );

    addNodeLayer = new OpenLayers.Layer.Vector(
            "AddNodeLayer" );
    map.addLayer(
            addNodeLayer );
    addNodeLayer.events.on( {
        "featureadded": addNodeMsg
    } );
    /*addNodeControl = new OpenLayers.Control.SelectFeature(
     [ addNodeLayer ] );
     map.addControl( addNodeControl );*/

    selectSingPointLayer.events.on( {
        "featureselected": setSingPoint
    } );
    selectSingPointControl = new OpenLayers.Control.SelectFeature(
            [ selectSingPointLayer ] );
    map.addControl( selectSingPointControl );

    panel = new OpenLayers.Control.Panel(
            {
                //displayClass: "olControlEditingToolbar",
                createControlMarkup: function(
                        control ) {
                    var button = document.createElement(
                            'button' ),
                            iconSpan = document.createElement(
                                    'span' ),
                            textSpan = document.createElement(
                                    'span' );
                    iconSpan.innerHTML = '&nbsp;';
                    button.appendChild(
                            iconSpan );
                    if ( control.text ) {
                        textSpan.innerHTML = control.text;
                    }
                    button.appendChild(
                            textSpan );
                    return button;
                }
            }
    );

    navigationCon = new OpenLayers.Control.Navigation(
            {
                text: "Навигация",
                displayClass: "olControlNavigation",
                mode: OpenLayers.Control.Navigation
            } );
    navigationCon.events.register( "activate", this, function() {
        disableControls();
        if ( typeof notyInformation !== "undefined" ) {
            notyInformation.close();
        }
    } );

    editCableCon = new OpenLayers.Control.ModifyFeature(
            lineLayer, {
                title: "Позволяет редактировать кабельные линии",
                text: 'Изменить<br>линию',
                vertexRenderIntent: 'temporary',
                displayClass: "olControlEditCable",
                modified: true,
                createVertices: true,
                mode: OpenLayers.Control.ModifyFeature.RESHAPE
            } );
    editCableCon.events.register( "activate", this, function() {
        disableControls();
        showInformation( 'topCenter', 'Выберите линию' );
    } );

    drawCableCon = new OpenLayers.Control.DrawFeature(
            addCableLineLayer,
            OpenLayers.Handler.Path,
            {
                title: "Позволяет добавлять кабельные линии",
                text: 'Добавить<br>линию',
                displayClass: "olControlDrawCable",
                handlerOptions: { multi: false }
            } );
    drawCableCon.events.register( "activate", this, function() {
        disableControls();
        showInformation( 'topCenter',
                'Щелкните два раза для завершения рисования' );
        setTimeout( function() {
            notyInformation.close();
        }, 5000 );
    } );

    deleteCableLineCon = new OpenLayers.Control.Navigation(
            {
                title: "Позволяет удалять кабельные линии",
                text: "Удалить<br>линию",
                displayClass: "olControlDeleteCable",
                mode: OpenLayers.Control.Navigation
            }
    );
    deleteCableLineCon.events.register( "activate", this,
            function() {
                disableControls();
                selectDeleteCableLineControl.activate();
                selectDeleteCableLineMode = true;
                showInformation( 'topCenter', 'Выберите линию' );
            } );
    divCableLineCon = new OpenLayers.Control.Navigation(
            {
                title: "Делит линию",
                text: "Деление<br>линии",
                displayClass: "olControlAddSingPoint",
                mode: OpenLayers.Control.Navigation
            } );
    divCableLineCon.events.register( "activate", this,
            function() {
                disableControls();
                selectLineControl.activate();
                selectSingPoint = true;
                divLineMode = true;
                showInformation( 'topCenter', 'Выберите линию' );
            } );

    addSingPointCon = new OpenLayers.Control.Navigation(
            {
                title: "Позволяет добавлять особые точки",
                text: "Добавить<br>особую точку",
                displayClass: "olControlAddSingPoint",
                mode: OpenLayers.Control.Navigation
            } );
    addSingPointCon.events.register( "activate", this,
            function() {
                disableControls();
                selectLineControl.activate();
                selectSingPoint = true;
                showInformation( 'topCenter', 'Выберите линию' );
            } );

    deleteSingPointCon = new OpenLayers.Control.Navigation(
            {
                title: "Позволяет удалять особые точки",
                text: "Удалить<br>особую точку",
                displayClass: "olControlDeleteSingPoint",
                mode: OpenLayers.Control.Navigation
            } );
    deleteSingPointCon.events.register( "activate", this,
            function() {
                disableControls();
                selectDeleteSingPointControl.activate();
                selectDeleteSingPointMode = true;
                showInformation( 'topCenter',
                        'Выберите особую точку' );
            } );

    addNodeCon = new OpenLayers.Control.DrawFeature( addNodeLayer,
            OpenLayers.Handler.Point, {
                title: "Позволяет добавлять узлы",
                text: "Добавить<br>узел",
                displayClass: "olControlAddNode",
                handlerOptions: { multi: false }
            } );
    addNodeCon.events.register( "activate", this,
            function() {
                disableControls();
                //addNodeControl.activate();
            } );

    deleteNodeCon = new OpenLayers.Control.Navigation(
            {
                title: "Позволяет удалять узлы",
                text: "Удалить<br>узел",
                displayClass: "olControlDeleteNode",
                mode: OpenLayers.Control.Navigation
            } );
    deleteNodeCon.events.register( "activate", this,
            function() {
                disableControls();
                deleteNodeControl.activate();
                selectDeleteNodeMode = true;
                showInformation( 'topCenter', 'Выберите узел' );
            } );

    moveNode = new OpenLayers.Control.ModifyFeature(
            layerNodes, {
                title: "Позволяет перемещать узлы",
                text: 'Переместить<br>узел',
                vertexRenderIntent: 'temporary',
                displayClass: "olControlMoveNode",
                mode: OpenLayers.Control.DragFeature
            } );
    moveNode.events.register( "activate", this, function() {
        disableControls();
        showInformation( 'topCenter', 'Выберите узел и переместите его' );
    } );

    saveCon = new OpenLayers.Control.Button( {
        title: "Сохраняет изменения",
        text: "Сохранить<br>изменения",
        trigger: function() {
            showInformation( 'topCenter', 'Сохраняем...' );
            setTimeout( function() {
                notyInformation.close();
            }, 9000 );
            $.post( "map_post.php", { mode: "save", userId: userId },
            function(data) {
                res = JSON.parse( data );
                if (res.error) {
                    alert(res.error);
                }
                refreshAllLayers();
            } );
        },
        displayClass: "olControlSave"
    } );

    cancelCon = new OpenLayers.Control.Button( {
        title: "Отменяет все изменения",
        text: "Отменить<br>изменения",
        trigger: function() {
            showInformation( 'topCenter', 'Отменяем изменения...' );
            setTimeout( function() {
                notyInformation.close();
            }, 9000 );
            $.post( "map_post.php", { mode: "cancel", userId: userId },
            function(data) {
                res = JSON.parse( data );
                if (res.error) {
                    alert(res.error);
                }
                refreshAllLayers();
            } );
        },
        displayClass: "olControlCancel"
    } );

    panel.addControls(
            [ navigationCon, editCableCon, drawCableCon, deleteCableLineCon,
                divCableLineCon, addSingPointCon, deleteSingPointCon,
                addNodeCon, deleteNodeCon, moveNode, saveCon, cancelCon ] );
    map.addControl(
            panel );
    navigationCon.activate();

    map.addControls( [
        new OpenLayers.Control.Navigation(),
        new OpenLayers.Control.PanZoomBar(),
        //new OpenLayers.Control.Zoom(),
        new OpenLayers.Control.LayerSwitcher(
                { 'ascending': false } ),
        new OpenLayers.Control.Permalink(),
        new OpenLayers.Control.ScaleLine(),
        new OpenLayers.Control.Permalink(
                'permalink' ),
        new OpenLayers.Control.MousePosition()
                //new OpenLayers.Control.OverviewMap(),
    ] );
    var lonLat = new OpenLayers.LonLat(
            lon,
            lat ).transform(
            new OpenLayers.Projection(
                    "EPSG:4326" ),
            map.getProjectionObject() );
    map.setCenter( lonLat,
            zoom );
    map.setLayerIndex(
            map.layers[6],
            7 );

    mapCr = false;
    drawFeatures();
}

function drawFeatures() {
    var k, k2;
    for ( k = 0; k < j; k++ ) {
        var style_halo = {
            strokeColor: 'black',
            strokeWidth: style_arr[CableLine_arr[k]['style']]['strokeWidth'] + 1
        };

        var points = Array();
        for ( k2 = 0; k2 < CableLine_Points_count[k]; k2++ ) {
            //alert(CableLine_Points_count[k]);
            lon1 = CableLine_arr[k]['points'][k2]['lon'];
            lat1 = CableLine_arr[k]['points'][k2]['lat'];
            points[k2] = new OpenLayers.Geometry.Point(
                    lon1,
                    lat1 );
        }
        var line = new OpenLayers.Geometry.LineString(
                points );
        line.transform( new OpenLayers.Projection(
                "EPSG:4326" ),
                new OpenLayers.Projection(
                        "EPSG:900913" ) );
        var lineFeature = new OpenLayers.Feature.Vector(
                line,
                null, style_halo );
        lineLayer_halo.addFeatures(
                [ lineFeature ] );
    }
    // рисуем типо гало :)

    for ( k = 0; k < j; k++ ) {
        var points = Array();
        for ( k2 = 0; k2 < CableLine_Points_count[k]; k2++ ) {
            lon1 = CableLine_arr[k]['points'][k2]['lon'];
            lat1 = CableLine_arr[k]['points'][k2]['lat'];
            points[k2] = new OpenLayers.Geometry.Point(
                    lon1,
                    lat1 );
        }
        var line = new OpenLayers.Geometry.LineString(
                points );
        line.transform(
                new OpenLayers.Projection(
                        "EPSG:4326" ),
                new OpenLayers.Projection(
                        "EPSG:900913" ) );
        var lineFeature = new OpenLayers.Feature.Vector(
                line,
                null,
                style_arr[CableLine_arr[k]['style']] );
        var line_halo = lineFeature.clone();
        lineLayer.addFeatures(
                [ lineFeature ] );
        CableLineEdtInfo[lineFeature.id] = { };
        CableLineEdtInfo[lineFeature.id]['seqStart'] = CableLine_arr[k]['sequenceStart'];
        CableLineEdtInfo[lineFeature.id]['seqEnd'] = CableLine_arr[k]['sequenceEnd'];
        CableLineEdtInfo[lineFeature.id]['cableLineId'] = CableLine_arr[k]['cableLineId'];
        CableLineEdtInfo[lineFeature.id]['superSeqEnd'] = CableLine_arr[k]['superSeqEnd'];
    }

    var lat2, lon2, title, ident;
    for ( l = 0; l < nodesLabels_Count; l++ ) {
        if ( !!nodesLabels_arr[l]["points"][0] ) {
            lat2 = nodesLabels_arr[l]["points"][0]["lat"];
            lon2 = nodesLabels_arr[l]["points"][0]["lon"];
            title = nodesLabels_arr[l]["title"];
            ident = nodesLabels_arr[l]["ident"];
            addPoint( lon2,
                    lat2,
                    title,
                    ident,
                    layerNodeNames );
        }
    }
}

j = 0;
j2 = 0;
GetXMLFile(
        "getLayers_edt.php?mode=GetCableLines",
        parseCableLineXML ); // получаем кабельные линии