<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	SPORT HELPER.
 *	A helper that defines the specific types of data for each specific sport.
 *
 *
 * 	@sport 		Baseball
 *	@author		Jeff Fox <jfox@gmail.com>
 *
 */
 /*
	Copyright (c)  Jeff Fox

	Permission is hereby grantedfree of chargeto any person obtaining a copy
	of this software and associated documentation files (the "Software")to deal
	in the Software without restrictionincluding without limitation the rights
	to usecopymodifymergepublishdistributesublicenseand/or sell
	copies of the Softwareand to permit persons to whom the Software is
	furnished to do sosubject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS"WITHOUT WARRANTY OF ANY KINDEXPRESS OR
	IMPLIEDINCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIMDAMAGES OR OTHER
	LIABILITYWHETHER IN AN ACTION OF CONTRACTTORT OR OTHERWISEARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

//---------------------------------------------------------------

/**
 *	POSITION LIST.
 *	This function returns an array that defines the positions for offense, 
 *	defense and speciality.
 *
 *	@return		Array	Stat Categories offensedefense and specilty types
 */
if(!function_exists('position_list')) 
{
	function position_list() 
	{
		$positions = 
		array(
			"PH"	=>array('lang'=>"PH", 'type' => 'offense'),
			"C"		=>array('lang'=>"C",  'type' => 'offense'),
			"1B"	=>array('lang'=>"1B", 'type' => 'offense'),
			"2B"	=>array('lang'=>"2B", 'type' => 'offense'),
			"3B"	=>array('lang'=>"3B", 'type' => 'offense'),
			"SS"	=>array('lang'=>"SS", 'type' => 'offense'),
			"LF"	=>array('lang'=>"LF", 'type' => 'offense'),
			"CF"	=>array('lang'=>"CF", 'type' => 'offense'),
			"RF"	=>array('lang'=>"RF", 'type' => 'offense'),
			"DH"	=>array('lang'=>"DH", 'type' => 'offense'),
			"OF"	=>array('lang'=>"OF", 'type' => 'offense'),
			"IF"	=>array('lang'=>"IF", 'type' => 'offense'),
			"MI"	=>array('lang'=>"MI", 'type' => 'offense'),
			"CI"	=>array('lang'=>"CI", 'type' => 'offense'),
			"U"		=>array('lang'=>"U",  'type' => 'offense'),
			"P"		=>array('lang'=>"P",  'type' => 'speciality'),
			"SP"	=>array('lang'=>"SP", 'type' => 'speciality'),
			"RP"	=>array('lang'=>"RP", 'type' => 'speciality'),
			"CL"	=>array('lang'=>"CL", 'type' => 'speciality'),
			"SU"	=>array('lang'=>"SU", 'type' => 'speciality'),
			"MU"	=>array('lang'=>"MU", 'type' => 'speciality'),
		);
		return $positions;
	}
}
//---------------------------------------------------------------

/**
 *	STAT LIST.
 *	This function returns an array that defines the stat categories broekn down by the 
 * category type (offensedefense and speciality).
 *
 *	@return		Array	Stat Categories offensedefense and specilty types
 */
