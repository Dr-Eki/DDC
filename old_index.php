<?php

if ($_POST['origin'] == 'military')
{
#	echo '<pre>';
#	echo "military form submission\n";
#	print_r($_POST);
#	echo '</pre>';	
}

require_once('units.php');
require_once('spells.php');

$dominion = array();
$dominion['defender']['general'] = array();
$dominion['defender']['military'] = array();
$dominion['defender']['buildings'] = array();
$dominion['defender']['castle'] = array();
$dominion['defender']['land'] = array();

// Clear Sight
$clearsight = trim($_POST['clearsight']);
$clearsight = explode("\n", $clearsight);


// Survey
$survey = trim($_POST['survey']);
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
$castle = trim($_POST['castle']);
$castle = explode("\n", $castle);

foreach($castle as $part)
{
	$improvements[] = explode("\t", $part);
}
foreach($improvements as $part)
{
	if($part[0] !== 'Part')
	{
		$part[0] = trim($part[0]);
		$dominion['defender']['castle'][$part[0]] = floatval($part[1])/100;
	}
}

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
$dominion['defender']['general']['name'] = $name[1];
unset($name);

$dominion['defender']['general']['ruler'] = trim(str_replace('Ruler: 	','',$clearsight[2]));
$dominion['defender']['general']['race'] = trim(str_replace('Race: 	','',$clearsight[3]));
$dominion['defender']['general']['land'] = intval(trim(str_replace('Land: 	','',$clearsight[4])));
$dominion['defender']['general']['peasants'] = intval(str_replace(',','',str_replace('Peasants: 	','',$clearsight[5])));
$dominion['defender']['general']['prestige'] = intval(str_replace(',','',str_replace('Prestige: 	','',$clearsight[5])));
$dominion['defender']['general']['morale'] = intval(str_replace(',','',str_replace('Morale: 	','',$clearsight[5])));

# Piece together trained army.
$dominion['defender']['military'] = $scribes['military'][$dominion['defender']['general']['race']];

if($_POST['origin'] == 'military')
{
	$dominion['defender']['military']['draftees'] = $_POST['military_draftees'];
	$dominion['defender']['military']['unit1']['trained'] = $_POST['military_unit1_trained'];
	$dominion['defender']['military']['unit2']['trained'] = $_POST['military_unit2_trained'];
	$dominion['defender']['military']['unit3']['trained'] = $_POST['military_unit3_trained'];
	$dominion['defender']['military']['unit4']['trained'] = $_POST['military_unit4_trained'];
	$dominion['defender']['buildings']['Forest Haven'] = $_POST['military_forest_havens'];
}
else
{
	$dominion['defender']['military']['draftees'] = filter_var(str_replace('Draftees: 	','',$clearsight[20]),FILTER_SANITIZE_NUMBER_INT);
	$dominion['defender']['military']['unit1']['trained'] = filter_var(str_replace($scribes['military'][$dominion['defender']['general']['race']]['unit1']['name'] . ' 	','',$clearsight[21]),FILTER_SANITIZE_NUMBER_INT);
	$dominion['defender']['military']['unit2']['trained'] = filter_var(str_replace($scribes['military'][$dominion['defender']['general']['race']]['unit1']['name'] . ' 	','',$clearsight[22]),FILTER_SANITIZE_NUMBER_INT);
	$dominion['defender']['military']['unit3']['trained'] = filter_var(str_replace($scribes['military'][$dominion['defender']['general']['race']]['unit1']['name'] . ' 	','',$clearsight[23]),FILTER_SANITIZE_NUMBER_INT);
	$dominion['defender']['military']['unit4']['trained'] = filter_var(str_replace($scribes['military'][$dominion['defender']['general']['race']]['unit1']['name'] . ' 	','',$clearsight[24]),FILTER_SANITIZE_NUMBER_INT);
}

# Calculate raw DP
$dominion['defender']['dp']['raw'] = 0;

