<?php

$dominion['defender']['general'] = array();
$dominion['defender']['military'] = array();
$dominion['defender']['buildings'] = array();
$dominion['defender']['castle'] = array();
$dominion['defender']['land'] = array();

$dominion['defender']['ops']['clearsight'] = $_POST['defender_clearsight'];
$dominion['defender']['ops']['survey'] = $_POST['defender_survey'];
$dominion['defender']['ops']['castle'] = $_POST['defender_castle'];
$dominion['defender']['ops']['barracks_returning'] = $_POST['defender_barracks_returning'];
$dominion['defender']['ops']['barracks_home'] = $_POST['defender_barracks_home'];
$dominion['defender']['ops']['land'] = $_POST['defender_land'];

// Parse Op Center
$ops = parse_op_center($_POST['defender_op_center']);

#echo '<pre>';print_r($ops); echo '</pre>';

// Clear Sight for DEFENDER
$defender_clearsight = parse_clear_sight($_POST['defender_clearsight']);
#echo '<pre>';print_r($defender_clearsight);echo '</pre>';

if(isset($defender_clearsight['race']))
{
	$dominion['defender']['clearsight_info'] = TRUE;
}

$dominion['defender']['general']['name'] = $defender_clearsight['name'];
$dominion['defender']['general']['ruler'] = $defender_clearsight['ruler'];
$dominion['defender']['general']['race'] =  $defender_clearsight['race'];
$dominion['defender']['general']['land'] = $defender_clearsight['land'];
$dominion['defender']['general']['peasants'] = $defender_clearsight['peasants'];
$dominion['defender']['general']['prestige'] = $defender_clearsight['prestige'];
$dominion['defender']['general']['morale'] = $defender_clearsight['morale'];

# Overwrite race and land if set manually.
if($_POST['origin'] == 'output')
{
	if(isset($_POST['defender_race']))
	{
		$dominion['defender']['general']['race'] = $_POST['defender_race'];
	}

	if(is_numeric($_POST['defender_land']) and $_POST['defender_land'] > 0)
	{
		$dominion['defender']['general']['land'] = $_POST['defender_land'];
	}
}

# Prepare the military.
$dominion['defender']['military'] = $scribes['military'][$dominion['defender']['general']['race']];

// Survey
$dominion['defender']['buildings'] = parse_survey($_POST['defender_survey'], $dominion['defender']['general']['land']);

// Castle
$dominion['defender']['castle'] = parse_castle($_POST['defender_castle']);

// Barracks (returning)
$defender_barracks = parse_barracks($_POST['defender_barracks_returning'], $_POST['defender_barracks_home'], $dominion['defender']['general']['race']);

$dominion['defender']['military']['unit1']['returning'] = $defender_barracks['unit1']['returning'];
$dominion['defender']['military']['unit2']['returning'] = $defender_barracks['unit2']['returning'];
$dominion['defender']['military']['unit3']['returning'] = $defender_barracks['unit3']['returning'];
$dominion['defender']['military']['unit4']['returning'] = $defender_barracks['unit4']['returning'];

$dominion['defender']['military']['unit1']['home'] = $defender_barracks['unit1']['home'];
$dominion['defender']['military']['unit2']['home'] = $defender_barracks['unit2']['home'];
$dominion['defender']['military']['unit3']['home'] = $defender_barracks['unit3']['home'];
$dominion['defender']['military']['unit4']['home'] = $defender_barracks['unit4']['home'];

$dominion['defender']['military']['unit1']['training'] = $defender_barracks['unit1']['training'];
$dominion['defender']['military']['unit2']['training'] = $defender_barracks['unit2']['training'];
$dominion['defender']['military']['unit3']['training'] = $defender_barracks['unit3']['training'];
$dominion['defender']['military']['unit4']['training'] = $defender_barracks['unit4']['training'];

# Do we have BS info?