if(!function_exists('stat_list')) 
{
	function stat_list() 
	{
		$stats = array(
            'general'=>
				array(
					"FN"	=>array('lang' => "FN"),
					"LN"	=>array('lang' => "LN"),
					"PN"	=>array('lang' => "PN"),
					"PNABBR"=>array('lang' => "PNABBR"),
					"PID"	=>array('lang' => "PID"),
					"TN"	=>array('lang' => "TN"),
					"TNACR"	=>array('lang' => "TNACR"),
					"TID"	=>array('lang' => "TID"),
					"AGE"	=>array('lang' => "AGE"),
					"POS"	=>array('lang' => "POS"),
					"ROLE"	=>array('lang' => "ROLE"),
					"LVL"	=>array('lang' => "LVL"),
					"TH"	=>array('lang' => "TH"),
					"BA"	=>array('lang' => "BA"),
					"FPTS"	=>array('lang' => "FPTS"),
					"PR15"	=>array('lang' => "PR15"),
					"INJURY"=>array('lang' => "INJ"),
				),
			'offense'=>
                array(
                    "G" => array('lang' => "G"),
                    "GS"  => array('lang' => "GS"),
                    "PA"  => array('lang' => "PA"),
                    "AB"  => array('lang' => "AB"),
                    "H"  => array('lang' => "H"),
                    "SO"  => array('lang' => "SO"),
                    "TB"  => array('lang' => "TB"),
                    "2B"  => array('lang' => "2B"),
                    "3B"  => array('lang' => "3B"),
                    "HR"  => array('lang' => "HR"),
                    "SB"  => array('lang' => "SB"),
                    "RBI" => array('lang' => "RBI"),
                    "R" => array('lang' => "R"),
                    "BB" => array('lang' => "BB"),
                    "IBB" => array('lang' => "IBB"),
                    "HBP" => array('lang' => "HBP"),
                    "SH" => array('lang' => "SH"),
                    "SF" => array('lang' => "SF"),
                    "XBH" => array('lang' => "XBH"),
                    "AVG" => array('lang' => "AVG"),
                    "OBP" => array('lang' => "OBP"),
                    "SLG" => array('lang' => "SLG"),
                    "RC" => array('lang' => "RC"),
                    "RC_27" => array('lang' => "RC_27"),
                    "ISO" => array('lang' => "ISO"),
                    "WOBA" => array('lang' => "WOBA"),
                    "TAVG" => array('lang' => "TAVG"),
                    "OPS" => array('lang' => "OPS"),
                    "VORP" => array('lang' => "VORP"),
                    "GIDP" => array('lang' => "GIDP"),
                    "RISP" => array('lang' => "RISP") ,
                    "WIFF" => array('lang' => "WIFF"),
                    "WALK" => array('lang' => "WALK"),
                    "TRO" => array('lang' => "TRO")
                ),
			'speciality' =>
                array(
                    "G" => array('lang' => "G"),
                    "GS" => array('lang' => "GS"),
                    "W" => array('lang' => "W"),
                    "L" => array('lang' => "L"),
                    "PCT" => array('lang' => "PCT"),
                    "SV" => array('lang' => "SV"),
                    "HLD" => array('lang' => "HLD"),
                    "IP" => array('lang' => "IPI"),
                    "BF" => array('lang' => "BF"),
                    "HRA" => array('lang' => "HRA"),
                    "BB" => array('lang' => "BB"),
                    "SO" => array('lang' => "SO"),
                    "WP" => array('lang' => "WP"),
                    "ERA" => array('lang' => "ERA"),
                    "BABIP" => array('lang' => "BABIP"),
                    "WHIP" => array('lang' => "WHIP"),
                    "SO_BB" => array('lang' => "SO_BB"),
                    "RA_IP" => array('lang' => "RA_9IP"),
                    "HR_IP" => array('lang' => "HR_9IP"),
                    "H_IP" => array('lang' => "H_9IP"),
                    "BB_IP" => array('lang' => "BB_9IP"),
                    "SO_IP" => array('lang' => "SO_9IP"),
                    "VORP" => array('lang' => "VORP"),
                    "RA" => array('lang' => "RA"),
                    "GF" => array('lang' => "GF"),
                    "QS" => array('lang' => "QS"),
                    "QS%" => array('lang' => "QS%"),
                    "CG" => array('lang' => "CG"),
                    "CG%" => array('lang' => "CG%"),
                    "SHO" => array('lang' => "SHO"),
                    "SHO%" => array('lang' => "SHO%"),
                    "CS" => array('lang' => "CS"),
                    "HA" => array('lang' => "HA"),
                    "BS" => array('lang' => "BS"),
                    "SVO" => array('lang' => "SVO"),
                    "ER" => array('lang' => "ER"),
                    "IPF" => array('lang' => "IPIF"),
                    //"IR" => array('lang' => "IR"),
                    //"IRA" => array('lang' => "IRA"),
                    "BK" => array('lang' => "BK"),
                    "HB" => array('lang' => "HB"),
                    "OBA" => array('lang' => "OBA"),
                    "TRP" => array('lang' => "TRP"),
                    "ERC" => array('lang' => "ERC"),
                ),
			"defense"=>
                array(
                    "TC" => array('lang' => 'TC'),
                    "A" => array('lang' => 'A'),
                    "PO" => array('lang' => 'PO'),
                    "ER" => array('lang' => 'ER'),
                    "IP" => array('lang' => 'IPL'),
                    "G" => array('lang' => 'G'),
                    "GS" => array('lang' => 'GS'),
                    "E" => array('lang' => 'E'),
                    "DP" => array('lang' => 'DP'),
                    "TP" => array('lang' => 'TC'),
                    "PB" => array('lang' => 'PB'),
                    "SBA" => array('lang' => 'SBA'),
                    "RTO" => array('lang' => 'RTO'),
                    "IPF" => array('lang' => 'IPLF'),
                    "PLAYS" => array('lang' => 'PLAYS'),
                    "PLAYS_BASE" => array('lang' => 'PLAYS_BASE'),
                    "ROE" => array('lang' => 'ROE'),
                    "FP" => array('lang' => 'FP'),
                    "RF" => array('lang' => 'RF'),
                ),
            "injury"=>
                array(
                    "INJ" => array('lang' => 'INJURY'),
                    "DTD" => array('lang' => 'DTD_INJURY'),
                    "CE" => array('lang' => 'CE'),
                    "DL" => array('lang' => 'DL'),
                    "DAYS" => array('lang' => 'DAYS'),
                    "ID" => array('lang' => 'ID')
                ),
			"team"=>
				array(
					"W" => array('lang' => 'W'),
					"L" => array('lang' => 'L'),
					"PCT" => array('lang' => 'PCT'),
					"GB" => array('lang' => 'GB'),
					"HOME" => array('lang' => 'HOME'),
					"ROAD" => array('lang' => 'ROAD'),
					"RS" => array('lang' => 'RS'),
					"RA" => array('lang' => 'RA'),
					"DIFF" => array('lang' => 'DIFF'),
					"STRK" => array('lang' => 'STRK'),
					"L10" => array('lang' => 'L10'),
					"POFF" => array('lang' => 'POFF')
				),
			"game"=>
				array(
					"DATE" => array('lang' => 'DATE')
				)
		);
		return $stats;
	} // END function
} // END if

