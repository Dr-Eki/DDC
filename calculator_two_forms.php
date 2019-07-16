<?php

require_once('functions.php');
require_once('constants.php');
require_once('units.php');
require_once('spells.php');

$dominion = array();
$dominion['defender']['general'] = array();
$dominion['defender']['military'] = array();
$dominion['defender']['buildings'] = array();
$dominion['defender']['castle'] = array();
$dominion['defender']['land'] = array();

// Clear Sight
$clearsight = trim($_POST['defender_clearsight']);
$clearsight = explode("\n", $clearsight);

if(count($clearsight) > 3)
{
	$dominion['clearsight_info'] = TRUE;
}

// Survey
$survey = trim($_POST['defender_survey']);
$survey = explode("\n", $survey);

foreach($survey as $building)
{
	$buildings[] = explode("\t", $building);
}
foreach($buildings as $building)
{
	if($building[0] !== 'Building Type')
	{
		$building[0] = trim(str_replace('NYI','',str_replace('PI','',$building[0])));
		$dominion['defender']['buildings'][$building[0]] = intval($building[1]);
	}
}

// Castle
$castle = trim($_POST['defender_castle']);
$castle = explode("\n", $castle);

foreach($castle as $part)
{
	$improvements[] = explode("\t", $part);
}
foreach($improvements as $part)
{
	if(trim($part[0]) !== 'Part')
	{
		$part[0] = trim($part[0]);
		$dominion['defender']['castle'][$part[0]] = floatval($part[1])/100;
	}
}

// Barracks (returning)

$ignored_units = array('Unit','Spies','Wizards','Archmages');

$barracks_returning = trim($_POST['defender_barracks_returning']);
$barracks_returning = explode("\n", $barracks_returning);

foreach($barracks_returning as $unit)
{
	$units[] = explode("\t", $unit);
}
unset($barracks_returning);
foreach($units as $unit)
{
	if(!in_array(trim($unit[0]),$ignored_units))
	{	
		$returning[] = intval(str_replace(',','',$unit[13]));
	}
}

unset($units);

// Barracks (home)
$barracks_home = trim($_POST['defender_barracks_home']);
$barracks_home = explode("\n", $barracks_home);
foreach($barracks_home as $unit)
{
	$units[] = explode("\t", $unit);
}
unset($barracks_home);
foreach($units as $unit)
{
	if(!in_array(trim($unit[0]),$ignored_units))
	{	
		$home[] = intval(str_replace(',','',str_replace('~','',$unit[13])));
	}
}

# Do we have BS info?
if(count($home) === 5 and count($returning) === 4)
{
	$dominion['defender']['barracks_info'] = TRUE;
}

unset($units);

/*
	0 = Dominion
	2 = Ruler
	3 = Race
	4 = Land
	5 = Peasants
	8 = Prestige
	19 = Morale
	20 = Draftees
	21 = Unit 1
	22 = Unit 2
	23 = Unit 3
	24 = Unit 4
*/

preg_match('#\((.*?)\)#', $clearsight[0], $name);
$dominion['defender']['general']['name'] = trim($name[1]);
unset($name);

$dominion['defender']['general']['ruler'] = trim(str_replace('Ruler:','',str_replace("\t",'',$clearsight[2])));
$dominion['defender']['general']['race'] = trim(str_replace('Race:','',str_replace("\t",'',$clearsight[3])));
$dominion['defender']['general']['land'] = intval(str_replace(',','',str_replace('Land:','',str_replace("\t",'',$clearsight[4]))));
$dominion['defender']['general']['peasants'] = intval(str_replace(',','',str_replace("Peasants:\t",'',$clearsight[5])));
$dominion['defender']['general']['prestige'] = intval(str_replace(',','',str_replace('Prestige:','',str_replace("\t",'',$clearsight[8]))));
$dominion['defender']['general']['morale'] = intval(str_replace('%','',str_replace('Morale:','',str_replace("\t",'',$clearsight[19]))));

# Piece together trained army.
$dominion['defender']['military'] = $scribes['military'][$dominion['defender']['general']['race']];

if($_POST['origin'] == 'defender-military')
{
	$dominion['defender']['military']['draftees'] = $_POST['defender_military_draftees'];
	$dominion['defender']['military']['unit1']['trained'] = $_POST['defender_military_unit1_trained'];
	$dominion['defender']['military']['unit2']['trained'] = $_POST['defender_military_unit2_trained'];
	$dominion['defender']['military']['unit3']['trained'] = $_POST['defender_military_unit3_trained'];
	$dominion['defender']['military']['unit4']['trained'] = $_POST['defender_military_unit4_trained'];
	$dominion['defender']['buildings']['Forest Haven'] = $_POST['defender_military_forest_havens'];
}
else
{
	$dominion['defender']['military']['draftees'] = filter_var(str_replace('Draftees: 	','',$clearsight[20]),FILTER_SANITIZE_NUMBER_INT);
	$dominion['defender']['military']['unit1']['trained'] = filter_var(str_replace($scribes['military'][$dominion['defender']['general']['race']]['unit1']['name'] . ' 	','',$clearsight[21]),FILTER_SANITIZE_NUMBER_INT);
	$dominion['defender']['military']['unit2']['trained'] = filter_var(str_replace($scribes['military'][$dominion['defender']['general']['race']]['unit1']['name'] . ' 	','',$clearsight[22]),FILTER_SANITIZE_NUMBER_INT);
	$dominion['defender']['military']['unit3']['trained'] = filter_var(str_replace($scribes['military'][$dominion['defender']['general']['race']]['unit1']['name'] . ' 	','',$clearsight[23]),FILTER_SANITIZE_NUMBER_INT);
	$dominion['defender']['military']['unit4']['trained'] = filter_var(str_replace($scribes['military'][$dominion['defender']['general']['race']]['unit1']['name'] . ' 	','',$clearsight[24]),FILTER_SANITIZE_NUMBER_INT);
}

