<?php


echo '<h1>Raw Output</h1><p>For debugging.</p><textarea class="dominion">';
print_r($dominion);
echo '</textarea>';

$data['dominion'] = base64_encode(serialize($dominion));
$data['calculation_id'] = hash('tiger192,4', $data['dominion']);

if($_SERVER['REQUEST_METHOD'] == 'POST')
{

?>

<div class="save-calculation">
  <span style="color:#a00; font-weight:bold; font-size:200%;">SAVE/LOAD FUNCTION NOT YET IMPLEMENTED</span>
  <h1>Save Calculation</h1>
  <p>Save your calculation to easily share it with people in your realm. You are not allowed to share a calculation with someone outside of your realm, without permission from both the Attacker and Defender dominions.</p>
  <p>Calculations are saved indefinitely and may be removed at any time. Only the calculation ID, calculation data, and a timestamp are stored.</p>
  <p>Calculation ID: <?php echo $data['calculation_id']; ?></p>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="save" name="save">

    <input type="hidden" name="calculation_id" value="<?php echo $data['calculation_id']; ?>"/>
    <input type="hidden" name="calculation_dominion" value="<?php echo $data['dominion']; ?>"/>
    <input type="hidden" name="origin" value="save-calculation"/>
    <input type="submit" class="submit" value="Save Calculation">

  </form>

  <h1>Load Calculation</h1>
  <p>If you want to load a saved calculation, enter the calculation ID below and click Load Calculation.</p>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="load" name="load">

    <input type="text" name="calculation_id" value="" placeholder="Calculation ID" />
    <input type="hidden" name="origin" value="load-calculation"/>
    <input type="submit" class="submit" value="Load Calculation">

  </form> 

</div>
<?php
}