if(
	$dominion['defender']['military']['unit1']['home'] > 0
	OR $dominion['defender']['military']['unit2']['home'] > 0
	OR $dominion['defender']['military']['unit3']['home'] > 0
	OR $dominion['defender']['military']['unit4']['home'] > 0
	OR $dominion['defender']['military']['unit4']['returning'] > 0
	OR $dominion['defender']['military']['unit4']['returning'] > 0
	OR $dominion['defender']['military']['unit4']['returning'] > 0
	OR $dominion['defender']['military']['unit4']['returning'] > 0

	OR $_POST['defender_military_unit1_returning'] > 0
	OR $_POST['defender_military_unit2_returning'] > 0
	OR $_POST['defender_military_unit3_returning'] > 0
	OR $_POST['defender_military_unit4_returning'] > 0
	OR $_POST['defender_military_unit1_at_home'] > 0
	OR $_POST['defender_military_unit2_at_home'] > 0
	OR $_POST['defender_military_unit3_at_home'] > 0
	OR $_POST['defender_military_unit4_at_home'] > 0

	)
{
	$dominion['defender']['barracks_info'] = TRUE;
}

if($_POST['origin'] == 'output')
{
	$dominion['defender']['military']['draftees']['trained'] = $_POST['defender_military_draftees'];
	$dominion['defender']['military']['unit1']['trained'] = $_POST['defender_military_unit1_trained'];
	$dominion['defender']['military']['unit2']['trained'] = $_POST['defender_military_unit2_trained'];
	$dominion['defender']['military']['unit3']['trained'] = $_POST['defender_military_unit3_trained'];
	$dominion['defender']['military']['unit4']['trained'] = $_POST['defender_military_unit4_trained'];
	$dominion['defender']['buildings']['Forest Haven'] = $_POST['defender_military_forest_havens'];

	$dominion['defender']['military']['unit1']['returning'] = $_POST['defender_military_unit1_returning'];
	$dominion['defender']['military']['unit2']['returning'] = $_POST['defender_military_unit2_returning'];
	$dominion['defender']['military']['unit3']['returning'] = $_POST['defender_military_unit3_returning'];
	$dominion['defender']['military']['unit4']['returning'] = $_POST['defender_military_unit4_returning'];

	$dominion['defender']['military']['draftees']['home'] = $_POST['defender_military_draftees_at_home'];
	$dominion['defender']['military']['unit1']['home'] = $_POST['defender_military_unit1_at_home'];
	$dominion['defender']['military']['unit2']['home'] = $_POST['defender_military_unit2_at_home'];
	$dominion['defender']['military']['unit3']['home'] = $_POST['defender_military_unit3_at_home'];
	$dominion['defender']['military']['unit4']['home'] = $_POST['defender_military_unit4_at_home'];
}
else
{

	$dominion['defender']['military']['draftees']['trained'] = 	$defender_clearsight['draftees']['trained'];
	$dominion['defender']['military']['unit1']['trained'] = $defender_clearsight['unit1']['trained'];
	$dominion['defender']['military']['unit2']['trained'] = $defender_clearsight['unit2']['trained'];
	$dominion['defender']['military']['unit3']['trained'] = $defender_clearsight['unit3']['trained'];
	$dominion['defender']['military']['unit4']['trained'] = $defender_clearsight['unit4']['trained'];
}

# Calculate raw DP
$dominion['defender']['dp']['raw'] = 0.0;

# Calculate available DP

