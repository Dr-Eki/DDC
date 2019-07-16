## Dreki's Dominion Calculator

This is an invasion calculator to supplement [OpenDominion](https://github.com/WaveHack/OpenDominion). Players use it to calculate the defensive power (DP) and offensive power (OP) of themselves or other players.

# How it works

User is on index.php and inputs ops in the Ops textareas (Defender-Ops and Attacker-Ops) or manually enters units in the Military and Mods areas (Defender-Military, Defender-Mods, Attacker-Military, and Attacker-Mods).

If index.php is posted to, calculator.php is used to calculate the input.

calculator.php only calculates the DP for the Defender but within it includes calculator_attacker.php which calculates the OP for the Attacker.

Everything is stored in the array $dominion. It is split into two major subarrays (dimensions):

$dominion['defender'] and $dominion['attacker], each containing the military, buildings, castle improvements, spells, and modifiers of the defender and attacker.