## Draftees -- REMOVE FOR DARK ELF
$dominion['defender']['dp']['raw'] += $dominion['defender']['military']['draftees']*1;

## FH -- Assumes 20 peasants are available for each FH
$dominion['defender']['dp']['raw'] += $dominion['defender']['buildings']['Forest Haven'] * 20 * 0.75;

## Units 1-4
$dominion['defender']['dp']['raw'] += $dominion['defender']['military']['unit1']['trained'] * $dominion['defender']['military']['unit1']['dp'];
$dominion['defender']['dp']['raw'] += $dominion['defender']['military']['unit2']['trained'] * $dominion['defender']['military']['unit2']['dp'];
$dominion['defender']['dp']['raw'] += $dominion['defender']['military']['unit3']['trained'] * $dominion['defender']['military']['unit3']['dp'];
$dominion['defender']['dp']['raw'] += $dominion['defender']['military']['unit4']['trained'] * $dominion['defender']['military']['unit4']['dp'];

# Calculate DP mods
$dominion['defender']['dp']['mods'] = 0.00;
$dominion['defender']['dp']['mods'] += min(round(($dominion['defender']['buildings']['Guard Tower'] / $dominion['defender']['general']['land']),4,PHP_ROUND_HALF_UP)*1.75, 0.35);
$dominion['defender']['dp']['mods'] += $dominion['defender']['castle']['Walls'];

# Calculate visualisations/ratios
$dominion['defender']['display']['Guard Tower'] = round(($dominion['defender']['buildings']['Guard Tower'] / $dominion['defender']['general']['land']),4,PHP_ROUND_HALF_UP)*100;
$dominion['defender']['display']['Gryphon Nest'] = round(($dominion['defender']['buildings']['Gryphon Nest'] / $dominion['defender']['general']['land']),4,PHP_ROUND_HALF_UP)*100;

## Assume best DP spell is on.
if($dominion['defender']['general']['race'] === 'Icekin')
{
	$dominion['defender']['dp']['mods'] += $scribes['spells']['Blizzard']['dp'];
}
elseif($dominion['defender']['general']['race'] === 'Halfling')
{
	$dominion['defender']['dp']['mods'] += $scribes['spells']['Defensive Frenzy']['dp'];
}
elseif($dominion['defender']['general']['race'] === 'Kobold')
{
	$dominion['defender']['dp']['mods'] += $scribes['spells']['Howling']['dp'];
}
else
{
	$dominion['defender']['dp']['mods'] += $scribes['spells']['Ares Call']['dp'];
}

# Calculate mod DP
$dominion['defender']['dp']['mod'] = $dominion['defender']['dp']['raw'] * (1 + $dominion['defender']['dp']['mods']);

# Calculate net DP
$dominion['defender']['dp']['net'] = $dominion['defender']['dp']['raw'] * (1 + $dominion['defender']['dp']['mods'] - $attacker['temples']['reduction']);


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
?>

<!DOCTYPE HTML>
<html>

	<head>
		<title>Dreki's Dominion Calculator</title>
		<link href="https://fonts.googleapis.com/css?family=Karla|Source+Code+Pro" rel="stylesheet"> 
		<style>
			*
			{
				padding: 0;
				margin: 0;
				font-family: 'Karla', sans-serif;
				font-size: 16px;
			}

			div
			{
				margin: 4px;
				border: 1px solid #b00;
			}

			div.ops
			{
				width: 20%;
			}
			div.button
			{
				width: 100%;
				border: none;
			}

			h1
			{
				font-weight: bold;
				font-size: 120%;
			}
			h2
			{
				font-weight: bold;
				font-size: 110%;
			}

			input,textarea
			{
				font-family: 'Source Code Pro', monospace;
			}

			input.submit
			{
				height: 3em;
				width: 100%;
			}

			input[type=number]
			{
				width:4em;
			}

			/*

			http://the-echoplex.net/flexyboxes/?fixed-height=on&display=inline-flex&flex-direction=row&flex-wrap=nowrap&justify-content=flex-start&align-items=flex-start&align-content=stretch&order[]=0&flex-grow[]=0&flex-shrink[]=1&flex-basis[]=auto&align-self[]=auto&order[]=0&flex-grow[]=0&flex-shrink[]=1&flex-basis[]=auto&align-self[]=auto&order[]=0&flex-grow[]=0&flex-shrink[]=1&flex-basis[]=auto&align-self[]=auto&order[]=0&flex-grow[]=0&flex-shrink[]=1&flex-basis[]=auto&align-self[]=auto

			*/
