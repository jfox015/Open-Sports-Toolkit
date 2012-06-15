<?php
/**
 *	GENERAL HELPER
 *
 *	This helper includes a general set of functions used by the site to run common
 * 	helper functions such as reslving positon names and numbers, building common
 *	stat queries and display lists and more.
 *
 * 	@author 	All functions are written by Frank Esselink unless otherwise noted.
 */

/**
 *	MAKE INJURY STATUS STRING
 *
 *	Converts standard OOTP injury data (found in the player profile data object and injuries
										in the database) into a human readbale string.
 *
 *	@param	$row		Array 	Array of player data with injuriy fields
 *	@return				String	Injury String text
 *
 * 	@author	Jeff Fox
 *	@since	1.0.2
 */
function makeInjuryStatusString($row) {
	$injStatus = '';
	if (isset($row['injury_dtd_injury']) && $row['injury_dtd_injury'] == 1) {
		$injStatus .= "Questionable - ";
	} else if (isset($row['injury_career_ending']) && $row['injury_career_ending'] == 1) {
		$injStatus .= "Career Ending Injury! ";
	} else {
		$injStatus .= "Injured - ";
	}
	// GET injury name
	$injury_name = "Unknown Injury";
	if (isset($row['injury_id'])) {
		$injury_name = getInjuryName($row['injury_id']);
	}
	$injStatus .= $injury_name;
	if ((isset($row['injury_dl_left']) && $row['injury_dl_left'] > 0)) {
		$injStatus .= ", on DL - ".$row['injury_dl_left']." Days Left";
	}
	if (isset($row['injury_left']) && ($row['injury_left'] > 0 || (isset($row['injury_dl_left']) && $row['injury_left'] > $row['injury_dl_left']))) {
		$injStatus .= ", ".$row['injury_left']." Total Days Left";
	}
	return $injStatus;
}
/**
 *	MAKE ELIDGIBILUITY STRING
 *
 *	Converts an array of positions into a readable list of position acroymns.
 *
 *	@param	$positions	Array 	List of positions
 *	@return				String	Position list String
 *
 * 	@author	Jeff Fox
 *	@since	1.0
 */
function makeElidgibilityString($positions) {
	$gmPos = "";
	if (strpos($positions,":")) {
		$pos = unserialize($positions);
		foreach($pos as $tmpPos) {
			if ($tmpPos != 25) {
				if (!empty($gmPos)) $gmPos .= ",";
				$gmPos .= get_pos($tmpPos);
			}
		}
	}
	return $gmPos;
}
/**
 *	CALCULATE RATING
 *
 *	@param	$rating		int			Rating Value INT
 *	@param	$ratOrTal	int			Rate or Tally INT
 *	@param	$max		varchar		Max Value
 *	@return				String		Rating Value
 *
 *	@todo	Rewrite this so settings can be in DB, remove session component
 * 	@author	Frank Esselink
 * 	@since	1.0
 */
function calc_rating($rating,$ratOrTal=0,$max="")
 {
   if ($rating==0) {return 0;}

   if ((file_exists("./settings/lgSettings.txt"))&&(($_SESSION['ratings']=="")||($_SESSION['talents']=="")||($_SESSION['others']==""))) {
      $f = fopen("./settings/lgSettings.txt",'r');
      if ($f)
       {
         while (!feof($f))
          {
            $text=fgets($f);
            $split=explode("|",$text);
            switch ($split[0])
             {
	        case 'RATINGS'  : $e=explode("\n",$split[1]);$ratings=$e[0];     break;
	        case 'TALENTS'  : $e=explode("\n",$split[1]);$talents=$e[0];     break;
	        case 'OTHERS'   : $e=explode("\n",$split[1]);$others=$e[0];      break;
	     }
          }
	 fclose($f);
	 $_SESSION['ratings']=$ratings;
	 $_SESSION['talents']=$talents;
	 $_SESSION['others']=$others;
       }
    }
   $scale=$_SESSION['ratings'];
   if ($ratOrTal==1) {$scale=$_SESSION['talents'];}
   if ($ratOrTal==2) {$scale=$_SESSION['others'];}
   if ($scale=="") {$scale='Hidden';}

   if ($max!="") {$rating=max($rating,$max);}

   switch ($scale)
    {
      case "2-8":
	$rat=intval(($rating/31) + 2);
	$rat=min(8,$rat);
	$rat=max(2,$rat);
        break;
      case "20-80":
        $rat=intval(($rating+10)/15);
	$rat=min(13,$rat);
	$rat=max(1,$rat);
	$rat=5*$rat+15;
	break;
      case "Hidden": $rat=0; break;
      default:
        switch ($scale)
	 {
	   case "1-5":   $maxRat=5;   break;
           case "1-10":  $maxRat=10;  break;
           case "1-20":  $maxRat=20;  break;
	   case "1-100"; $maxRat=100; break;
	 }
	$sc=200/$maxRat;
	$rat=intval(($rating+$sc)/$sc);
	$rat=min($maxRat,$rat);
	$rat=max(1,$rat);
    }

   return $rat;
 }

