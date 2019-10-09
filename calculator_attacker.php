<?php

$dominion['attacker']['general'] = array();
$dominion['attacker']['military'] = array();
$dominion['attacker']['buildings'] = array();
$dominion['attacker']['castle'] = array();
$dominion['attacker']['land'] = array();

$dominion['attacker']['ops']['clearsight'] = $_POST['attacker_clearsight'];
$dominion['attacker']['ops']['survey'] = $_POST['attacker_survey'];
$dominion['attacker']['ops']['castle'] = $_POST['attacker_castle'];
$dominion['attacker']['ops']['barracks_returning'] = $_POST['attacker_barracks_returning'];
$dominion['attacker']['ops']['barracks_home'] = $_POST['attacker_barracks_home'];
$dominion['attacker']['ops']['land'] = $_POST['attacker_land'];

// Parse Op Center
if(isset($_POST['attacker_op_center']))
{
	$oc = parse_op_center($_POST['attacker_op_center']);

	$dominion['attacker']['general']['name'] = $oc['clearsight']['dominion'];
	$dominion['attacker']['general']['ruler'] = $oc['clearsight']['ruler'];
	$dominion['attacker']['general']['race'] =  ucwords($oc['clearsight']['race']);
	$dominion['attacker']['general']['land'] = $oc['clearsight']['land'];
	$dominion['attacker']['general']['peasants'] = $oc['clearsight']['peasants'];
	$dominion['attacker']['general']['prestige'] = $oc['clearsight']['prestige'];
	$dominion['attacker']['general']['morale'] = $oc['clearsight']['morale'];

	# Prepare the military.
	$dominion['attacker']['military'] = $scribes['military'][$dominion['defender']['general']['race']];

	$dominion['attacker']['military']['unit1']['returning'] = $oc['military']['unit1']['returning'];
	$dominion['attacker']['military']['unit2']['returning'] = $oc['military']['unit2']['returning'];
	$dominion['attacker']['military']['unit3']['returning'] = $oc['military']['unit3']['returning'];
	$dominion['attacker']['military']['unit4']['returning'] = $oc['military']['unit4']['returning'];

	$dominion['attacker']['military']['unit1']['home'] = $oc['military']['unit1']['home'];
	$dominion['attacker']['military']['unit2']['home'] = $oc['military']['unit2']['home'];
	$dominion['attacker']['military']['unit3']['home'] = $oc['military']['unit3']['home'];
	$dominion['attacker']['military']['unit4']['home'] = $oc['military']['unit4']['home'];

	$dominion['attacker']['military']['unit1']['training'] = $oc['military']['unit1']['training'];
	$dominion['attacker']['military']['unit2']['training'] = $oc['military']['unit2']['training'];
	$dominion['attacker']['military']['unit3']['training'] = $oc['military']['unit3']['training'];
	$dominion['attacker']['military']['unit4']['training'] = $oc['military']['unit4']['training'];

	$dominion['attacker']['military']['draftees']['trained'] = 	$oc['clearsight']['draftees'];
	$dominion['attacker']['military']['unit1']['trained'] = $oc['military']['unit1']['trained'];
	$dominion['attacker']['military']['unit2']['trained'] = $oc['military']['unit2']['trained'];
	$dominion['attacker']['military']['unit3']['trained'] = $oc['military']['unit3']['trained'];
	$dominion['attacker']['military']['unit4']['trained'] = $oc['military']['unit4']['trained'];

	$dominion['attacker']['military']['unit1']['training'] = $oc['military']['unit1']['training'];
	$dominion['attacker']['military']['unit2']['training'] = $oc['military']['unit2']['training'];
	$dominion['attacker']['military']['unit3']['training'] = $oc['military']['unit3']['training'];
	$dominion['attacker']['military']['unit4']['training'] = $oc['military']['unit4']['training'];



	$dominion['attacker']['buildings'] = $oc['buildings'];
	$dominion['attacker']['castle'] = $oc['castle'];

}
else
{
	// Clear Sight for ATTACKER
	$attacker_clearsight = parse_clear_sight($_POST['attacker_clearsight']);

	if(isset($attacker_clearsight['race']))
	{
		$dominion['attacker']['clearsight_info'] = TRUE;
	}

	$dominion['attacker']['general']['name'] = $attacker_clearsight['name'];
	$dominion['attacker']['general']['ruler'] = $attacker_clearsight['ruler'];
	$dominion['attacker']['general']['race'] =  $attacker_clearsight['race'];
	$dominion['attacker']['general']['land'] = $attacker_clearsight['land'];
	$dominion['attacker']['general']['peasants'] = $attacker_clearsight['peasants'];
	$dominion['attacker']['general']['prestige'] = $attacker_clearsight['prestige'];
	$dominion['attacker']['general']['morale'] = $attacker_clearsight['morale'];

	# Prepare the military.
	$dominion['attacker']['military'] = $scribes['military'][$dominion['attacker']['general']['race']];

	// Survey
	$dominion['attacker']['buildings'] = parse_survey($_POST['attacker_survey'], $dominion['attacker']['general']['land']);
	#echo '<pre>'; print_r($dominion['attacker']['buildings']);echo '</pre>';

	// Castle
	$dominion['attacker']['castle'] = parse_castle($_POST['attacker_castle']);

	// Barracks (returning) – useless?
	$attacker_barracks = parse_barracks($_POST['attacker_barracks_returning'], $_POST['attacker_barracks_home'], $dominion['attacker']['general']['race']);

	$dominion['attacker']['military']['unit1']['returning'] = $attacker_barracks['unit1']['returning'];
	$dominion['attacker']['military']['unit2']['returning'] = $attacker_barracks['unit2']['returning'];
	$dominion['attacker']['military']['unit3']['returning'] = $attacker_barracks['unit3']['returning'];
	$dominion['attacker']['military']['unit4']['returning'] = $attacker_barracks['unit4']['returning'];

	$dominion['attacker']['military']['unit1']['home'] = $attacker_barracks['unit1']['home'];
	$dominion['attacker']['military']['unit2']['home'] = $attacker_barracks['unit2']['home'];
	$dominion['attacker']['military']['unit3']['home'] = $attacker_barracks['unit3']['home'];
	$dominion['attacker']['military']['unit4']['home'] = $attacker_barracks['unit4']['home'];

	$dominion['attacker']['military']['unit1']['training'] = $attacker_barracks['unit1']['training'];
	$dominion['attacker']['military']['unit2']['training'] = $attacker_barracks['unit2']['training'];
	$dominion['attacker']['military']['unit3']['training'] = $attacker_barracks['unit3']['training'];
	$dominion['attacker']['military']['unit4']['training'] = $attacker_barracks['unit4']['training'];

	# Do we have BS info?

	if(
		$dominion['attacker']['military']['unit1']['home'] > 0
		OR $dominion['attacker']['military']['unit2']['home'] > 0
		OR $dominion['attacker']['military']['unit3']['home'] > 0
		OR $dominion['attacker']['military']['unit4']['home'] > 0
		OR $dominion['attacker']['military']['unit4']['returning'] > 0
		OR $dominion['attacker']['military']['unit4']['returning'] > 0
		OR $dominion['attacker']['military']['unit4']['returning'] > 0
		OR $dominion['attacker']['military']['unit4']['returning'] > 0

		OR $_POST['attacker_military_unit1_returning'] > 0
		OR $_POST['attacker_military_unit2_returning'] > 0
		OR $_POST['attacker_military_unit3_returning'] > 0
		OR $_POST['attacker_military_unit4_returning'] > 0
		OR $_POST['attacker_military_unit1_at_home'] > 0
		OR $_POST['attacker_military_unit2_at_home'] > 0
		OR $_POST['attacker_military_unit3_at_home'] > 0
		OR $_POST['attacker_military_unit4_at_home'] > 0

		)
	{
		$dominion['attacker']['barracks_info'] = TRUE;
	}
}

