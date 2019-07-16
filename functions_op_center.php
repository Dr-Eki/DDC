<?php

/**
 * Clamps $current number between $min and $max.
 *
 * (tfw no generics)
 *
 * @param int|float $attacker
 * @param int|float $defender
 * @return int|float
 */
function parse_op_center($op_center)
{

	$ops['array'] = explode("\n",$op_center);
	foreach($ops['array'] as $key => $line)
	{
		$ops['array'][$key] = trim($line);
	}

#	$ops['op_center'] = $op_center;
#	$ops['clear_sight'] = parse_clear_sight(find_between($op_center,'Status Screen','ArchMages'));
	$ops['survey'] = parse_survey($op_center,$size);
	$ops['castle'] = '';
	$ops['barracks_returning'] = '';
	$ops['barracks_home'] = '';




	return $ops;

}
