<?php
session_start();

$time = microtime(true);

require_once('calculator.php');

// Until attacker is implemented.
$dominion['attacker']['military']['unit1']['name'] = '&mdash;';
$dominion['attacker']['military']['unit2']['name'] = '&mdash;';
$dominion['attacker']['military']['unit3']['name'] = '&mdash;';
$dominion['attacker']['military']['unit4']['name'] = '&mdash;';

?>

<!DOCTYPE HTML>
<html>
 <head>
    <title>Dreki's Dominion Calculator</title>
    <link href="https://fonts.googleapis.com/css?family=Karla|Source+Code+Pro" rel="stylesheet">
    <link rel="preload" href="grid.css" as="style">
    <link rel="preload" href="style.css" as="style">

    <link rel="stylesheet" href="grid.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="notice">
<p>Known issues/bugs:</p>

<table>
  <thead>
    <tr>
      <th>Issue</th>
      <th>Workaround</th>
      <th>Resolution</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>General wonkiness when manually editing Units figures.</td>
      <td>Manually modify the numbers in the Ops you have pasted.</td>
      <td>Aiming to fix this ASAP.</td>
    </tr>
    <tr>
      <td>Making changes to the Units, updating, and then making changes to Modifiers causes Units to reset to what was in the ops.</td>
      <td>Again, it's best to update the Ops you have pasted for now.</td>
      <td>Second on the to-do list.</td>
    </tr>
    <tr>
      <td>DP mods are inflated by 0.01% Walls and 0.01% Guard Towers. This was a feature in Talium's old calculator, likely because of rounding errors in old Dominion.</td>
      <td>Drop Walls and GTs by 0.01% or simply enjoy the peace of mind of slightly oversending.</td>
      <td>Will investigate if needed.</td>
    </tr>
    <!--
    <tr>
      <td>Morale is ignored.</td>
      <td>No workaround available.</td>
      <td>I'll fix this soon.</td>
    </tr>
    -->
    <tr>
      <td>Land Spy is unavailable.</td>
      <td>Not an issue until land-dependent races/units are in the game.</td>
      <td>Will be fixed after such units/races are implemented.</td>
    </tr>
    <tr>
      <td>Attacker missing.</td>
      <td>No calculations available/performed for attacker.</td>
      <td>Once I'm content with how Defender calculations work, will add Attacker. Until then, calculate your OP in-game.</td>
    </tr>
  </tbody>
</table>

</div>