# Do we have BS figures for Returning units?
$dominion['defender']['military']['unit1']['returning'] = $returning[0];
$dominion['defender']['military']['unit2']['returning'] = $returning[1];
$dominion['defender']['military']['unit3']['returning'] = $returning[2];
$dominion['defender']['military']['unit4']['returning'] = $returning[3];
unset($returning);

# Do we have BS figures for at Home units?
$dominion['defender']['military']['unit1']['home'] = $home[1];
$dominion['defender']['military']['unit2']['home'] = $home[2];
$dominion['defender']['military']['unit3']['home'] = $home[3];
$dominion['defender']['military']['unit4']['home'] = $home[4];
unset($home);

# Calculate raw DP
$dominion['defender']['dp']['raw'] = 0;

# Calculate available DP

## Calculate available.
if($dominion['defender']['barracks_info'] == TRUE)
{
	if($dominion['clearsight_info'] !== TRUE)
	{
		$dominion['defender']['draftee_dp'] = $home[0] / 0.85;
	}
	else
	{
		$dominion['defender']['draftee_dp'] = $dominion['defender']['military']['draftees'] * 1;
	}

	$dominion['defender']['military']['unit1']['available_dp'] = min(($dominion['defender']['military']['unit1']['trained'] - $dominion['defender']['military']['unit1']['returning'] * 0.85), ($dominion['defender']['military']['unit1']['home'] / 0.85)) * $dominion['defender']['military']['unit1']['dp'];
	$dominion['defender']['military']['unit2']['available_dp'] = min(($dominion['defender']['military']['unit2']['trained'] - $dominion['defender']['military']['unit2']['returning'] * 0.85), ($dominion['defender']['military']['unit2']['home'] / 0.85)) * $dominion['defender']['military']['unit2']['dp'];
	$dominion['defender']['military']['unit3']['available_dp'] = min(($dominion['defender']['military']['unit3']['trained'] - $dominion['defender']['military']['unit3']['returning'] * 0.85), ($dominion['defender']['military']['unit3']['home'] / 0.85)) * $dominion['defender']['military']['unit3']['dp'];
	$dominion['defender']['military']['unit4']['available_dp'] = min(($dominion['defender']['military']['unit4']['trained'] - $dominion['defender']['military']['unit4']['returning'] * 0.85), ($dominion['defender']['military']['unit4']['home'] / 0.85)) * $dominion['defender']['military']['unit4']['dp'];
}
else
{
	## Draftees -- REMOVE FOR DARK ELF
	$dominion['defender']['draftee_dp'] = $dominion['defender']['military']['draftees'] * 1;
	## Units 1-4
	$dominion['defender']['military']['unit1']['available_dp'] = $dominion['defender']['military']['unit1']['trained'] * $dominion['defender']['military']['unit1']['dp'];
	$dominion['defender']['military']['unit2']['available_dp'] = $dominion['defender']['military']['unit2']['trained'] * $dominion['defender']['military']['unit2']['dp'];
	$dominion['defender']['military']['unit3']['available_dp'] = $dominion['defender']['military']['unit3']['trained'] * $dominion['defender']['military']['unit3']['dp'];
	$dominion['defender']['military']['unit4']['available_dp'] = $dominion['defender']['military']['unit4']['trained'] * $dominion['defender']['military']['unit4']['dp'];	
}



## FH -- Assumes 20 peasants are available for each FH
$dominion['defender']['forest_havens_dp'] = $dominion['defender']['buildings']['Forest Haven'] * 20 * 0.75;

# Sum up raw DP to create Raw DP in $dominion['defender']['dp']['raw']
$dominion['defender']['dp']['raw'] += $dominion['defender']['military']['unit1']['available_dp'];
$dominion['defender']['dp']['raw'] += $dominion['defender']['military']['unit2']['available_dp'];
$dominion['defender']['dp']['raw'] += $dominion['defender']['military']['unit3']['available_dp'];
$dominion['defender']['dp']['raw'] += $dominion['defender']['military']['unit4']['available_dp'];
$dominion['defender']['dp']['raw'] += $dominion['defender']['draftee_dp'];
$dominion['defender']['dp']['raw'] += $dominion['defender']['forest_havens_dp'];





# Calculate the DP mods to create the DP modifier
$dominion['defender']['dp']['modifier'] = 0.00;