# Used in unit special abilities, and land gain calc
$land_ratio = $dominion['defender']['general']['land'] / $dominion['attacker']['general']['land'];

if($_POST['origin'] == 'output')
{
	$dominion['attacker']['military']['unit1']['trained'] = $_POST['attacker_military_unit1_trained'];
	$dominion['attacker']['military']['unit2']['trained'] = $_POST['attacker_military_unit2_trained'];
	$dominion['attacker']['military']['unit3']['trained'] = $_POST['attacker_military_unit3_trained'];
	$dominion['attacker']['military']['unit4']['trained'] = $_POST['attacker_military_unit4_trained'];

	$dominion['attacker']['military']['unit1']['returning'] = $_POST['attacker_military_unit1_returning'];
	$dominion['attacker']['military']['unit2']['returning'] = $_POST['attacker_military_unit2_returning'];
	$dominion['attacker']['military']['unit3']['returning'] = $_POST['attacker_military_unit3_returning'];
	$dominion['attacker']['military']['unit4']['returning'] = $_POST['attacker_military_unit4_returning'];

	$dominion['attacker']['military']['unit1']['home'] = $_POST['attacker_military_unit1_at_home'];
	$dominion['attacker']['military']['unit2']['home'] = $_POST['attacker_military_unit2_at_home'];
	$dominion['attacker']['military']['unit3']['home'] = $_POST['attacker_military_unit3_at_home'];
	$dominion['attacker']['military']['unit4']['home'] = $_POST['attacker_military_unit4_at_home'];
}


