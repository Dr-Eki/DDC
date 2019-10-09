<?php
require_once('core.php');

if($environment == 'local')
{
  error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
  ini_set('display_errors', 1);
}
else
{
  error_reporting(0);
}

#error_reporting(E_ALL &  ~E_NOTICE);
#ini_set('display_errors', 1);

require('constants.php');
require('units.php');
require('races.php');
require('spells.php');
require('functions.php');
require('functions_op_center.php');

if(isset($_POST['defender_op_center']))
{
#  echo '<pre>';
#  print_r(parse_op_center($_POST['defender_op_center']));
#  echo '</pre>';
#  die();
}

# Stylesheet
if(!isset($_COOKIE['ddc_style']))
{
  $_COOKIE['ddc_style'] = 'dark';
}

if($_COOKIE['ddc_style'] == 'dark')
{
  $new_style = 'light';
}
elseif($_COOKIE['ddc_style'] == 'light')
{
  $new_style = 'dark';
}


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
  require_once('calculator.php');
}

?>

<!DOCTYPE HTML>
<html>
 <head>
  <title>Dreki's Dominion Calculator (BETA) - Round 14</title>
  <?php if($environment !== 'local')
  {
    echo '<link href="https://fonts.googleapis.com/css?family=Karla|Ubuntu+Mono&display=swap" rel="stylesheet">';
    echo '<link href="https://fonts.googleapis.com/css?family=Karla|Inconsolata&display=swap" rel="stylesheet">';
  }
  ?>

  <link rel="preload" href="newgrid.css" as="style">
  <link rel="preload" href="base.css">
  <link rel="preload" href="<?php echo $_COOKIE['ddc_style']; ?>.css">

  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/grid.css">
  <link rel="stylesheet" href="css/<?php echo $_COOKIE['ddc_style']; ?>.css">

  <link rel="shortcut icon" type="image/png" href="images/axe.png"/>

  <script>
    function copyOutput(elementToCopy)
    {
      var copyText = document.getElementById(elementToCopy);
      copyText.select();
      document.execCommand("copy");
    }
  </script>
</head>

<body>
<form action="./" method="post" id="output" name="output">
<?php
  require('attacker_hidden_fields.php');
  require('defender_hidden_fields.php');
?>
<input type="hidden" name="origin" value="output" />

<div class="grid-container">
<div class="Notice">

