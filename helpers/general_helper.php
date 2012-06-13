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
 *	FORMAT STATS FOR DISPLAY
 *
 *	Based on the stat selected, handles converting the raw data output to display ready HTML.
 *
 * 	@todo	Remove fantasy specific references and code
 *	@author	Frank Esselink
 * 	@author	Jeff Fox
 *	@since	1.0
 */
function formatStatsForDisplay($player_stats = array(), $fields = array(), $config = array(), $league_id = -1, $player_teams = array(), $team_list = array(), $statsOnly = false, $showTrans = false, $showDraft = false,
								$pick_team_id = false,  $user_team_id = false, $draftStatus = false, $accessLevel = false, $isCommish = false, $draftDate = EMPTY_DATE_TIME_STR) {
	$count = 10;
	$newStats = array();
	foreach($player_stats as $row) {
		$newRow = array();
		foreach ($fields as $col) {
			if (isset($row[$col]) && !empty($row[$col])) {
				$newRow['id'] = $id = $row['id'];
				switch ($col) {
					case 'add':
						if ($showTrans === true) {
							$newRow[$col] = '<a href="#" rel="itemPick" id="'.$row['id'].'"><img src="'.$config['fantasy_web_root'].'images/icons/add.png" width="16" height="16" alt="Add" title="Add" /></td>';
						}
						break;
					case 'draft':
						if ($showDraft === true) {
							if (($pick_team_id == $user_team_id && ($draftStatus >= 2 && $draftStatus < 4)) || (($accessLevel == ACCESS_ADMINISTRATE || $isCommish) && ($draftDate != EMPTY_DATE_TIME_STR && time() > strtotime($draftDate)))) {
								$newRow[$col] = '<a href="#" rel="draft" id="'.$row['id'].'"><img src="'.$config['fantasy_web_root'].'images/icons/next.png" width="16" height="16" alt="Draft Player" title="Draft Player" /></a>';
							} else {
								$newRow[$col] = '- -';
							}
						}
						break;
					case 'player_name':

						if ($statsOnly === false) {
							$link = '/players/profile/';
							if (isset($league_id) && !empty($league_id) && $league_id != -1) {
								$link .= 'player_id/'.$id.'/league_id/'.$league_id;
							} else {
								$link .= $id;
							}
							$val = anchor($link,$row['first_name']." ".$row['last_name'],array('target'=>'_blank')).' <span style="font-size:smaller;">'.makeElidgibilityString($row['positions']).'</span>';

							// INJURY STATUS
							$injStatus = "";
							if ($row['injury_is_injured'] == 1) {
								$injStatus = makeInjuryStatusString($row);
							}
							if (!empty($injStatus)){
								if (isset($row['injury_dl_left']) && $row['injury_dl_left'] > 0) {
									$val .= '&nbsp;<img src="'.$config['fantasy_web_root'].'images/icons/red_cross.gif" width="7" height="7" align="absmiddle" alt="'.$injStatus.'" title="'.$injStatus.'" />&nbsp; ';
								} else if (isset($row['injury_dtd_injury']) && $row['injury_dtd_injury'] != 0) {
									$val .= '&nbsp;<acronym style="font-size:smaller;text-decoration:none, outline:none;font-weight:bold; color:#C00;" title="'.$injStatus.'">DTD</acronym>';
								}
							}
							if (isset($row['on_waivers']) && $row['on_waivers'] == 1) {
								$val .= '&nbsp;<b style="color:#ff6600;">W</b>&nbsp; ';
							}
							$newRow[$col] = $val;
						} else {
							$newRow[$col] = $row[$col];
							if (isset($row['on_waivers']) && $row['on_waivers'] == 1) {
								$newRow['on_waivers'] = 1;
							}
							if (isset($row['injury_dl_left']) && $row['injury_dl_left'] > 0) {
								$newRow['injury_dl_left'] = $row['injury_dl_left'];
							}
							if (isset($row['injury_left']) && $row['injury_left'] > 0) {
								$newRow['injury_left'] = $row['injury_left'];
							}
							if (isset($row['injury_id'])) {
								$newRow['injury_id'] = $row['injury_id'];
							}
							if (isset($row['injury_is_injured'])) {
								$newRow['injury_is_injured'] = $row['injury_is_injured'];
							}
							if (isset($row['injury_career_ending'])) {
								$newRow['injury_career_ending'] = $row['injury_career_ending'];
							}
							if (isset($row['injury_dtd_injury'])) {
								$newRow['injury_dtd_injury'] = $row['injury_dtd_injury'];
							}
						}
						break;
					case 'teamname':
						if ($statsOnly === false) {
							if ($league_id != -1) {
									if (isset($player_teams[$id])) {
									$team_obj = $team_list[$player_teams[$id]];
									$val = anchor('/team/info/'.$player_teams[$id],$team_obj['teamname']." ".$team_obj['teamnick'])."</td>";
								} else {
									$val = "Free Agent";
								}
							} else {
								$val = '';
							}
							$newRow[$col] = $val;
						}
						break;
					case 'bats':
					case 'throws':
						$newRow[$col] = get_hand($row[$col]);
						break;
					case 'pos':
					case 'positions':
						if (strpos($row[$col],":")) {
							$newRow[$col] = makeElidgibilityString($row[$col]);
						} else {
							$newRow[$col] = get_pos($row[$col]);
						}
						break;
					case 'position':
					case 'role':
						$newRow[$col] = get_pos($row[$col]);
						break;
					case 'level_id':
						$newRow[$col] = get_level($row[$col]);
						break;
					case 'avg':
					case 'obp':
					case 'slg':
					case 'ops':
					case 'wOBA':
					case 'oavg':
					case 'babip':
						$val=sprintf("%.3f",$row[$col]);
						if ($val<1) {$val=strstr($val,".");}
						$newRow[$col] = $val;
						break;
					case 'era':
					case 'whip':
					case 'k9':
					case 'bb9':
					case 'hr9':
					case 'rating':
						$val=sprintf("%.2f",$row[$col]);
						if (($val<1)&&($col=='whip')) {$val=strstr($val,".");}
						$newRow[$col] = $val;
						break;
					/*case 'rating':
						$val=sprintf("%.2f",$row[$col]);
						if ($rating > 0) {
							$color = "#080";
						} else if ($rating < 0) {
							$color = "#C00";
						} else {
							$color = "#000";
						}
						$val = '<span style="color:'.$color.';">'.$rating.'</span>';
						$newRow[$col] = $val;
						break;*/
					case 'ip':
					case 'vorp':
						$val=sprintf("%.1f",$row[$col]);
						$newRow[$col] = $val;
						break;
					case 'walk':
					case 'wiff':
						$newRow[$col] = intval($row[$col])."%";
						break;
					default:
						$newRow[$col] = intval($row[$col]);
						break;
				} // END switch

				// DEBUGGING
				if ($count < 5) {
					if (isset($newRow[$col])) {
						echo($col." = ".$newRow[$col]."<br />");
					}
				}

			} else {
				$newRow[$col] = 0;
			}
		} // END foreach
		array_push($newStats, $newRow);
		$count++;
	} // END foreach
	return $newStats;
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
 *	GET POSITION.
 *	Returns a position string name for the position passed.
 *
 *	@param	$pos		int		Position index int
 *	@return				String	Position name
 *
 *	@author	Frank Esselink
 * 	@since	1.0
 */
function get_pos($pos)
 {
   switch ($pos)
    {
      case -1:
	  	$txt="All";
	break;
	  case 0:
        $txt="PH";
	break;
      case 1:
        $txt="P";
	break;
      case 2:
        $txt="C";
	break;
      case 3:
        $txt="1B";
	break;
      case 4:
        $txt="2B";
	break;
      case 5:
        $txt="3B";
	break;
      case 6:
        $txt="SS";
	break;
      case 7:
        $txt="LF";
	break;
      case 8:
        $txt="CF";
	break;
      case 9:
        $txt="RF";
	break;
      case 10:
        $txt="DH";
	break;
      case 11:
        $txt="SP";
	break;
      case 12:
        $txt="MR";
	break;
      case 13:
        $txt="CL";
	break;
	  case 20:
	     $txt="OF";
	break;
	  case 21:
	     $txt="RP";
	break;
	  case 22:
	     $txt="IF";
	break;
	  case 23:
	     $txt="MI";
	break;
	case 24:
	     $txt="CI";
	break;
	case 25:
	     $txt="U";
	break;
      default:
        $txt="-";
	break;
    }
   return $txt;
 }
/**
 *	GET POSITION NUMBER.
 *	A reverse of the get_pos() function that returns a position number for the position
 *	string name passed.
 *
 *	@param	$pos		String	Position name
 *	@return				int		Position index int
 *
 *	@author	Jeff Fox
 * 	@since	1.0
 */
function get_pos_num($pos)
 {
   switch ($pos)
    {
	  case "PH":
        $txt=0;
	break;
      case "P":
        $txt=1;
	break;
      case "C":
        $txt=2;
	break;
      case "1B":
        $txt=3;
	break;
      case "2B":
        $txt=4;
	break;
      case "3B":
        $txt=5;
	break;
      case "SS":
        $txt=6;
	break;
      case "LF":
        $txt=7;
	break;
      case "CF":
        $txt=8;
	break;
      case "RF":
        $txt=9;
	break;
      case "DH":
        $txt=10;
	break;
      case "SP":
        $txt=11;
	break;
      case "MR":
        $txt=12;
	break;
      case "CL":
        $txt=13;
	break;
	  case "OF":
	     $txt=20;
	break;
	  case "RP":
	     $txt=21;
	break;
	  case "IF":
	     $txt=22;
	break;
	  case "MI":
	     $txt=23;
	break;
	case "CI":
	     $txt=24;
	break;
	case "U":
	     $txt=25;
	break;
      default:
	case "All":
	  	$txt=-1;
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
 *	GET LL CAT.
 *
 *	@param	$catID		int 		Category Int
 *	@param	$forSQL		boolean		TRUE to use SQL friendly names, FALSE for general names
 *	@return				String		Category Name
 *
 *	@author	Frank Esselink
 *	@author	Jeff Fox
 * 	@since	1.0
 */
function get_ll_cat($catID,$forSQL = false)
 {
   switch ($catID)
    {
      ## Batter Stats
      case 0: $txt="GS"; break;
      case 1: $txt="PA"; break;
      case 2: $txt="AB"; break;
      case 3: $txt="H"; break;
      case 4: $txt="K"; break;
      case 5: $txt="TB"; break;
      case 6: if($forSQL) $txt="d"; else $txt="2B"; break;
      case 7: if($forSQL) $txt="t"; else $txt="3B"; break;
      case 8: $txt="HR"; break;
      case 9: $txt="SB"; break;
      case 10: $txt="RBI"; break;
      case 11: $txt="R"; break;
      case 12: $txt="BB"; break;
      case 13: $txt="IBB"; break;
      case 14: if($forSQL) $txt="hp"; else $txt="HBP"; break;
      case 15: $txt="SH"; break;
      case 16: $txt="SF"; break;
      case 17: $txt="EBH"; break;
      case 18: $txt="AVG"; break;
      case 19: $txt="OBP"; break;
      case 20: $txt="SLG"; break;
      case 21: $txt="RC"; break;
      case 22: $txt="RC/27"; break;
      case 23: $txt="ISO"; break;
      case 24: $txt="TAVG"; break;
      case 25: $txt="OPS"; break;
      case 26: $txt="VORP"; break;

      ## Pitcher Stats
      case 27: $txt="G"; break;
      case 28: $txt="GS"; break;
      case 29: $txt="W"; break;
      case 30: $txt="L"; break;
      case 31: $txt="Win%"; break;
      case 32: if($forSQL) $txt="s"; else $txt="SV"; break;
      case 33: $txt="HLD"; break;
      case 34: $txt="IP"; break;
      case 35: $txt="BF"; break;
      case 36: $txt="HRA"; break;
      case 37: $txt="BB"; break;
      case 38: $txt="K"; break;
      case 39: $txt="WP"; break;
      case 40: $txt="ERA"; break;
      case 41: $txt="BABIP"; break;
      case 42: $txt="WHIP"; break;
      case 43: $txt="K/BB"; break;
      case 44: $txt="RA/9IP"; break;
      case 45: $txt="HR/9IP"; break;
      case 46: $txt="H/9IP"; break;
      case 47: $txt="BB/9IP"; break;
      case 48: $txt="K/9IP"; break;
      case 49: $txt="VORP"; break;
      case 50: $txt="RA"; break;
      case 51: $txt="GF"; break;
      case 52: $txt="QS"; break;
      case 53: $txt="QS%"; break;
      case 54: $txt="CG"; break;
      case 55: $txt="CG%"; break;
      case 56: $txt="SHO"; break;
      case 57: $txt="GB%"; break;

	  case 58: $txt="CS"; break;

	  case 59: $txt="HA"; break;
	  case 60: $txt="ER"; break;
	  case 61: $txt="BS"; break;
	  case 62: $txt="IPF"; break;
      default: $txt=$catID; break;
    }
   return $txt;
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
