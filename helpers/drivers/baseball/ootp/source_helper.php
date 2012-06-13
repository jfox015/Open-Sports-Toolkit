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
                STATS_CAREER => 'players_career_batting_stats',
                STATS_SEASON => 'players_batting',
                STATS_GAME => 'players_game_batting',
                STATS_SEASON_AVG => 'players_batting'
			),
			'specialty'=>
			array(
                STATS_CAREER => 'players_career_pitching_stats',
                STATS_SEASON => 'players_pitching',
                STATS_GAME => 'players_game_pitching_stats',
                STATS_SEASON_AVG => 'players_pitching'
			),
			'defense'=>
			array(
                STATS_CAREER => 'players_career_fielding_stats',
                STATS_SEASON => 'players_fielding',
                STATS_GAME => 'players_game_fielding',
                STATS_SEASON_AVG => 'players_fielding'
			),
			'injury'=>
			array(
                STATS_CAREER => 'players',
                STATS_SEASON => 'players',
                STATS_GAME => 'players',
                STATS_SEASON_AVG => 'players'
			)
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
        $stats = array('offense'=>
			array(
				"GS"  => array('id' => 0, 'field' => 'gs'),
				"PA"  => array('id' => 1,  'field' => 'pa'),
				"AB"  => array('id' => 2, 'field' => 'ab'),
				"H"  => array('id' => 3, 'field' => 'h'),
				"SO"  => array('id' => 4, 'field' => 'k'),
				"TB"  => array('id' => 5, 'field' => 'tb', 'formula' => '[OPERATOR](h)+[OPERATOR](d)+([OPERATOR](t)*2)+([OPERATOR](hr)*3) as tb'),
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
				"XBH" => array('id' => 17, 'formula' => '([OPERATOR](d)+[OPERATOR](t)+[OPERATOR](hr)) as xbh'),
				"AVG" => array('id' => 18, 'formula' => 'if([OPERATOR](ab)=0,0,[OPERATOR](h)/[OPERATOR](ab)) as avg'),
				"OBP" => array('id' => 19, 'formula' => 'if([OPERATOR](ab)=0,0,[OPERATOR](h)/[OPERATOR](ab)) as avg,if(([OPERATOR](ab)+[OPERATOR](bb)+[OPERATOR](hp)+[OPERATOR](sf))=0,0,([OPERATOR](h)+[OPERATOR](bb)+[OPERATOR](hp))/([OPERATOR](ab)+[OPERATOR](bb)+[OPERATOR](hp)+[OPERATOR](sf))) as obp'),
				"SLG" => array('id' => 20, 'formula' => 'if([OPERATOR](ab)=0,0,([OPERATOR](h)+[OPERATOR](d)+2*[OPERATOR](t)+3*[OPERATOR](hr))/[OPERATOR](ab)) as slg'),
				"RC" => array('id' => 21, 'field' => 'rc'),
				"RC/27" => array('id' => 22, 'field' => 'rc/27'),
				"ISO" => array('id' => 23, 'formula' => 'if([OPERATOR](ab)=0,0,([OPERATOR](tb)-[OPERATOR](h))/[OPERATOR](ab)) as iso'),
				"WOBA" => array('id' => 24, 'formula' => 'if(([OPERATOR](ab)+[OPERATOR](bb)+[OPERATOR](hp)+[OPERATOR](sf))=0,0,([OPERATOR](h)+[OPERATOR](bb)+[OPERATOR](hp))/([OPERATOR](ab)+[OPERATOR](bb)+[OPERATOR](hp)+[OPERATOR](sf)))+if([OPERATOR](ab)=0,0,([OPERATOR](h)+[OPERATOR](d)+2*[OPERATOR](t)+3*[OPERATOR](hr))/[OPERATOR](ab)) as ops,if([OPERATOR](pa)=0,0,(0.72*[OPERATOR](bb)+0.75*[OPERATOR](hp)+0.9*([OPERATOR](h)-[OPERATOR](d)-[OPERATOR](t)-[OPERATOR](hr))+0.92*0+1.24*[OPERATOR](d)+1.56*[OPERATOR](t)+1.95*[OPERATOR](hr))/[OPERATOR](pa)) as wOBA'),
				"TAVG" => array('id' => 24, 'formula' => 'if([OPERATOR](ab)=0,0, (([OPERATOR](h)+[OPERATOR](d)+([OPERATOR](t)*2)+([OPERATOR](hr)*3))+[OPERATOR](bb)+[OPERATOR](hbp)+[OPERATOR](sb)-[OPERATOR](cs)/([OPERATOR](ab)+[OPERATOR](gidp))))'),
				"OPS" => array('id' => 25, 'formula' => 'if(([OPERATOR](ab)+[OPERATOR](bb)+[OPERATOR](hp)+[OPERATOR](sf))=0,0,([OPERATOR](h)+[OPERATOR](bb)+[OPERATOR](hp))/([OPERATOR](ab)+[OPERATOR](bb)+[OPERATOR](hp)+[OPERATOR](sf)))+if([OPERATOR](ab)=0,0,([OPERATOR](h)+[OPERATOR](d)+2*[OPERATOR](t)+3*[OPERATOR](hr))/[OPERATOR](ab)) as ops'),
				"VORP" => array('id' => 26, 'field' => 'vorp'),
				"GIDP" => array('id' => 80, 'field' => 'gidp'),
				"RISP" => array('id' => 81, 'field' => 'risp') ,
				"WIFF" => array('id' => 82, 'formula' => 'if (([OPERATOR](k)/[OPERATOR](ab))*100=0,0,[OPERATOR](k)/[OPERATOR](ab)*100) as wiff')        ,
				"WALK" => array('id' => 83, 'formula' => 'if (([OPERATOR](bb)/([OPERATOR](ab)+[OPERATOR](bb)))*100=0,0,[OPERATOR](bb)/([OPERATOR](ab)+[OPERATOR](bb))*100) as walk')
			),
			"specialty"=>
			array(
				"G" => array('id' => 27, 'field' => 'g'),
                "GS" => array('id' => 28, 'field' => 'gs'),
                "W" => array('id' => 29, 'field' => 'w'),
                "L" => array('id' => 30, 'field' => 'l'),
                "Win%" => array('id' => 31, 'formula' => 'if([OPERATOR](gs)=0,0, ([OPERATOR](w)/[OPERATOR](gs)) as win%'),
                "SV" => array('id' => 32, 'field' => 's'),
                "HLD" => array('id' => 33, 'field' => 'hld'),
                "IP" => array('id' => 34, 'formula' => '([OPERATOR](ip)+([OPERATOR](ipf)/3)) as ip'),
                "BF" => array('id' => 35, 'field' => 'bf'),
                "HRA" => array('id' => 36, 'field' => 'hra'),
                "BB" => array('id' => 37, 'field' => 'bb'),
                "SO" => array('id' => 38, 'field' => 'k'),
                "WP" => array('id' => 39, 'field' => 'wp'),
                "ERA" => array('id' => 40, 'formula' => 'if(([OPERATOR](ip)+([OPERATOR](ipf)/3))=0,0,9*[OPERATOR](er)/([OPERATOR](ip)+([OPERATOR](ipf)/3))) as era'),
                "BABIP" => array('id' => 41, 'formula' => 'if(([OPERATOR](ab)-[OPERATOR](k)-[OPERATOR](hra)+[OPERATOR](sf))=0,0,([OPERATOR](ha)-[OPERATOR](hra))/([OPERATOR](ab)-[OPERATOR](k)-[OPERATOR](hra)+[OPERATOR](sf))) as babip'),
                "WHIP" => array('id' => 42, 'formula' => 'if(([OPERATOR](ip)+([OPERATOR](ipf)/3))=0,0,([OPERATOR](ha)+[OPERATOR](bb))/([OPERATOR](ip)+([OPERATOR](ipf)/3))) as whip'),
                "SO_BB" => array('id' => 43,  'formula' => 'if (([OPERATOR](bb)=0,0,([OPERATOR](k))/[OPERATOR](bb)) as k/bb)'),
                "RA_IP" => array('id' => 44, 'formula' => 'if (([OPERATOR](ra)*9)/[OPERATOR](ip)=0,0,([OPERATOR](ra)*9)/[OPERATOR](ip)) as ra9)'),
                "HR_IP" => array('id' => 45, 'formula' => 'if (([OPERATOR](hra)*9)/[OPERATOR](ip)=0,0,([OPERATOR](hra)*9)/[OPERATOR](ip)) as ha9'),
                "H_IP" => array('id' => 46, 'formula' => 'if (([OPERATOR](ha)*9)/[OPERATOR](ip)=0,0,([OPERATOR](ha)*9)/[OPERATOR](ip)) as ha9'),
                "BB_IP" => array('id' => 47, 'formula' => 'if (([OPERATOR](bb)*9)/[OPERATOR](ip)=0,0,([OPERATOR](bb)*9)/[OPERATOR](ip)) as BB_9'),
                "SO_IP" => array('id' => 48, 'formula' => 'if (([OPERATOR](k)*9)/[OPERATOR](ip)=0,0,([OPERATOR](k)*9)/[OPERATOR](ip)) as k9'),
                "VORP" => array('id' => 49, 'field' => 'vorp'),
                "RA" => array('id' => 50, 'field' => 'ra'),
                "GF" => array('id' => 51, 'field' => 'gf'),
                "QS" => array('id' => 52, 'field' => 'qs'),
                "QS%" => array('id' => 53, 'formula' => 'if([OPERATOR](gs)=0,0, ([OPERATOR](qs)/[OPERATOR](gs)) as qs%'),
                "CG" => array('id' => 54, 'field' => 'cg'),
                "CG%" => array('id' => 55, 'formula' => 'if([OPERATOR](gs)=0,0, ([OPERATOR](cg)/[OPERATOR](gs)) as cg%'),                
				"SHO" => array('id' => 56, 'field' => 'sho'),
                "SHO%" => array('id' => 57, 'formula' => 'if([OPERATOR](gs)=0,0, ([OPERATOR](sho)/[OPERATOR](gs)) as sho%'),
                "CS" => array('id' => 58, 'field' => 'cs'),
                "HA" => array('id' => 59, 'field' => 'ha'),
                "BS" => array('id' => 60, 'field' => 'bs'),
                "ER" => array('id' => 61, 'field' => 'er'),
                "IPF" => array('id' => 62, 'field' => 'ipf'),
				"IR" => array('id' => 84, 'field' => "ir"),
                "IRA" => array('id' => 85, 'field' => "ira"),
                "BK" => array('id' => 86, 'field' => "bk"),
                "HB" => array('id' => 87, 'field' => "hb"),
                "OBA" => array('id' => 88, 'formula' => 'if([OPERATOR](ab)=0,0,[OPERATOR](ha)/[OPERATOR](ab)) as oavg'),
            
            ),
			"defense"=>
			array(
				"TC" => array('id' => 63, 'field' => 'tc'),
				"A" => array('id' => 64, 'field' => 'a'),
				"PO" => array('id' => 65, 'field' => 'po'),
				"ER" => array('id' => 66, 'field' => 'er'),
				"IP" => array('id' => 67, 'formula' => '([OPERATOR](ip)+([OPERATOR](ipf)/3)) as ip'),
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
				"FP" => array('id' => 89, 'formula' => 'if([OPERATOR](tc)=0,0,(if([OPERATOR](tc)=0,0,[OPERATOR](tc)-[OPERATOR](e)))-[OPERATOR](tc)) as fp'),
				"RF" => array('id' => 90, 'formula' => 'if([OPERATOR](ip)=0,0,((9*([OPERATOR](po)+[OPERATOR](a)))/([OPERATOR](ip)+([OPERATOR](ipf)/3)) as rf'),
			),
            "injury"=>
            array(
                "I" => array('id' => 91, 'field' => 'injury_is_injured'),
                "DTD" => array('id' => 92, 'field' => 'injury_dtd_injury'),
                "CE" => array('id' => 93, 'field' => 'injury_career_ending'),
                "DL" => array('id' => 94, 'field' => 'injury_dl_left'),
                "DAYS" => array('id' => 95, 'field' => 'injury_left'),
                "ID" => array('id' => 96, 'field' => 'injury_id')
            )
		);
		return $stats;
	}
}