<h1>Known Issues / Bugs:</h1>
<ul>
  <li>Kobold not properply supported.</li>
  <li>Basher only gets +1 DP if you click Update (not Process Ops).</li>
  <li>All barren is assumed to be home land type for land-dependent races.</li>
  <li>Remember to update racial OP/DP bonuses if you manually switch races.</li>
  <li>For any other issues, please contact me (Dreki) on <a href="https://discordapp.com/invite/mFk2wZT" rel="nofollow" target="_new">Discord</a> (#drekis-calc).</li>
</ul>
<p>No guarantees are made about the accuracy or appropriateness of this calculator. </p>
<p>Check out the <a href="https://opendominion.miraheze.org/wiki/Main_Page" target="_new">The OpenDominion Encyclopedia (wiki)</a>.</p>
</div>

<div class="Info">
<h1>How To Use</h1>
<ul>
  <li>See <a href="#Ops-Samples">Ops Samples</a> below.</li>
  <li>Paste ops in corresponding field.</li>
  <li>Click "Process Ops" to process both sets of ops (Attacker and Defender).</li>
  <li>You can make manual changes in the input fields. Click Update afterwards.</li>
  <li>"Process Ops" will overwrite any manual changes.</li>
  <li>Use the Copy Attacker and Copy Target buttons to copy a summary. Useful for pasting into <a href="https://beta.opendominion.net/dominion/council" rel="nofollow" target="_new">Council</a> or Discord.</li>
</ul>

<?php

  echo '<p>';
  echo '<a href="settings.php?style='. $new_style . '" title="Switch stylesheet">Change to ' . ucfirst($new_style) . ' Mode</a>';
  echo ' | ';

  if($_COOKIE['ddc_experimental'] == 'disable' or !isset($_COOKIE['ddc_experimental']))
  {
    $new_experimental = 'enable';
  }
  else
  {
    $new_experimental = 'disable';
  }

  echo '<a href="settings.php?experimental='. $new_experimental . '" title="Enables/disables experimental featuers">' . ucfirst($new_experimental) . ' Experimental Features</a>';

  echo ' | ';

  echo '<a href="settings.php?reset=1">Reset Calculator</a>';

#  echo "Wiz: " . $dominion['attacker']['military']['Wizards']['trained'];  echo "AM: " . $dominion['attacker']['military']['ArchMages']['trained'];
  echo '</p>';

?>

</div>

<div class="Battle-Report">
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST' and $dominion['attacker']['op']['net'] > 0 and $dominion['defender']['dp']['net'] > 0)
{
  require_once('output_stats.php');
}
?>
</div>

<div class="Attacker-Military">

    <table class="military">
      <thead>
        <tr>
          <th>Unit</th>
          <th>Count</th>
          <th>OP</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $dominion['attacker']['military']['unit1']['name']; ?></td>
          <td><input type="number" name="attacker_military_unit1_trained" value="<?php echo max($dominion['attacker']['military']['unit1']['trained'], $dominion['attacker']['military']['unit1']['available']); ?>" step='1' placeholder='0' min='0' /></td>
          <td><?php echo number_format($dominion['attacker']['military']['unit1']['available_op'],2); ?></td>
        </tr>
        <tr>
          <td><?php echo $dominion['attacker']['military']['unit2']['name']; ?></td>
          <td><input type="number" name="attacker_military_unit2_trained" value="<?php echo max($dominion['attacker']['military']['unit2']['trained'], $dominion['attacker']['military']['unit2']['available']); ?>" step='1' placeholder='0' min='0' /></td>
          <td><?php echo number_format($dominion['attacker']['military']['unit2']['available_op'],2); ?></td>
        </tr>
        <tr>
          <td><?php echo $dominion['attacker']['military']['unit3']['name']; ?></td>
          <td><input type="number" name="attacker_military_unit3_trained" value="<?php echo max($dominion['attacker']['military']['unit3']['trained'], $dominion['attacker']['military']['unit3']['available']); ?>" step='1' placeholder='0' min='0' /></td>
          <td><?php echo number_format($dominion['attacker']['military']['unit3']['available_op'],2); ?></td>
        </tr>
        <tr>
          <td><?php echo $dominion['attacker']['military']['unit4']['name']; ?></td>
          <td><input type="number" name="attacker_military_unit4_trained" value="<?php echo max($dominion['attacker']['military']['unit4']['trained'], $dominion['attacker']['military']['unit4']['available']); ?>" step='1' placeholder='0' min='0' /></td>
          <td><?php echo number_format($dominion['attacker']['military']['unit4']['available_op'],2); ?></td>
        </tr>
      </tbody>
    </table>

    <input type="submit" class="submit" value="Update">


</div>
<div class="Attacker-Mods">

  <table class="mods">
    <tbody>
      <tr>
        <td>Gryphon Nests: </td>
        <td><input type="number" name="attacker_mods_gryphon_nest" value="<?php echo $dominion['attacker']['buildings']['Gryphon Nest']; ?>" step='1' placeholder='0' min='0' /> acres</td>
      </tr>
      <tr>
        <td>Temples: </td>
        <td><input type="number" name="attacker_mods_temple" value="<?php echo $dominion['attacker']['buildings']['Temple']; ?>" step='1' placeholder='0' min='0' /> acres</td>
      </tr>
      <tr>
        <td>Forges: </td>
        <td><input type="number" name="attacker_mods_castle_forges" value="<?php echo $dominion['attacker']['display']['Forges']; ?>" step='0.01' placeholder='0.00' min='0' />%</td>
      </tr>
      <tr>
        <td>Racial: </td>
        <td><input type="number" name="attacker_mods_racial" value="<?php echo $dominion['attacker']['display']['Racial']; ?>" step='0.01' placeholder='0.00' min='0'/>%</td>
      </tr>
      <tr>
        <td>Morale: </td>
        <td><input type="number" name="attacker_mods_morale" value="<?php
                                                                        if(isset($dominion['attacker']['general']['morale']))
                                                                        {
                                                                            echo $dominion['attacker']['general']['morale'];
                                                                        }
                                                                        else
                                                                        {
                                                                          echo '100';
                                                                        }
                                                                        ?>" step='1' placeholder='100' min='0' max='100' />%</td>
      </tr>
      <tr>
        <td>Prestige: </td>
        <td><input type="number" name="attacker_mods_prestige" value="<?php echo $dominion['attacker']['general']['prestige']; ?>" step='1' placeholder='0' min='0'/></td>
      </tr>
      <?php
        if($dominion['attacker']['military']['has_special'] === TRUE)
        {
            echo '<tr class="racial">';

              if($dominion['attacker']['general']['race'] == 'Orc')
              {
                 echo '<td class="guard_towers">Target Guard Towers: </td>';
                 echo '<td class="guard_towers"><input type="number" name="defender_special_guard_towers" value="' . $dominion['defender']['buildings']['Guard Tower'] . "\" step='1' placeholder='0' disabled='disabled' title='Guard Towers are taken from Defender. Change Guard Towers there to be reflected here.'/> acres</td>";
              }
              elseif($dominion['attacker']['general']['race'] == 'Dark Elf')
              {
                 echo '<td class="wizard_guilds">Wizard Guilds: </td>';
                 echo '<td class="wizard_guilds"><input type="number" name="attacker_special_wizard_guilds" value="' . $dominion['attacker']['buildings']['Wizard Guild'] . "\" step='1' placeholder='0'/> acres</td>";
              }
              elseif($dominion['attacker']['general']['race'] == 'Icekin')
              {
                 echo '<td class=" ">WPA: </td>';
                 echo '<td class="wpa"><input type="number" name="attacker_special_wpa" value="' . $dominion['attacker']['general']['WPA'] . "\" step='0.001' placeholder='0.000' min='0'/></td>";
              }
              elseif($dominion['attacker']['general']['race'] == 'Wood Elf')
              {
                 echo '<td class="forest">Forest: </td>';
                 echo '<td class="forest"><input type="number" name="attacker_special_forest" value="' . $dominion['attacker']['land']['Forest'] . "\" step='1' placeholder='0'/> acres</td>";
              }
              else
              {
                // Special ability based on target race or size.
                echo '<td></td>';
                echo '<td></td>';
              }


            echo '</tr>';
          }
        ?>
    </tbody>
  </table>
  <h3>Spell</h3>
  <select name="attacker_spell" id="attacker_spell">
    <option value="none">None</option>
    <?php

      foreach($scribes['spells'] as $spell)
      {
        if($spell['name'] == $dominion['attacker']['op']['mods']['spell']['name'])
        {
            $selected = 'selected="selected"';
        }

        if($spell['op'] > 0)
        {
          echo '<option value="' . $spell['name'] .'" ' . $selected . '>' . $spell['name'] . ' (+'. $spell['op']*100 . '% OP)</option>';
        }
        elseif($spell['name'] == 'Unholy Ghost')
        {
          echo '<option value="' . $spell['name'] .'" ' . $selected . '>' . $spell['name'] . ' (no draftees)</option>';
        }
        unset($selected);
      }

    ?>
  </select>

  <input type="submit" class="submit" value="Update">

  </div>
  <div class="Attacker-Calcs">

    <table class="output">
      <tbody>
        <tr>
          <td>Attacker:</td>
          <td><?php echo $dominion['attacker']['general']['name']; ?></td>
        </tr>
        <tr>
          <td>Race:</td>
          <td><?php
          echo '<select name="attacker_race">';
          foreach($scribes['races'] as $race)
          {
            if($race['name'] == $dominion['attacker']['general']['race'])
            {
              echo '<option value="' . $race['name'] . '" selected="selected">' . $race['name'] . '</option>';
            }
            else
            {
              echo '<option value="' . $race['name'] . '">' . $race['name'] . '</option>';
            }
          }
          echo '</select>';
          ?></td>
        </tr>
        <tr>
          <td>Size:</td>
          <td><input type="number" name="attacker_land" value="<?php echo $dominion['attacker']['general']['land']; ?>" step='1' placeholder='0' min='0' /></td>
        </tr>
        <tr>
          <td>Raw:</td>
          <td><?php echo number_format($dominion['attacker']['op']['raw'],2); ?> OP</td>
        </tr>
        <tr>
          <td>Mods:</td>
          <td><?php echo round($dominion['attacker']['op']['modifier']*100, ROUNDING_PRECISION); ?>%</td>
        </tr>
        <?php
        if($dominion['attacker']['morale_debuff'] < 1)
        {
          echo '<tr>';
          echo '<td>Morale: </td>';
          echo '<td>-' . (1-$dominion['attacker']['morale_debuff'])*100 . '%</td>';
          echo '<td>';
        }
        ?>
        <tr>
          <td>Net OP:</td>
          <td><?php echo number_format($dominion['attacker']['op']['net'],2); ?> OP</td>
        </tr>
        <tr>
          <td>Temples:</td>
          <td>-<?php echo round($dominion['attacker']['op']['mods']['Temple']*100, ROUNDING_PRECISION); ?>% DP mods</td>
        </tr>
      </tbody>
    </table>

  <textarea type="text" id="copy_output_attacker" class="copy_output">
  <?php
  echo "```Attacker:\t" . $dominion['attacker']['general']['name'] . "\n";
  echo "Race:\t" . $dominion['attacker']['general']['race'] . "\n";
  echo "Size:\t" . $dominion['attacker']['general']['land'] . "\n";
  echo "Raw OP:\t" . number_format($dominion['attacker']['op']['raw'],2) . "\n";
  echo "OP mods:\t". round($dominion['attacker']['op']['modifier']*100,ROUNDING_PRECISION) . "%\n";
  echo "Net OP:\t" . number_format($dominion['attacker']['op']['net'],2) . "\n";
  if($dominion['attacker']['morale_debuff'] < 1)
  {
    echo "Morale:\t-".(1-$dominion['defender']['morale_debuff'])*100 . "%\n";
  }
  echo "Temples:\t-" . round($dominion['attacker']['op']['mods']['Temple']*100,ROUNDING_PRECISION) . "% DP mods```";
  ?>
  </textarea>
  <button onclick='copyOutput("copy_output_attacker")' class="copy_output attacker" title="Copy attacker to clipboard for easy pasting, including ``` for Discord formatting.">Copy Attacker</button>

    <input type="submit" class="submit" value="Update">


</div>

<div class="Defender-Military">

  <table class="military">
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
        <td><input type="number" name="defender_military_draftees" value="<?php echo $dominion['defender']['military']['draftees']['trained']; ?>" step='1' placeholder='0' min='0' /></td>
        <td>&mdash;</td>
        <td><input type="number" name="defender_military_draftees_at_home" value="<?php echo $dominion['defender']['military']['draftees']['home']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><?php echo number_format($dominion['defender']['draftee_dp'],2); ?></td>
      </tr>
      <tr>
        <td>Forest Havens</td>
        <td><input type="number" name="defender_military_forest_havens" value="<?php echo $dominion['defender']['buildings']['Forest Haven']; ?>" step='1' placeholder='0' min='0' /></td>
        <td>&mdash;</td>
        <td>&mdash;</td>
        <td><?php echo number_format($dominion['defender']['forest_havens_dp']); ?></td>
      </tr>
      <tr>
        <td><?php echo $dominion['defender']['military']['unit1']['name']; ?></td>
        <td><input type="number" name="defender_military_unit1_trained" value="<?php echo $dominion['defender']['military']['unit1']['trained']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><input type="number" name="defender_military_unit1_returning" value="<?php echo $dominion['defender']['military']['unit1']['returning']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><input type="number" name="defender_military_unit1_at_home" value="<?php echo $dominion['defender']['military']['unit1']['home']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><?php echo number_format($dominion['defender']['military']['unit1']['available_dp'],2); ?></td>
      </tr>
      <tr>
        <td><?php echo $dominion['defender']['military']['unit2']['name']; ?></td>
        <td><input type="number" name="defender_military_unit2_trained" value="<?php echo $dominion['defender']['military']['unit2']['trained']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><input type="number" name="defender_military_unit2_returning" value="<?php echo $dominion['defender']['military']['unit2']['returning']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><input type="number" name="defender_military_unit2_at_home" value="<?php echo $dominion['defender']['military']['unit2']['home']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><?php echo number_format($dominion['defender']['military']['unit2']['available_dp'],2); ?></td>
      </tr>
      <tr>
        <td><?php echo $dominion['defender']['military']['unit3']['name']; ?></td>
        <td><input type="number" name="defender_military_unit3_trained" value="<?php echo $dominion['defender']['military']['unit3']['trained']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><input type="number" name="defender_military_unit3_returning" value="<?php echo $dominion['defender']['military']['unit3']['returning']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><input type="number" name="defender_military_unit3_at_home" value="<?php echo $dominion['defender']['military']['unit3']['home']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><?php echo number_format($dominion['defender']['military']['unit3']['available_dp'],2); ?></td>
      </tr>
      <tr>
        <td><?php echo $dominion['defender']['military']['unit4']['name']; ?></td>
        <td><input type="number" name="defender_military_unit4_trained" value="<?php echo $dominion['defender']['military']['unit4']['trained']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><input type="number" name="defender_military_unit4_returning" value="<?php echo $dominion['defender']['military']['unit4']['returning']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><input type="number" name="defender_military_unit4_at_home" value="<?php echo $dominion['defender']['military']['unit4']['home']; ?>" step='1' placeholder='0' min='0' /></td>
        <td><?php echo number_format($dominion['defender']['military']['unit4']['available_dp'],2); ?></td>
      </tr>
    </tbody>
  </table>

  <input type="submit" class="submit" value="Update">

</div>

<div class="Defender-Mods">

  <table class="mods">
    <tbody>
      <tr>
        <td>Guard Towers: </td>
        <td><input type="number" name="defender_mods_guard_towers" value="<?php echo $dominion['defender']['buildings']['Guard Tower']; ?>" step='1' placeholder='0' min='0' /> acres</td>
      </tr>
      <tr>
        <td>Walls: </td>
        <td><input type="number" name="defender_mods_castle_walls" value="<?php echo $dominion['defender']['display']['Walls']; ?>" step='0.01' placeholder='0.00' min='0' />%</td>
      </tr>
      <tr>
        <td>Racial: </td>
        <td><input type="number" name="defender_mods_racial" value="<?php echo $dominion['defender']['display']['Racial']; ?>" step='0.01' placeholder='0.00' min='0' />%</td>
      </tr>
      <tr>
        <td>Morale: </td>
        <td><input type="number" name="defender_mods_morale" value="<?php
                                                                        if(isset($dominion['defender']['general']['morale']))
                                                                        {
                                                                            echo $dominion['defender']['general']['morale'];
                                                                        }
                                                                        else
                                                                        {
                                                                          echo '100';
                                                                        }
                                                                        ?>" step='1' placeholder='100' min='0' max='100' />%</td>
      </tr>
      <?php
        if($dominion['defender']['military']['has_special'] === TRUE)
        {
            echo '<tr class="racial">';

              if($dominion['defender']['general']['race'] == 'Orc')
              {
                 echo '<td class="prestige">Prestige: </td>';
                 echo '<td class="prestige"><input type="number" name="defender_special_prestige" value="' . $dominion['defender']['general']['prestige'] . "\" step='1' placeholder='0'/></td>";
              }
              elseif($dominion['defender']['general']['race'] == 'Dark Elf')
              {
                 echo '<td class="wizard_guilds">Wizard Guilds: </td>';
                 echo '<td class="wizard_guilds"><input type="number" name="defender_special_wizard_guilds" value="' . $dominion['defender']['buildings']['Wizard Guild'] . "\" step='1' placeholder='0'/> acres</td>";
              }
              elseif($dominion['defender']['general']['race'] == 'Icekin')
              {
                 echo '<td class="mountains">Mountains: </td>';
                 echo '<td class="mountains"><input type="number" name="defender_special_mountains" value="' . $dominion['defender']['land']['Mountains'] . "\" step='1' placeholder='0'/> acres</td>";
              }
              elseif($dominion['defender']['general']['race'] == 'Gnome')
              {
                 echo '<td class="mountains">Mountains: </td>';
                 echo '<td class="mountains"><input type="number" name="defender_special_mountains" value="' . $dominion['defender']['land']['Mountains'] . "\" step='1' placeholder='0'/> acres</td>";
              }
              elseif($dominion['defender']['general']['race'] == 'Sylvan')
              {
                 echo '<td class="forest">Forest: </td>';
                 echo '<td class="forest"><input type="number" name="defender_special_forest" value="' . $dominion['defender']['land']['Forest'] . "\" step='1' placeholder='0'/> acres</td>";
              }
              elseif($dominion['defender']['general']['race'] == 'Wood Elf')
              {
                 echo '<td class="forest">Forest: </td>';
                 echo '<td class="forest"><input type="number" name="defender_special_forest" value="' . $dominion['defender']['land']['Forest'] . "\" step='1' placeholder='0'/> acres</td>";
              }
              elseif($dominion['defender']['general']['race'] == 'Nox')
              {
                 echo '<td class="swamp">Swamp: </td>';
                 echo '<td class="swamp"><input type="number" name="defender_special_swamp_buildings" value="' . $dominion['defender']['land']['Swamp']['buildings'] . "\" step='1' placeholder='0'/> acres</td>";
              }
              elseif($dominion['defender']['general']['race'] == 'Troll')
              {
                if($dominion['attacker']['general']['race'] == 'Goblin' OR $dominion['attacker']['general']['race'] == 'Kobold' OR $dominion['attacker']['general']['race'] == 'Wood Elf')
                {
                    echo '<td class="basher">Basher: </td>';
                    echo '<td class="basher">+1 DP</td>';
                }
              }
              else
              {
                echo '<td>Special:</td>';
                echo '<td>ERROR?</td>';
              }

            echo '</tr>';
          }
        ?>
    </tbody>
  </table>
  <h3>Spell</h3>
  <select name="defender_spell" id="defender_spell">
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
    <input type="submit" class="submit" value="Update">

</div>
<div class="Defender-Calcs">

  <table class="output">
    <tbody>
      <tr>
        <td>Target:</td>
        <td><?php echo $dominion['defender']['general']['name']; ?></td>
      </tr>
      <tr>
        <td>Race:</td>
        <td><?php

        echo '<select name="defender_race">';
        foreach($scribes['races'] as $race)
        {
          if($race['name'] == $dominion['defender']['general']['race'])
          {
            echo '<option value="' . $race['name'] . '" selected="selected">' . $race['name'] . '</option>';
          }
          else
          {
            echo '<option value="' . $race['name'] . '">' . $race['name'] . '</option>';
          }
        }
        echo '</select>';
        ?></td>
      </tr>
      <tr>
        <td>Size:</td>
        <td><input type="number" name="defender_land" value="<?php echo $dominion['defender']['general']['land']; ?>" step='1' placeholder='0' min='0' /></td>
      </tr>
      <tr>
        <td>Raw:</td>
        <td><?php echo number_format($dominion['defender']['dp']['raw'],2); ?> DP</td>
      </tr>
      <tr>
        <td>Mods:</td>
        <td><?php echo round($dominion['defender']['dp']['modifier']*100,ROUNDING_PRECISION); ?>%<?php # Only show if attacker has temple mods.
                                                                            if($dominion['attacker']['op']['mods']['Temple'] > 0)
                                                                            {
                                                                              echo '&nbsp;(-'.round($dominion['attacker']['op']['mods']['Temple']*100,ROUNDING_PRECISION) . '%)';
                                                                            }
                                                                      ?></td>
      </tr>
      <tr>
        <td>Net mods:</td>
        <td><?php
                  echo round($dominion['defender']['dp']['modifier_net']*100,ROUNDING_PRECISION) . '%';

                  if($dominion['defender']['morale_debuff'] < 1)
                  {
                    echo '&nbsp;(-'.(1-$dominion['defender']['morale_debuff'])*100 . '% morale)';
                  }
              ?></td>
      <tr>
        <td>Net DP:</td>
        <td><?php echo number_format($dominion['defender']['dp']['net'],2); ?> DP</td>
      </tr>
    </tbody>
  </table>

<textarea type="text" id="copy_output_defender" class="copy_output">
<?php
  echo "```Target:\t" . $dominion['defender']['general']['name'] . "\n";
  echo "Race:\t" . $dominion['defender']['general']['race'] . "\n";
  echo "Size:\t" . $dominion['defender']['general']['land'] . "\n";
  echo "Raw DP:\t" . number_format($dominion['defender']['dp']['raw'],2) . "\n";
  echo "DP mods:\t". round($dominion['defender']['dp']['modifier']*100,ROUNDING_PRECISION) . "%\n";
  echo "Net mods:\t". round($dominion['defender']['dp']['modifier_net']*100, ROUNDING_PRECISION) . "%\n";
  if($dominion['defender']['morale_debuff'] < 1)
  {
    echo "Morale:\t-".(1-$dominion['defender']['morale_debuff'])*100 . "%\n";
  }
  echo "Net DP:\t" . number_format($dominion['defender']['dp']['net'],2)."```";
?>
</textarea>
<button onclick='copyOutput("copy_output_defender")' class="copy_output defender" title="Copy target to clipboard for easy pasting, including ``` for Discord formatting.">Copy Target</button>
<input type="submit" class="submit" value="Update">
</form>
</div>

<div class="Attacker-Ops">
  <form action="./" method="post" id="ops" name="ops">
  <input type="hidden" name="origin" value="ops" />

  <h1>Attacker Ops</h1>

  <h2>Ops Center</h2>
  <textarea type="text" name="attacker_op_center" id="attacker_op_center" class="ops"><?php echo $_POST['attacker_op_center']; ?></textarea>

  <h2>Clear Sight or Status Screen (DEPRECATED)</h2>
  <textarea type="text" name="attacker_clearsight" id="attacker_clearsight" class="ops"><?php echo $_POST['attacker_clearsight']; ?></textarea>

  <h2>Survey (DEPRECATED)</h2>
  <textarea type="text" name="attacker_survey" id="attacker_survey" class="ops"><?php echo $_POST['attacker_survey']; ?></textarea>

  <h2>Castle (DEPRECATED)</h2>
  <textarea type="text" name="attacker_castle" id="attacker_castle" class="ops"><?php echo $_POST['attacker_castle']; ?></textarea>

  <h2>Barracks (home/training) &mdash; Units in Training added if CS available (DEPRECATED)</h2>
  <textarea type="text" name="attacker_barracks_home" id="attacker_barracks_home" class="ops"><?php echo $_POST['attacker_barracks_home']; ?></textarea>

  <h2>Barracks (returning) &mdash; Ignored (DEPRECATED)</h2>
  <textarea type="text" name="attacker_barracks_returning" id="attacker_barracks" class="ops"><?php echo $_POST['attacker_barracks_returning']; ?></textarea>

  <h2>Land (DEPRECATED)</h2>
  <textarea type="text" name="attacker_land" id="attacker_land" class="ops" disabled="disabled"></textarea>

  <input type="hidden" name="origin" value="attacker-ops"/>
  <p>&nbsp;</p>
  <input type="submit" class="submit" value="Process Ops">

</div>

<div class="Defender-Ops">
  <h1>Defender Ops</h1>

  <h2>Ops Center</h2>
  <textarea type="text" name="defender_op_center" id="defender_op_center" class="ops"><?php echo $_POST['defender_op_center']; ?></textarea>

  <h2>Clear Sight or Status Screen (DEPRECATED)</h2>
  <textarea type="text" name="defender_clearsight" id="defender_clearsight" class="ops"><?php echo $_POST['defender_clearsight']; ?></textarea>

  <h2>Survey (DEPRECATED)</h2>
  <textarea type="text" name="defender_survey" id="defender_survey" class="ops"><?php echo $_POST['defender_survey']; ?></textarea>

  <h2>Castle (DEPRECATED)</h2>
  <textarea type="text" name="defender_castle" id="defender_castle" class="ops"><?php echo $_POST['defender_castle']; ?></textarea>

  <h2>Barracks (home/training) (DEPRECATED)</h2>
  <textarea type="text" name="defender_barracks_home" id="defender_barracks_home" class="ops"><?php echo $_POST['defender_barracks_home']; ?></textarea>

  <h2>Barracks (returning) (DEPRECATED)</h2>
  <textarea type="text" name="defender_barracks_returning" id="defender_barracks_returning" class="ops"><?php echo $_POST['defender_barracks_returning']; ?></textarea>

  <h2>Land (DEPRECATED)</h2>
  <textarea type="text" name="defender_land" id="defender_land" class="ops" disabled="disabled"></textarea>


  <input type="hidden" name="origin" value="defender-ops"/>
  <p>&nbsp;</p>
  <input type="submit" class="submit" value="Process Ops">

  </form>
</div>

<div class="Ops-Samples">
<h1><a id="Ops-Samples">Ops Samples</a></h1>
<p>Unsure how or what to copy-paste? Take a look at the sample images below.</p>
<p>Clear Sight supports both Clear Sight and copying your own <a href="https://beta.opendominion.net/dominion/status" rel="nofollow" target="_new">Status page</a>.</p>
<table class="ops_samples">
  <thead>
    <tr>
      <th>Op</th>
      <th>Screenshot</th>
      <th>Text</th>
    </tr>
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

<p>Icons made by <a href="https://www.freepik.com/" title="Freepik" rel="nofollow">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon" rel="nofollow">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" rel="nofollow" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></p>

<?php

if($environment == 'local')
{
  echo  '<pre>';
  print_r($dominion);
  echo '</pre>';
}
?>
<section class="cookies">
<h1>Cookie and Privacy</h1>
<p>This website uses cookies to store your preferences (stylesheet and enable/disable experimental features).</p>
<p>The cookies expire after 30 days. Changing a setting resets the 30-day counter.</p>
<p>You can use your browser's settings to remove cookies before the 30 days are over. See your browser's manual for details.</p>
<p>No personal data is collected. Aside from normal server logs, no data is stored on the web server.</p>
<p>The following cookies are used:</p>
<ul>
  <li>"ddc_style": a value of "dark" or "light" used to select a style sheet.</li>
  <li>"ddc_experimental": a value of "enable" or "disable" used to determine whether to show experimental features.</li>
</ul>
</section>

</div>
</body>
</html>
