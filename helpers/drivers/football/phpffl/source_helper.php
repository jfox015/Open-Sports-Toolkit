<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	SOURCE HELPER.
 *	A helper that defines the specific db mappings for the dtata source.
 *
 *	This source driver is scustom tuned for OOTP baseball version 13 and up.
 *
 * 	@sport 		Baseball
 *	@source		OOTP 13
 *	@author		Jeff Fox <jfox015@gmail.com>
 *
 */
 /*
	Copyright (c) 2012 Jeff Fox

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

//---------------------------------------------------------------

/**
 *	ID MAP.
 *	This function returns an array that maps the specific types of data identifers 
 *	keys in WHERE statements, to their specific identifier in the source data structure.
 *
 *	@return		Array	ID source values for league, team, players, etc.
 */
if(!function_exists('identifier_map')) 
{
	function identifier_map() 
	{
		$fields = array(
			'league'		=>		'ID',
			'team'			=>		'ID',
			'player'		=>		'ID',
			'game'			=>		'ID'
		);
        return $fields;
	}
}
//---------------------------------------------------------------

/**
 *	POSITION VERIFIER.
 *	This function returns an array that maps the specific players types to their
 *  position identifer.
 *
 *	For OOTP, position is set in the <i>position</i> field. 1 = Pitcher. All other 
 *	numbers are offense. Pitchers "position" is determined by the <i>role</i> field.
 *
 *	@return		Array	ID source values for league, team, players, etc.
 */
if(!function_exists('where_clause_speciality')) 
{
	function where_clause_speciality() 
	{
		return 'position = 1';
	}
}
if(!function_exists('where_clause_offense')) 
{
	function where_clause_offense() 
	{
		return 'position <> 1';
	}
}
if(!function_exists('get_split')) 
{
	function get_split($split_cat = SPLIT_SEASON, $tbl = '') 
	{
		$split_sql = '';
		if (!empty($tbl)) { $split_sql = $tbl.'.'; }
		switch ($split_cat) 
		{
			case SPLIT_SEASON:
				$split_sql .= 'split_id = 1';
				break;
			case SPLIT_PRESEASOM:
				$split_sql .= 'split_id = 2';
				break;
			case SPLIT_PLAYOFFS:
				$split_sql .= 'split_id = 3';
				break;
			case SPLIT_NONE:
			default:
				break;
		}
		return $split_sql;
	}
}
//---------------------------------------------------------------

/**
 *	TABLE MAP.
 *	This function returns an array that maps the specific type of carrer scopes
 * 	to their corresponding database tables or data endpoints.
 *
 *	@return		Array	Data source values for offense, defense and specilty fields
 */
if(!function_exists('table_map')) 
{
	function table_map() 
	{
		$fields = array('offense'=>
			array(
                STATS_CAREER => 'players_career_batting_stats',
                STATS_SEASON => 'players_career_batting_stats',
                STATS_GAME => 'players_game_batting',
                STATS_SEASON_AVG => 'players_career_batting_stats'
			),
			'speciality'=>
			array(
                STATS_CAREER => 'players_career_pitching_stats',
                STATS_SEASON => 'players_career_pitching_stats',
                STATS_GAME => 'players_game_pitching_stats',
                STATS_SEASON_AVG => 'players_career_pitching_stats'
			),
			'defense'=>
			array(
                STATS_CAREER => 'players_career_fielding_stats',
                STATS_SEASON => 'players_career_fielding_stats',
                STATS_GAME => 'players_game_fielding',
                STATS_SEASON_AVG => 'players_career_fielding_stats'
			),
			'injury'=>
			array(
                STATS_CAREER => 'players',
                STATS_SEASON => 'players',
                STATS_GAME => 'players',
                STATS_SEASON_AVG => 'players'
			),
			'team'=>'teams',
			'players'=>'players'
		);
        return $fields;
	}
}
//---------------------------------------------------------------

/**
 *	FIELD MAP.
 *	This function returns an array that maps the specific stats categories to source specific
 *	field values such as Index IDs and DB/endpoint fields.
 *
 *	@return		Array	Data field source values for offense, defense and specilty fields
 */
if(!function_exists('field_map')) 
{
	function field_map() 
	{
        $map = array(
			"stats" => array(
			'general'=>
				array(
					"" => array('id' => , 'field' => ''),
				),
				"speciality"=>
				array(
					"" => array('id' => , 'field' => ''),
				
				),
				"defense"=>
				array(
					"" => array('id' => , 'field' => ''),
					
				),
				"injury"=>
				array(
					"INJ" => array('id' => 91, 'field' => 'players.injury_is_injured'),
					"DTD" => array('id' => 92, 'field' => 'players.injury_dtd_injury'),
					"CE" => array('id' => 93, 'field' => 'players.injury_career_ending'),
					"DL" => array('id' => 94, 'field' => 'players.injury_dl_left'),
					"DAYS" => array('id' => 95, 'field' => 'players.injury_left'),
					"ID" => array('id' => 96, 'field' => 'players.injury_id')
				),
				"team"=>
				array(
					"TEAM_NAME" => array('id' => 103, 'field' => 'name'),
					"TEAM_NICK" => array('id' => 104, 'field' => 'nickname'),
					"W" => array('id' => 97, 'field' => 'w'),
					"L" => array('id' => 98, 'field' => 'l'),
					"PCT" => array('id' => 99, 'field' => 'pct'),
					"GB" => array('id' => 100, 'field' => 'gb'),
					"HOME" => array('id' => 101, 'field' => 'home'),
					"ROAD" => array('id' => 102, 'field' => 'road'),
					"RS" => array('id' => 102, 'field' => 'rs'),
					"RA" => array('id' => 102, 'field' => 'ra'),
					"DIFF" => array('id' => 102, 'field' => 'diff'),
					"STRK" => array('id' => 102, 'field' => 'strk'),
					"L10" => array('id' => 102, 'field' => 'l10') ,
					"POFF" => array('id' => 102, 'field' => 'poff')
				)
			),
			'positions'=>
			array(
				"QB"	=>array('id'=>1),
				"RB"	=>array('id'=>2),
				"WR"	=>array('id'=>3),
				"TE"	=>array('id'=>4),
				"K"		=>array('id'=>5),
				"DT"	=>array('id'=>8,
				"DB"	=>array('id'=>9),
				"CB"	=>array('id'=>10),
				"DE"	=>array('id'=>11),
				"ST"	=>array('id'=>12),
				"FB"	=>array('id'=>13),
				"FS"	=>array('id'=>14),
				"ILB"	=>array('id'=>15),
				"MLB"	=>array('id'=>16),
				"NT"	=>array('id'=>17),
				"SAF"	=>array('id'=>19),
				"SS"	=>array('id'=>20),
				"LB"	=>array('id'=>21)
			)
		return $map;
	}
}

/* End of file source_helper.php */
/* Location: ./open_sports_toolkit/helpers/drivers/baseball/ootp13/source_helper.php */
