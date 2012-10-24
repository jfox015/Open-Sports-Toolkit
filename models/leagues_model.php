<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	LEAGUES MODEL CLASS.
 *
 *	The Leagues model is designed to act as the interface for a collection of teams in a league. Right now, the 
 * 	league is defined as a top level organization. Sub leagues (LIke American and National Legaues) are considered 
 * 	to be seperate from this type of object.
 *
 *	@author			Jeff Fox <jfox015 (at) gmail (dot) com>
 *  @copyright   	(c)2009-12 Jeff Fox/Aeolian Digital Studios
 *	@version		1.0.3
 */
/*
	Copyright (c) 2012 Jeff Fox.

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
require_once(dirname(dirname(__FILE__)).'/models/base_ootp_model.php');
class Leagues_model extends Base_ootp_model 
{

	protected $table		= 'leagues';
	protected $key			= 'league_id';
	protected $soft_deletes	= false;
	protected $date_format	= 'datetime';
	protected $set_created	= false;
	protected $set_modified = false;
	
	/*--------------------------------------
	/	GENERAL LEAGUE INFORMATION
	/-------------------------------------*/
	/**
	 *	Get Leagues.
	 *	Returns a list of all public leagues. This function is an alias for the Bonfire BasebModel find_all() method. 
	 *	@return		Object	List of Leagues
	 */
	public function get_leagues()
	{
		return $this->find_all();
	}
	/**
	 *	Get Leagues Array.
	 *	Returns a list of all public leagues in array, not object format
	 *	@return		Array	Array of Leagues
	 */
	public function get_leagues_array()
	{
		$leagues = array();
		$query = $this->db->get($this->table);
		if ($query->num_rows() > 0) 
		{
			$leagues = $query->result();
		}
		return $leagues;
	}
	/**
	 *	Get League Count.
	 *	Returns a count of the number of leagues in the database.
	 *	@return		Int	league Count
	 */
	public function get_league_count()
	{
		return $this->db->count_all_results($this->table);
	}
	
	/*--------------------------------------
	/	SEASON SPECIFIC INFORMATION
	/-------------------------------------*/
	/**
	 *	In Season.
	 *	Returns a list of public leagues.
	 *	@param	$league_id	Defaults to 100
	 *	@return	TRUE or FALSE
	 */
	public function in_season($league_id = 100) {
		
		$league = $this->find_all_by('league_id',$league_id);
		
		if (isset($league) && is_array($league) && count($league)) {
			if ($league->league_state > 1 && $league->league_state < 4) {
				return true;
			} else {
				return false;
			}
		} else {
			return 'Required OOTP database tables have not been loaded.';
		}
	}
	/**
	 *	Get All Season.
	 *	Returns a list of years as found in the players stats tables.
	 *	@param	$league_id	int		Defaults to 100
	 *	@return				array	Array of year values
	 */
	public function get_all_seasons($league_id = 100) {
		$years = array();
		if (!$this->use_prefix) $this->db->dbprefix = '';
		$sql="SELECT DISTINCT year FROM players_career_batting_stats WHERE league_id=".$league_id." GROUP BY year ORDER BY year DESC;";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach($query->result_array() as $row) {
			   array_push($years,$row['year']);
			}
		}
		$query->free_result();
		if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
		return $years;
	}
	/**
	 *	Returns a string with the state of the league.
	 *	@return	String
	 */
	public function get_league_date($date_type = false, $league_id = 100) 
	{
		$league = $this->find_all_by('league_id',$league_id);
		if (isset($league) && is_array($league) && count($league)) 
		{
			$date = '';
			switch($date_type) 
			{
				case 'current':
					$date = $league->current_date;
					break;
				case 'start':
					$date = $league->start_date;
					break;
			}
			return $date;
		}
		else
		{
			return false;
		}
	}
	/**
	 *	GET SUBLEAGUES INFO.
		Returns an array of sub league IDs and names.
	 *	@return	Array $subleagues
	 */
	public function get_subleague_info($league_id = 100) {
		$subleagues = array();
        if (!$this->use_prefix) $this->db->dbprefix = '';
        $this->db->select('sub_league_id,name')
				 ->where('league_id',$league_id)
				 ->order_by('sub_league_id');
		$query = $this->db->get('sub_leagues');
		$subleagues = $query->result_array();
		$query->free_result();
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $subleagues;
	}
	/**
	 *	Returns a string with the state of the league.
	 *	@return	String
	 */
	public function get_league_state($league_id = 100) {
		
		$state = '';
		
		$league = $this->find_all_by('league_id',$league_id);
		
		if (isset($league) && is_array($league) && count($league)) {
			switch ($league->league_state) {
				case 4:
					$state = "Off Season";
					break;
				case 3:
					$state = "Playoffs";
					break;
				case 2:
					$state = "Regular Season";
					break;
				case 1:
					$state = "Spring Training";
					break;
				case 0:
					$state = "Preseason";
					break;
			}
		} else {
			$state = 'Required OOTP database tables have not been loaded.';
		}
		return $state;
	}

	/*---------------------------------------------------------
	/	!STATS
	/--------------------------------------------------------*/

	//---------------------------------------------------------------
	
	public function get_league_stats($league_id = false, $stats_type = TYPE_OFFENSE, $stats_class = array(), $stats_scope = STATS_SEASON, $params = array())
	{
		if ($league_id === false)
		{
			$this->error = "A league id value was not received.";
			return false;
		}
		
		if (Stats::get_sport() === false)
		{
			Stats::init('baseball','ootp13');
		}
		$stats = array();
		$fields = array();
		$sql = Stats::get_league_stats($league_id, $stats_type, $stats_class, $stats_scope, $params);

		if ($debug === true)
		{
			return $sql;
		}
		else 
		{
			$query = $this->db->query($sql);
			if ($query->num_rows() > 0)
			{
				$stats = $query->result_array();
				$fields = $query->list_fields();
			}
			$query->free_result();
			
			$stats = Stats::format_stats_for_display($stats, Stats::get_stats_fields($stats_type, $stats_class));
			
			return $stats;
		}
	}
	
}
/* End of leagues_model.php */
/* Location: ./open_sports_toolkit/models/leagues_model.php */