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
		$stats = array('offense'=>
			array(
				"GS"  => array('lang' => "GS"),
				"PA"  => array('lang' => "PA"),
				"AB"  => array('lang' => "AB"),
				"H"  => array('lang' => "H"),
				"SO"  => array('lang' => "SO"),
				"TB"  => array('lang' => "TB", 'formula' => $sqlOperator.'(h)+'.$sqlOperator.'(d)+('.$sqlOperator.'(t)*2)+('.$sqlOperator.'(hr)*3) as tb'),
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
				"XBH" => array('lang' => "XBH", 'formula' => '('.$sqlOperator.'(d)+'.$sqlOperator.'(t)+'.$sqlOperator.'(hr)) as xbh'),
				"AVG" => array('lang' => "AVG", 'formula' => 'if('.$sqlOperator.'(ab)=0,0,'.$sqlOperator.'(h)/'.$sqlOperator.'(ab)) as avg'),
				"OBP" => array('lang' => "OBP", 'formula' => 'if('.$sqlOperator.'(ab)=0,0,'.$sqlOperator.'(h)/'.$sqlOperator.'(ab)) as avg,if(('.$sqlOperator.'(ab)+'.$sqlOperator.'(bb)+'.$sqlOperator.'(hp)+'.$sqlOperator.'(sf))=0,0,('.$sqlOperator.'(h)+'.$sqlOperator.'(bb)+'.$sqlOperator.'(hp))/('.$sqlOperator.'(ab)+'.$sqlOperator.'(bb)+'.$sqlOperator.'(hp)+'.$sqlOperator.'(sf))) as obp'),
				"SLG" => array('lang' => "SLG", 'formula' => 'if('.$sqlOperator.'(ab)=0,0,('.$sqlOperator.'(h)+'.$sqlOperator.'(d)+2*'.$sqlOperator.'(t)+3*'.$sqlOperator.'(hr))/'.$sqlOperator.'(ab)) as slg'),
				"RC" => array('lang' => "RC"),
				"RC_27" => array('lang' => "RC_27"),
				"ISO" => array('lang' => "ISO", 'formula' => 'if('.$sqlOperator.'(ab)=0,0,('.$sqlOperator.'(tb)-'.$sqlOperator.'(h))/'.$sqlOperator.'(ab)) as iso'),
				"WOBA" => array('lang' => "WOBA", 'formula' => 'if(('.$sqlOperator.'(ab)+'.$sqlOperator.'(bb)+'.$sqlOperator.'(hp)+'.$sqlOperator.'(sf))=0,0,('.$sqlOperator.'(h)+'.$sqlOperator.'(bb)+'.$sqlOperator.'(hp))/('.$sqlOperator.'(ab)+'.$sqlOperator.'(bb)+'.$sqlOperator.'(hp)+'.$sqlOperator.'(sf)))+if('.$sqlOperator.'(ab)=0,0,('.$sqlOperator.'(h)+'.$sqlOperator.'(d)+2*'.$sqlOperator.'(t)+3*'.$sqlOperator.'(hr))/'.$sqlOperator.'(ab)) as ops,if('.$sqlOperator.'(pa)=0,0,(0.72*'.$sqlOperator.'(bb)+0.75*'.$sqlOperator.'(hp)+0.9*('.$sqlOperator.'(h)-'.$sqlOperator.'(d)-'.$sqlOperator.'(t)-'.$sqlOperator.'(hr))+0.92*0+1.24*'.$sqlOperator.'(d)+1.56*'.$sqlOperator.'(t)+1.95*'.$sqlOperator.'(hr))/'.$sqlOperator.'(pa)) as wOBA'),
				"TAVG" => array('lang' => "TAVG"),
				"OPS" => array('lang' => "OPS", 'formula' => 'if(('.$sqlOperator.'(ab)+'.$sqlOperator.'(bb)+'.$sqlOperator.'(hp)+'.$sqlOperator.'(sf))=0,0,('.$sqlOperator.'(h)+'.$sqlOperator.'(bb)+'.$sqlOperator.'(hp))/('.$sqlOperator.'(ab)+'.$sqlOperator.'(bb)+'.$sqlOperator.'(hp)+'.$sqlOperator.'(sf)))+if('.$sqlOperator.'(ab)=0,0,('.$sqlOperator.'(h)+'.$sqlOperator.'(d)+2*'.$sqlOperator.'(t)+3*'.$sqlOperator.'(hr))/'.$sqlOperator.'(ab)) as ops'),
				"VORP" => array('lang' => "VORP"),
				"GIDP" => array('lang' => "GIDP")
				"RISP" => array('lang' => "RISP")
				"WIFF" => array('lang' => "WIFF", 'formula' => 'if (('.$sqlOperator.'(k)/'.$sqlOperator.'(ab))*100=0,0,'.$sqlOperauu or.'(k)/'.$sqlOperator.'(ab)*100) as wiff')
				"WALK" => array('lang' => "WALK", 'formula' => 'if (('.$sqlOperator.'(bb)/('.$sqlOperator.'(ab)+'.$sqlOperator.'(bb)))*100=0,0,'.$sqlOperator.'(bb)/('.$sqlOperator.'(ab)+'.$sqlOperator.'(bb))*100) as walk')
			),
			"pitching"=>
			array(
				"G" => array('lang' => "G"),
                "GS" => array('lang' => "GS"),
                "W" => array('lang' => "W"),
                "L" => array('lang' => "L"),
                "Win%" => array('lang' => "Win%"),
                "SV" => array('lang' => "SV"),
                "HLD" => array('lang' => "HLD"),
                "IP" => array('lang' => "IPI", 'formula' => '('.$sqlOperator.'(ip)+('.$sqlOperator.'(ipf)/3)) as ip'),
                "BF" => array('lang' => "BF"),
                "HRA" => array('lang' => "HRA"),
                "BB" => array('lang' => "BB"),
                "SO" => array('lang' => "SO"),
                "WP" => array('lang' => "WP"),
                "ERA" => array('lang' => "ERA", 'formula' => 'if(('.$sqlOperator.'(ip)+('.$sqlOperator.'(ipf)/3))=0,0,9*'.$sqlOperator.'(er)/('.$sqlOperator.'(ip)+('.$sqlOperator.'(ipf)/3))) as era'),
                "BABIP" => array('lang' => "BABIP", 'formula' => 'if(('.$sqlOperator.'(ab)-'.$sqlOperator.'(k)-'.$sqlOperator.'(hra)+'.$sqlOperator.'(sf))=0,0,('.$sqlOperator.'(ha)-'.$sqlOperator.'(hra))/('.$sqlOperator.'(ab)-'.$sqlOperator.'(k)-'.$sqlOperator.'(hra)+'.$sqlOperator.'(sf))) as babip'),
                "WHIP" => array('lang' => "WHIP", 'formula' => 'if(('.$sqlOperator.'(ip)+('.$sqlOperator.'(ipf)/3))=0,0,('.$sqlOperator.'(ha)+'.$sqlOperator.'(bb))/('.$sqlOperator.'(ip)+('.$sqlOperator.'(ipf)/3))) as whip'),
                "SO_BB" => array('lang' => "SO_BB", 'formula' => ),
                "RA_IP" => array('lang' => "RA_9IP", 'formula' => ),
                "HR_IP" => array('lang' => "HR_9IP", 'formula' => 'if (('.$sqlOperator.'(hra)*9)/'.$sqlOperator.'(ip)=0,0,('.$sqlOperator.'(hra)*9)/'.$sqlOperator.'(ip)) as hr9'),
                "H_IP" => array('lang' => "H_9IP", 'formula' => ),
                "BB_IP" => array('lang' => "BB_9IP", 'formula' => 'if (('.$sqlOperator.'(bb)*9)/'.$sqlOperator.'(ip)=0,0,('.$sqlOperator.'(bb)*9)/'.$sqlOperator.'(ip)) as BB_9'),
                "SO_IP" => array('lang' => "SO_9IP", 'formula' => 'if (('.$sqlOperator.'(k)*9)/'.$sqlOperator.'(ip)=0,0,('.$sqlOperator.'(k)*9)/'.$sqlOperator.'(ip)) as k9'),
                "VORP" => array('lang' => "VORP"),
                "RA" => array('lang' => "RA"),
                "GF" => array('lang' => "GF"),
                "QS" => array('lang' => "QS"),
                "QS%" => array('lang' => "QS%"),
                "CG" => array('lang' => "CG"),
                "CG%" => array('lang' => "CG%")
				"SHO" => array('lang' => "SHO"),
                "SHO%" => array('lang' => "SHO%"),
                "CS" => array('lang' => "CS"),
                "HA" => array('lang' => "HA"),
                "BS" => array('lang' => "BS"),
                "SVO" => array('lang' => "SVO"),
                "ER" => array('lang' => "ER"),
                "IPF" => array('lang' => "IPIF"),
                "IR" => array('lang' => "IR"),
                "IRA" => array('lang' => "IRA"),
                "BK" => array('lang' => "BK"),
                "HB" => array('lang' => "HB"),
                "OBA" => array('lang' => "OBA", 'formula' => 'if('.$sqlOperator.'(ab)=0,0,'.$sqlOperator.'(ha)/'.$sqlOperator.'(ab)) as oavg'),
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
			)
		);
		return $stats;
	}
}