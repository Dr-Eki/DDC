<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    require_once('constants.php');
    require_once('units.php');
    require_once('races.php');
    require_once('spells.php');
    require_once('functions.php');
    require_once('functions_op_center.php');

    $dominion = array();
    require_once('calculator.php');
    header('Content-Type: application/json');
    echo json_encode($dominion, JSON_PARTIAL_OUTPUT_ON_ERROR);
}