## Is user submitting manual GT/Walls/Racial figures?
if($_POST['origin'] == 'defender-mods')
{
	$dominion['defender']['dp']['mods']['Walls'] = $_POST['defender_mods_castle_walls'] / 100  + 0.01/100;
	$dominion['defender']['dp']['mods']['Racial'] = $_POST['defender_mods_racial'] / 100;
	$dominion['defender']['dp']['mods']['Guard Tower'] = min(((($_POST['defender_mods_guard_towers'] / 100) * GUARD_TOWER_MULTIPLIER) + 0.01/100), GUARD_TOWER_MAX);

	$dominion['defender']['display']['Guard Tower'] = $_POST['defender_mods_guard_towers'];
	$dominion['defender']['display']['Walls'] = $_POST['defender_mods_castle_walls'];
}
else
{
	$dominion['defender']['dp']['mods']['Walls'] = $dominion['defender']['castle']['Walls'] + 0.01/100;
	$dominion['defender']['dp']['mods']['Racial'] = $dominion['defender']['dp']['mods']['Racial'];
	$dominion['defender']['dp']['mods']['Guard Tower'] = round($dominion['defender']['buildings']['Guard Tower'] / $dominion['defender']['general']['land'],4,PHP_ROUND_HALF_UP) * GUARD_TOWER_MULTIPLIER + 0.01/100;

	# Define GT and Walls for input field
	$dominion['defender']['display']['Guard Tower'] = round(($dominion['defender']['buildings']['Guard Tower'] / $dominion['defender']['general']['land']),4,PHP_ROUND_HALF_UP)*100;
	$dominion['defender']['display']['Walls'] = $dominion['defender']['dp']['mods']['Walls']*100;
}

# Calculate visualisations/ratios
$dominion['attacker']['display']['Gryphon Nest'] = round(($dominion['attacker']['buildings']['Gryphon Nest'] / $dominion['attacker']['general']['land']),4,PHP_ROUND_HALF_UP)*100;

## Assume best DP spell is on.

if($_POST['origin'] == 'defender-mods')
{
	$dominion['defender']['dp']['mods']['spell']['name'] = $_POST['spell'];
	$dominion['defender']['dp']['mods']['spell']['dp'] += $scribes['spells'][$dominion['defender']['dp']['mods']['spell']['name']]['dp'];
}
else
{
	if($dominion['defender']['general']['race'] === 'Icekin')
	{
		$dominion['defender']['dp']['mods']['spell']['dp'] = $scribes['spells']['Blizzard']['dp'];
		$dominion['defender']['dp']['mods']['spell']['name'] = $scribes['spells']['Blizzard']['name'];
	}
	elseif($dominion['defender']['general']['race'] === 'Halfling')
	{
		$dominion['defender']['dp']['mods']['spell']['dp'] = $scribes['spells']['Defensive Frenzy']['dp'];
		$dominion['defender']['dp']['mods']['spell']['name'] = $scribes['spells']['Defensive Frenzy']['name'];
	}
	elseif($dominion['defender']['general']['race'] === 'Kobold')
	{
		$dominion['defender']['dp']['mods']['spell']['dp'] = $scribes['spells']['Howling']['dp'];
		$dominion['defender']['dp']['mods']['spell']['name'] = $scribes['spells']['Howling']['name'];
	}
	else
	{
		$dominion['defender']['dp']['mods']['spell']['dp'] = $scribes['spells']['Ares Call']['dp'];
		$dominion['defender']['dp']['mods']['spell']['name'] = $scribes['spells']['Ares Call']['name'];
	}
}

# Sum up the modifier.
$dominion['defender']['dp']['modifier'] += $dominion['defender']['dp']['mods']['Guard Tower'];
$dominion['defender']['dp']['modifier'] += $dominion['defender']['dp']['mods']['Walls'];
$dominion['defender']['dp']['modifier'] += $dominion['defender']['dp']['mods']['Racial'];
$dominion['defender']['dp']['modifier'] += $dominion['defender']['dp']['mods']['spell']['dp'];

# Apply morale
$dominion['defender']['dp']['modifier'] = $dominion['defender']['dp']['modifier'];# * clamp((0.9 + ($dominion['defender']['general']['morale'] / 100)), 0.9, 1.0);

# Calculate mod DP
$dominion['defender']['dp']['mod'] = $dominion['defender']['dp']['raw'] * (1 + $dominion['defender']['dp']['modifier']);

# Calculate net DP
$dominion['defender']['dp']['net'] = $dominion['defender']['dp']['raw'] * (1 + max($dominion['defender']['dp']['modifier'] - $attacker['temples']['reduction'],0));


/*
echo '<pre>';
#echo "clearsight\n";
#print_r($clearsight);
echo '</pre>';

echo '<pre>';
#echo "survey\n";
#print_r($survey);
echo '</pre>';

echo '<pre>';
#echo "castle\n";
#print_r($castle);
echo '</pre>';


echo '<pre>';
echo "dominion\n";
print_r($dominion);
echo '</pre>';


echo '<pre>';
#echo "scribes\n";
#print_r($scribes);
echo '</pre>';
*/