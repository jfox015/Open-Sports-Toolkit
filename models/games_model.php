<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	GAMES MODEL CLASS.
 *
 *	A model for interactig with game data for the chosen sport.
 *
 *	@author			Jeff Fox <jfox015 (at) gmail (dot) com>
 *  @copyright   	(c)2009-12 Jeff Fox/Aeolian Digital Studios
 *	@version		1.0
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
class Games_model extends Base_ootp_model 
{

	protected $table		= 'games';
	protected $key			= 'game_id';
	protected $soft_deletes	= false;
	protected $date_format	= 'datetime';
	protected $set_created	= false;
	protected $set_modified = false;
	
	/*--------------------------------------
	/	GENERAL LEAGUE INFORMATION
	/-------------------------------------*/
	/**
	 *	Get Games.
	 *	Returns a list of all public games. This function is an alias for the Bonfire BasebModel find_all() method. 
	 *	@return		Object	List of Games
	 */
	public function get_games()
	{
		return $this->find_all();
	}
	/**
	 *	Get Games Count.
	 *	Returns a count of the number of games in the database.
	 *	@return		Int	league Count
	 */
	public function get_games_count()
	{
		return $this->db->count_all_results($this->table);
	}
	
}
/* End of leagues_model.php */
/* Location: ./open_sports_toolkit/models/leagues_model.php */