//---------------------------------------------------------------

/**
 *	AWARD LIST.
 *	This function returns an array that defines the awards for this sport.
 *
 *	@return		Array	Award Categories
 */
if(!function_exists('award_list')) 
{
	function award_list() 
	{
		$awards = 
		array(
			"POW"	=>array('lang'=>"POW"),
			"POM"	=>array('lang'=>"POM"),
			"BOM"	=>array('lang'=>"BOM"),
			"ROM"	=>array('lang'=>"ROM"),
			"POY"	=>array('lang'=>"POY"),
			"BOY"	=>array('lang'=>"BOY"),
			"ROY"	=>array('lang'=>"ROY"),
			"GG"	=>array('lang'=>"GG"),
			"AS"	=>array('lang'=>"AS"),
			"POG"	=>array('lang'=>"POG"),
		);
		return $awards;
	}
}
//---------------------------------------------------------------

/**
 *	LEVEL LIST.
 *	This function returns an array that defines the levels for this sport.
 *
 *	@return		Array	Level Categories
 */
if(!function_exists('level_list')) 
{
	function level_list() 
	{
	  $levels =
		array(
			"ML"	=>array('lang'=>"ML"),
			"AAA"	=>array('lang'=>"AAA"),
			"AA"	=>array('lang'=>"AA"),
			"A_L"	=>array('lang'=>"A"),
			"SS"	=>array('lang'=>"SS"),
			"R_L"	=>array('lang'=>"R"),
			"INT"	=>array('lang'=>"INT"),
			"WL"	=>array('lang'=>"WL"),
			"COL"	=>array('lang'=>"COL"),
			"HS"	=>array('lang'=>"HS"),
		);
		return $levels;
	}
}
//---------------------------------------------------------------

/**
 *	Get Stats Class.
 *	Accepts the player type ans stats class (and custom field defs) and builds a SQL query.
 *	@param		$stat_type	int				TYPE_OFFENSE, TYPE_DEFENSE or TYPE_SPECIALTY
 *	@param		$stats_class	int			The class of stats to use
 *	@return						Array		Array of stat definitions
 */