# Start by beefing up raw DP for units with special ability.
if($dominion['defender']['military']['unit3']['special'] == 1 OR $dominion['defender']['military']['unit4']['special'] == 1)
{

	$dominion['defender']['military']['has_special'] = TRUE;

	if($dominion['defender']['general']['race'] == 'Dark Elf')
	{
		# Increase Adept DP/OP based on WG
		if(isset($_POST['defender_special_wizard_guilds']))
		{
			$dominion['defender']['buildings']['Wizard Guild'] = $_POST['defender_special_wizard_guilds'];
		}
		$dominion['defender']['military']['unit3']['dp'] += MIN(5,10 * ($dominion['defender']['buildings']['Wizard Guild'] / $dominion['defender']['general']['land']));
		$dominion['defender']['military']['unit3']['op'] += MIN(5,10 * ($dominion['defender']['buildings']['Wizard Guild'] / $dominion['defender']['general']['land']));

	}
	elseif($dominion['defender']['general']['race'] == 'Icekin')
	{
		# Increase FrostMage DP based on Mountains
		if(isset($_POST['defender_special_mountains']))
		{
			$dominion['defender']['land']['Mountains'] = $_POST['defender_special_mountains'];
		}
		else
		{
			$dominion['defender']['land']['Mountains'] = $dominion['defender']['buildings']['Home'];
			$dominion['defender']['land']['Mountains'] += $dominion['defender']['buildings']['Ore Mine'];
			$dominion['defender']['land']['Mountains'] += $dominion['defender']['buildings']['Gryphon Nest'];
			$dominion['defender']['land']['Mountains'] += $dominion['defender']['buildings']['Barren'];
			$dominion['defender']['land']['Mountains'] += $dominion['defender']['buildings']['Under Construction'];
		}	
		$dominion['defender']['military']['unit3']['dp'] += MIN(3,5 * ($dominion['defender']['land']['Mountains'] / $dominion['defender']['general']['land']));

	}
	elseif($dominion['defender']['general']['race'] == 'Gnome')
	{
		# Increase FrostMage DP based on Mountains
		if(isset($_POST['defender_special_mountains']))
		{
			$dominion['defender']['land']['Mountains'] = $_POST['defender_special_mountains'];
		}
		else
		{
			$dominion['defender']['land']['Mountains'] = $dominion['defender']['buildings']['Home'];
			$dominion['defender']['land']['Mountains'] += $dominion['defender']['buildings']['Ore Mine'];
			$dominion['defender']['land']['Mountains'] += $dominion['defender']['buildings']['Gryphon Nest'];
			$dominion['defender']['land']['Mountains'] += $dominion['defender']['buildings']['Barren'];
			$dominion['defender']['land']['Mountains'] += $dominion['defender']['buildings']['Under Construction'];
		}

		$dominion['defender']['military']['unit3']['dp'] += MIN(2,5 * ($dominion['defender']['land']['Mountains'] / $dominion['defender']['general']['land']));
	}
	elseif($dominion['defender']['general']['race'] == 'Sylvan')
	{
		# Increase Dryad DP based on Forest
		if(isset($_POST['defender_special_forest']))
		{
			$dominion['defender']['land']['Forest'] = $_POST['defender_special_forest'];
		}
		else
		{
			$dominion['defender']['land']['Forest'] = $dominion['defender']['buildings']['Home'];
			$dominion['defender']['land']['Forest'] += $dominion['defender']['buildings']['Lumberyard'];
			$dominion['defender']['land']['Forest'] += $dominion['defender']['buildings']['Forest Haven'];
			$dominion['defender']['land']['Forest'] += $dominion['defender']['buildings']['Barren'];
			$dominion['defender']['land']['Forest'] += $dominion['defender']['buildings']['Under Construction'];
		}
		$dominion['defender']['military']['unit3']['dp'] += MIN(4,10 * ($dominion['defender']['land']['Forest'] / $dominion['defender']['general']['land']));

	}
	elseif($dominion['defender']['general']['race'] == 'Wood Elf')
	{
		# Increase Mystic and Druid DP based on Forest
		if(isset($_POST['defender_special_forest']))
		{
			$dominion['defender']['land']['Forest'] = $_POST['defender_special_forest'];
		}
		else
		{
			$dominion['defender']['land']['Forest'] = $dominion['defender']['buildings']['Home'];
			$dominion['defender']['land']['Forest'] += $dominion['defender']['buildings']['Lumberyard'];
			$dominion['defender']['land']['Forest'] += $dominion['defender']['buildings']['Forest Haven'];
			$dominion['defender']['land']['Forest'] += $dominion['defender']['buildings']['Barren'];
			$dominion['defender']['land']['Forest'] += $dominion['defender']['buildings']['Under Construction'];
		}
		$dominion['defender']['military']['unit3']['dp'] += 5 * ($dominion['defender']['land']['Forest'] / $dominion['defender']['general']['land']);
		$dominion['defender']['military']['unit4']['dp'] += 5 * ($dominion['defender']['land']['Forest'] / $dominion['defender']['general']['land']);

	}
	elseif($dominion['defender']['general']['race'] == 'Nox')
	{
		# Increase Nightshade DP based on Swamp buildings (actually uses Swamp land because it includes Barren and Under Construction)
		if(isset($_POST['defender_special_swamp_buildings']))
		{
			$dominion['defender']['land']['Swamp']['buildings'] = $_POST['defender_special_swamp_buildings'];
		}
		else
		{
			$dominion['defender']['land']['Swamp']['buildings'] = $dominion['defender']['buildings']['Home'];
			$dominion['defender']['land']['Swamp']['buildings'] += $dominion['defender']['buildings']['Tower'];
			$dominion['defender']['land']['Swamp']['buildings'] += $dominion['defender']['buildings']['Temple'];
			$dominion['defender']['land']['Swamp']['buildings'] += $dominion['defender']['buildings']['Wizard Guild'];
			$dominion['defender']['land']['Swamp']['buildings'] += $dominion['defender']['buildings']['Barren'];
			$dominion['defender']['land']['Swamp']['buildings'] += $dominion['defender']['buildings']['Under Construction'];
		}
		$dominion['defender']['military']['unit3']['dp'] += MIN(4,10 * ($dominion['defender']['land']['Swamp']['buildings'] / $dominion['defender']['general']['land']));

	}
	elseif($dominion['defender']['general']['race'] == 'Orc')
	{
		# Increase Voodoo Magi DP based on Prestige
		if(isset($_POST['defender_special_prestige']))
		{
			$dominion['defender']['general']['prestige'] = $_POST['defender_special_prestige'];
		}

		$dominion['defender']['military']['unit3']['dp'] += min(2, $dominion['defender']['general']['prestige'] / 600);

	}
	elseif($dominion['defender']['general']['race'] == 'Troll')
	{
		# Increase Voodoo Magi DP based on Prestige
		if($dominion['attacker']['general']['race'] == 'Goblin' OR $dominion['attacker']['general']['race'] == 'Kobold' OR $dominion['attacker']['general']['race'] == 'Wood Elf')
		{
			$dominion['defender']['military']['unit3']['dp'] += 1;
		}
		elseif($_POST['attacker_race'] == 'Goblin' OR $_POST['attacker_race'] == 'Kobold' OR $_POST['attacker_race'] == 'Wood Elf')
		{
			$dominion['defender']['military']['unit3']['dp'] += 1;
		}

	}
}

