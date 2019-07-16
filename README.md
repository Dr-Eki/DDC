## Dreki's Dominion Calculator

This is an invasion calculator to supplement [OpenDominion](https://github.com/WaveHack/OpenDominion). Players use it to calculate the defensive power (DP) and offensive power (OP) of themselves or other players.

# How it works

User is on index.php and inputs ops in the Ops textareas (Defender-Ops and Attacker-Ops) or manually enters units in the Military and Mods areas (Defender-Military, Defender-Mods, Attacker-Military, and Attacker-Mods).

If index.php is posted to, calculator.php is used to calculate the input.

calculator.php only calculates the DP for the Defender ($dominion['defender']) but within it includes calculator_attacker.php which calculates the OP for the Attacker.