if(!function_exists('stats_class')) 
{
	function stats_class($stat_type = TYPE_OFFENSE, $stats_class = CLASS_STANDARD, $extended = array())
	{
		$fieldList = array();
		if ($stat_type == TYPE_OFFENSE) {
			// BATTERS
			switch ($stats_class) {
				case CLASS_ULTRA_COMPACT:
					$fieldList = array('AVG','R','HR');
					break;
				case CLASS_COMPACT:
					$fieldList = array('AVG','R','HR','RBI','OPS');
					break;
				case CLASS_BASIC:
					$fieldList = array('AVG','R','H','HR','RBI','SB','OBP','OPS');
					break;
				case CLASS_RECENT:
					$fieldList = array('AB','R','H','HR','RBI','BB','SO','SB');
					break;
				case CLASS_COMPLETE:
					$fieldList = array('AVG','G','AB','R','H','2B','3B','HR','RBI','BB','SO','SB','OBP','SLG','OPS');
					break;
				case CLASS_EXPANDED:
					$fieldList = array('AVG','G','AB','R','H','2B','3B','HR','RBI','BB','SO','SB','CS','OBP','SLG','OPS','wOBA','XBH','OPSPLUS');
					break;
				case CLASS_EXTENDED:
					$fieldList = array('AVG','G','PA','HP','SF','ISO','TB');
					break;
				case CLASS_STANDARD:
				default:
					$fieldList = array('AVG','AB','R','H','HR','RBI','BB','SO','SB','OBP','SLG','OPS');
					break;
			} // END switch
		} else if ($stat_type == TYPE_SPECIALTY){
			switch ($stats_class) {
				case CLASS_ULTRA_COMPACT:
					$fieldList = array('W','L','ERA');
					break;
				case CLASS_COMPACT:
					$fieldList = array('W','L','ERA','BB','SO');
					break;
				case CLASS_BASIC:
					$fieldList = array('W','L','SV','ERA','IP','BB','SO','WHIP');
					break;
				case CLASS_RECENT:
					$fieldList = array('W','L','SV','IP','HA','ERA','BB','SO');
					break;
				case CLASS_COMPLETE:
					$fieldList = array('W','L','SV','ERA','G','GS','IP','CG','SHO','HA','RA','ER','HRA','BB','SO','WHIP');
					break;
				case CLASS_EXPANDED:
					$fieldList = array('W','L','SV','ERA','G','GS','IP','CG','SHO','HA','RA','ER','BB','SO','HRA','BB_9','K_9','HR_9','WHIP','BIFP','ERAPLUS');
					break;
				case CLASS_EXTENDED:
					$fieldList = array('SO','BS','QS','QS%','CG%','SHO%','GF');
					break;
				case CLASS_STANDARD:
				default:
					$fieldList = array('W','L','SV','ERA','IP','SHO','HA','ER','HRA','BB','SO','WHIP');
					break;
			} // END switch
		} else if ($stat_type == TYPE_DEFENSE){
			switch ($stats_class) {
				case CLASS_ULTRA_COMPACT:
					$fieldList = array("FP","PO","E");
					break;
				case CLASS_COMPACT:
					$fieldList = array("TC","A","PO","E","FP");
					break;
				default:
					$fieldList = array("TC","A","PO","ER","IP","E","DP","TP","PB","SBA","RTO","ROE","FP");
					break;
			} // END switch
		}  else if ($stat_type == TYPE_INJURY){
			switch ($stats_class) {
				default:
					$fieldList = array("I","DTD","CE","DL","DAYS","ID");
					break;
			} // END switch
		} // END if
		
		$fields = array();
		// NOTE 
		if (in_array('NAME',$extended))
		{
			$genArr = array('PN','PID');
			foreach($genArr as $field) {
				array_push($fields,$field);
			}
		} else if (in_array('PNABBR',$extended))
		{
			$genArr = array('PNABBR','PID');
			foreach($genArr as $field) {
				array_push($fields,$field);
			}
		}
        if (in_array('TID',$extended))
        {
            array_push($fields,'TID');
        }
        if (in_array('TEAM',$extended))
		{
			$genArr = array('TN','TID');
			foreach($genArr as $field) {
				array_push($fields,$field);
			}
		}
		if (in_array('TEAM_ACY',$extended))
		{
			$genArr = array('TNACR','TID');
			foreach($genArr as $field) {
				array_push($fields,$field);
			}
		}
        if (in_array('POS',$extended))
        {
            array_push($fields,'POS');
        }
        if (in_array('GENERAL',$extended))
		{
			$genArr = array('AGE','TH','BA');
			foreach($genArr as $field) {
				array_push($fields,$field);
			}
		}
		if (in_array('TRANSACTIONS',$extended))
		{
			array_push($fields,'ADD');
		}
		if (in_array('DRAFT',$extended))
		{
			array_push($fields,'DRAFT');
		}
		if (in_array('INJURY',$extended))
		{
			array_push($fields,'INJURY');
		}
		foreach($fieldList as $field) {
			array_push($fields,$field);
		}
		if (in_array('FANTASY_POINTS',$extended))
		{
			array_push($fields,'FPTS');
		}
		if (in_array('FANTASY_RATINGS',$extended))
		{
			array_push($fields,'PR15');
		}
		if (in_array('TOP_PLAYERS',$extended))
		{
			array_push($fields,(($stat_type == TYPE_OFFENSE) ? 'TRO' : 'TRP'));
		}
		
		return $fields;
		
	} // END function
} // END if

