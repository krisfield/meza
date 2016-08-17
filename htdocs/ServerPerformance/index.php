<?php

# Debug
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

# hack to prevent notice of undefined constant NS_MAIN from /opt/meza/config/local/preLocalSettings_allWikis.php
define("NS_MAIN", "");

# set timezone to prevent warnings when using strtotime()
date_default_timezone_set('America/Chicago');

require_once "/opt/meza/config/local/preLocalSettings_allWikis.php";
$username = $wgDBuser;
$password = $wgDBpassword;

$servername = "localhost";
$dbname = "server";
$dbtable = "performance";

$query = "SELECT
            DATE_FORMAT( datetime, '%Y-%m-%d %H:%i:%s' ) AS ts,
            loadavg1,
            loadavg5,
            loadavg15,
            memorypercentused,
            mysql,
            es,
            memcached,
            parsoid,
            apache
        FROM $dbtable;";

$mysqli = mysqli_connect("$servername", "$username", "$password", "$dbname");

$res = mysqli_query($mysqli, $query);

# Format query results
$data = array();
$variables = array("1-min Load Avg"=>"loadavg1",
    "5-min Load Avg"=>"loadavg5",
    "15-min Load Avg"=>"loadavg15",
    "Total Memory % Used"=>"memorypercentused",
    "MySQL"=>"mysql",
    "Elasticsearch"=>"es",
    "Memcached"=>"memcached",
    "Parsoid"=>"parsoid",
    "Apache"=>"apache");
while( $row = mysqli_fetch_assoc($res) ){

    list($ts, $loadavg1, $loadavg5, $loadavg15, $memorypercentused, $mysql, $es, $memcached, $parsoid, $apache
        ) = array($row['ts'], $row['loadavg1'], $row['loadavg5'], $row['loadavg15'], $row['memorypercentused'],
        $row['mysql'], $row['es'], $row['memcached'], $row['parsoid'], $row['apache']);

    foreach( $variables as $varname => $varvalue ){

        if( substr($varvalue,0,4) == "load"){
            $tempdata[$varvalue][] = array(
                'x' => strtotime($ts) * 1000,       // e.g. from 20160624080000 to 1384236000000
                'y' => floatval($$varvalue) * 100,  // e.g. from 0.1 to 10
            );
        } else {
            $tempdata[$varvalue][] = array(
                'x' => strtotime($ts) * 1000,   // e.g. from 20160624080000 to 1384236000000
                'y' => floatval($$varvalue),    // e.g. 10
            );
        }

        // $tempdata[$varvalue][] = array(
        //     'x' => strtotime($ts) * 1000,   // e.g. from 20160624080000 to 1384236000000
        //     'y' => floatval($$varvalue),    // e.g. 10
        // );

    }

}

foreach( $variables as $varname => $varvalue ){
    $data[] = array(
        'key'       => $varname,                // e.g. loadavg1
        'values'    => $tempdata[$varvalue],    // e.g. {"x":1384236000000,"y":0.1},{"x":1384256000000,"y":0.2},etc
    );
}

$html = '';
$html .= '<div id="server-performance-plot"><svg height="400px"></svg></div>';
$html .= "<script type='text/template-json' id='server-performance-data'>" . json_encode( $data ) . "</script>";

?><!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Meza Server Performance</title>
    <link rel="stylesheet" href="css/nv.d3.css" />
</head>
<body><!--

 --></body>
</html>

<?php
echo $html;
?>

<script type='application/javascript' src='js/jquery-3.1.0.min.js'></script>
<script type='application/javascript' src='js/d3.js'></script>
<script type='application/javascript' src='js/nv.d3.js'></script>
<script type='application/javascript' src='js/server-performance.nvd3.js'></script>
