<?php

function clean_line($line)
{
	$line = str_replace('NYI','',$line);
	$line = str_replace(' PI','',$line);
	$line = trim($line);

	return $line;
}

function clean_value($value, $type)
{

	$value = trim($value);
	$value = str_replace(':','',$value);
	$value = str_replace("\t",'',$value);

	if ($type == 'integer')
	{
		$value = str_replace(',','',$value);
		$value = str_replace(' ','',$value);
		$value = str_replace('~','',$value);
		$value = intval($value);
	}
	elseif ($type == 'float')
	{
		$value = str_replace(',','',$value);
		$value = str_replace(' ','',$value);
		$value = floatval($value);
	}
	elseif($type == 'string')
	{
		$value = str_replace("'",'',$value);
		$value = str_replace(' ','_',$value);
		$value = strtolower($value);
	}
	elseif($type == 'status_screen')
	{
		$value = preg_match('#\((.*?)\)#', $value, $dominion);
		$value = trim($dominion[1]);
	}

	return $value;

}

/*
* Extracts an array from another array.
* The extracted array started and ended
* between $start and $end in the original
* array.
*/
function extract_from_array_between(array $array, string $start, string $end)
{

	$new_array = array();

	foreach($array as $line)
	{
		if($line == $start)
		{
			$go = TRUE;
		}

		if($go)
		{
			$new_array[] = $line;
		}

		if($line == $end)
		{
			break;
		}
	}

	return $new_array;
}

# Should this be an object? What is OOP? help
function parse_op_center($op_center)
{

	$ops = explode("\n",$op_center);
	foreach($ops as $key => $line)
	{
		$ops[$key] = trim($line);
	}


	# Specific lines we're looking for.
	$fields = array(
		'race' => array(
			'name' => 'Race:',
			'type' => 'string'
		),
		'land' => array(
			'name' => 'Land:',
			'type' => 'integer'
		),
		'peasants' => array(
			'name' => 'Peasants:',
			'type' => 'integer'
		),
		'networth' => array(
			'name' => 'Networth:',
			'type' => 'integer'
		),
		'prestige' => array(
			'name' => 'Prestige:',
			'type' => 'integer'
		),
		'boats' => array(
			'name' => 'boats:',
			'type' => 'integer'
		),
		'morale' => array(
			'name' => 'Morale:',
			'type' => 'integer'
		),
		'ruler' => array(
			'name' => 'Ruler:',
			'type' => 'string'
		),
		'draftees' => array(
			'name' => 'Draftees:',
			'type' => 'integer'
		),
		'dominion' => array(
			'name' => 'Status Screen',
			'type' => 'status_screen'
		),
	);

	// CLEAR SIGHT: Look for specific fields.
	foreach($fields as $field => $fieldDetails)
	{
		foreach($ops as $line)
		{
			if(substr($line, 0, strlen($fieldDetails['name'])) == $fieldDetails['name'])
			{
				if($field == 'dominion')
				{
					$fieldValue = $line;
				}
				else
				{
					$fieldValue = explode(':', $line);
					$fieldValue = $fieldValue[1];
				}
				$fieldValue = clean_value($fieldValue, $fieldDetails['type']);
			}

		}
		$data['clearsight'][$field] = $fieldValue;
	}

	// CLEAR SIGHT: Look for military units in CS.
	require('units.php');
	$data['military'] = $scribes['military'][ucfirst($data['clearsight']['race'])];

	foreach($data['military'] as $unit => $unitDetails)
	{
		foreach($ops as $line)
		{
			if(substr($line, 0, strlen($unitDetails['name'].':')) == $unitDetails['name'].':')
			{
					$fieldValue = explode(':', $line);
					$fieldValue = $fieldValue[1];
			}
			$fieldValue = clean_value($fieldValue, 'integer');
		}

		$data['military'][$unit]['trained'] = $fieldValue;
	}

	// REVELATION: Look for spells.
	$spells = array("Ares' Call", 'Defensive Frenzy', 'Blizzard', 'Howling', 'Bloodrage', 'Killing Rage', 'Nightfall', 'Crusade');

	foreach($spells as $spell)
	{
		foreach($ops as $line)
		{
				if(substr($line, 0, strlen($spell)) == $spell)
				{
					$spell = clean_value($spell, 'string');
					$data[$spell] = TRUE;
				}
		}
	}

	// CASTLE: Look for Castle.
	$improvements = array('Science', 'Keep', 'Towers', 'Forges', 'Walls', 'Harbor');

	foreach($improvements as $improvement)
	{
		foreach($ops as $line)
		{
				$line = clean_line($line);
				if(substr($line, 0, strlen($improvement)) == $improvement)
				{
					$improvementValue = explode("\t",$line);
					$improvementValue = $improvementValue[1];
				}
				$improvementValue = clean_value($improvementValue, 'float');
		}

		$data['castle'][$improvement] = $improvementValue/100;
	}

	// BARRACKS SPY: Look for military units in BS home.
	$home = extract_from_array_between($ops, 'Units in training and home', 'Units returning from battle');

	foreach($data['military'] as $unit => $unitDetails)
	{
		foreach($home as $line)
		{
			if(substr($line, 0, strlen($unitDetails['name'])) == $unitDetails['name'])
			{
					$unitValue = explode("\t", $line);
					$unitValueHome = $unitValue[13];
					$unitValueTraining = $unitValue[12];
			}
			$unitValueHome = clean_value($unitValueHome, 'integer');
			$unitValueTraining = clean_value($unitValueTraining, 'integer');
		}

		$data['military'][$unit]['home'] = $unitValueHome;
		$data['military'][$unit]['training'] = $unitValueTraining;
	}

	// BARRACKS SPY: Look for military units BS returning.
	$returning = extract_from_array_between($ops, 'Units returning from battle', 'Constructed Buildings Barren Land');

	foreach($data['military'] as $unit => $unitDetails)
	{
		foreach($returning as $line)
		{
			if(substr($line, 0, strlen($unitDetails['name'])) == $unitDetails['name'])
			{
					$unitValue = explode("\t", $line);
					$unitValue = $unitValue[13];
			}
			$unitValue = clean_value($unitValue, 'integer');
		}

		$data['military'][$unit]['returning'] = $unitValue;
	}

	// SURVEY: Look for barren land
	foreach($ops as $line)
	{
		if(substr($line, 0, strlen('Constructed Buildings Barren Land')) == 'Constructed Buildings Barren Land')
		{
				$barrenValue = explode(':', $line);
				$barrenValue = $barrenValue[1];
		}
		$barrenValue = clean_value($barrenValue, 'integer');

		$data['buildings']['Barren'] = $barrenValue;
	}

	// SURVEY: Look for buildings
	$buildings = array('Home', 'Alchemy', 'Farm', 'Smithy', 'Masonry', 'Ore Mine', 'Gryphon Nest', 'Tower', 'Wizard Guild',
	'Temple', 'Diamond Mine', 'School', 'Lumberyard', 'Forest Haven', 'Factory', 'Guard Tower', 'Shrine', 'Barracks', 'Docks');

	foreach($buildings as $building)
	{
		foreach($ops as $line)
		{
				$line = clean_line($line);
				if(substr($line, 0, strlen($building)) == $building)
				{
					$buildingValue = explode("\t",$line);
					$buildingValue = $buildingValue[1];
				}
				$buildingValue = clean_value($buildingValue, 'integer');

				if ($line == 'Incoming building breakdown')
				{
					break;
				}
		}

		$data['buildings'][$building] = $buildingValue;
	}

	return array_merge($data, $ops);

}
