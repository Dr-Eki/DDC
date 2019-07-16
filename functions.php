<?php

/**
 * Clamps $current number between $min and $max.
 *
 * (tfw no generics)
 *
 * @param int|float $current
 * @param int|float $min
 * @param int|float $max
 * @return int|float
 */
function clamp($current, $min, $max) {
    return max($min, min($max, $current));
}

/**
 * Finds a substring between two strings
 * @param  string $string The string to be searched
 * @param  string $start The start of the desired substring
 * @param  string $end The end of the desired substring
 * @param  bool   $greedy Use last instance of`$end` (default: false)
 * @return string
 */
function find_between(string $string, string $start, string $end, bool $greedy = false) {
    $start = preg_quote($start);
    $end   = preg_quote($end);
 
    $format = '/(%s)(.*';
    if (!$greedy) $format .= '?';
    $format .= ')(%s)/';
 
    $pattern = sprintf($format, $start, $end);
    preg_match($pattern, $string, $matches);
 
    return $matches[2];
}

/**
 * Takes pasted survey and returns an array 
 * containing built buildings and barren land.
 *
 * @param str $survey
 * @return array
 */
function parse_clear_sight($clearsight) {
    $clearsight = explode("\n", trim($clearsight));
    foreach($clearsight as $line)
    {

        $line = trim($line);
        #$i++; echo '<pre>Line: ' . $i . ':' .$line.'</pre>';
        # Get name if CS is a Status Screen.
        if(substr($line, 0, strlen("Status Screen (")) == 'Status Screen (')
        {
            preg_match('#\((.*?)\)#', $line, $name);
            $dominion['clearsight']['name'] = trim($name[1]);
            unset($name);
            $is_status_screen = TRUE;
        }
        # Get name if CS is a CS.
        elseif(substr($line, 0, strlen("The Dominion of ")) == 'The Dominion of ')
        {
            $dominion['clearsight']['name'] = trim(str_replace('The Dominion of ','',$line));
        }
        # Get rule name.
        elseif(substr($line, 0, strlen("Ruler:")) == 'Ruler:')
        {
            $ruler = explode(':', $line);
            $dominion['clearsight']['ruler'] = trim(str_replace('Ruler: ','',str_replace("\t",'',trim($ruler[1]))));
        }
        # Get race.
        elseif(substr($line, 0, strlen("Race:")) == 'Race:')
        {
            $race = explode(':', $line);
            $dominion['clearsight']['race'] = str_replace('Race: ','',str_replace("\t",'',trim($race[1])));
        }
        # Get land.
        elseif(substr($line, 0, strlen("Land:")) == 'Land:')
        {
            $land = explode(':', $line);
            $dominion['clearsight']['land'] = intval(str_replace(',','',str_replace('Land:','',str_replace("\t",'',trim($land[1])))));
        }
        # Get peasants. - Why?
        elseif(substr($line, 0, strlen("Peasants:")) == 'Peasants:')
        {
            $peasants = explode(':', $line);
            $dominion['clearsight']['peasants'] = intval(str_replace(',','',str_replace('Peasants:','',str_replace("\t",'',trim($peasants[1])))));
        }
        # Get morale.
        elseif(substr($line, 0, strlen("Morale:")) == 'Morale:')
        {
            $morale = explode(':', $line);
            $dominion['clearsight']['morale'] = intval(trim(str_replace('Morale: ','',str_replace("\t",'',str_replace('%','',trim($morale[1]))))));
        }
        # Get prestige.
        elseif(substr($line, 0, strlen("Prestige:")) == 'Prestige:')
        {
            $prestige = explode(':', $line);
            $dominion['clearsight']['prestige'] = intval(str_replace(',','',str_replace('Prestige:','',str_replace("\t",'',trim($prestige[1])))));
        }

        # Now let's prepare the military.
        require('units.php');
        if(substr($line, 0, strlen($scribes['military'][$dominion['clearsight']['race']]['unit1']['name'])) == $scribes['military'][$dominion['clearsight']['race']]['unit1']['name'])
        {
            $dominion['clearsight']['unit1']['trained'] = filter_var(str_replace($scribes['military'][$dominion['clearsight']['race']]['unit1']['name'] . ':    ','',$line),FILTER_SANITIZE_NUMBER_INT);
        }
        elseif(substr($line, 0, strlen($scribes['military'][$dominion['clearsight']['race']]['unit2']['name'])) == $scribes['military'][$dominion['clearsight']['race']]['unit2']['name'])
        {
            $dominion['clearsight']['unit2']['trained'] = filter_var(str_replace($scribes['military'][$dominion['clearsight']['race']]['unit2']['name'] . ':    ','',$line),FILTER_SANITIZE_NUMBER_INT);
        }
        elseif(substr($line, 0, strlen($scribes['military'][$dominion['clearsight']['race']]['unit3']['name'])) == $scribes['military'][$dominion['clearsight']['race']]['unit3']['name'])
        {
            $dominion['clearsight']['unit3']['trained'] = filter_var(str_replace($scribes['military'][$dominion['clearsight']['race']]['unit3']['name'] . ':    ','',$line),FILTER_SANITIZE_NUMBER_INT);
        }
        elseif(substr($line, 0, strlen($scribes['military'][$dominion['clearsight']['race']]['unit4']['name'])) == $scribes['military'][$dominion['clearsight']['race']]['unit4']['name'])
        {
            $dominion['clearsight']['unit4']['trained'] = filter_var(str_replace($scribes['military'][$dominion['clearsight']['race']]['unit4']['name'] . ':    ','',$line),FILTER_SANITIZE_NUMBER_INT);
        }
        elseif(substr($line, 0, strlen('Draftees')) == 'Draftees')
        {
            $dominion['clearsight']['draftees']['trained'] = filter_var(str_replace('Draftees:    ','',$line),FILTER_SANITIZE_NUMBER_INT);
        }
        elseif(substr($line, 0, strlen('Wizards')) == 'Wizards')
        {
            $dominion['clearsight']['Wizards']['trained'] = filter_var(str_replace('Wizards:    ','',$line),FILTER_SANITIZE_NUMBER_INT);
        }
        elseif(substr($line, 0, strlen('ArchMages')) == 'ArchMages')
        {
            $dominion['clearsight']['ArchMages']['trained'] = filter_var(str_replace('ArchMages:    ','',$line),FILTER_SANITIZE_NUMBER_INT);
        }

    }

    return $dominion['clearsight'];
}


