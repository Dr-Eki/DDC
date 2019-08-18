<div class="output_stats">
<?php

  $military_ratio = $dominion['attacker']['op']['net'] / $dominion['defender']['dp']['net'];
  $military_ratio_inverse = $dominion['defender']['dp']['net'] / $dominion['attacker']['op']['net'];

  $attackers_margin = ($dominion['attacker']['op']['net'] - $dominion['defender']['dp']['net']);

  if($dominion['attacker']['op']['net'] > $dominion['defender']['dp']['net'])
  {
    $invasion_success = TRUE;
    $div_sub_class = 'success';
  }
  else
  {
    $div_sub_class = 'bounce';
  }

  echo '<div class="output_stats '. $div_sub_class .'">';

  if($invasion_success === TRUE)
  {


    # Construction cost.
    $construction['platinum_cost'] = 850+(floor($dominion['attacker']['general']['land'] + $acresLost * 1.5)-250)*1.53;
    $construction['lumber_cost'] = 88+(floor($dominion['attacker']['general']['land'] + $acresLost * 1.5)-250)*0.53;

    $range = $land_ratio*100;

    $rangeMultiplier = ($range / 100);
    $landGrabRatio = 1;
    // todo: if mutual war, $landGrabRatio = 1.2
    // todo: if non-mutual war, $landGrabRatio = 1.15
    // todo: if peace, $landGrabRatio = 0.9
    $bonusLandRatio = 1.5;
    $attackerLandWithRatioModifier = (/* $this->landCalculator->getTotalLand($dominion) */ $dominion['attacker']['general']['land'] * $landGrabRatio);
    if ($range < 55) {
        $acresLost = (0.304 * ($rangeMultiplier ^ 2) - 0.227 * $rangeMultiplier + 0.048) * $attackerLandWithRatioModifier;
    } elseif ($range < 75) {
        $acresLost = (0.154 * $rangeMultiplier - 0.069) * $attackerLandWithRatioModifier;
    } else {
        $acresLost = (0.129 * $rangeMultiplier - 0.048) * $attackerLandWithRatioModifier;
    }

    $acresLost = (int)max(floor($acresLost*0.75), 10);

    echo '<p class="outcome"><span class="dominion_name">' . $dominion['attacker']['general']['name'] . '</span> invades <span class="dominion_name">' . $dominion['defender']['general']['name'] . '</span> successfully and conquers ' . number_format($acresLost) . ' acres. ';
    echo '<span class="dominion_name">' . $dominion['attacker']['general']['name'] . '</span> also gains ' . number_format(floor($acresLost * 1)) .' for a total of ' . number_format(floor($acresLost * 2)) . ' acres.</p>';

  }
  else
  {
    if($military_ratio <= 0.85)
    {
      echo '<p class="outcome"><span class="dominion_name">' . $dominion['defender']['general']['name'] . '</span> overwhelms and easily fends off the forces from <span class="dominion_name">' . $dominion['attacker']['general']['name'] . '</span>.</p>';
    }
    else
    {
      echo '<p class="outcome"><span class="dominion_name">' . $dominion['defender']['general']['name'] . '</span> fends off the attack from <span class="dominion_name">' . $dominion['attacker']['general']['name'] . '</span>.</p>';
    }
  }


  echo '<table class="output_stats">';
  echo '<tr>';
  echo '<td>';

  echo '<h1>Battle Report</h1>';

  echo '<table class="battle_report">';

  if($military_ratio <= 0.85)
  {
    echo '<tr>';
    echo '<td colspan="2" class="overwhelmed">The attacker is overwhelmed by the defender, causing additional offensive causalties.</td>';
    echo '</tr>';
  }


  echo '<tr>';
  echo '<td>Offensive power: </td><td>'. number_format($dominion['attacker']['op']['net']) . ' mod OP</td>';
  echo '</tr>';
  echo '<tr>';
  echo '<td>Defensive power: </td><td>'. number_format($dominion['defender']['dp']['net']) . ' net mod DP</td>';
  echo '</tr>';

  echo "<td>OP margin:</td><td>" . number_format($attackers_margin) . '. ';
  if($attackers_margin > 0 AND $military_ratio < 1.005)
  {
    echo '⚠️ Very narrow margin!</td>';
  }
  else
  {
    '</td>';
  }
  echo '</tr>';

  echo '<tr>';
  echo '<td>OP/DP ratio: </td><td>' . sprintf('%0.2f',round($military_ratio,4)*100) . '%</td>';
  echo '</tr>';
  echo '<tr>';

  echo '<tr>';
  echo '<td>Land ratio: </td><td>' . sprintf('%0.2f',round($land_ratio,4)*100) . '% ('. number_format($dominion['defender']['general']['land']) .'/'. number_format($dominion['attacker']['general']['land']) . ' acres)</td>';
  echo '</tr>';


  if($invasion_success === TRUE)
  {
    echo '<tr>';
    echo '<td>Land conquered: </td><td>' . number_format($acresLost) . ' acres.</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td>Land generated: </td><td>' . number_format(floor($acresLost * 0.5)) . ' acres.</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td>Land gained: </td><td>' . number_format(floor($acresLost * 1.5)) . ' acres.</td>';
    echo '</tr>';
  }

  if($range >= 75 and $invasion_success === TRUE)
  {
    $prestige_gain = floor($dominion['defender']['general']['prestige'] * 0.05 + 20);
    $prestige_loss = floor($dominion['defender']['general']['prestige'] * 0.05);   
    echo '<tr>';   
    echo '<td>Attacker prestige gain: </td><td>' . $prestige_gain . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td>Defender prestige loss: </td><td>' . $prestige_loss . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td>Construction cost: </td><td>' . number_format(floor(($construction['platinum_cost'] * $acresLost * 1.5)/2)) . ' plat,<br>' . number_format(floor(($construction['lumber_cost'] * $acresLost * 1.5)/2)) . ' lumber</td>';
    echo '</tr>';

  }
  elseif($invasion_success === TRUE and $range < 75)
  {
    echo '<tr>';
    echo '<td>Construction cost: </td><td>' . number_format(floor(($construction['platinum_cost'] * $acresLost * 1.5))) . ' plat,<br>' . number_format(floor(($construction['lumber_cost'] * $acresLost * 1.5))) . ' lumber</td>';
    echo '</tr>';
  }



  echo '</table>';
  echo '</td>';
  echo '<td class="casualties">';
  echo '<h1>Casualties</h1>';
  echo '<p>Casualties are calculated without modifications.</p>';
  echo '<h2>Attacker</h2>';

  echo '<table class="offensive_casualties">';

  # Estimate defensive casualties ratio.
  $offensive_casualties_ratio = 0.085 * $military_ratio_inverse;

  # Figure out which units participate in battle.
  if($dominion['attacker']['military']['unit1']['op'] > 0)
  {
    echo '<tr>';
    echo '<td>' . $dominion['attacker']['military']['unit1']['name'] . ': </td><td>' . number_format(floor($dominion['attacker']['military']['unit1']['available'] * $offensive_casualties_ratio)) . '</td>';
    echo '</tr>';
  }
  if($dominion['attacker']['military']['unit2']['op'] > 0)
  {
    echo '<tr>';
    echo '<td>' . $dominion['attacker']['military']['unit2']['name'] . ': </td><td>' . number_format(floor($dominion['attacker']['military']['unit2']['available'] * $offensive_casualties_ratio)) . '</td>';
    echo '</tr>';
  }
  if($dominion['attacker']['military']['unit3']['op'] > 0)
  {
    echo '<tr>';
    echo '<td>' . $dominion['attacker']['military']['unit3']['name'] . ': </td><td>' . number_format(floor($dominion['attacker']['military']['unit3']['available'] * $offensive_casualties_ratio)) . '</td>';
    echo '</tr>';
  }
  if($dominion['attacker']['military']['unit4']['op'] > 0)
  {
    echo '<tr>';
    echo '<td>' . $dominion['attacker']['military']['unit4']['name'] . ': </td><td>' . number_format(floor($dominion['attacker']['military']['unit4']['available'] * $offensive_casualties_ratio)) . '</td>';
    echo '</tr>';
  }
  echo '</table>';

  echo '<p>&nbsp;</p>';
  echo '<h2>Target</h2>';

  echo '<table class="defensive_casualties">';

  # Estimate defensive casualties ratio.
  $defensive_casualties_ratio = 0.045;

  # Figure out which units participate in battle.
  if($dominion['defender']['military']['draftees'] > 0)
  {
    echo '<tr>';
    echo '<td>Draftees: </td><td>' . number_format(floor($dominion['defender']['military']['draftees']['trained'] * $defensive_casualties_ratio)) . '</td>';
    echo '</tr>';
  }
  if($dominion['defender']['military']['unit1']['dp'] > 0)
  {
    echo '<tr>';
    echo '<td>' . $dominion['defender']['military']['unit1']['name'] . ': </td><td>' . number_format(floor($dominion['defender']['military']['unit1']['available'] * $defensive_casualties_ratio)) . '</td>';
    echo '</tr>';
  }
  if($dominion['defender']['military']['unit2']['dp'] > 0)
  {
    echo '<tr>';
    echo '<td>' . $dominion['defender']['military']['unit2']['name'] . ': </td><td>' . number_format(floor($dominion['defender']['military']['unit2']['available'] * $defensive_casualties_ratio)) . '</td>';
    echo '</tr>';
  }
  if($dominion['defender']['military']['unit3']['dp'] > 0)
  {
    echo '<tr>';
    echo '<td>' . $dominion['defender']['military']['unit3']['name'] . ': </td><td>' . number_format(floor($dominion['defender']['military']['unit3']['available'] * $defensive_casualties_ratio)) . '</td>';
    echo '</tr>';
  }
  if($dominion['defender']['military']['unit4']['dp'] > 0)
  {
    echo '<tr>';
    echo '<td>' . $dominion['defender']['military']['unit4']['name'] . ': </td><td>' . number_format(floor($dominion['defender']['military']['unit4']['available'] * $defensive_casualties_ratio)) . '</td>';
    echo '</tr>';
  }

  echo '</table>';
  echo '</td>';
  echo '<td>';

  if($dominion['attacker']['name'] === $dominion['defender']['name'] and $dominion['attacker']['race'] === $dominion['defender']['race'] and $_COOKIE['ddc_experimental'] == 'enable')
  {
    require_once('functions_max_sendable.php');
    $suicide = calculate_max_sendable($dominion['attacker'], $dominion['defender']);

    echo '<h1>5:4 Calculator (EXPERIMENTAL)</h1>';

    echo '<table class="suicide">';

    echo '<thead>';
    echo '<tr>';
    echo '<th>Unit</th>';
    echo '<th>Attacking</th>';
    echo '<th>Defending</th>';
    echo '</tr>';
    echo '</thead>';

    echo '<tr>';
    echo '<td>Draftees</td>';
    echo '<td>0</td>';
    echo '<td>' . number_format($suicide['defender']['military']['draftees']['defending']) . '</td>';
    echo '</tr>';


    echo '<tr>';
    echo '<td>' . $suicide['attacker']['military']['unit1']['name'] . '</td>';
    echo '<td>' . number_format($suicide['attacker']['military']['unit1']['attacking']) . '</td>';
    echo '<td>' . number_format($suicide['defender']['military']['unit1']['defending']) . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td>' . $suicide['attacker']['military']['unit2']['name'] . '</td>';
    echo '<td>' . number_format($suicide['attacker']['military']['unit2']['attacking']) . '</td>';
    echo '<td>' . number_format($suicide['defender']['military']['unit2']['defending']) . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td>' . $suicide['attacker']['military']['unit3']['name'] . '</td>';
    echo '<td>' . number_format($suicide['attacker']['military']['unit3']['attacking']) . '</td>';
    echo '<td>' . number_format($suicide['defender']['military']['unit3']['defending']) . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td>' . $suicide['attacker']['military']['unit4']['name'] . '</td>';
    echo '<td>' . number_format($suicide['attacker']['military']['unit4']['attacking']) . '</td>';
    echo '<td>' . number_format($suicide['defender']['military']['unit4']['defending']) . '</td>';
    echo '</tr>';
    echo '</table>';

    echo '<ul>';
    echo '<li> Method: ' . $suicide['method'] . '.' . $suicide['sub_method'] . '</li>';
    echo '<li>Iteration: ' . $suicide['iteration'] . '</li>';
    echo '<li>Ratio: ' . $suicide['ratio'] . '</li>';
    echo '<li>OP: ' . number_format($suicide['op']) . '</li>';
    echo '<li>DP: ' . number_format($suicide['dp']) . '</li>';
    echo '</ul>';

    echo '<pre>';
  #  print_r($suicide);
    echo '</pre>';
  }
  else
  {
      echo '<p>[Space reserved for upcoming experimental feature.]</p>';
  }

  echo '</td>';
  echo '</tr>';
  echo '</table>';

echo '</div>';
echo '</div>';