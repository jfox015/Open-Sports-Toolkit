<?php
/**
 *	GENERAL HELPER
 *
 *	This helper includes a general set of functions used by the site to run common
 * 	helper functions such as reslving positon names and numbers, building common
 *	stat queries and display lists and more.
 *
 * 	@author 	Jeff Fox
 * 	@author 	Several functions written by Frank Esselink where noted.
 */

/**
 *	SPORT MAP.
 *	This function returns an array that maps the specific stats categories to source specific
 *	field values such as Index IDs and DB/endpoint fields.
 *
 *	@return		Array	Data field source values for offense, defense and specilty fields
 */
if(!function_exists('sports_map')) 
{
	function sports_map() 
	{
		$map = array(
			0 		=> 'baseball',
			1		=> 'football',
			2		=> 'backetball',
			3		=> 'hockey',
			4		=> 'soccer'
		);
		return $map;
	}
}
/**
 *	SOURCE MAP.
 *	This function returns an array that maps the specific stats categories to source specific
 *	field values such as Index IDs and DB/endpoint fields. Map index numbers correspond to 
 *	sport index numbers in sports_map().
 *
 *	@return		Array	Data field source values for offense, defense and specilty fields
 *	@see				open_sports_toolkit::general_helper->sports_map()
 */
if(!function_exists('source_map')) 
{
	function source_map() 
	{
		$map = array(
			0 => array(
				'ootp'=>"Out of the Park Baseball (OOTP)"
			),
			1 => array(
				'phpffl' => 'PHP Fantasy Football League Manager'
			)
		);
		return $map;
	}
}
/**
 *	SOURCE VERSION MAP.
 *	This function returns an array that maps the specific stats categories to source specific
 *	field values such as Index IDs and DB/endpoint fields.
 *
 *	@return		Array	Data field source values for offense, defense and specilty fields
 */
if(!function_exists('source_version_map')) 
{
	function source_version_map() 
	{
		$map = array(
			"ootp" => array(
				'13'=>"OOTP 13",
				'12'=>"OOTP 12",
				'11'=>"OOTP 11",
				'10'=>"OOTP 10"
			),
			"phpffl" => array(
				'1' => 'phpFFL 1.23'
			)
		);
		return $map;
	}
}
/**
 *	MAKE INJURY STATUS STRING
 *
 *	Converts standard OOTP injury data (found in the player profile data object and injuries
										in the database) into a human readbale string.
 *
 *	@param	$row		Array 	Array of player data with injuriy fields
 *	@return				String	Injury String text
 *
 *	@since	0.3
 */
 if(!function_exists('make_injury_status_string'))
{
	function make_injury_status_string($row) 
	{
		$injStatus = '';
		if (isset($row['injury_dtd_injury']) && $row['injury_dtd_injury'] == 1) {
			$injStatus .= lang('full_QUES')." - ";
		} else if (isset($row['injury_career_ending']) && $row['injury_career_ending'] == 1) {
			$injStatus .= lang('full_CE')."! ";
		} else {
			$injStatus .= lang('full_INJR')." - ";
		}
		// GET injury name
		$injury_name = lang('full_UNKN');
		if (isset($row['injury_id'])) {
			$injury_name = get_injury_name($row['injury_id']);
		}
		$injStatus .= $injury_name;
		if ((isset($row['injury_dl_left']) && $row['injury_dl_left'] > 0)) {
			$injStatus .= ", ".lang('acyn_ONDL')." - ".$row['injury_dl_left']." ".lang('acyn_DL');
		}
		if (isset($row['injury_left']) && ($row['injury_left'] > 0 || (isset($row['injury_dl_left']) && $row['injury_left'] > $row['injury_dl_left']))) {
			$injStatus .= ", ".$row['injury_left']." ".lang('acyn_DAYS');
		}
		return $injStatus;
	}
}
// ------------------------------------------------------------------------

if ( ! function_exists('get_injury_name')) {
    function get_injury_name($injuryId = false) {
        if ($injuryId === false) {
            return false;
        }
        $ci =& get_instance();
        $injuryName = '';
        $ci->db->select('injury_text')
               ->where('id', $injuryId);
        $query = $ci->db->get('list_injuries');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $injuryName = $row->injury_text;
        } // END if
        $query->free_result();

        return $injuryName;
    } // END function
} // END if
/**
 *	MAKE ELIDGIBILITY STRING
 *
 *	Converts an array of positions into a readable list of position acroymns.
 *
 *	@param	$positions	Array 	List of positions
 *	@return				String	Position list String
 *
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
 //--------------------------------------------------------------------
	
/**
 * 	Get Position.
 * 	Returns the acronym for a position.
 *
 *	NOTE: This function requies that the Stats library be inialtized with 
 *	the sport and scource values to populate the level_list property.
 
 * 	@static
 * 	@param 		int			$pos				Position ID
 * 	@param 		array		$position_list		The sport and source specific position list
 * 	@return 	string          				Position acronym
 */
 if (!function_exists('get_pos'))
 {
    function get_pos($pos, $position_list = false)
    {
        $pos_str = '';
		if ($position_list === false || count($position_list) == 0)
		{
			return false;
		}
        foreach($position_list as $position => $details)
        {
            if (isset($details['id']) && $details['id'] == $pos)
            {
                $pos_str = $position;
                break;
            }
        }
		return $pos_str;
    }
}