/**
 * Takes pasted survey and returns an array 
 * containing built buildings and barren land.
 *
 * @param str $survey
 * @return array
 */
function parse_survey($survey,$size) {
    $survey = trim($survey);
    $survey = explode("\n", $survey);
    $used_land = 0;

    foreach($survey as $building)
    {
        $buildings[] = explode("\t", $building);
    }
    foreach($buildings as $building)
    {

        if(substr(trim($building[0]), 0, strlen("Constructed Buildings Barren Land: ")) == 'Constructed Buildings Barren Land: ')
        {
    #       echo $building[0];
            $building[0] = intval(trim(str_replace(',','',str_replace('Constructed Buildings Barren Land: ','',$building[0]))));
            $dominion['buildings']['Barren'] = $building[0];
            $used_land += intval($building[0]);
        }
        elseif(trim($building[0]) !== 'Building Type')
        {
            $building[0] = trim(str_replace('NYI','',str_replace('PI','',$building[0])));
            $dominion['buildings'][$building[0]] = intval(str_replace(',','',str_replace(' ','',str_replace("\t",'',trim($building[1])))));
            $used_land += intval(str_replace(',','',str_replace(' ','',str_replace("\t",'',trim($building[1])))));
        }
    }


    $dominion['buildings']['Under Construction'] = $size - $used_land;
    
    return $dominion['buildings'];
}


/**
 * Takes pasted castle and returns an array 
 * containing castle improvements.
 *
 * @param str $survey
 * @return array
 */
function parse_castle($castle) {
    $castle = trim($castle);
    $castle = explode("\n", $castle);

    $parts = array('Science','Keep','Towers','Forges','Walls','Harbor');
    foreach($castle as $part)
    {
        $improvements[] = explode("\t", $part);
    }
    foreach($improvements as $part)
    {
        if(in_array(trim($part[0]),$parts))
        {
            $part[0] = trim($part[0]);
            $dominion['castle'][$part[0]] = floatval($part[1])/100;
        }
    }

    return $dominion['castle'];
}