.flex-container {
    display: -ms-inline-flexbox;
    display: -webkit-inline-flex;
    display: inline-flex;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-flex-wrap: nowrap;
    -ms-flex-wrap: nowrap;
    flex-wrap: nowrap;
    -webkit-justify-content: flex-start;
    -ms-flex-pack: start;
    justify-content: flex-start;
    -webkit-align-content: stretch;
    -ms-flex-line-pack: stretch;
    align-content: stretch;
    -webkit-align-items: flex-start;
    -ms-flex-align: start;
    align-items: flex-start;
    }

.flex-item:nth-child(1) {
    -webkit-order: 0;
    -ms-flex-order: 0;
    order: 0;
    -webkit-flex: 0 1 auto;
    -ms-flex: 0 1 auto;
    flex: 0 1 auto;
    -webkit-align-self: auto;
    -ms-flex-item-align: auto;
    align-self: auto;
    }

.flex-item:nth-child(2) {
    -webkit-order: 0;
    -ms-flex-order: 0;
    order: 0;
    -webkit-flex: 0 1 auto;
    -ms-flex: 0 1 auto;
    flex: 0 1 auto;
    -webkit-align-self: auto;
    -ms-flex-item-align: auto;
    align-self: auto;
    }

.flex-item:nth-child(3) {
    -webkit-order: 0;
    -ms-flex-order: 0;
    order: 0;
    -webkit-flex: 0 1 auto;
    -ms-flex: 0 1 auto;
    flex: 0 1 auto;
    -webkit-align-self: auto;
    -ms-flex-item-align: auto;
    align-self: auto;
    }

.flex-item:nth-child(4) {
    -webkit-order: 0;
    -ms-flex-order: 0;
    order: 0;
    -webkit-flex: 0 1 auto;
    -ms-flex: 0 1 auto;
    flex: 0 1 auto;
    -webkit-align-self: auto;
    -ms-flex-item-align: auto;
    align-self: auto;
    }

			input[type=number]::-webkit-outer-spin-button,
			input[type=number]::-webkit-inner-spin-button {
			    -webkit-appearance: none;
			    margin: 0;
			}

			input[type=number] {
			    -moz-appearance:textfield;
			}

		</style>

	</head>

<body>