//---------------------------------------------------------------

if(!function_exists('format_stats')) 
{
	function format_stats($stat_type = TYPE_OFFENSE, $stats = false, $categories = false, $stat_list = false, $position_list = false, $debug = false)
	{
		// ERROR HANDLING
		if ($stats === false || $categories === false || $stat_list === false)
		{	
			return false;
		}
		$max_count = 10;
		$newStats = array();
        foreach($stats as $row) {
			$newRow = array();
            $count = 0;

            foreach ($categories as $field) {
				if (isset($stat_list['general'][$field]['field']) && !empty($stat_list['general'][$field]['field']))
				{
					$col = $stat_list['general'][$field]['field'];
				}
				else if (isset($stat_list[$stat_type][$field]['field']) && !empty($stat_list[$stat_type][$field]['field']))
				{
					$col = $stat_list[$stat_type][$field]['field'];
				}
                if (strstr($col, ".") !== false)
                {
                    $tmpCat = explode(".",$col);
                    $col = $tmpCat[1];
                }
                switch ($field) {
					case 'BA':
					case 'TH':
						$newRow[$col] = get_hand($row[$col]);
						break;
					case 'pos':
					case 'positions':
						if (strpos($row[$col],":")) {
							$newRow[$col] = makeElidgibilityString($row[$col]);
						} else {
							$newRow[$col] = get_pos($row[$col],$position_list);
						}
						break;
					case 'POS':
					case 'ROLE':
						$newRow[$col] = get_pos($row[$col], $position_list);
						break;
					case 'LVL':
						$newRow[$col] = get_level($row[$col]);
						break;
					case 'AVG':
					case 'OBP':
					case 'SLG':
					case 'OPS':
					case 'WOBA':
					case 'OAVG':
					case 'BABIP':
						$val=sprintf("%.3f",$row[$col]);
						if ($val<1) {$val=strstr($val,".");}
						$newRow[$col] = $val;
						break;
					case 'ERA':
					case 'WHIP':
					case 'SO_IP':
					case 'BB_IP':
					case 'HR_IP':
					case 'PR15':
						$val=sprintf("%.2f",$row[$col]);
						if (($val<1)&&($col=='whip')) {$val=strstr($val,"0.");}
						$newRow[$col] = $val;
						break;
					case 'IP':
					case 'IPI':
					case 'IPL':
					case 'VORP':
						$val=sprintf("%.1f",$row[$col]);
						$newRow[$col] = $val;
						break;
					case 'WALK':
					case 'WIFF':
						$newRow[$col] = intval($row[$col])."%";
						break;
					case 'ADD':
					case 'DRAFT':
					case 'PN':
					case 'PNABBR':
					case 'TN':
					case 'TNACR':
					case 'INJURY':
						$newRow[$col] = format_extended_fields($field, $col, $row);
						break;
					default:
						if (!isset($row[$col]) && empty($row[$col])) {
							$newRow[$col] = 0;
						} else {
							$newRow[$col] = intval($row[$col]);
						}
						break;
				} // END switch

				// DEBUGGING
				if ($debug && $count < $max_count) {
					if (isset($newRow[$col])) {
						echo($col." = ".$newRow[$col]."<br />");
					}
				}
			} // END foreach
			array_push($newStats, $newRow);
			$count++;
		} // END foreach
		return $newStats;
	}
}

