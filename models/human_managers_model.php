<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	Human Managers Model.
 *
 *	Deals with human team owner information from OOTP games for online leagues.
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
class Human_managers_model extends Base_ootp_model {

	protected $table		= 'human_managers';
	protected $key			= 'human_manager_id';
	protected $soft_deletes	= false;
	protected $set_created	= false;
	protected $set_modified = false;
	
	/*--------------------------------------------------
	/
	/	PUBLIC FUNCTIONS
	/
	/-------------------------------------------------*/
	
	public function find_all_by($field=NULL, $value=NULL)
	{
		$this->join('teams','teams.team_id = '.$this->table.'.team_id','right outer');
		$this->select('human_manager_id, first_name, last_name, teams.team_id, teams.name as team_name, teams.nickname as team_nick, teams.logo_file');
		return parent::find_all_by($field, $value);
	}
	
	
	public function get_unowned_team_managers($league_id = 100, $team_exclusions = false) 
	{
		if (!$this->use_prefix) $this->db->dbprefix = '';
		$this->select("human_manager_id, first_name, last_name, teams.team_id, teams.name as team_name, teams.nickname as team_nick, teams.logo_file")
			 //->join('teams','teams.team_id = '.$this->table.'.team_id','left')
			 ->join($this->dbprefix.'teams_owners',$this->dbprefix.'teams_owners.team_id = '.$this->table.'.team_id','left')
			 ->where($this->dbprefix.'teams_owners.team_id IS NULL')
			 ->where($this->table.'.team_id <> 0');
		if ($team_exclusions !== false)
		{
			$exclude_team_str = "(";
			if (is_array($team_exclusions))
			{
				foreach($team_exclusions as $team_id)
				{
					if ($exclude_team_str != "(") { $exclude_team_str .= ","; }
					$exclude_team_str .= $team_id;
				}
			}
			else
			{
				$exclude_team_str .= $exclude_team_str;
			}
			$exclude_team_str .= ")";
			if ($exclude_team_str != "()")
			{
				$this->where($this->table.'.team_id NOT IN '.$exclude_team_str);
			}
		}
		$human_managers = $this->find_all_by($this->table.'.league_id',(int)$league_id);
		if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $human_managers;
	}
	public function get_owner_user_matches($league_id = 100, $exclusions = false) 
	{
		
		$userMatches = array();
		$nonMatches = array();
		if (!isset($this->user_model)) {
			$this->load->model('user_model');
		}
		$users = $this->user_model->find_all();
		
		$human_managers = $this->get_unowned_team_managers($league_id, $exclusions);
		if (isset($human_managers) && is_array($human_managers) && count($human_managers) > 0) {
			foreach ($human_managers as $row) {
				$match = false;
				$userCount = 0;
				foreach($users as $user) {
					if ((isset($user->first_name) && !empty($user->first_name) && $row->first_name == $user->first_name) &&
					(isset($user->last_name) && !empty($user->last_name) && $row->last_name == $user->last_name))
					{
						array_push($userMatches, array('human_manager_id'=>$row->human_manager_id, 'first_name'=>$row->first_name, 'last_name'=>$row->last_name, 'user_id'=>$user->id,'username'=>$user->username,
                        'team_name'=>$row->team_name,'team_nick'=>$row->team_nick,'logo_file'=>$row->logo_file));
						array_splice($users, $userCount, 1); // REMOVE THE USER MATCH
						$match = true;
					}
					$userCount++;
				}
				if ($match === false)
				{
					array_push($nonMatches, array('human_manager_id'=>$row->human_manager_id, 'first_name'=>$row->first_name, 'last_name'=>$row->last_name,'team_name'=>$row->team_name,'team_nick'=>$row->team_nick,'logo_file'=>$row->logo_file));
				}
			}
		}
		return array($userMatches,$nonMatches);
	}
	
}