## Calculate available.
if($dominion['defender']['barracks_info'] == TRUE)
{

#	echo 'We have barracks.';

	# We don't have CS...
#	if($dominion['defender']['clearsight_info'] !== TRUE)
#	{

#		$dominion['defender']['military']['draftees']['available'] = $dominion['defender']['military']['draftees']['home'] / 0.85;
#		$dominion['defender']['military']['unit1']['available'] = $dominion['defender']['military']['unit1']['home'] / 0.85;
#		$dominion['defender']['military']['unit2']['available'] = $dominion['defender']['military']['unit2']['home'] / 0.85;
#		$dominion['defender']['military']['unit3']['available'] = $dominion['defender']['military']['unit3']['home'] / 0.85;
#		$dominion['defender']['military']['unit4']['available'] = $dominion['defender']['military']['unit4']['home'] / 0.85;
#	}
	# We do have CS...
#	else
#	{
		$dominion['defender']['military']['draftees']['available'] = $dominion['defender']['military']['draftees']['trained'];
		$dominion['defender']['military']['unit1']['available'] = min($dominion['defender']['military']['unit1']['trained'] - $dominion['defender']['military']['unit1']['returning'] * 0.85, $dominion['defender']['military']['unit1']['home'] / 0.85);
		$dominion['defender']['military']['unit2']['available'] = min($dominion['defender']['military']['unit2']['trained'] - $dominion['defender']['military']['unit2']['returning'] * 0.85, $dominion['defender']['military']['unit2']['home'] / 0.85);
		$dominion['defender']['military']['unit3']['available'] = min($dominion['defender']['military']['unit3']['trained'] - $dominion['defender']['military']['unit3']['returning'] * 0.85, $dominion['defender']['military']['unit3']['home'] / 0.85);
		$dominion['defender']['military']['unit4']['available'] = min($dominion['defender']['military']['unit4']['trained'] - $dominion['defender']['military']['unit4']['returning'] * 0.85, $dominion['defender']['military']['unit4']['home'] / 0.85);
#	}
	
	# Calculate the raw DP
	$dominion['defender']['draftee_dp'] = $dominion['defender']['military']['draftees']['available'] * 1;
	$dominion['defender']['military']['unit1']['available_dp'] = $dominion['defender']['military']['unit1']['available'] * $dominion['defender']['military']['unit1']['dp'];
	$dominion['defender']['military']['unit2']['available_dp'] = $dominion['defender']['military']['unit2']['available'] * $dominion['defender']['military']['unit2']['dp'];
	$dominion['defender']['military']['unit3']['available_dp'] = $dominion['defender']['military']['unit3']['available'] * $dominion['defender']['military']['unit3']['dp'];
	$dominion['defender']['military']['unit4']['available_dp'] = $dominion['defender']['military']['unit4']['available'] * $dominion['defender']['military']['unit4']['dp'];
}
else
{

	# See if Returning and At Home are manually set.
	# UNCLEAR IF USED?
	if($_POST['defender_military_unit1_returning'] > 0 OR $_POST['defender_military_unit1_at_home'] > 0)
	{

		$dominion['defender']['military']['unit1']['available'] = max(0,min(($dominion['defender']['military']['unit1']['trained'] - $dominion['defender']['military']['unit1']['returning'] * 0.85), ($dominion['defender']['military']['unit1']['home'] / 0.85)));
		$dominion['defender']['military']['unit2']['available'] = max(0,min(($dominion['defender']['military']['unit2']['trained'] - $dominion['defender']['military']['unit2']['returning'] * 0.85), ($dominion['defender']['military']['unit2']['home'] / 0.85)));
		$dominion['defender']['military']['unit3']['available'] = max(0,min(($dominion['defender']['military']['unit3']['trained'] - $dominion['defender']['military']['unit3']['returning'] * 0.85), ($dominion['defender']['military']['unit3']['home'] / 0.85)));
		$dominion['defender']['military']['unit4']['available'] = max(0,min(($dominion['defender']['military']['unit4']['trained'] - $dominion['defender']['military']['unit4']['returning'] * 0.85), ($dominion['defender']['military']['unit4']['home'] / 0.85)));
	}
	else
	{
		$dominion['defender']['military']['unit1']['available'] = $dominion['defender']['military']['unit1']['trained'];
		$dominion['defender']['military']['unit2']['available'] = $dominion['defender']['military']['unit2']['trained'];
		$dominion['defender']['military']['unit3']['available'] = $dominion['defender']['military']['unit3']['trained'];
		$dominion['defender']['military']['unit4']['available'] = $dominion['defender']['military']['unit4']['trained'];
	}
	# END UNCLEAR IF USED

	## Draftees
	$dominion['defender']['draftee_dp'] = $dominion['defender']['military']['draftees']['trained'] * 1;

	## Units 1-4
	$dominion['defender']['military']['unit1']['available'] = $dominion['defender']['military']['unit1']['trained'];
	$dominion['defender']['military']['unit2']['available'] = $dominion['defender']['military']['unit2']['trained'];
	$dominion['defender']['military']['unit3']['available'] = $dominion['defender']['military']['unit3']['trained'];
	$dominion['defender']['military']['unit4']['available'] = $dominion['defender']['military']['unit4']['trained'];

	$dominion['defender']['military']['unit1']['available_dp'] = $dominion['defender']['military']['unit1']['available'] * $dominion['defender']['military']['unit1']['dp'];
	$dominion['defender']['military']['unit2']['available_dp'] = $dominion['defender']['military']['unit2']['available'] * $dominion['defender']['military']['unit2']['dp'];
	$dominion['defender']['military']['unit3']['available_dp'] = $dominion['defender']['military']['unit3']['available'] * $dominion['defender']['military']['unit3']['dp'];
	$dominion['defender']['military']['unit4']['available_dp'] = $dominion['defender']['military']['unit4']['available'] * $dominion['defender']['military']['unit4']['dp'];	
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
if($_POST['origin'] == 'output')
{
	$dominion['defender']['dp']['mods']['Walls'] = $_POST['defender_mods_castle_walls'] / 100;
	$dominion['defender']['dp']['mods']['Racial'] = $_POST['defender_mods_racial'] / 100;

	# Set GT count
	$dominion['defender']['buildings']['Guard Tower'] = $_POST['defender_mods_guard_towers'];

	$dominion['defender']['dp']['mods']['Guard Tower'] = min(round($dominion['defender']['buildings']['Guard Tower'] / $dominion['defender']['general']['land'],4,PHP_ROUND_HALF_UP) * GUARD_TOWER_MULTIPLIER, GUARD_TOWER_MAX);

	$dominion['defender']['general']['morale'] = intval($_POST['defender_mods_morale']);

	$dominion['defender']['display']['Walls'] = $_POST['defender_mods_castle_walls'];
	$dominion['defender']['display']['Racial'] = $_POST['defender_mods_racial'];
}
else
{
	$dominion['defender']['dp']['mods']['Walls'] = $dominion['defender']['castle']['Walls'];
	$dominion['defender']['dp']['mods']['Racial'] = $scribes['races'][$dominion['defender']['general']['race']]['dp'];
	$dominion['defender']['dp']['mods']['Guard Tower'] = min(round($dominion['defender']['buildings']['Guard Tower'] / $dominion['defender']['general']['land'],4,PHP_ROUND_HALF_UP) * GUARD_TOWER_MULTIPLIER, GUARD_TOWER_MAX);


	$dominion['defender']['display']['Walls'] = $dominion['defender']['castle']['Walls']*100;
	$dominion['defender']['display']['Racial'] = $dominion['defender']['dp']['mods']['Racial']*100;
}

## Assume best DP spell is on.
if($_POST['origin'] == 'output')
{
	$dominion['defender']['dp']['mods']['spell']['name'] = $_POST['defender_spell'];
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
$dominion['defender']['dp']['modifier'] = $dominion['defender']['dp']['modifier'] * clamp((0.9 + ($dominion['defender']['general']['morale'] / 1000)), 0.9, 1.0);

# Calculate mod DP
#$dominion['defender']['dp']['mod'] = $dominion['defender']['dp']['raw'] * (1 + $dominion['defender']['dp']['modifier']);

# Get OP
require_once('calculator_attacker.php');

# Calculate net mods
if($dominion['attacker']['op']['mods']['Temple'] > 0)
{
	$dominion['defender']['dp']['modifier_net'] = max(($dominion['defender']['dp']['modifier'] - $dominion['attacker']['op']['mods']['Temple']),0);
}
else
{
	$dominion['defender']['dp']['modifier_net'] = $dominion['defender']['dp']['modifier'];
}

# Remove draftees for DE.

if($dominion['attacker']['op']['mods']['spell']['name'] == 'Unholy Ghost')
{
	$dominion['defender']['dp']['raw'] -= $dominion['defender']['draftee_dp'];
}

# Calculate net DP
$dominion['defender']['dp']['net'] = $dominion['defender']['dp']['raw'] * (1 + $dominion['defender']['dp']['modifier_net']);