/**
 *	FORMAT BYTES.
 *
 *	@param	$bytes		int			Bytes value
 *	@param	$precision	int			Math Round Precicion Value
 *	@return				String		Bytes String
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function formatBytes($bytes, $precision = 1) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 *	CALCULATE MOVEMENT.
 *
 *	@param	$mvmnt		int			Bytes value
 *	@param	$gb			int			Math Round Precicion Value
 *	@return				int			Movement Rating
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function calc_movement($mvmnt,$gb)
 {
   $rat=200.5-(5*((18+(54-$gb)*.6)+((200-$mvmnt)/6))/2);
   return $rat;
 }

/**
 *	GET PITCH RATING.
 *
 *	@param	$pitch		String	Pitch type	
 *	@param	$ir			int			
 *	@param	$gb			int			
 *	@param	$mvmnt		int		Movement Int
 *	@param	$velo		int		Velocity int
 *	@return				int		Rating Value		
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function get_pitch_rating($pitch,$ir,$gb,$mvmnt,$velo)
{
   $velo=$velo*10;
   switch ($pitch)
    {
      case 'fastball':
        $rat=($velo*0.6) + ($ir*0.4);
        break;
      case 'slider':
        $rat=($velo*0.3) + ($ir*0.7);
	break;
      case 'forkball':
      case 'splitter':
      case 'cutter':
        $rat=($velo*0.4) + ($ir*0.4) + ($mvmnt*0.2);
        break;
      case 'sinker':
        $rat=($velo*0.3) + ($ir*0.3) + ($mvmnt*0.4);
        break;
      case 'changeup':
      case 'knuckleball':
      case 'circlechange':
        $rat=$ir;
        break;
      case 'curveball':
      case 'screwball':
      case 'knucklecurve':
        $rat=($velo*0.2) + ($ir*0.8);
        break;
    }
   return $rat;
 }

/**
 *	GET LEAGUE LEVEL.
 *	Returns a text string name for the level passed.
 *
 *	@param	$lvl	int		Level index
 *	@return			String	level name
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function get_level($lvl)
 {
   switch ($lvl)
    {
      case 1: $txt="ML"; break;
      case 2: $txt="AAA"; break;
      case 3: $txt="AA"; break;
      case 4: $txt="A"; break;
      case 5: $txt="SS"; break;
      case 6: $txt="R"; break;
      case 7: $txt="INT"; break;
      case 8: $txt="WL"; break;
      case 9: $txt="COL"; break;
      case 10: $txt="HS"; break;
      default: $txt=$lvl; break;

    }
   return $txt;
 }

/**
 *	GET AWARD.
 *	Returns a award string name for the level passed.
 *
 *	@param	$awid	int		Award type ID
 *	@return			String	Award name Value
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function get_award($awid)
 {
    switch ($awid)
     {
       case 0:
         $txt="Player of the Week";
	 break;
       case 1:
         $txt="Pitcher of the Month";
         break;
       case 2:
         $txt="Batter of the Month";
         break;
       case 3:
         $txt="Rookie of the Month";
         break;
       case 4:
         $txt="Oustanding Pitcher";
         break;
       case 5:
         $txt="Oustanding Hitter";
         break;
       case 6:
         $txt="Oustanding Rookie";
         break;
       case 7:
         $txt="Gold Glove";
         break;
       case 8:
         $txt=$awid;
         break;
       case 9:
         $txt="All-Star";
         break;
       default:
         $txt=$awid." not found";
	 break;
     }
    return $txt;
 }
/**
 *	GET VELOCITY.
 *	Converts a velocity index int into a text string.
 *
 *	@param	$velo	int		Velocity Int
 *	@return			String	Velocity String
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function get_velo($velo)
 {
  switch ($velo)
   {
     case 1:  $txt="<75 Mph";    break;
     case 2:  $txt="81-83 Mph";  break;
     case 3:  $txt="82-84 Mph";  break;
     case 4:  $txt="83-85 Mph";  break;
     case 5:  $txt="84-86 Mph";  break;
     case 6:  $txt="85-87 Mph";  break;
     case 7:  $txt="86-88 Mph";  break;
     case 8:  $txt="87-89 Mph";  break;
     case 9:  $txt="89-90 Mph";  break;
     case 10: $txt="90-92 Mph";  break;
     case 11: $txt="91-93 Mph";  break;
     case 12: $txt="92-94 Mph";  break;
     case 13: $txt="93-95 Mph";  break;
     case 14: $txt="94-96 Mph";  break;
     case 15: $txt="95-97 Mph";  break;
     case 16: $txt="96-98 Mph";  break;
     case 17: $txt="97-99 Mph";  break;
     case 18: $txt="98-100 Mph"; break;
     case 19: $txt="99-101 Mph"; break;
     case 20: $txt="101+ Mph";   break;
   }
  return $txt;
 }

/**
 *	HALL OF FAME POSITION.
 *
 *	@param	$pos		pos Int
 *	@return			int		Positon Value
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function hof_pos($pos)
{
   switch ($pos)
    {
      case 2: $val=20;break;
      case 3: $val=1;break;
      case 4: $val=14;break;
      case 5: $val=13;break;
      case 6: $val=16;break;
      case 7: $val=3;break;
      case 8: $val=12;break;
      case 9: $val=6;break;
      default: $val=0;break;
    }
   return $val;
}

/**
 *	ALL STAR POSITION.
 *
 *	@param	$pos	int		pos Int
 *	@return			int		Positon Value
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function ss_pos($pos)
{
   switch ($pos)
    {
      case 2: $val=20;break;
      case 3: $val=1;break;
      case 4: $val=11;break;
      case 5: $val=7;break;
      case 6: $val=14;break;
      case 7: $val=3;break;
      case 8: $val=5;break;
      case 9: $val=4;break;
      default: $val=0;break;
    }
   return $val;
}
 
/**
 *	GET HAND.
 *	Converts a hand index ID to a string.
 *
 *	@param	$handID		int 		Hand Index
 *	@return				String		String value
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function get_hand($handID)
{
  switch ($handID)
   {
     case 3: $hand="S"; break;
     case 2: $hand="L"; break;
     case 1: $hand="R"; break;
     default: $hand="U"; break;
   }
  return $hand;
}

/**
 *	DATE DIFFERENCE.
 *
 *	@param	$start		String 	Start Date 
 *	@param	$end		String 	End Date 
 *	@return				int		Difference in days
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function datediff($start,$end)
 {
   $start_ts = strtotime($start);
   $end_ts = strtotime($end);

   $diff = $end_ts - $start_ts;
   return round($diff / 86400);
 }

/**
 *	ORDINAL SUFFIX.
 *	Determines a suffix based on the value passed.
 *
 *	@param	$value		int 		Numeric Value
 *	@param	$sup		int			1 to wrap return value in <Sup> tags, 0 to not
 *	@return				String		String value with ordinal
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function ordinal_suffix($value, $sup = 0) {
    if(substr($value, -2, 2) == 11 || substr($value, -2, 2) == 12 || substr($value, -2, 2) == 13){
        $suffix = "th";
    }
    else if (substr($value, -1, 1) == 1){
        $suffix = "st";
    }
    else if (substr($value, -1, 1) == 2){
        $suffix = "nd";
    }
    else if (substr($value, -1, 1) == 3){
        $suffix = "rd";
    }
    else {
        $suffix = "th";
    }
    if($sup){
        $suffix = "<sup>" . $suffix . "</sup>";
    }
    return $value . $suffix;
}

/**
 *	CENTIMETERS TO FEET/INCHES.
 *	Converts a centimeter value to a feet and inches string.
 *
 *	@param	$len		int 		Length value in centimeters
 *	@return				String		String value
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function cm_to_ft_in($len)
 {
   $in=$len/2.54;
   $ft=floor($in/12);
   $in=$in%12;
   $in=round($in);
   $txt=$ft."' ".$in."\"";
   return $txt;
 }
/**
 *	AVERAGE.
 *	Creates an average based on the values of the array passed.
 *
 *	@param	$array		Array 		Contains int values
 *	@return				Int			Average value
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function average($array)
 {
   $sum   = array_sum($array);
   $count = count($array);
   return $sum/$count;
 }

/**
 *	DEVIATION.
 *	Creates a standard deviation based on the values of the array passed. This function 
 * 	is helpful when trying to determine if a value is higher or lower than the standard 
 *	deviation from a median value.
 *
 *	@param	$array		Array 		Contains int values
 *	@return				Int			Average value
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function deviation($array)
{
   $avg = average($array);
   foreach ($array as $value)
    {
      $variance[] = pow($value-$avg, 2);
     }
   $deviation = sqrt(average($variance));
   return $deviation;
}
/**
 *	RETURN BYTES.
 *	Cronverts a file size string into a byte value.
 *
 *	@param	$val		String 		String file size value 
 *	@return				Int			Bytes value
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function return_bytes($val) {
	$val = trim($val);
	$last = strtolower($val[strlen($val)-1]);
	switch($last) {
		// The 'G' modifier is available since PHP 5.1.0
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}
	return $val;
}
?>