# Overwrite race and land if set manually.
if($_POST['origin'] == 'output')
{
	if(isset($_POST['attacker_race']))
	{
		$dominion['attacker']['general']['race'] = $_POST['attacker_race'];
	}

	if(is_numeric($_POST['attacker_land']) and $_POST['attacker_land'] > 0)
	{
		$dominion['attacker']['general']['land'] = $_POST['attacker_land'];
	}
}

# Calculate raw op
$dominion['attacker']['op']['raw'] = 0;

if($dominion['attacker']['military']['unit3']['special'] == 1 OR $dominion['attacker']['military']['unit4']['special'] == 1)
{

	$dominion['attacker']['military']['has_special'] = TRUE;

	if($dominion['attacker']['general']['race'] == 'Dark Elf')
	{
		# Increase Adept DP/OP based on WG
		if(isset($_POST['attacker_special_wizard_guilds']))
		{
			$dominion['attacker']['buildings']['Wizard Guild'] = $_POST['attacker_special_wizard_guilds'];
		}
		$dominion['attacker']['military']['unit3']['op'] += MIN(5,1 * 10 * ($dominion['attacker']['buildings']['Wizard Guild'] / $dominion['attacker']['general']['land']));

		# Increase SW based on target land.
		if($land_ratio < 0.75)
		{
			$dominion['attacker']['military']['unit4']['op'] += 0;
		}
		elseif($land_ratio < 0.95)
		{
			$dominion['attacker']['military']['unit4']['op'] += 0.5;
		}
		elseif($land_ratio >= 0.95)
		{
			$dominion['attacker']['military']['unit4']['op'] += 1;
		}
	}
	elseif($dominion['attacker']['general']['race'] == 'Gnome')
	{
		# Increase Juggernaut based on target land.
		if($land_ratio < 0.75)
		{
			$dominion['attacker']['military']['unit4']['op'] += 0;
		}
		elseif($land_ratio >= 0.75 and $land_ratio < 0.80)
		{
			$dominion['attacker']['military']['unit4']['op'] += 1;
		}
		elseif($land_ratio >= 0.80 and $land_ratio < 0.85)
		{
			$dominion['attacker']['military']['unit4']['op'] += 1.5;
		}
		elseif($land_ratio >= 0.85 and $land_ratio < 0.90)
		{
			$dominion['attacker']['military']['unit4']['op'] += 2;
		}
		elseif($land_ratio >= 0.90)
		{
			$dominion['attacker']['military']['unit4']['op'] += 2.5;
		}
		/*
		elseif($land_ratio < 0.85)
		{
			$dominion['attacker']['military']['unit4']['op'] += 1;
		}
		elseif($land_ratio >= 0.85)
		{
			$dominion['attacker']['military']['unit4']['op'] += 2;
		}
		*/
	}
	elseif($dominion['attacker']['general']['race'] == 'Troll')
	{

		# +1 OP on Smashers and Bashers against Goblin and Kobold
		if($dominion['defender']['general']['race'] == 'Goblin' OR $dominion['defender']['general']['race'] == 'Kobold')
		{
			$dominion['attacker']['military']['unit3']['op'] += 1;
			$dominion['attacker']['military']['unit4']['op'] += 1;
		}

		# +1 OP on Bashers against Wood Elf
		if($dominion['defender']['general']['race'] == 'Wood Elf')
		{
			$dominion['attacker']['military']['unit3']['op'] += 1;
		}

	}
	elseif($dominion['attacker']['general']['race'] == 'Icekin')
	{

		# Increase Ice Elemental OP based on WPA

		# Grab AM from Status Screen if we have that.
		if(($dominion['attacker']['military']['Wizards']['trained'] + $dominion['attacker']['military']['ArchMages']['trained']) > 0)
		{
			$dominion['attacker']['general']['WPA'] = round(($dominion['attacker']['military']['Wizards']['trained'] + $dominion['attacker']['military']['ArchMages']['trained'] * 2)  / $dominion['attacker']['general']['land'],3);
		}
		elseif(isset($_POST['attacker_special_wpa']))
		{
			$dominion['attacker']['general']['WPA'] = $_POST['attacker_special_wpa'];
		}
		else
		{
			$dominion['attacker']['general']['WPA'] = 3.53;
		}


		$dominion['attacker']['military']['unit4']['op'] += MIN(3,0.85 * $dominion['attacker']['general']['WPA']);

	}
	elseif($dominion['attacker']['general']['race'] == 'Wood Elf')
	{
		# Increase Mystic and Druid DP based on Forest
		if(isset($_POST['attacker_special_forest']))
		{
			$dominion['attacker']['land']['Forest'] = $_POST['attacker_special_forest'];
		}
		else
		{
			$dominion['attacker']['land']['Forest'] = $dominion['attacker']['buildings']['Home'];
			$dominion['attacker']['land']['Forest'] += $dominion['attacker']['buildings']['Lumberyard'];
			$dominion['attacker']['land']['Forest'] += $dominion['attacker']['buildings']['Forest Haven'];
			$dominion['attacker']['land']['Forest'] += $dominion['attacker']['buildings']['Barren'];
			$dominion['attacker']['land']['Forest'] += $dominion['attacker']['buildings']['Under Construction'];
		}
		$dominion['attacker']['military']['unit4']['op'] += 5 * ($dominion['attacker']['land']['Forest'] / $dominion['attacker']['general']['land']);

	}
	elseif($dominion['attacker']['general']['race'] == 'Orc')
	{

		# Lower Bone Breaker OP based on target/defender Guard Towers
		$dominion['attacker']['military']['unit4']['op'] -= MIN(2,1 * 10 * ($dominion['defender']['buildings']['Guard Tower'] / $dominion['defender']['general']['land']));

	}
	else
	{
		// Race does not have special ability for OP.
	}

}