<div class="grid-container">
  <div class="Attacker">
    <div class="Attacker-Military">
      
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="defender-military" name="defender-military">
    <?php require('hidden_fields.php'); ?>
    <input type="hidden" name="origin" value="defender-military"/>

    <table>
      <thead>
        <tr>
          <th>Attacker</th>
          <th>Count</th>
          <th>OP</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $dominion['attacker']['military']['unit1']['name']; ?></td>
          <td>&mdash;</td>
          <td>&mdash;</td>
        </tr>
        <tr>
          <td><?php echo $dominion['attacker']['military']['unit2']['name']; ?></td>
          <td>&mdash;</td>
          <td>&mdash;</td>
        </tr>
        <tr>
          <td><?php echo $dominion['attacker']['military']['unit3']['name']; ?></td>
          <td>&mdash;</td>
          <td>&mdash;</td>
        </tr>
        <tr>
          <td><?php echo $dominion['attacker']['military']['unit4']['name']; ?></td>
          <td>&mdash;</td>
          <td>&mdash;</td>
        </tr>
      </tbody>
    </table>

    <input type="submit" class="submit" value="Update">
    
    </form>
    

    </div>
    <div class="Attacker-Mods">

    <h2>Modifiers</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="attacker_modifiers" name="attacker_modifiers">
    <?php require('hidden_fields.php'); ?>
    <input type="hidden" name="origin" value="attacker-mods"/>

    <table>
      <tbody>
        <tr>
          <td>Gryphon Nest: </td>
          <td>&mdash; %</td>
        </tr>
        <tr>
          <td>Walls: </td>
          <td>&mdash;</td>
        </tr>
        <tr>
          <td>Racial: </td>
          <td>&mdash; %</td>
        </tr>
        <tr>
          <td>Morale: </td>
          <td>&mdash; %</td>
        </tr>
        <tr>
          <td>Prestige: </td>
          <td>&mdash;</td>
        </tr>
      </tbody>
    </table>
    <h3>Spell</h3>
    <select name="attacker_spell" id="spell">
      <option value="none">None</option>
      <?php 

        foreach($scribes['spells'] as $spell)
        {
          if($spell['op'] > 0)
          {
            if($spell['name'] == $dominion['defender']['dp']['mods']['spell']['name'])
            {
              echo '<option value="' . $spell['name'] .'" selected="selected">' . $spell['name'] . ' (+'. $spell['op']*100 . '% OP)</option>';
            }
            else
            {
              echo '<option value="' . $spell['name'] .'">' . $spell['name'] . ' (+'. $spell['op']*100 . '% OP)</option>';
            }
          }
        }

      ?>
    </select>
    </table>
    
      <input type="submit" class="submit" value="Update">

    </form>

      
    </div>
    <div class="Attacker-Calcs">
      
    <table>
      <tbody>
        <tr>
          <td>Attacker:</td>
          <td>&mdash;</td>
        </tr>
        <tr>
          <td>Race:</td>
          <td>&mdash;</td>
        </tr>
        <tr>
          <td>Size:</td>
          <td>&mdash; acres</td>
        </tr>
        <tr>
          <td>Raw:</td>
          <td>&mdash; OP</td>
        </tr>
        <tr>
          <td>Mods:</td>
          <td>&mdash; %</td>
        </tr>
        <tr>
          <td>Modded:</td>
          <td>&mdash; OP</td>
        </tr>
        <tr>
          <td>Net:</td>
          <td>&mdash;</td>
        </tr>

      </tbody>
    </table>

    </div>
  </div>
  <div class="Defender">
    <div class="Defender-Military">

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="defender-military" name="defender-military">
    <?php require('hidden_fields.php'); ?>
    <input type="hidden" name="origin" value="defender-military"/>

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
          <td><input type="number" name="defender_military_draftees" value="<?php echo $dominion['defender']['military']['draftees']; ?>" /></td>
          <td>&mdash;</td>
          <td>&mdash;</td>
          <td><?php echo number_format($dominion['defender']['draftee_dp']); ?></td>
        </tr>
        <tr>
          <td>Forest Havens</td>
          <td><input type="number" name="defender_military_forest_havens" value="<?php echo $dominion['defender']['buildings']['Forest Haven']; ?>" /></td>
          <td>&mdash;</td>
          <td>&mdash;</td>
          <td><?php echo number_format($dominion['defender']['forest_havens_dp']); ?></td>
        </tr>
        <tr>
          <td><?php echo $dominion['defender']['military']['unit1']['name']; ?></td>
          <td><input type="number" name="defender_military_unit1_trained" value="<?php echo $dominion['defender']['military']['unit1']['trained']; ?>" /></td>
          <td><input type="number" name="defender_military_unit1_returning" value="<?php echo $dominion['defender']['military']['unit1']['returning']; ?>" /></td>
          <td><input type="number" name="defender_military_unit1_at_home" value="<?php echo $dominion['defender']['military']['unit1']['home']; ?>" /></td>
          <td><?php echo number_format($dominion['defender']['military']['unit1']['trained_dp']); ?></td>
        </tr>
        <tr>
          <td><?php echo $dominion['defender']['military']['unit2']['name']; ?></td>
          <td><input type="number" name="defender_military_unit2_trained" value="<?php echo $dominion['defender']['military']['unit2']['trained']; ?>" /></td>
          <td><input type="number" name="defender_military_unit2_returning" value="<?php echo $dominion['defender']['military']['unit2']['returning']; ?>" /></td>
          <td><input type="number" name="defender_military_unit2_at_home" value="<?php echo $dominion['defender']['military']['unit2']['home']; ?>" /></td>
          <td><?php echo number_format($dominion['defender']['military']['unit2']['trained_dp']); ?></td>
        </tr>
        <tr>
          <td><?php echo $dominion['defender']['military']['unit3']['name']; ?></td>
          <td><input type="number" name="defender_military_unit3_trained" value="<?php echo $dominion['defender']['military']['unit3']['trained']; ?>" /></td>
          <td><input type="number" name="defender_military_unit3_returning" value="<?php echo $dominion['defender']['military']['unit3']['returning']; ?>" /></td>
          <td><input type="number" name="defender_military_unit3_at_home" value="<?php echo $dominion['defender']['military']['unit3']['home']; ?>" /></td>
          <td><?php echo number_format($dominion['defender']['military']['unit3']['trained_dp']); ?></td>
        </tr>
        <tr>
          <td><?php echo $dominion['defender']['military']['unit4']['name']; ?></td>
          <td><input type="number" name="defender_military_unit4_trained" value="<?php echo $dominion['defender']['military']['unit4']['trained']; ?>" /></td>
          <td><input type="number" name="defender_military_unit4_returning" value="<?php echo $dominion['defender']['military']['unit4']['returning']; ?>" /></td>
          <td><input type="number" name="defender_military_unit4_at_home" value="<?php echo $dominion['defender']['military']['unit4']['home']; ?>" /></td>
          <td><?php echo number_format($dominion['defender']['military']['unit4']['trained_dp']); ?></td>
        </tr>
      </tbody>
    </table>

    <input type="submit" class="submit" value="Update">
    
    </form>
    
    </div>

    <div class="Defender-Mods">
      
    <h2>Modifiers</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="modifiers" name="modifiers">
    <?php require('hidden_fields.php'); ?>
    <input type="hidden" name="origin" value="defender-mods"/>

    <table>
      <tbody>
        <tr>
          <td>Guard Tower: </td>
          <td><input type="number" name="defender_mods_guard_towers" value="<?php echo $dominion['defender']['display']['Guard Tower']; ?>" step='0.01' placeholder='0.00' />%</td>
        </tr>
        <tr>
          <td>Walls: </td>
          <td><input type="number" name="defender_mods_castle_walls" value="<?php echo $dominion['defender']['display']['Walls']; ?>" step='0.01' placeholder='0.00' />%</td>
        </tr>
        <tr>
          <td>Racial: </td>
          <td><input type="number" name="defender_mods_racial" value="<?php echo $dominion['defender']['dp']['mods']['Racial']; ?>" step='0.01' placeholder='0.00' />%</td>
        </tr>
        <tr>
          <td>Morale: </td>
          <td><input type="number" name="defender_mods_morale" value="<?php echo $dominion['defender']['general']['morale']; ?>" step='1' placeholder='0'/>%</td>
        </tr>
        <tr>
          <td>Prestige: </td>
          <td>

            <?php

              if($dominion['defender']['general']['race'] == 'Orc')
              {
                echo '<input type="number" name="defender_mods_prestige" value="' . $dominion['defender']['general']['prestige'] ."\" step='1' placeholder='0'/>";
              }
              else
              {
                echo '<input type="text" name="defender_mods_prestige" value="Orc only" disabled="disabled"/>';
              }
            ?>

          </td>
        </tr>
      </tbody>
    </table>
    <h3>Spell</h3>
    <select name="spell" id="spell">
      <option value="none">None</option>
      <?php 

        foreach($scribes['spells'] as $spell)
        {
          if($spell['dp'] > 0)
          {
            if($spell['name'] == $dominion['defender']['dp']['mods']['spell']['name'])
            {
              echo '<option value="' . $spell['name'] .'" selected="selected">' . $spell['name'] . ' (+'. $spell['dp']*100 . '% DP)</option>';
            }
            else
            {
              echo '<option value="' . $spell['name'] .'">' . $spell['name'] . ' (+'. $spell['dp']*100 . '% DP)</option>';
            }
          }
        }

      ?>
    </select>
    </table>
    
      <input type="submit" class="submit" value="Update">

    </form>


    </div>
    <div class="Defender-Calcs">
      
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
          <td><?php echo number_format($dominion['defender']['general']['land']); ?> acres</td>
        </tr>
        <tr>
          <td>Raw:</td>
          <td><?php echo number_format($dominion['defender']['dp']['raw']); ?> DP</td>
        </tr>
        <tr>
          <td>Mods:</td>
          <td><?php echo $dominion['defender']['dp']['modifier']*100; ?>%</td>
        </tr>
        <tr>
          <td>Modded:</td>
          <td><?php echo number_format($dominion['defender']['dp']['mod']); ?> DP</td>
        </tr>
        <tr>
          <td>Net:</td>
          <td><?php echo number_format($dominion['defender']['dp']['net']); ?> DP ($temples% reduction)</td>
        </tr>

      </tbody>
    </table>
    </div>
  </div>
  <div class="Attacker-Ops">
    
    <h1>Attacker Ops</h1>

    <h2>Clear Sight</h2>
    
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="attacker_ops" name="attacker_ops">

       <textarea type="text" name="attacker_clearsight" id="attacker_clearsight" class="ops"><?php echo $_POST['attacker_clearsight']; ?></textarea>

      <h2>Survey</h2>
      <textarea type="text" name="attacker_survey" id="attacker_survey" class="ops"><?php echo $_POST['attacker_survey']; ?></textarea>

      <h2>Castle</h2>
      <textarea type="text" name="attacker_castle" id="attacker_castle" class="ops"><?php echo $_POST['attacker_castle']; ?></textarea>

      <h2>Barracks (returning)</h2>
      <textarea type="text" name="attacker_barracks_returning" id="attacker_barracks" class="ops"><?php echo $_POST['attacker_barracks_returning']; ?></textarea>

      <h2>Barracks (home)</h2>
      <textarea type="text" name="attacker_barracks_home" id="attacker_barracks_home" class="ops"><?php echo $_POST['attacker_barracks_home']; ?></textarea>

      <h2>Land</h2>
      <textarea type="text" name="attacker_land" id="attacker_land" class="ops" disabled="disabled"></textarea>

      <input type="submit" class="submit" value="Process Ops">

      </form>

  </div>
  <div class="Defender-Ops">
    
    <h1>Defender Ops</h1>

      <h2>Clear Sight</h2>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="attacker_ops" name="attacker_ops">

      <textarea type="text" name="defender_clearsight" id="defender_clearsight" class="ops"><?php echo $_POST['defender_clearsight']; ?></textarea>

      <h2>Survey</h2>
      <textarea type="text" name="defender_survey" id="defender_survey" class="ops"><?php echo $_POST['defender_survey']; ?></textarea>

      <h2>Castle</h2>
      <textarea type="text" name="defender_castle" id="defender_castle" class="ops"><?php echo $_POST['defender_castle']; ?></textarea>

      <h2>Barracks (returning)</h2>
      <textarea type="text" name="defender_barracks_returning" id="defender_barracks_returning" class="ops"><?php echo $_POST['defender_barracks_returning']; ?></textarea>

      <h2>Barracks (home)</h2>
      <textarea type="text" name="defender_barracks_home" id="defender_barracks_home" class="ops"><?php echo $_POST['defender_barracks_home']; ?></textarea>

      <h2>Land</h2>
      <textarea type="text" name="defender_land" id="defender_land" class="ops" disabled="disabled"></textarea>

      <input type="submit" class="submit" value="Process Ops">

      </form>
  </div>