/**
 * Takes pasted Barracks Spy for units 
 * returning and at home and returns 
 * an array with two arrays.
 *
 * @param str $returning
 * @param str $home
 * @param str $training
 * @param str $race
 * @return array
 */
function parse_barracks($returning = NULL, $home = NULL, $race = NULL) {

    require('units.php');

    # If race isn't specified, return Null.
    if($race === NULL)
    {
        return Null;
    }

    # Just ignore these fools.
    $ignored_units = array('Unit','Spies','Wizards','Archmages');

    # If returning is set, 
    if($returning !== NULL)
    {
        $barracks_returning = trim($returning);
        $barracks_returning = explode("\n", $barracks_returning);

        foreach($barracks_returning as $unit)
        {
            $units[] = explode("\t", $unit);
        }
        unset($barracks_returning);
        foreach($units as $unit)
        {

        #   echo '<pre>'; print_r($unit); echo '</pre>';

            if(trim($unit[0]) == $scribes['military'][$race]['unit1']['name'])
            {
                $dominion['military']['unit1']['returning'] = intval(str_replace(',','',str_replace('~','',trim($unit[13]))));           
            }
            elseif(trim($unit[0]) == $scribes['military'][$race]['unit2']['name'])
            {
                $dominion['military']['unit2']['returning'] = intval(str_replace(',','',str_replace('~','',trim($unit[13]))));
            }
            elseif(trim($unit[0]) ==  $scribes['military'][$race]['unit3']['name'])
            {
                $dominion['military']['unit3']['returning'] = intval(str_replace(',','',str_replace('~','',trim($unit[13]))));
            }
            elseif(trim($unit[0]) ==  $scribes['military'][$race]['unit4']['name'])
            {
                $dominion['military']['unit4']['returning'] = intval(str_replace(',','',str_replace('~','',trim($unit[13]))));
            }
        }
    }

    unset($units);

    if($home !== NULL)
    {
        $barracks_home = trim($home);
        $barracks_home = explode("\n", $barracks_home);
        foreach($barracks_home as $unit)
        {
            $units[] = explode("\t", $unit);
        }
        unset($barracks_home);
        foreach($units as $unit)
        {
            if(trim($unit[0]) === 'Draftees')
            {
                $dominion['military']['draftees']['home'] = intval(str_replace(',','',str_replace('~','',trim($unit[2]))));
            }
            elseif(trim($unit[0]) ==  $scribes['military'][$race]['unit1']['name'])
            {
                $dominion['military']['unit1']['home'] = intval(str_replace(',','',str_replace('~','',trim($unit[13]))));
                preg_match('#\((.*?)\)#', $unit[13], $match);
                $dominion['military']['unit1']['training'] = intval(str_replace(',','',trim($match[1])));
                unset($match);
            }
            elseif(trim($unit[0]) ==  $scribes['military'][$race]['unit2']['name'])
            {
                $dominion['military']['unit2']['home'] = intval(str_replace(',','',str_replace('~','',trim($unit[13]))));
                preg_match('#\((.*?)\)#', $unit[13], $match);
                $dominion['military']['unit2']['training'] = intval(str_replace(',','',trim($match[1])));
                unset($match);
            }
            elseif(trim($unit[0]) ==  $scribes['military'][$race]['unit3']['name'])
            {
                $dominion['military']['unit3']['home'] = intval(str_replace(',','',str_replace('~','',trim($unit[13]))));
                preg_match('#\((.*?)\)#', $unit[13], $match);
                $dominion['military']['unit3']['training'] = intval(str_replace(',','',trim($match[1])));
                unset($match);
            }
            elseif(trim($unit[0]) ==  $scribes['military'][$race]['unit4']['name'])
            {
                $dominion['military']['unit4']['home'] = intval(str_replace(',','',str_replace('~','',trim($unit[13]))));
                preg_match('#\((.*?)\)#', $unit[13], $match);
                $dominion['military']['unit4']['training'] = intval(str_replace(',','',trim($match[1])));
                unset($match);
            }

        }
    }

    return $dominion['military'];
}