# Calculate available op

## Calculate available.
if($dominion['attacker']['barracks_info'] == TRUE and $dominion['attacker']['clearsight_info'] !== TRUE)
{

	$dominion['attacker']['military']['unit1']['available'] = min(($dominion['attacker']['military']['unit1']['trained'] - $dominion['attacker']['military']['unit1']['returning'] * 0.85), ($dominion['attacker']['military']['unit1']['home'] / 0.85));
	$dominion['attacker']['military']['unit2']['available'] = min(($dominion['attacker']['military']['unit2']['trained'] - $dominion['attacker']['military']['unit2']['returning'] * 0.85), ($dominion['attacker']['military']['unit2']['home'] / 0.85));
	$dominion['attacker']['military']['unit3']['available'] = min(($dominion['attacker']['military']['unit3']['trained'] - $dominion['attacker']['military']['unit3']['returning'] * 0.85), ($dominion['attacker']['military']['unit3']['home'] / 0.85));
	$dominion['attacker']['military']['unit4']['available'] = min(($dominion['attacker']['military']['unit4']['trained'] - $dominion['attacker']['military']['unit4']['returning'] * 0.85), ($dominion['attacker']['military']['unit4']['home'] / 0.85));

	$dominion['attacker']['military']['unit1']['available_op'] = $dominion['attacker']['military']['unit1']['available'] * $dominion['attacker']['military']['unit1']['op'];
	$dominion['attacker']['military']['unit2']['available_op'] = $dominion['attacker']['military']['unit1']['available'] * $dominion['attacker']['military']['unit2']['op'];
	$dominion['attacker']['military']['unit3']['available_op'] = $dominion['attacker']['military']['unit1']['available'] * $dominion['attacker']['military']['unit3']['op'];
	$dominion['attacker']['military']['unit4']['available_op'] = $dominion['attacker']['military']['unit1']['available'] * $dominion['attacker']['military']['unit4']['op'];
}
else
{
	## Units 1-4
	$dominion['attacker']['military']['unit1']['available'] = $dominion['attacker']['military']['unit1']['trained'];
	$dominion['attacker']['military']['unit2']['available'] = $dominion['attacker']['military']['unit2']['trained'];
	$dominion['attacker']['military']['unit3']['available'] = $dominion['attacker']['military']['unit3']['trained'];
	$dominion['attacker']['military']['unit4']['available'] = $dominion['attacker']['military']['unit4']['trained'];

	if($_POST['origin'] === 'ops' or $_POST['origin'] === 'defender-ops' or $_POST['origin'] === 'attacker-ops')
	{
		$dominion['attacker']['military']['unit1']['available'] += $dominion['attacker']['military']['unit1']['training'];
		$dominion['attacker']['military']['unit2']['available'] += $dominion['attacker']['military']['unit2']['training'];
		$dominion['attacker']['military']['unit3']['available'] += $dominion['attacker']['military']['unit3']['training'];
		$dominion['attacker']['military']['unit4']['available'] += $dominion['attacker']['military']['unit4']['training'];
	}

	$dominion['attacker']['military']['unit1']['available_op'] = $dominion['attacker']['military']['unit1']['available'] * $dominion['attacker']['military']['unit1']['op'];
	$dominion['attacker']['military']['unit2']['available_op'] = $dominion['attacker']['military']['unit2']['available'] * $dominion['attacker']['military']['unit2']['op'];
	$dominion['attacker']['military']['unit3']['available_op'] = $dominion['attacker']['military']['unit3']['available'] * $dominion['attacker']['military']['unit3']['op'];
	$dominion['attacker']['military']['unit4']['available_op'] = $dominion['attacker']['military']['unit4']['available'] * $dominion['attacker']['military']['unit4']['op'];
}

