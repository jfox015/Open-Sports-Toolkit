<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	SPORT HELPER.
 *	A helper that defines the specific types of data for each specific sport.
 *
 *
 * 	@sport 		Football
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
			""	=>array('lang'=>"", 'type' => array(''),
			
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
					"TN"	=>array('lang' => "TN"),
					"AGE"	=>array('lang' => "AGE"),
					"POS"	=>array('lang' => "POS"),
					"TH"	=>array('lang' => "TH"),
					"FPTS"	=>array('lang' => "FPTS"),
					"PR15"	=>array('lang' => "PR15"),
				),
			'offense'=>
                array(
                    'CMP' => array('lang' => 'CMP'),
					'2PT' => array('lang' => '2PT'),
					'AST' => array('lang' => 'AST'),
					'ATT' => array('lang' => 'ATT'),
					'AVG_O' => array('lang' => 'AVG_O'),
					'CMP%' => array('lang' => 'CMP%'),
					'FC' => array('lang' => 'FC'),
					'FD' => array('lang' => 'FD'),
					'FF' => array('lang' => 'FF'),
					'FG' => array('lang' => 'FG'),
					'FR' => array('lang' => 'FR'),
					'FUM' => array('lang' => 'FUM'),
					'GP' => array('lang' => 'GP'),
					'INT' => array('lang' => 'INT'),
					'KB' => array('lang' => 'KB'),
					'LNG' => array('lang' => 'LNG'),
					'LST' => array('lang' => 'LST'),
					'PASS' => array('lang' => 'PASS'),
					'PAT' => array('lang' => 'PAT'),
					'PD' => array('lang' => 'PD'),
					'PTS_O' => array('lang' => 'PTS_O'),
					'RAT' => array('lang' => 'RAT'),
					'REC_O' => array('lang' => 'REC_O'),
					'RET' => array('lang' => 'RET'),
					'RUSH' => array('lang' => 'RUSH'),
					'SACK' => array('lang' => 'SACK'),
					'SOLO' => array('lang' => 'SOLO'),
					'STF' => array('lang' => 'STF'),
					'STFYDS' => array('lang' => 'STFYDS'),
					'TACK' => array('lang' => 'TACK'),
					'TD_O' => array('lang' => 'TD_O'),
					'YDS_O' => array('lang' => 'YDS_O')
                ),
			'specialty' =>
                array(
                    '1-19' => array('lang' => '1-19'),
					'20-29' => array('lang' => '20-29'),
					'2PT' => array('lang' => '2PT'),
					'30-39' => array('lang' => '30-39'),
					'40-49' => array('lang' => '40-49'),
					'50+' => array('lang' => '50+'),
					'AST' => array('lang' => 'AST'),
					'AVG' => array('lang' => 'AVG'),
					'FF' => array('lang' => 'FF'),
					'FG' => array('lang' => 'FG'),
					'FGA' => array('lang' => 'FGA'),
					'FGM' => array('lang' => 'FGM'),
					'FR' => array('lang' => 'FR'),
					'GP' => array('lang' => 'GP'),
					'INT' => array('lang' => 'INT'),
					'KB' => array('lang' => 'KB'),
					'LNG_D' => array('lang' => 'LNG_D'),
					'PASS' => array('lang' => 'PASS'),
					'PAT' => array('lang' => 'PAT'),
					'PCT' => array('lang' => 'PCT'),
					'PD' => array('lang' => 'PD'),
					'PTS_S' => array('lang' => 'PTS_S'),
					'REC_S' => array('lang' => 'REC_S'),
					'RET' => array('lang' => 'RET'),
					'RUSH' => array('lang' => 'RUSH'),
					'SACK' => array('lang' => 'SACK'),
					'SOLO' => array('lang' => 'SOLO'),
					'STF' => array('lang' => 'STF'),
					'STFYDS' => array('lang' => 'STFYDS'),
					'TACK' => array('lang' => 'TACK'),
					'TD' => array('lang' => 'TD'),
					'XPA' => array('lang' => 'XPA'),
					'XPM' => array('lang' => 'XPM'),
					'YDS' => array('lang' => 'YDS')
                ),
			"defense"=>
                array(
					'2PT' => array('lang' => '2PT'),
					'AST' => array('lang' => 'AST'),
					'AVG_D' => array('lang' => 'AVG_D'),
					'FF' => array('lang' => 'FF'),
					'FG' => array('lang' => 'FG'),
					'FR' => array('lang' => 'FR'),
					'GP' => array('lang' => 'GP'),
					'INT' => array('lang' => 'INT'),
					'KB' => array('lang' => 'KB'),
					'LNG' => array('lang' => 'LNG'),
					'PASS' => array('lang' => 'PASS'),
					'PAT' => array('lang' => 'PAT'),
					'PD' => array('lang' => 'PD'),
					'PTS' => array('lang' => 'PTS'),
					'REC' => array('lang' => 'REC'),
					'RET' => array('lang' => 'RET'),
					'RUSH' => array('lang' => 'RUSH'),
					'SACK' => array('lang' => 'SACK'),
					'SOLO' => array('lang' => 'SOLO'),
					'STF' => array('lang' => 'STF'),
					'STFYDS' => array('lang' => 'STFYDS'),
					'TACK' => array('lang' => 'TACK'),
					'TD_D' => array('lang' => 'TD_D'),
					'YDS_D' => array('lang' => 'YDS_D')
                ),
            "injury"=>
                array(
                    "INJ" => array('lang' => array('INJURY'),
                    "DTD" => array('lang' => array('DTD_INJURY'),
                    "CE" => array('lang' => array('CE'),
                    "DL" => array('lang' => array('DL'),
                    "DAYS" => array('lang' => array('DAYS'),
                    "ID" => array('lang' => array('ID')
                ),
			"team"=>
				array(
					"W" => array('lang' => array('W'),
					"L" => array('lang' => array('L'),
					"PCT" => array('lang' => array('PCT'),
					"GB" => array('lang' => array('GB'),
					"HOME" => array('lang' => array('HOME'),
					"ROAD" => array('lang' => array('ROAD'),
					"RS" => array('lang' => array('RS'),
					"RA" => array('lang' => array('RA'),
					"DIFF" => array('lang' => array('DIFF'),
					"STRK" => array('lang' => array('STRK'),
					"L10" => array('lang' => array('L10'),
					"POFF" => array('lang' => array('POFF')
				)
		);
		return $stats;
	} // END function
} // END if

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
				case CLASS_COMPACT:
					$fieldList = array();
					break;
				case CLASS_BASIC:
					$fieldList = array();
					break;
				case CLASS_COMPLETE:
					$fieldList = array();
					break;
				case CLASS_EXPANDED:
					$fieldList = array();
					break;
				case CLASS_EXTENDED:
					$fieldList = array();
					break;
				case CLASS_STANDARD:
				default:
					$fieldList = array();
					break;
			} // END switch
		} else if ($stat_type == TYPE_SPECIALTY){
			switch ($stats_class) {
				case CLASS_COMPACT:
					$fieldList = array();
					break;
				case CLASS_BASIC:
					$fieldList = array('CMP','ATT','YDS','CMP%','AVG','TD','LNG','INT','FUM','RAT');
					break;
				case CLASS_COMPLETE:
					$fieldList = array();
					break;
				case CLASS_EXPANDED:
					$fieldList = array();
					break;
				case CLASS_EXTENDED:
					$fieldList = array();
					break;
				case CLASS_STANDARD:
				default:
					$fieldList = array();
					break;
			} // END switch
		} else if ($stat_type == TYPE_DEFENSE){
			switch ($stats_class) {
				case CLASS_COMPACT:
					$fieldList = array();
					break;
				default:
					$fieldList = array();
					break;
			} // END switch
		}  else if ($stat_type == TYPE_INJURY){
			switch ($stats_class) {
				default:
					$fieldList = array();
					break;
			} // END switch
		} // END if
		
		$fields = array();
		if (in_array('DEFAULT',$extended))
		{
			$genArr = array('PN','TN','POS');
			foreach($genArr as $field) {
				array_push($fields,$field);
			}
		}
		if (in_array('GENERAL',$extended))
		{
			$genArr = array('AGE','TH');
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
		return $fields;
		
	} // END function
} // END if


/* End of file sport_helper.php */
/* Location: ./open_sports_toolkit/helpers/drivers/baseball/sport_helper.php */