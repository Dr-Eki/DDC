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
function calculate_max_sendable($attacker, $defender)
{
	if($attacker == NULL or $defender == NULL or $attacker['general']['race'] !== $defender['general']['race'] or $attacker['general']['size'])
	{
		return FALSE;
	}

	$race = $attacker['general']['race'];
	$max_ratio = 1.25; # 5/4

#	$military = $attacker['military'];

	$method = array(

		# Method 1
		'Human'=>1,
		'Nomad'=>1,
		'Dwarf'=>1,
		'Halfling'=>1,
		'Gnome'=>1,
		'Dark Elf'=>1,
		'Undead'=>1,
		'Spirit'=>1,
		'Lycanthrope'=>1,
		'Lizardfolk'=>1,
		'Icekin'=>1,
		'Goblin'=>1,

		# Method 3
		'Troll'=>3,

		# Method 4
		'Kobold'=>4,

		# Method 5
		'Wood Elf'=>5,
		'Nox'=>5,
		'Orc'=>5,
		'Sylvan'=>5,
		'Merfolk'=>5,
		'Firewalker'=>5
	);

	## METHOD 1
	# Assume all spec OP and elite OP is sent, and then gradually add more elite DP until 5:4 is reached. Applies to Human, Nomad, Dwarf, Halfer, Gnome, DE, SPUD, Lyc, Liz, Icer.
	if($method[$race] == 1)
	{

		$military['method'] = $method[$race];
		$military['iteration'] = 0;

		$attacker['military']['unit1']['attacking'] = $attacker['military']['unit1']['available'];
		$attacker['military']['unit2']['attacking'] = 0; # Won't be changed.
		$attacker['military']['unit3']['attacking'] = 0; 
		$attacker['military']['unit4']['attacking'] = $attacker['military']['unit4']['available'];

		$defender['military']['draftees']['defending'] = $defender['military']['draftees']['trained'];
		$defender['military']['unit1']['defending'] = 0; # Won't be changed.
		$defender['military']['unit2']['defending'] = $defender['military']['unit2']['available'];
		$defender['military']['unit3']['defending'] = $defender['military']['unit3']['available'];
		$defender['military']['unit4']['defending'] = 0;

		# Calculate OP and DP once before we loop.
		$op = $attacker['military']['unit1']['attacking'] * $attacker['military']['unit1']['op'];
		$op += $attacker['military']['unit2']['attacking'] * $attacker['military']['unit2']['op'];
		$op += $attacker['military']['unit3']['attacking'] * $attacker['military']['unit3']['op'];
		$op += $attacker['military']['unit4']['attacking'] * $attacker['military']['unit4']['op'];
		$op *= (1+$attacker['op']['modifier']);

		$dp = $defender['military']['draftees']['defending'];
		$dp += $defender['military']['unit1']['defending'] * $defender['military']['unit1']['dp'];
		$dp += $defender['military']['unit2']['defending'] * $defender['military']['unit2']['dp'];
		$dp += $defender['military']['unit3']['defending'] * $defender['military']['unit3']['dp'];
		$dp += $defender['military']['unit4']['defending'] * $defender['military']['unit4']['dp'];
		$dp *= (1+$defender['dp']['modifier']);

		# See what baseline ratio is.
		$military['initial_ratio'] = $op/$dp;


		if($military['initial_ratio'] < $max_ratio AND $defender['military']['unit3']['available'] == 0)
		{
			$military['sub_method'] = '3';
			$military['iteration']++;

			# Calculate OP and DP once before we loop.
			$op = $attacker['military']['unit1']['attacking'] * $attacker['military']['unit1']['op'];
			$op += $attacker['military']['unit2']['attacking'] * $attacker['military']['unit2']['op'];
			$op += $attacker['military']['unit3']['attacking'] * $attacker['military']['unit3']['op'];
			$op += $attacker['military']['unit4']['attacking'] * $attacker['military']['unit4']['op'];
			$military['op'] = $op * (1+$attacker['op']['modifier']);

			$dp = $defender['military']['draftees']['defending'];
			$dp += $defender['military']['unit1']['defending'] * $defender['military']['unit1']['dp'];
			$dp += $defender['military']['unit2']['defending'] * $defender['military']['unit2']['dp'];
			$dp += $defender['military']['unit3']['defending'] * $defender['military']['unit3']['dp'];
			$dp += $defender['military']['unit4']['defending'] * $defender['military']['unit4']['dp'];
			$military['dp'] = $dp * (1+$defender['dp']['modifier']);

			# Calculate the ratio.
			$military['ratio'] = $military['op']/$military['dp'];
		}
		elseif($military['initial_ratio'] < $max_ratio)
		{
			while($military['ratio'] <= $max_ratio)
			{
				if(($attacker['military']['unit3']['attacking'] == $defender['military']['unit3']['available'] OR $defender['military']['unit3']['defending'] == 0))
				{
					break;
				}

				$military['sub_method'] = '1';
				$military['iteration']++;
				# Add one Unit3 to attacking and remove one Unit3 from defending
				$attacker['military']['unit3']['attacking']++;
				$defender['military']['unit3']['defending']--;

				# Calculate OP and DP once before we loop.
				$op = $attacker['military']['unit1']['attacking'] * $attacker['military']['unit1']['op'];
				$op += $attacker['military']['unit2']['attacking'] * $attacker['military']['unit2']['op'];
				$op += $attacker['military']['unit3']['attacking'] * $attacker['military']['unit3']['op'];
				$op += $attacker['military']['unit4']['attacking'] * $attacker['military']['unit4']['op'];
				$military['op'] = $op * (1+$attacker['op']['modifier']);

				$dp = $defender['military']['draftees']['defending'];
				$dp += $defender['military']['unit1']['defending'] * $defender['military']['unit1']['dp'];
				$dp += $defender['military']['unit2']['defending'] * $defender['military']['unit2']['dp'];
				$dp += $defender['military']['unit3']['defending'] * $defender['military']['unit3']['dp'];
				$dp += $defender['military']['unit4']['defending'] * $defender['military']['unit4']['dp'];
				$military['dp'] = $dp * (1+$defender['dp']['modifier']);

				# Calculate the ratio.
				$military['ratio'] = $military['op']/$military['dp'];

			}
		}
		else
		{
			while($military['ratio'] > $max_ratio)
			{
				if($attacker['military']['unit3']['attacking'] == $defender['military']['unit3']['available'] OR $defender['military']['unit3']['defending'] == 0)
				{
					break;
				}

				$military['sub_method'] = '1';
				$military['iteration']++;

				# Remove one Unit3 from attacking and add one Unit3 to defending.
				$attacker['military']['unit3']['attacking']--;# = MAX($defender['military']['unit3']['defending']-1,0);
				$defender['military']['unit3']['defending']++;# = MIN($attacker['military']['unit3']['attacking']+1, $defender['military']['unit3']['available']);

				# Calculate OP and DP once before we loop.
				$op = $attacker['military']['unit1']['attacking'] * $attacker['military']['unit1']['op'];
				$op += $attacker['military']['unit2']['attacking'] * $attacker['military']['unit2']['op'];
				$op += $attacker['military']['unit3']['attacking'] * $attacker['military']['unit3']['op'];
				$op += $attacker['military']['unit4']['attacking'] * $attacker['military']['unit4']['op'];
				$military['op'] = $op * (1+$attacker['op']['modifier']);

				$dp = $defender['military']['draftees']['trained'];
				$dp += $defender['military']['unit1']['defending'] * $defender['military']['unit1']['dp'];
				$dp += $defender['military']['unit2']['defending'] * $defender['military']['unit2']['dp'];
				$dp += $defender['military']['unit3']['defending'] * $defender['military']['unit3']['dp'];
				$dp += $defender['military']['unit4']['defending'] * $defender['military']['unit4']['dp'];
				$military['dp'] = $dp * (1+$defender['dp']['modifier']);

				# Calculate the ratio.
				$military['ratio'] = $military['op']/$military['dp'];

			}
		}
		$military['attacker'] = $attacker;
		$military['defender'] = $defender;

		return $military;
	}
	## METHOD 2 (Goblin)
	# assume all spec OP and Wolf Riders are sent, then add HGs until 5:4 is reached.
	elseif($method[$race] == 2)
	{
		// Goblin moved to method 1.
	}
	## METHOD 3 (Troll)
	# assume all Brutes and Bashers are sent, and then gradually add Smashers until 5:4 is reached.
	elseif($method[$race] == 3)
	{
		$military['method'] = $method[$race];
		$military['iteration'] = 0;

		$attacker['military']['unit1']['attacking'] = $attacker['military']['unit1']['available'];
		$attacker['military']['unit2']['attacking'] = 0; # Won't be changed.
		$attacker['military']['unit3']['attacking'] = $attacker['military']['unit3']['available'];
		$attacker['military']['unit4']['attacking'] = 0;

		$defender['military']['draftees']['defending'] = $defender['military']['draftees']['trained'];
		$defender['military']['unit1']['defending'] = 0; # Won't be changed.
		$defender['military']['unit2']['defending'] = $defender['military']['unit2']['available'];
		$defender['military']['unit3']['defending'] = 0; # Won't be changed.
		$defender['military']['unit4']['defending'] = $attacker['military']['unit4']['available'];

		# Calculate OP and DP once before we loop.
		$op = $attacker['military']['unit1']['attacking'] * $attacker['military']['unit1']['op'];
		$op += $attacker['military']['unit2']['attacking'] * $attacker['military']['unit2']['op'];
		$op += $attacker['military']['unit3']['attacking'] * $attacker['military']['unit3']['op'];
		$op += $attacker['military']['unit4']['attacking'] * $attacker['military']['unit4']['op'];
		$op *= (1+$attacker['op']['modifier']);

		$dp = $defender['military']['draftees']['defending'];
		$dp += $defender['military']['unit1']['defending'] * $defender['military']['unit1']['dp'];
		$dp += $defender['military']['unit2']['defending'] * $defender['military']['unit2']['dp'];
		$dp += $defender['military']['unit3']['defending'] * $defender['military']['unit3']['dp'];
		$dp += $defender['military']['unit4']['defending'] * $defender['military']['unit4']['dp'];
		$dp *= (1+$defender['dp']['modifier']);

		# See what baseline ratio is.
		$military['initial_ratio'] = $op/$dp;

		while($military['ratio'] <= $max_ratio)
		{
			if($attacker['military']['unit4']['attacking'] == $defender['military']['unit4']['available'] OR $defender['military']['unit4']['defending'] == 0)
			{
				break;
			}

			$military['sub_method'] = '1';
			$military['iteration']++;
			# Add one Unit4 (Smasher) to attacking and remove one Unit4 (Smasher) from defending
			$attacker['military']['unit4']['attacking']++;
			$defender['military']['unit4']['defending']--;

			# Calculate OP and DP once before we loop.
			$op = $attacker['military']['unit1']['attacking'] * $attacker['military']['unit1']['op'];
			$op += $attacker['military']['unit2']['attacking'] * $attacker['military']['unit2']['op'];
			$op += $attacker['military']['unit3']['attacking'] * $attacker['military']['unit3']['op'];
			$op += $attacker['military']['unit4']['attacking'] * $attacker['military']['unit4']['op'];
			$military['op'] = $op * (1+$attacker['op']['modifier']);

			$dp = $defender['military']['draftees']['defending'];
			$dp += $defender['military']['unit1']['defending'] * $defender['military']['unit1']['dp'];
			$dp += $defender['military']['unit2']['defending'] * $defender['military']['unit2']['dp'];
			$dp += $defender['military']['unit3']['defending'] * $defender['military']['unit3']['dp'];
			$dp += $defender['military']['unit4']['defending'] * $defender['military']['unit4']['dp'];
			$military['dp'] = $dp * (1+$defender['dp']['modifier']);

			# Calculate the ratio.
			$military['ratio'] = $military['op']/$military['dp'];

		}
		$military['attacker'] = $attacker;
		$military['defender'] = $defender;

		return $military;

	}
	## METHOD 4 (Kobold)
	# kill me
	elseif($method[$race] == 4)
	{
		// ???
	}
	## METHOD 5 (others)
	# assume all spec OP is sent and then just add elite OP until 5:4 is reached.
	elseif($method[$race] == 5)
	{
		$military['method'] = $method[$race];
		$military['iteration'] = 0;

		$attacker['military']['unit1']['attacking'] = $attacker['military']['unit1']['available'];
		$attacker['military']['unit2']['attacking'] = 0; # Won't be changed.
		$attacker['military']['unit3']['attacking'] = 0; # Won't be changed. Unit3 has no OP.
		$attacker['military']['unit4']['attacking'] = 0; # Start at zero and increment.

		$defender['military']['draftees']['defending'] = $defender['military']['draftees']['trained'];
		$defender['military']['unit1']['defending'] = 0; # Won't be changed.
		$defender['military']['unit2']['defending'] = $defender['military']['unit2']['available'];
		$defender['military']['unit3']['defending'] = $attacker['military']['unit3']['available'];
		$defender['military']['unit4']['defending'] = $attacker['military']['unit4']['available']; # Start at max and decrease.

		# Calculate OP and DP once before we loop.
		$op = $attacker['military']['unit1']['attacking'] * $attacker['military']['unit1']['op'];
		$op += $attacker['military']['unit2']['attacking'] * $attacker['military']['unit2']['op'];
		$op += $attacker['military']['unit3']['attacking'] * $attacker['military']['unit3']['op'];
		$op += $attacker['military']['unit4']['attacking'] * $attacker['military']['unit4']['op'];
		$op *= (1+$attacker['op']['modifier']);

		$dp = $defender['military']['draftees']['defending'];
		$dp += $defender['military']['unit1']['defending'] * $defender['military']['unit1']['dp'];
		$dp += $defender['military']['unit2']['defending'] * $defender['military']['unit2']['dp'];
		$dp += $defender['military']['unit3']['defending'] * $defender['military']['unit3']['dp'];
		$dp += $defender['military']['unit4']['defending'] * $defender['military']['unit4']['dp'];
		$dp *= (1+$defender['dp']['modifier']);		

		while($military['ratio'] <= $max_ratio)
		{
			if($attacker['military']['unit4']['defending'] == $defender['military']['unit4']['available'] OR $defender['military']['unit4']['defending'] == 0)
			{
				break;
			}

			$military['sub_method'] = '1';
			$military['iteration']++;
			# Add one Unit4 (ie. Salamander) to attacking and remove one Unit4 (ie. Salamander) from defending
			$attacker['military']['unit4']['attacking']++;
			$defender['military']['unit4']['defending']--;

			# Calculate OP and DP once before we loop.
			$op = $attacker['military']['unit1']['attacking'] * $attacker['military']['unit1']['op'];
			$op += $attacker['military']['unit2']['attacking'] * $attacker['military']['unit2']['op'];
			$op += $attacker['military']['unit3']['attacking'] * $attacker['military']['unit3']['op'];
			$op += $attacker['military']['unit4']['attacking'] * $attacker['military']['unit4']['op'];
			$military['op'] = $op * (1+$attacker['op']['modifier']);

			$dp = $defender['military']['draftees']['defending'];
			$dp += $defender['military']['unit1']['defending'] * $defender['military']['unit1']['dp'];
			$dp += $defender['military']['unit2']['defending'] * $defender['military']['unit2']['dp'];
			$dp += $defender['military']['unit3']['defending'] * $defender['military']['unit3']['dp'];
			$dp += $defender['military']['unit4']['defending'] * $defender['military']['unit4']['dp'];
			$military['dp'] = $dp * (1+$defender['dp']['modifier']);

			# Calculate the ratio.
			$military['ratio'] = $military['op']/$military['dp'];

		}
		$military['attacker'] = $attacker;
		$military['defender'] = $defender;

		return $military;

	}
	else
	{
		return NULL;
	}



}
