<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	SOURCE HELPER.
 *	A helper that defines the specific db mappings for the dtata source.
 *
 *	This source driver is scustom tuned for OOTP baseball version 13 and up.
 *
 * 	@sport 		Baseball
 *	@source		OOTP
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
				0 => 'players_career_batting_stats',
				1 => 'players_batting',
				2 => 'players_game_batting',
				3 => 'players_batting'
			),
			'pithcing'=>
			array(
				0 => 'players_career_pitching_stats',
				1 => 'players_pitching',
				2 => 'players_game_pitching_stats',
				3 => 'players_pitching'
			),
			'defense'=>
			array(
				0 => 'players_career_fielding_stats',
				1 => 'players_fielding',
				2 => 'players_game_fielding',
				3 => 'players_fielding'
			);
		);
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
		$fields = array('offense'=>
			array(
				"GS"  => array('id' => 0, 'field' => 'gs'),
				"PA"  => array('id' => 1,  'field' => 'pa'),
				"AB"  => array('id' => 2, 'field' => 'ab'),
				"H"  => array('id' => 3, 'field' => 'h'),
				"SO"  => array('id' => 4, 'field' => 'k'),
				"TB"  => array('id' => 5, 'field' => 'tb'),
				"2B"  => array('id' => 6, 'field' => 'd'),
				"3B"  => array('id' => 7, 'field' => 't'),
				"HR"  => array('id' => 8, 'field' => 'hr'),
				"SB"  => array('id' => 9, 'field' => 'sb'),
				"RBI" => array('id' => 10, 'field' => 'rbi'),
				"R" => array('id' => 11, 'field' => 'r'),
				"BB" => array('id' => 12, 'field' => 'bb'),
				"IBB" => array('id' => 13, 'field' => 'ibb'),
				"HBP" => array('id' => 14, 'field' => 'hp'),
				"SH" => array('id' => 15, 'field' => 'sh'),
				"SF" => array('id' => 16, 'field' => 'sf'),
				"XBH" => array('id' => 17, 'field' => 'xbh'),
				"AVG" => array('id' => 18, 'field' => 'avg'),
				"OBP" => array('id' => 19, 'field' => 'obp'),
				"SLG" => array('id' => 20, 'field' => 'slg'),
				"RC" => array('id' => 21, 'field' => 'rc'),
				"RC/27" => array('id' => 22, 'field' => 'rc/27'),
				"ISO" => array('id' => 23, 'field' => 'iso'),
				"TAVG" => array('id' => 24, 'field' => 'tavg'),
				"OPS" => array('id' => 25, 'field' => 'ops'),
				"VORP" => array('id' => 26, 'field' => 'vorp'),
			),
			"pitching"=>
			array(
				"G" => array('id' => 27, 'field' => 'g'),
                "GS" => array('id' => 28, 'field' => 'gs'),
                "W" => array('id' => 29, 'field' => 'w'),
                "L" => array('id' => 30, 'field' => 'l'),
                "Win%" => array('id' => 31, 'field' => 'win%'),
                "SV" => array('id' => 32, 'field' => 's'),
                "HLD" => array('id' => 33, 'field' => 'hld'),
                "IP" => array('id' => 34, 'field' => 'ip'),
                "BF" => array('id' => 35, 'field' => 'bf'),
                "HRA" => array('id' => 36, 'field' => 'hra'),
                "BB" => array('id' => 37, 'field' => 'bb'),
                "SO" => array('id' => 38, 'field' => 'k'),
                "WP" => array('id' => 39, 'field' => 'wp'),
                "ERA" => array('id' => 40, 'field' => 'era'),
                "BABIP" => array('id' => 41, 'field' => 'babip'),
                "WHIP" => array('id' => 42, 'field' => 'whip'),
                "K/BB" => array('id' => 43,  'field' => 'k/bb'),
                "RA/9IP" => array('id' => 44, 'field' => 'ra/9ip'),
                "HR/9IP" => array('id' => 45, 'field' => 'hr/9ip'),
                "H/9IP" => array('id' => 46, 'field' => 'h/9ip'),
                "BB/9IP" => array('id' => 47, 'field' => 'bb/9ip'),
                "K/9IP" => array('id' => 48, 'field' => 'k/9ip'),
                "VORP" => array('id' => 49, 'field' => 'vorp'),
                "RA" => array('id' => 50, 'field' => 'ra'),
                "GF" => array('id' => 51, 'field' => 'gf'),
                "QS" => array('id' => 52, 'field' => 'qs'),
                "QS%" => array('id' => 53, 'field' => 'qs%'),
                "CG" => array('id' => 54, 'field' => 'cg'),
                "CG%" => array('id' => 55, 'field' => 'cg%'),                
				"SHO" => array('id' => 56, 'field' => 'sho'),
                "SHO%" => array('id' => 57, 'field' => 'sho%'),
                "CS" => array('id' => 58, 'field' => 'cs'),
                "HA" => array('id' => 59, 'field' => 'ha'),
                "BS" => array('id' => 60, 'field' => 'bs'),
                "ER" => array('id' => 61, 'field' => 'er'),
                "IPF" => array('id' => 62, 'field' => 'ipf'),
            ),
			"defense"=>
			array(
				"TC" => array('id' => 63, 'field' => 'tc'),
				"A" => array('id' => 64, 'field' => 'a'),
				"PC" => array('id' => 65, 'field' => 'po'),
				"ER" => array('id' => 66, 'field' => 'er'),
				"IP" => array('id' => 67, 'field' => 'ip'),
				"G" => array('id' => 68, 'field' => 'g'),
				"GS" => array('id' => 69, 'field' => 'gs'),
				"E" => array('id' => 70, 'field' => 'e'),
				"DP" => array('id' => 71, 'field' => 'dp'),
				"TP" => array('id' => 72, 'field' => 'tp'),
				"PB" => array('id' => 73, 'field' => 'pb'),
				"SBA" => array('id' => 74, 'field' => 'sba'),
				"RTO" => array('id' => 75, 'field' => 'rto'),
				"IPF" => array('id' => 76, 'field' => 'ipf'),
				"PLAYS" => array('id' => 77, 'field' => 'plays'),
				"PLAYS_BASE" => array('id' => 78, 'field' => 'plays_base'),
				"ROE" => array('id' => 79, 'field' => 'roe'),
			)									
		);
		return $fields;
	}
}