</div>

<h2>Sample</h2>
<p>Unsure how to copy and paste? Take a look at the sample images below.</p>
<table class="ops_samples">
  <thead>
    <tr>
      <th>Op</th>
      <th>Screenshot</th>
      <th>Text</th>
  </thead>
  <tbody>
    <tr>
      <td class="op"><h3>Clear Sight</h3><p>Select from and including where it says "Status Screen" until the ??? for ArchMages.</p></td>
      <td><a href="images/clear_sight.png" target="_new"><img src="images/clear_sight.png" class="ops_samples" /></a></td>
      <td><textarea class="ops_samples"><?php echo file_get_contents('text/clear_sight.txt'); ?></textarea></td>
    </tr>
    <tr>
      <td class="op"><h3>Survey</h3><p>Select from and including where it says "Building Type" all the way down to and including the line with Docks.</p></td>
      <td><a href="images/survey.png" target="_new"><img src="images/survey.png" class="ops_samples" /></a></td>
      <td><textarea class="ops_samples"><?php echo file_get_contents('text/survey.txt'); ?></textarea></td>
    </tr>
    <tr>
      <td class="op"><h3>Castle</h3><p>Select from and including where it says "Part" until the Invested points for Harbor.</p></td>
      <td><a href="images/castle.png" target="_new"><img src="images/castle.png" class="ops_samples" /></a></td>
      <td><textarea class="ops_samples"><?php echo file_get_contents('text/castle.txt'); ?></textarea></td>
    </tr>
    <tr>
      <td class="op"><h3>Barracks (returning)</h3><p>Select from and including where it says "Unit" until the the Total for the final unit.</p></td>
      <td><a href="images/barracks_spy_returning.png" target="_new"><img src="images/barracks_spy_returning.png" class="ops_samples" /></a></td>
      <td><textarea class="ops_samples"><?php echo file_get_contents('text/barracks_spy_returning.txt'); ?></textarea></td>

    </tr>
    <tr>
      <td class="op"><h3>Barracks (home)</h3><p>Select from and including where it says "Unit" until the ??? for ArchMages.</p></td>
      <td><a href="images/barracks_spy_home.png" target="_new"><img src="images/barracks_spy_home.png" class="ops_samples" /></a></td>
      <td><textarea class="ops_samples"><?php echo file_get_contents('text/barracks_spy_home.txt'); ?></textarea></td>
    </tr>
    <tr>
      <td class="op"><h3>Land</h3><p>Not yet implemented.</p></td>
      <td>&mdash;</td>
      <td><textarea class="ops_samples" disabled="disabled"><?php #echo file_get_contents('text/land.txt'); ?></textarea></td>
    </tr>
  </tbody>
</table>

<?php

echo '<pre>';
print_r($dominion);
echo '</pre>';

//execution time of the script
echo '<p>Execution time: '. (microtime(true) - $time) .' seconds.</p>';

$_SESSION['dominion'] = $dominion;

?>

</body>
</html>