//--------------------------------------------------------------------
	
/**
 * 	Get Award.
 * 	Returns the acronym for a award for use in displaying the text.
 *
 *	NOTE: This function requies that the Stats library be inialtized with 
 *	the sport and scource values to populate the award_list property.
 *
 * 	@static
 * 	@param 		string		$award			Award ID
 * 	@param 		array		$award_list		The sport and source specific award list
 * 	@return 	string          			Award acronym
 *	@since	0.3
 */
 if (!function_exists('get_award'))
 {
    function get_award($award, $award_list = false)
    {
        $award_str = '';
		if ($award_list === false || count($award_list) == 0)
		{
			return false;
		}
        foreach($award_list as $awd => $details)
        {
            if (isset($details['id']) && $details['id'] == $award)
            {
                $award_str = $awd;
                break;
            }
        }
		return $award_str;
    }
}

//--------------------------------------------------------------------
	
/**
 * 	Get Level.
 * 	Returns the acronym for a level for use in displaying the text.
 *
 *	NOTE: This function requies that the Stats library be inialtized with 
 *	the sport and scource values to populate the level_list property.
 *
 * 	@static
 * 	@param 		string		$level			Level ID
 * 	@param 		array		$level_list		The sport and source specific level list
 * 	@return 	string          			Level acronym
 *	@since	0.3
 */
 if (!function_exists('get_level'))
 {
    function get_level($level, $level_list = false)
    {
        $level_str = '';
		if ($level_list === false || count($level_list) == 0)
		{
			return false;
		}
        foreach($level_list as $lvl => $details)
        {
            if (isset($details['id']) && $details['id'] == $level)
            {
                $level_str = $lvl;
                break;
            }
        }
		return $level_str;
    }
}

//--------------------------------------------------------------------

/**
 * Get Position Number.
 * Returns the ID for a position.
 *
 * @static
 * @param 	string 		$pos	Position Name
 * @return 	string|int          Position ID
 */
if (!function_exists('get_pos_num'))
{
    function get_pos_num($pos_str, $position_list = false)
    {
        $pos = '';
        if ($position_list === false || count($position_list) == 0)
        {
            return false;
        }
		if (isset($position_list[$pos_str]))
		{
			$pos = $position_list[$pos_str]['id'];
		}
		return $pos;
    }
}
/**
 *	GET ASSET PATHS.
 *	A function to create correct paths to OOTP assets like logos and playerpictures. The structure 
 *	changed in OOTP 13 so this function helps create backwards compatibility with odler versions.
 *
 *	@param	Array	$settings	Settings Array Object
 *	@return	Array				Appended Settings object
 *
 */
if (!function_exists('get_asset_path')) {
	function get_asset_path($settings) {
		// SET UP CUSTOM ASSET PATHS
		$league_logo_path = $team_logo_path = $players_img_path = $player_profile_img_path = $settings['osp.asset_path'].'images/';
		$league_logo_url = $team_logo_url = $players_img_url = $player_profile_img_url = $settings['osp.asset_url'].'images/';
		if ($settings['osp.game_source'] == 'ootp' && intval($settings['osp.source_version']) >= 13) {
			$league_logo_path .= 'league_logos/';
			$team_logo_path .= 'team_logos/';
			$players_img_path .= 'person_pictures/';
			$player_profile_img_path .= 'profile_pictures/';
            $league_logo_url .= 'league_logos/';
			$team_logo_url .= 'team_logos/';
			$players_img_url .= 'person_pictures/';
			$player_profile_img_url .= 'profile_pictures/';
		}
		$settings['osp.team_logo_url'] = $team_logo_url;
		$settings['osp.league_logo_url'] = $league_logo_url;
		$settings['osp.players_img_url'] = $players_img_url;
		$settings['osp.player_profile_img_url'] = $player_profile_img_url;
		$settings['osp.team_logo_path'] = $team_logo_path;
		$settings['osp.league_logo_path'] = $league_logo_path;
		$settings['osp.players_img_path'] = $players_img_path;
		$settings['osp.player_profile_img_path'] = $player_profile_img_path;

		return $settings;
	}
}
/**
 *	FORMAT TIME.
 *	A function to create correct time display based on a passed value. OOTP 13 seperates games by date and 24 based 
 *	so, this function resolves thiose issues.
 *
 *	@param	Array	$settings	Settings Array Object
 *	@return	Array				Appended Settings object
 *
 */
if (!function_exists('format_time')) {
	function format_time($inTime = false, $settings = false) {
		$time = 0;
		$timestr = strval($inTime);
		$hour = intval(substr($timestr, 0, 2));
		$minutes = substr($timestr, 2, 2);
		if ($hour > 12) {
			$meridian = "pm";
			$hour = $hour - 12;
		} else {
			$meridian = "am";
		}
		$time = $hour. ":".$minutes." ".$meridian;
		//}
		return $time;
	}
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