<div id="defender" class="flex-container">

	<div id="military" class="flex-item">
		<h2>Military</h2>

		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="military" name="military">

		<input type="hidden" name="clearsight" value="<?php echo $_POST['clearsight']; ?>" />
		<input type="hidden" name="survey" value="<?php echo $_POST['survey']; ?>" />
		<input type="hidden" name="castle" value="<?php echo $_POST['castle']; ?>" />
		<input type="hidden" name="barracks" value="<?php echo $_POST['barracks']; ?>" />
		<input type="hidden" name="land" value="<?php echo $_POST['land']; ?>" />
		<input type="hidden" name="origin" value="military"/>

		<table>
			<thead>
				<tr>
					<th>Defender</th>
					<th>Trained</th>
					<th>Returning</th>
					<th>At&nbsp;Home</th>
					<th>DP</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Draftees</td>
					<td><input type="number" name="military_draftees" value="<?php echo $dominion['defender']['military']['draftees']; ?>" /></td>
					<td></td>
					<td></td>
					<td><?php echo number_format($dominion['defender']['military']['draftees'] * 1); ?></td>
				</tr>
				<tr>
					<td>Forest Havens</td>
					<td><input type="number" name="military_forest_havens" value="<?php echo $dominion['defender']['buildings']['Forest Haven']; ?>" /></td>
					<td></td>
					<td></td>
					<td><?php echo number_format($dominion['defender']['buildings']['Forest Haven'] * 20 * 0.75); ?></td>
				</tr>
				<tr>
					<td><?php echo $dominion['defender']['military']['unit1']['name']; ?></td>
					<td><input type="number" name="military_unit1_trained" value="<?php echo $dominion['defender']['military']['unit1']['trained']; ?>" /></td>
					<td><input type="number" name="military_unit1_returning" value="$unit1_returning" /></td>
					<td><input type="number" name="military_unit1_at_home" value="$unit1_at_home" /></td>
					<td><?php echo number_format($dominion['defender']['military']['unit1']['trained'] * $dominion['defender']['military']['unit1']['dp']); ?></td>
				</tr>
				<tr>
					<td><?php echo $dominion['defender']['military']['unit2']['name']; ?></td>
					<td><input type="number" name="military_unit2_trained" value="<?php echo $dominion['defender']['military']['unit2']['trained']; ?>" /></td>
					<td><input type="number" name="military_unit2_returning" value="$unit2_returning" /></td>
					<td><input type="number" name="military_unit2_at_home" value="$unit2_at_home" /></td>
					<td><?php echo number_format($dominion['defender']['military']['unit2']['trained'] * $dominion['defender']['military']['unit2']['dp']); ?></td>
				</tr>
				<tr>
					<td><?php echo $dominion['defender']['military']['unit3']['name']; ?></td>
					<td><input type="number" name="military_unit3_trained" value="<?php echo $dominion['defender']['military']['unit3']['trained']; ?>" /></td>
					<td><input type="number" name="military_unit3_returning" value="$unit3_returning" /></td>
					<td><input type="number" name="military_unit3_at_home" value="$unit3_at_home" /></td>
					<td><?php echo number_format($dominion['defender']['military']['unit3']['trained'] * $dominion['defender']['military']['unit3']['dp']); ?></td>
				</tr>
				<tr>
					<td><?php echo $dominion['defender']['military']['unit4']['name']; ?></td>
					<td><input type="number" name="military_unit4_trained" value="<?php echo $dominion['defender']['military']['unit4']['trained']; ?>" /></td>
					<td><input type="number" name="military_unit4_returning" value="$unit4_returning" /></td>
					<td><input type="number" name="military_unit4_at_home" value="$unit4_at_home" /></td>
					<td><?php echo number_format($dominion['defender']['military']['unit4']['trained'] * $dominion['defender']['military']['unit4']['dp']); ?></td>
				</tr>
			</tbody>
		</table>

		<div id="button" class="button">
			<input type="submit" class="submit" value="Update">
		</div>

		</form>

		<table>
			<tbody>
				<tr>
					<td>Target:</td>
					<td><?php echo $dominion['defender']['general']['name']; ?></td>
				</tr>
				<tr>
					<td>Race:</td>
					<td><?php echo $dominion['defender']['general']['race']; ?></td>
				</tr>
				<tr>
					<td>Size:</td>
					<td><?php echo $dominion['defender']['general']['land']; ?> acres</td>
				</tr>
				<tr>
					<td>Raw:</td>
					<td><?php echo number_format($dominion['defender']['dp']['raw']); ?> DP</td>
				</tr>
				<tr>
					<td>Mods:</td>
					<td><?php echo $dominion['defender']['dp']['mods']*100; ?>%</td>
				</tr>
				<tr>
					<td>Modded:</td>
					<td><?php echo number_format($dominion['defender']['dp']['mod']); ?> DP</td>
				</tr>
				<tr>
					<td>Net:</td>
					<td><?php echo number_format($dominion['defender']['dp']['net']); ?> ($temples% reduction)</td>
				</tr>

			</tbody>

		</table>

	</div>

	<div id="modifiers" class="flex-item">
		<h2>Modifiers</h2>

		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="modifiers" name="modifiers">

		<h3>Buildings</h3>
		<table>
			<thead>
				<tr>
					<th>Building</th>
					<th>Ratio</th>
					<th>Bonus</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Gryphon Nest</td>
					<td><input type="number" name="mods_guard_towers" value="<?php echo $dominion['defender']['display']['Gryphon Nest']; ?>" step='0.01' placeholder='0.00' />%</td>
					<td><?php echo min(round(($dominion['defender']['buildings']['Gryphon Nest'] / $dominion['defender']['general']['land']),4,PHP_ROUND_HALF_UP)*1.75, 0.35)*100; ?>%</td>
				</tr>
				<tr>
					<td>Guard Tower</td>
					<td><input type="number" name="mods_guard_towers" value="<?php echo $dominion['defender']['display']['Guard Tower']; ?>" step='0.01' placeholder='0.00' />%</td>
					<td><?php echo min(round(($dominion['defender']['buildings']['Guard Tower'] / $dominion['defender']['general']['land']),4,PHP_ROUND_HALF_UP)*1.75, 0.35)*100; ?>%</td>
				</tr>
			</tbody>
		</table>
		<h3>Castle</h3>
		<table>
			<thead>
				<tr>
					<th>Castle Part</th>
					<th>Bonus</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Forges</td>
					<td><input type="number" name="mods_castle_forges" value="<?php echo $dominion['defender']['castle']['Forges']*100; ?>" step='0.01' placeholder='0.00' />%</td>
				</tr>
				<tr>
					<td>Walls</td>
					<td><input type="number" name="mods_castle_walls" value="<?php echo $dominion['defender']['castle']['Walls']*100; ?>" step='0.01' placeholder='0.00' />%</td>
				</tr>
			</tbody>
		</table>
		<h3>Spell</h3><p>NOT IMPLEMENTED - Defaults to +10%</p>
		<select>
			<option value="none">None</option>
			<?php 

				foreach($scribes['spells'] as $spell)
				{
					if($spell['dp'] > 0)
					{
						echo '<option value="' . $spell['name'] .'">' . $spell['name'] . ' (+'. $spell['dp']*100 . '% DP)</option>';
					}
				}

			?>

		</select>
		<h3>Other</h3>
		<table>
			<thead>
				<tr>
					<th>Modifier</th>
					<th>Value</th>
					<th>Bonus</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Racial</td>
					<td><input type="number" name="racial" value="$racial" step="any" /></td>
					<td>$dp_racial</td>
				</tr>
				<tr>
					<td>Temples</td>
					<td><input type="number" name="temples" value="$temples" step="any" />%</td>
					<td>$temples</td>
				</tr>
				<tr>
					<td>Manual</td>
					<td><input type="number" name="manual" value="$manual" step="any" /></td>
					<td>$dp_manual</td>
				</tr>
			</tbody>
		</table>
		

		<div id="button" class="button">
			<input type="submit" class="submit" value="Update">
		</div>

		</form>

	</div>



</div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="ops" name="ops">
<div id="ops" class="flex-container">

	<div id="ops">
		<h2>Clear Sight</h2>
		<textarea type="text" name="clearsight" id="clearsight" class="ops"><?php echo $_POST['clearsight']; ?></textarea>

		<h2>Survey</h2>
		<textarea type="text" name="survey" id="survey"  class="ops"><?php echo $_POST['survey']; ?></textarea>

		<h2>Castle</h2>
		<textarea type="text" name="castle" id="castle"  class="ops"><?php echo $_POST['castle']; ?></textarea>

		<h2>Barracks</h2>
		<textarea type="text" name="barracks" id="barracks"  class="ops"><?php echo $_POST['barracks']; ?></textarea>

		<h2>Land</h2>
		<textarea type="text" name="land" id="land"  class="ops" disabled="disabled"></textarea>


	<div id="button" class="button">
		<input type="submit" class="submit" value="Process Ops">
	</div>

	</div>

</div>


</form>





</body>
</html>