//--------------------------------------------------------------------

/**
 * Format Extended Fields.
 * Formats speciality stats fields for display. These fields have some extra formatting beyond numbers so they are segmented off.
 * This can also help extendability for future modules.
 *
 * @static
 * @param 	string 		$field			Field name
 * @param 	Array 		$row			Array of row information
 * @param 	Array 		$settings		OSP Settings object
 * @param 	Int 		$league_id		(Optional) League ID, defaults to 100
 * @param 	Boolean 	$debug			TRUE to enable debug messaging, FALSE to disable
 * @return 	String         				Modified single field of stats values
 *
 * @since 	0.3
 */
if(!function_exists('format_extended_fields')) 
{
    function format_extended_fields($field = false, $col = false, $row = false, $settings = false, $league_id = 100, $debug = false)
	{
		$newVal = array();
		switch ($field) 
		{
			case 'ADD':
				if (isset($fields['showTrans']) && $fields['showTrans'] === true)
				{
					$newVal = '<a href="#" rel="itemPick" id="'.$row['id'].'"><img src="'.$fields['img_path'].'add.png" width="16" height="16" alt="Add" title="Add" /></td>';
				}
				break;
			case 'DRAFT':
				if (isset($fields['showDraft']) && $fields['showDraft'] === true) {
					$newVal = '<a href="#" rel="draft" id="'.$row['id'].'"><img src="'.$fields['img_path'].'next.png" width="16" height="16" alt="Draft Player" title="Draft Player" /></a>';
				} else {
					$newVal = '- -';
				}
				break;
			case 'INJURY':
				// INJURY STATUS
				$injStatus = "";
				if ($row['injury_is_injured'] == 1) {
					$injStatus = make_injury_status_string($row);
				}
				if (!empty($injStatus)){
					if (isset($row['injury_dl_left']) && $row['injury_dl_left'] > 0) 
					{
                        $newVal = '&nbsp;<img src="'.img_path().'red_cross.gif" width="7" height="7" align="absmiddle" alt="'.$injStatus.'" title="'.$injStatus.'" />&nbsp; ';
					}
					else if (isset($row['injury_dtd_injury']) && $row['injury_dtd_injury'] != 0) 
					{
                        $newVal = '&nbsp;<acronym style="font-size:smaller;text-decoration:none, outline:none;font-weight:bold; color:#C00;" title="'.$injStatus.'">DTD</acronym>';
					}
				}
				break;
			// PLAYER NAME
			case 'PN':
			case 'PNABBR':
				
				if (isset($row['player_abbr_name']) && !empty($row['player_abbr_name']))
				{
					$name = $row['player_abbr_name'];
				}
				else 
				{
					$name = $row['first_name']." ".$row['last_name'];
				}
				if (isset($row['player_id'])) 
				{
					$val = anchor('players/profile/' . $row['player_id'],$name,array('target'=>'_blank'));
				}
				else 
				{
					$val = $name;
				}
				$newVal = $val;
				break;
            // TEAM NAME AND ACCRONYM
			case 'TN':
			case 'TNACR':
				
				if (isset($row['team_acr']) && !empty($row['team_acr']))
				{
					$name = $row['team_acr'];
				}
				else 
				{
					$name = $row['teamname']." ".$row['teamnick'];
				}
				if (isset($row['team_id'])) 
				{
					$val = anchor('teams/' . $row['team_id'],$name,array('target'=>'_blank'));
				}
				else 
				{
					$val = $name;
				}
				$newVal = $val;
				break;
		}
		// DEBUGGING
		if ($debug) {
			echo($col." = ".$newVal."<br />");
		}
		return $newVal;
	}
}

/* End of file sport_helper.php */
/* Location: ./open_sports_toolkit/helpers/drivers/baseball/sport_helper.php */