# Sum up raw op to create Raw op in $dominion['attacker']['op']['raw']
$dominion['attacker']['op']['raw'] += $dominion['attacker']['military']['unit1']['available_op'];
$dominion['attacker']['op']['raw'] += $dominion['attacker']['military']['unit2']['available_op'];
$dominion['attacker']['op']['raw'] += $dominion['attacker']['military']['unit3']['available_op'];
$dominion['attacker']['op']['raw'] += $dominion['attacker']['military']['unit4']['available_op'];

# Calculate the op mods to create the op modifier
$dominion['attacker']['op']['modifier'] = 0.00;

## Is user submitting manual GT/Forges/Racial figures?
if($_POST['origin'] == 'output')
{
	$dominion['attacker']['op']['mods']['Forges'] = $_POST['attacker_mods_castle_forges'] / 100;
	$dominion['attacker']['op']['mods']['Racial'] = $_POST['attacker_mods_racial'] / 100;

	# Set GN and Temple counts
	$dominion['attacker']['buildings']['Gryphon Nest'] = $_POST['attacker_mods_gryphon_nest'];
	$dominion['attacker']['buildings']['Temple'] = $_POST['attacker_mods_temple'];

#	$dominion['attacker']['op']['mods']['Gryphon Nest'] = min(round($dominion['attacker']['buildings']['Gryphon Nest'] / $dominion['attacker']['general']['land'],4,PHP_ROUND_HALF_UP) * GRYPHON_NEST_MULTIPLIER, GRYPHON_NEST_MAX);
#	$dominion['attacker']['op']['mods']['Temple'] = min(round($dominion['attacker']['buildings']['Temple'] / $dominion['attacker']['general']['land'],4,PHP_ROUND_HALF_UP) * TEMPLE_MULTIPLIER, TEMPLE_MAX);

	$dominion['attacker']['op']['mods']['Gryphon Nest'] = min((($dominion['attacker']['buildings']['Gryphon Nest'] / $dominion['attacker']['general']['land']) * GRYPHON_NEST_MULTIPLIER), GRYPHON_NEST_MAX);
	$dominion['attacker']['op']['mods']['Temple'] = min((($dominion['attacker']['buildings']['Temple'] / $dominion['attacker']['general']['land']) * TEMPLE_MULTIPLIER), TEMPLE_MAX);


	$dominion['attacker']['general']['prestige'] = $_POST['attacker_mods_prestige'];
	$dominion['attacker']['general']['morale'] = intval($_POST['attacker_mods_morale']);

	$dominion['attacker']['display']['Forges'] = $_POST['attacker_mods_castle_forges'];
	$dominion['attacker']['display']['Racial'] = $_POST['attacker_mods_racial'];
}
else
{
	$dominion['attacker']['op']['mods']['Forges'] = $dominion['attacker']['castle']['Forges'];
	$dominion['attacker']['op']['mods']['Racial'] = $scribes['races'][$dominion['attacker']['general']['race']]['op'];

	$dominion['attacker']['op']['mods']['Gryphon Nest'] = min((($dominion['attacker']['buildings']['Gryphon Nest'] / $dominion['attacker']['general']['land']) * GRYPHON_NEST_MULTIPLIER), GRYPHON_NEST_MAX);
	$dominion['attacker']['op']['mods']['Temple'] = min((($dominion['attacker']['buildings']['Temple'] / $dominion['attacker']['general']['land']) * TEMPLE_MULTIPLIER), TEMPLE_MAX);

	$dominion['attacker']['display']['Forges'] = $dominion['attacker']['castle']['Forges'] * 100;
	$dominion['attacker']['display']['Racial'] = $dominion['attacker']['op']['mods']['Racial'] * 100;
}

## Assume OP spell is on.
if($_POST['origin'] == 'output')
{
	$dominion['attacker']['op']['mods']['spell']['name'] = $_POST['attacker_spell'];
	$dominion['attacker']['op']['mods']['spell']['op'] += $scribes['spells'][$dominion['attacker']['op']['mods']['spell']['name']]['op'];
}
else
{
	if($dominion['attacker']['general']['race'] === 'Human')
	{
		$dominion['attacker']['op']['mods']['spell']['op'] = $scribes['spells']['Crusade']['op'];
		$dominion['attacker']['op']['mods']['spell']['name'] = $scribes['spells']['Crusade']['name'];
	}
	elseif($dominion['attacker']['general']['race'] === 'Nomad')
	{
		$dominion['attacker']['op']['mods']['spell']['op'] = $scribes['spells']['Crusade']['op'];
		$dominion['attacker']['op']['mods']['spell']['name'] = $scribes['spells']['Crusade']['name'];
	}
	elseif($dominion['attacker']['general']['race'] === 'Kobold')
	{
		$dominion['attacker']['op']['mods']['spell']['op'] = $scribes['spells']['Howling']['op'];
		$dominion['attacker']['op']['mods']['spell']['name'] = $scribes['spells']['Howling']['name'];
	}
	elseif($dominion['attacker']['general']['race'] === 'Goblin')
	{
		$dominion['attacker']['op']['mods']['spell']['op'] = $scribes['spells']['Killing Rage']['op'];
		$dominion['attacker']['op']['mods']['spell']['name'] = $scribes['spells']['Killing Rage']['name'];
	}
	elseif($dominion['attacker']['general']['race'] === 'Sylvan')
	{
		$dominion['attacker']['op']['mods']['spell']['op'] = $scribes['spells']['Warsong']['op'];
		$dominion['attacker']['op']['mods']['spell']['name'] = $scribes['spells']['Warsong']['name'];
	}
	elseif($dominion['attacker']['general']['race'] === 'Orc')
	{
		$dominion['attacker']['op']['mods']['spell']['op'] = $scribes['spells']['Bloodrage']['op'];
		$dominion['attacker']['op']['mods']['spell']['name'] = $scribes['spells']['Bloodrage']['name'];
	}
	elseif($dominion['attacker']['general']['race'] === 'Nox')
	{
		$dominion['attacker']['op']['mods']['spell']['op'] = $scribes['spells']['Nightfall']['op'];
		$dominion['attacker']['op']['mods']['spell']['name'] = $scribes['spells']['Nightfall']['name'];
	}
	elseif($dominion['attacker']['general']['race'] === 'Dark Elf')
	{
		$dominion['attacker']['op']['mods']['spell']['name'] = $scribes['spells']['Unholy Ghost']['name'];
	}
}

# Sum up the modifier.
$dominion['attacker']['op']['modifier'] += $dominion['attacker']['op']['mods']['Gryphon Nest'];
$dominion['attacker']['op']['modifier'] += $dominion['attacker']['op']['mods']['Forges'];
$dominion['attacker']['op']['modifier'] += $dominion['attacker']['op']['mods']['Racial'];
$dominion['attacker']['op']['modifier'] += $dominion['attacker']['op']['mods']['spell']['op'];
$dominion['attacker']['op']['modifier'] += $dominion['attacker']['general']['prestige'] / 10000;

# Calculate mod OP
$dominion['attacker']['op']['net'] = $dominion['attacker']['op']['raw'] * (1 + $dominion['attacker']['op']['modifier']);

# Morale debuff
$dominion['attacker']['morale_debuff'] = clamp((0.9 + ($dominion['attacker']['general']['morale'] / 1000)), 0.9, 1.0);

# Apply morale debuff
$dominion['attacker']['op']['net'] = $dominion['attacker']['op']['net'] * $dominion['attacker']['morale_debuff'];
