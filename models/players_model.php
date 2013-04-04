<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	PLAYERS MODEL CLASS.
 *
 *	@author			Jeff Fox <jfox015 (at) gmail (dot) com>
 *  @copyright   	(c)2009-11 Jeff Fox/Aeolian Digital Studios
 *	@version		1.0
 *
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
class Players_model extends Base_ootp_model {

    /*--------------------------------
     /	VARIABLES
     /-------------------------------*/
    /**
     *	SLUG.
     *	@var $_NAME:Text
     */
    var $_NAME = 'player_model';
    protected $table        = 'players';
    protected $tables        = array();
    protected $key          = 'player_id';
    protected $soft_deletes = false;
    protected $date_format  = 'datetime';
    protected $set_created  = false;
    protected $set_modified = false;
    /**
     *	PLAYER ID.
     *	@var $player_id:Int
     */
    var $player_id = -1;
    /**
     *	PLAYER ROSTER STATUS.
     *	@var $player_status:Int
     */
    var $player_status = -1;
    /*---------------------------------------------
     /
     /	C'TOR
     /	Creates a new instance of player_model
     /
     /---------------------------------------------*/
    public function __construct() {
        parent::__construct();
        $this->load->database('default');
    }
    /*--------------------------------------------------
     /
     /	PUBLIC FUNCTIONS
     /
     /-------------------------------------------------*/
    
	//---------------------------------------------------------------
	
	// SPECIAL QUERIES
    /**
     * 	GET PLAYER CONUNT.
     *	Test function to assure that players have been imported from the OOTP players file
     * 	into the fantasy database.
     *
     *	@since	1.0.3
     */
    public function getPlayerCount() {
        if (!$this->use_prefix) $this->db->dbprefix = '';
        $this->db->select('player_id');
        $this->db->from($this->table);
        $count = $this->db->count_all_results();
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $count;
    }
    
	//---------------------------------------------------------------
	
	/**
	 *	GET PLAYER DETAILS.
	 *
	 *	Returns and array of players details values.
	 *
	 *	@param	$player_id	int		Player ID
	 *	@return				Array	Array of player detail values
	 */
	public function get_player_details($player_id = false, $settings = false) {

        if ($player_id === false) { return; }
        $details = array();
        $this->db->dbprefix = '';
        $this->db->select('players.player_id,first_name,last_name,players.nick_name as playerNickname,teams.team_id, teams.name AS team_name, teams.nickName as teamNickname, position,role, date_of_birth,weight,height,bats,throws,draft_year,draft_round,draft_pick,draft_team_id,retired,injury_is_injured, injury_dtd_injury, injury_career_ending, injury_dl_left, injury_left, injury_id, logo_file, players.city_of_birth_id, age');
        $this->db->join('teams','teams.team_id = players.team_id','right outer');
        $this->db->where('players.player_id',$player_id);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            $details = $query->row_array();

            $birthCity = '';
			$birthRegion = '';
			$birthNation = '';
			if ($settings !== false && $settings['osp.game_sport'] == 0 && $settings['osp.game_source'] == 'ootp')
			{
				if(isset($details['city_of_birth_id']) && $details['city_of_birth_id'] != 0)
				{
					$ver = intval($settings['osp.source_version']);
					$select = 'cities.name as birthCity, nations.short_name as birthNation';
					if ($ver < 12) {
						$select .= ',cities.region as birthRegion';
					} else {
						$select .= ',states.name as birthRegion';
					}
					$this->db->select($select);
					$this->db->join('nations','nations.nation_id = cities.nation_id','right outer');
					if ($ver >= 12) {
						$this->db->join('states','states.state_id = cities.state_id','right outer');
					}
					$this->db->where('cities.city_id',$details['city_of_birth_id']);
					$cQuery = $this->db->get('cities');
					if ($cQuery->num_rows() > 0) {
						$cRow = $cQuery->row();
						$birthCity = $cRow->birthCity;
						$birthRegion = $cRow->birthRegion;
						$birthNation = $cRow->birthNation;
					}
					$cQuery->free_result();
				}
				$details = $details + array('birthCity'=>$birthCity,'birthRegion'=>$birthRegion,'birthNation'=>$birthNation);
			}
		} else {
              $details['id'] = $details['player_id'] = -1;
              $details['first_name'] = "Not";
              $details['last_name'] = "Found";
		}
		$query->free_result();
		// print($this->db->last_query()."<br />");
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $details;
    }
    
	//---------------------------------------------------------------
	
	/**
	 *	GET PLAYERS DETAILS.
	 *
	 *	Similar to get_player_details, but accepts an array of player ids and returns and array
	 *	of players details objects.
	 *
	 *	@param	$players	Array	Array of Player IDs
	 *	@return				Array	Array of player detail value arrays
	 */
	public function get_players_details($players = array(), $settings = false) {

        if (sizeof($players) == 0) { return; }
        $playersInfo = array();

        foreach($players as $row) {
            $playersInfo = $playersInfo + array($row['player_id'] => $this->getPlayerDetails($row['player_id'], $settings));
        }
        //echo($this->db->last_query()."<br />");
        return $playersInfo;
    }

	//---------------------------------------------------------------
	
	/**
	 *	GET PLAYER NAME.
	 *
	 *	Returns the	players name.
	 *
	 *	@param	$player_id	Int		Player ID
	 *	@return				String	The players name
	 */
	public function get_player_name($player_id = false) {

        if ($player_id === false) { $player_id = $this->player_id; }

        $name = "";
        $this->db->dbprefix = '';
        // GET PLAYER POSITION
        $this->db->select('first_name, last_name');
        //$this->db->join("players","fantasy_players.player_id = players.player_id", "right outer");
        $this->db->where('id',$player_id);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $name = $row->first_name." ".$row->last_name;
        }
        $query->free_result();
        $this->db->dbprefix = $this->dbprefix;
        return $name;
    }
    
	//---------------------------------------------------------------
	
	/**
	 *	GET PLAYER POSITION.
	 *
	 *	Returns a players position ID. Requires the ganerla_helper <code>get_pos</code> function to covert to 
	 *	a human readable name.
	 *
	 *	@param	$player_id	Int		Player ID
	 *	@return				int		The player position ID.
	 */
	public function get_player_position($player_id = false) {

        if ($player_id === false) { $player_id = $this->player_id; }

        $pos = -1;
        if (!$this->use_prefix) $this->db->dbprefix = '';
        // GET PLAYER POSITION
        $this->db->select('position');
        $this->db->from($this->table);
        $this->db->where('player_id',$player_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $pos = $row->position;
        }
        $query->free_result();
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $pos;

    }

	//---------------------------------------------------------------
	
	/**
	 *	GET TEAM.
	 *	Returns a given players team ID.
	 *	
	 *	@param	$player_id	int	The Player ID
	 *	@return				int	Team ID
	 */
	public function get_player_team($player_id = false) {

        if ($player_id === false) { $player_id = $this->player_id; }

        $team_id = -1;
        // GET PLAYER TEAM ID
        if (!$this->use_prefix) $this->db->dbprefix = '';
        $this->db->select('team_id');
        $this->db->where('player_id',$player_id);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $team_id = $row->team_id;
        }
        $query->free_result();
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $team_id;
    }

	//---------------------------------------------------------------
	
	/**
	 *	GET PLAYERS.
	 *	Returns an array of players based on the passed arguments.
	 *	
	 *	@param	$league_id		int			The League ID
	 *	@param	$search_type	String		Search type (alpha, pos, status, all - default)
	 *	@param	$search_param	<mixed>		Search parameter value
	 *	@param	$selectBox		Boolean		TRUE to return players for select box, FALSE for normal return
	 *	@return					Array		Array of player data values
	 *
	 */
    public function get_players($league_id = false, $search_type = false, $search_param = false, $selectBox = false) {
        $players = array();
        if (!$this->use_prefix) $this->db->dbprefix = '';
        $this->db->select('players.player_id, first_name, last_name, players.position, players.role, players.injury_is_injured,
        players.injury_dtd_injury, players.injury_career_ending, players.injury_dl_left, players.injury_left, players.injury_id, age,
        if (players.team_id = 0, "-1", teams.team_id) as team_id,
       if (players.team_id = 0, "", teams.name) as teamname, if (players.team_id = 0, "",teams.nickname) as teamnick', false);
        $this->db->join('teams', 'teams.team_id = players.team_id', 'right outer');
        switch ($search_type) {
            case 'alpha':
                $this->db->like('players.last_name', $search_param, 'after');
                break;
            case 'pos':
                $col = "position";
                if ($search_param == 11 || $search_param == 12 || $search_param == 13) {
                    $col = "role";
                }
                if ($search_param == 20) {
                    $this->db->where('(players.position = 7 OR players.position = 8 OR players.position = 9)');
                } else if ($search_param == 12 || $search_param == 13) {
                    $this->db->where('(players.role = 12 OR players.role = 13)');
                } else {
                    $this->db->where('players.'.$col, $search_param);
                }
                break;
			case 'status':
				if ($search_param === false) $search_param = 1;
				$this->db->where('players.retired',$search_param);
            case 'all':
            default:
                break;
        } // END switch

        if ($league_id !== false) {
            $this->db->where('players.league_id',$league_id);
        }
        $this->db->order_by('players.last_name, players.first_name','asc');

        $query = $this->db->get($this->table);
        //echo($this->db->last_query()."<br />");

        if ($query->num_rows() > 0) {
            $fields = $query->list_fields();
            if ($selectBox === true) {
                $players = array(-1=>"Select Player");
            }
            foreach ($query->result() as $row) {
                $tmpPos = "";
                if ($row->position == 1) {
                    $tmpPos = $row->role;
                } else {
                    $tmpPos = $row->position;
                }
                if ($selectBox === false) {
                    $player = array();
                    foreach($fields as $field) {
                        $player[$field] = $row->$field;
                    }
                    $player['player_name'] = $row->first_name." ".$row->last_name;
                    $player['pos'] = $tmpPos;
                    array_push($players,$player);
                } else {
                    $players = $players + array($row->id=>$row->last_name.", ".$row->first_name." - ".get_pos($tmpPos));
                }
            }
        }
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $players;
    }

	//---------------------------------------------------------------
	
	/**
     *	GET ACTIVE PLAYERS.
     *	Custom Alias of get_players that filters out only active players.
     *
     *	@param	$league_id		int			League ID
     *	@param	$select_box		Boolean		TRUE to return players for select box
     *	@return					Array		Array of player values
     *
     */
   public function get_active_players($league_id = false, $select_box = false) {
        return $this->get_players($league_id, 'status', 1, $select_box);
    }

	//---------------------------------------------------------------
	
	/**
     *	GET RETIRED PLAYERS.
     *	Custom Alias of get_players that filters out only retired players.
     *
     *	@param	$league_id		int			League ID
     *	@param	$select_box		Boolean		TRUE to return players for select box
     *	@return					Array		Array of player values
     *
     */
    public function get_retired_players($league_id = false, $select_box = false) {
        return $this->get_players($league_id, 'status', 0, $select_box);
    }

	//---------------------------------------------------------------
	
	/**
     *	GET PLAYER AWARDS.
     *	Returns all awards won by the players broken out by award type.
     *
     *	@param	$league_id			League ID value
     *	@param	$player_id			Player Id, defaults to current player id if empty
     *	@return						Award Array
     *	@since						1.0
     *	@version					1.0.1
     *
     */
    public function get_player_awards($league_id, $player_id = false) {

        if ($player_id === false) { $player_id = $this->player_id; }

        $awards = array();
        if (!$this->use_prefix) $this->db->dbprefix = '';
        $this->db->select("award_id,year,position");
        $this->db->from('players_awards');
        $this->db->where('league_id',$league_id);
        $this->db->where('player_id',$player_id);
        $this->db->where_in('award_id',array(4,5,6,7,9));
        $this->db->order_by('award_id','award_id,year,position');
        $query = $this->db->get();
        $prevAW=-1;
        $cnt=0;
        if ($query->num_rows > 0) {
            $awardsByYear = array();
            $poy = array();
            $boy = array();
            $roy = array();
            $gg = array();
            $as = array();
            foreach($query->result_array() as $row) {
                $awid=$row['award_id'];
                $yr=$row['year'];
                $pos=$row['position'];
                if ($prevAW!=$awid) {
                    $awardsByYear[$awid]=$yr;
                } else {
                    $awardsByYear[$awid]=$awardsByYear[$awid].", ".$yr;
                } // END if

                switch ($awid) {
                    case 4: $poy[$yr]=1; break;
                    case 5: $boy[$yr]=1; break;
                    case 6: $roy[$yr]=1; break;
                    case 7: $gg[$yr][$pos]=1; break;
                    case 9: $as[$yr]=1; break;
                } // END switch
                $cnt++;
                $prevAW=$awid;


            } // END foreach
            $awards['byYear'] = $awardsByYear;
            $awards['poy'] = $poy;
            $awards['boy'] = $boy;
            $awards['roy'] = $roy;
            $awards['gg'] = $gg;
            $awards['as'] = $as;
        } // END if
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $awards;
    }
	
	//---------------------------------------------------------------
	
	/**
     *	UPDATE PLAYER RATINGS.
     *	Loads players tstistical performance for the period in days specififed to compute the average
     *	and standard population deviation values. Then, all players compiled stats for the given
     *	period are loaded and rated based on coparison to these averages and stored in the
     *	fantasy_players table.
     *
     *	@param	$ratingsPeriod		Number of days back to rate stats
     *	@param	$scoring_period		Scoring Period object
     *	@param	$ootp_league_id		OOTP League ID value, defaults to 100 if no value passed
     *	@return						Summary String
     *	@since						1.0.4
     *	@version					1.0
     *	@see						Controller->Admin->playerRatings()
     *
     */
    public function update_player_ratings($ratingsPeriod = 15, $scoring_period = false, $league_id = 100) {
        if (($scoring_period === false|| sizeof($scoring_period) < 1)) { return false; } // END if

        $this->lang->load('admin');
        /*--------------------------------------
          /
          /	1.0 ARRAY PREP
          /
          /-------------------------------------*/
        $error = false;
        /*--------------------------------------
          /	1.1 GET PLAYERS ARRAY
          /-------------------------------------*/
        $player_list = $this->getActivePlayers();
        $summary = $this->lang->line('sim_player_ratings');
        /*--------------------------------------
          /	1.2 DEFINE RATING PERIOD
          /-------------------------------------*/
        $day = 60*60*24;
        $period_start = date('Y-m-d',((strtotime($scoring_period['date_end']))-($day*$ratingsPeriod)));
        $statsTypes = array(1=>'batting',2=>'pitching');
        $statCats = array();
        $ratingsCats = array();

        $period_str = str_replace('[START_DATE]',$period_start,$this->lang->line('sim_player_rating_period'));
        $period_str = str_replace('[END_DATE]',$scoring_period['date_end'],$period_str);
        $summary .= str_replace('[DAYS]',$ratingsPeriod,$period_str);
        if (!$this->use_prefix) $this->db->dbprefix = '';
        /*--------------------------------------
        /
        /	2.0 STAT AVG,STDDEV LOOP
        /
        /-------------------------------------*/
        if (sizeof($player_list) > 0) {
            $summary .= str_replace('[PLAYER_COUNT]',sizeof($player_list),$this->lang->line('sim_player_rating_count'));
            $processCount = 0;
            /*-------------------------------------------
            /	2.1 BUOLD LIST OF ACTIVE PLAYERS
            /-------------------------------------------*/
            $players_str = "(";
            foreach($player_list as $row) {
                if ($players_str != "(") { $players_str .= ","; }
                $players_str .= $row['player_id'];
            }
            $players_str .= ")";
            /*-------------------------------
            /	2.2 SWITCH ON PLAYER TYPE
            /------------------------------*/
            $statTotals = array(1=>array(),2=>array());
            $statSummaries = array(1=>array(),2=>array());
            $summary .= $this->lang->line('sim_player_rating_statload');

            foreach ($statsTypes as $typeId => $type) {
                if ($typeId == 1) {
                    $table = "players_game_batting";
                    $qualifier = "ab";
                    $minQualify = 3.1;
                } else {
                    $table = "players_game_pitching_stats";
                    $qualifier = "ip";
                    $minQualify = 1;
                } // END if
                /*-------------------------------
                /	2.2.1 INDIVIDUAL STAT LOOP
                /------------------------------*/
                // BUILD QUERY TO PULL CURRENT GAME DATA FOR THIS PLAYER
                $ratingsCats = $ratingsCats + array($typeId => get_stats_for_ratings($typeId));
                $localStats = array();
                $statSum = "";
                foreach($ratingsCats[$typeId] as $id => $val) {
                    $statSum .= "<b>Stat = ".$val."</b><br />";
                    $tmpSelect = 'games.date, ';
                    $id = intval($id);
                    $stat = '';
                    // FILTER OUT COMPILED STATS LIKE AVG, ERA AND WHIP
                    switch($typeId) {
                        case 1:
                            if ($id <= 17 || $id >= 26) {
                                $stat = strtolower(get_ll_cat($id, true));
                            } // END if
                            break;
                        case 2:
                            if ($id <= 39 || $id >= 43) {
                                $stat = strtolower(get_ll_cat($id, true));
                            } // END if
                            break;
                        default:
                            break;
                    } // END switch
                    if (!empty($stat)) { $tmpSelect .= 'SUM(g) as sum_g, SUM('.$stat.') as sum_'.$stat.', SUM('.$qualifier.') as sum_'.$qualifier; }
                    /*-----------------------------------------
                    /	2.2.1.1 EXECUTE THE QUERY FOR THIS STAT
                    /----------------------------------------*/
                    $this->db->dbprefix = '';
                    $this->db->flush_cache();
                    $this->db->select($tmpSelect);
                    $this->db->join($table,'games.game_id = '.$table.'.game_id','left');
                    $this->db->where($table.'.player_id IN '.$players_str);
                    $this->db->where("DATEDIFF('".$period_start."',games.date)<=",0);
                    $this->db->where("DATEDIFF('".$scoring_period['date_end']."',games.date)>=",0);
                    $this->db->group_by($table.'.player_id');
                    $this->db->order_by($table.'.player_id', 'asc');
                    $query = $this->db->get($this->tables['OOTP_GAMES']);
                    //echo($this->db->last_query()."<br />");
                    if ($query->num_rows() > 0) {
                        $statCount = 0;
                        $statTotal = 0;
                        $statStr = 'sum_'.$stat;
                        $statQalifier = 'sum_'.$qualifier;
                        $statArr = array();
                        foreach($query->result() as $row) {
                            if (($row->$statQalifier / $row->sum_g) > $minQualify) {
                                array_push($statArr,$row->$statStr);
                            }
                        }
                        $statAvg = average($statArr);
                        $statSum .= $stat." total = ".$statTotal."<br />";
                        $statSum .= $stat." AVG = ".sprintf('%.3f',$statAvg)." (".$statTotal."/".$statCount.")<br />";
                        $stdDevTotal = 0;
                        $statDev = deviation($statArr);
                        if ($statDev < 0) { $statDev = -$statDev; }
                        $statSum .= $stat." STDDEV = ".$statDev."<br />";
                    } // END if
                    $localStats[$stat] = array('avg'=>$statAvg,'stddev'=>$statDev);
                    $query->free_result();
                    $statSum .= $statCount." Player Statistics met the qualified minimum.<br />";
                }
                $statSummaries[$typeId] = $statSum;
                $statTotals[$typeId] = $localStats;
            }
            $statTotalStr = str_replace('[BATTING_STAT_COUNT]',sizeof($statTotals[1]),$this->lang->line('sim_player_rating_statcount'));
            $summary .= str_replace('[PITCHING_STAT_COUNT]',sizeof($statTotals[2]),$statTotalStr);
            $summary .= "Batting Stat Details:<br />".$statSummaries[1];
            $summary .= "Pitching Stat Details:<br />".$statSummaries[2];

            $summary .= $this->lang->line('sim_players_rating_processing');
            foreach($player_list as $row) {
                $playerSum = "";
                if ($row['position'] != 1) {
                    $type = 1;
                    $table = "players_game_batting";
                    $qualifier = "ab";
                } else {
                    $type = 2;
                    $table = "players_game_pitching_stats";
                    $qualifier = "ip";
                } // END if
                $select = $table.'.player_id,SUM('.$qualifier.') as sum_'.$qualifier.',';
                foreach($ratingsCats[$type] as $id => $val) {
                    $stat = "";
                    $id = intval($id);
                    switch($type) {
                        case 1:
                            if ($id <= 17 || $id >= 26) {
                                $tmpStat = strtolower(get_ll_cat($id, true));
                                $stat = "SUM(".$tmpStat.") as sum_".$tmpStat;
                            } // END if
                            break;
                        case 2:
                            if ($id <= 39 || $id >= 43) {
                                $tmpStat = strtolower(get_ll_cat($id, true));
                                $stat = "SUM(".$tmpStat.") as sum_".$tmpStat;
                            } // END if
                            break;
                        default:
                            break;
                    } // END switch
                    if (!empty($stat)) {
                        if ($select != '') { $select.=","; } // END if
                        $select .= $stat;
                    } // END if
                } // END foreach

                $this->db->select($select);
                $this->db->join($table,'games.game_id = '.$table.'.game_id','left');
                $this->db->where($table.'.player_id', $row['player_id']);
                $this->db->where("DATEDIFF('".$period_start."',games.date)<=",0);
                $this->db->where("DATEDIFF('".$scoring_period['date_end']."',games.date)>=",0);
                $this->db->group_by($table.'.player_id');
                $this->db->order_by($table.'.'.$qualifier,'desc');
                $query = $this->db->get($this->tables['OOTP_GAMES']);
                $statCount = 0;
                $rating = 0;
                if ($query->num_rows() > 0) {
                    $pRow = $query->row();
                    $tmpQulaify = "sum_".$qualifier;
                    // ONLY PROCESS THIS PLAYER IS THERE ARE GOING TO BE STATS TO PROCESS
                    if ($pRow->$tmpQulaify > 0) {
                        foreach($ratingsCats[$type] as $id => $val) {
                            $stat = strtolower(get_ll_cat($id, true));
                            $tmpStat = "sum_".$stat;
                            // SKIP PLAYERS WITH NO APPEARENCES IN PLAY
                            $negative = false;
                            if (($type == 1 && $id == 4) || ($type == 2 && $id == 36) || ($type == 2 && $id == 37)) {
                                $negative = true;
                            }
                            $rawRating = $pRow->$tmpStat - $statTotals[$type][$stat]['avg'];
                            if ($statTotals[$type][$stat]['stddev'] != 0) {
                                $upRating = $rawRating / $statTotals[$type][$stat]['stddev'];
                                //print("rawRating /stdev = ".$upRating." (".$rawRating." / ".$statTotals[$type][$stat]['stddev'].")<br />");
                            } else {
                                $upRating = $rawRating;
                            }
                            if ($negative) {
                                $rating -= $upRating;
                            } else {
                                $rating += $upRating;
                            }
                            $statCount++;
                        }
                    }
                }
                $query->free_result();
                // GET THE AVERAGE OVERALL RATING
                //if ($rating != 0 && $statCount != 0) {
                //	$rating = $rating / $statCount;
                //}
                // SAVE THE UPDATED RATING
                $this->db->flush_cache();
                $data = array('rating'=>$rating);
                $this->db->where('player_id',$row['player_id']);
                $this->db->update($this->tblName,$data);
                $processCount++;
            }
            $result = 1;
            $summary .= str_replace('[PLAYER_COUNT]',$processCount,$this->lang->line('sim_players_rating_result'));

        } else {
            $result = -1;
            $summary .= $this->lang->line('sim_players_rating_no_players');
        }
        //print("<br />".$summary."<br />");
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return array($result,$summary);
    }
	
	/*---------------------------------------------------------------
	/
	/	STATS
	/   @deprecated
	/   All stats functions have been deprecated in favor of
	/   loading stats from the Stats class.
	/-------------------------------------------------------------*/
	
	//---------------------------------------------------------------
	
	/**
     *	GET PLAYER STATS FOR PERIOD
     *	Returns player stats from OOTP game data based on a fixed time period or a year.
     *	@since	1.0.1.9
     *	@param	$playerType			1= Batters, 2 = Pitchers
     *	@param	$scoring_period		Scoring Period Array object
     *	@param	$rules				Scoring Rules Array
     *	@param	$players			Players List Array, generated by TeamModel->GetBatters() or TeamModel->getPitchers()
     *	@param	$excludeList		Array of Player Ids to exclude
     *	@param	$batting_sort		Batting Stats sort column
     *	@param	$pitching_sort 		Pitching Stats sort column
     *	@return						Stats array object
     *	@see	TeamModel
     *  @deprecated
     *
     */
    public function getStatsforPeriod($playerType = 1, $scoring_period = array(), $rules = array(),
                                      $players = array(),$excludeList = array(), $searchType = 'all', $searchParam = false,
                                      $query_type = QUERY_STANDARD, $stats_range = -1, $limit = -1, $startIndex = 0,
                                      $batting_sort = false, $pitching_sort = false) {
        $stats = array();

        $playerList = "(";
        if (is_array($players) && sizeof($players) > 0) {
            foreach($players as $player_id => $playerData) {
                if ($playerList != "(") { $playerList .= ","; }
                $playerList .= $player_id;
            }
        }
        $playerList .= ")";

        $excludeLostStr = "(";
        if (is_array($excludeList) && sizeof($excludeList) > 0) {
            foreach($excludeList as $player_id) {
                if ($excludeLostStr != "(") { $excludeLostStr .= ","; }
                $excludeLostStr .= $player_id;
            }
        }
        $excludeLostStr .= ")";

        if (!$this->use_prefix) $this->db->dbprefix = '';
        // BUILD QUERY TO PULL CURRENT GAME DATA FOR THIS PLAYER
        $sql = 'SELECT players.player_id, players.position, players.role, players.player_id ,first_name, last_name,players.injury_is_injured, players.injury_dtd_injury, players.injury_career_ending, players.injury_dl_left, players.injury_left, players.injury_id,rating,';
        $sql .= player_stat_query_builder($playerType, $query_type, $rules);
        if ($playerType == 1) {
            $sql .= ",players.position as pos ";
            $tblName = 'players_game_batting';
            $posType = 'players.position';
            if ($batting_sort !== false) $order = $batting_sort;
            $order = 'ab';
        } else {
            $sql .= ",players.role as pos ";
            $tblName = 'players_game_pitching_stats';
            $posType = 'players.role';
            if ($pitching_sort !== false) $order = $pitching_sort;
            $order = 'ip';
        }
        $sql .= "FROM games ";
        $sql .= 'LEFT JOIN '.$tblName.' ON games.game_id = '.$tblName.'.game_id ';
        $sql .= 'RIGHT OUTER JOIN players ON players.player_id = '.$tblName.'.player_id ';
        //$sql .= 'RIGHT OUTER JOIN fantasy_players ON players.player_id = fantasy_players.player_id ';
        if (sizeof($rules) > 0 && isset($rules['scoring_type']) && $rules['scoring_type'] == LEAGUE_SCORING_HEADTOHEAD) {
            $order = 'fpts';
        }
        if (sizeof($scoring_period) > 0 && $stats_range == -1) {
            $sql .= "WHERE DATEDIFF('".$scoring_period['date_start']."',games.date)<= 0 ";
            $sql .= "AND DATEDIFF('".$scoring_period['date_end']."',games.date)>= 0 ";
        } else if (sizeof($scoring_period) == 0 && $stats_range != -1) {
            $year_time = (60*60*24*365);
            if ($stats_range != 4) {
                $sql .= ' AND games.year = '.date('Y',time()-($year_time * $stats_range));
            } else {
                $sql .= ' AND (games.year = '.date('Y',time()-($year_time)).' OR games.year = '.date('Y',time()-($year_time * 2)).' OR games.year = '.date('Y',time()-($year_time * 3)).")";
            }
        }
        switch ($searchType) {
            case 'alpha':
                $sql .= ' AND players.last_name LIKE "'.$searchParam.'%" ';
                break;
            case 'pos':
                $col = "position";
                if ($searchParam == 11 || $searchParam == 12 || $searchParam == 13) {
                    $col = "role";
                }
                if ($searchParam == 20) {
                    $sql .= ' AND (players.position = 7 OR players.position = 8 OR players.position = 9) ';
                } else if ($searchParam == 12 || $searchParam == 13) {
                    $sql .= ' AND (players.role = 12 OR players.role = 13) ';
                } else {
                    $sql .= ' AND players.'.$col.' = '.$searchParam." ";
                }
                break;
            case 'all':
            default:
                break;
        } // END switch
        if ($playerList != "()") {
            $sql .= "AND ".$tblName.".player_id IN ".$playerList.' ';
        }
        if ($excludeLostStr != "()") {
            $sql .= "AND ".$tblName.".player_id NOT IN ".$excludeLostStr.' ';
        }
        $sql .= "GROUP BY ".$tblName.'.player_id ';
        $sql .= "ORDER BY ".$order." DESC ";
        if ($limit != -1 && $startIndex == 0) {
            $sql.="LIMIT ".$limit;
        } else if ($limit != -1 && $startIndex > 0) {
            $sql.="LIMIT ".$startIndex.", ".$limit;
        }
        $gQuery = $this->db->query($sql);
        //echo($sql."<br />");
        if ($gQuery->num_rows() > 0) {
            $fields = $gQuery->list_fields();
            foreach ($gQuery->result() as $sRow) {
                $player = array();
                foreach($fields as $field) {
                    $player[$field] = $sRow->$field;
                }
                $player['player_name'] = $sRow->first_name." ".$sRow->last_name;
                if ($sRow->position == 1) {
                    $player['pos'] = $sRow->role;
                } else {
                    $player['pos'] = $sRow->position;
                }
                array_push($stats,$player);
            }
        }
        $gQuery->free_result();
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $stats;
    }

	//---------------------------------------------------------------
	
	/**
     *	GET CAREER STATS.
     *	Returns players career statistics.
     *
     *	@param	$ootp_league_id		OOTP League ID value
     *	@param	$player_id			Player Id, defaults to current player id if empty
     *	@return						Stat Array
     *	@since						1.0
     *	@version					1.0.2
     *
     */
    public function get_career_stats($ootp_league_id, $player_id = false) {
        if ($player_id === false) { $player_id = $this->player_id; }

        $career_stats = array();
        // GET PLAYER POSITION
        $pos = $this->getPlayerPosition();
        if (!$this->use_prefix) $this->db->dbprefix = '';
        $this->db->flush_cache();
        if ($pos == 1) {
            $sql="SELECT pcp.year,pcp.team_id,g,gs,w,l,s,(ip*3+ipf)/3 as ip,ha,r,er,hra,bb,k,hld,cg,sho,ab,sf,vorp";
            $sql.=",bf,pi,qs,gf,gb,fb,wp,bk,svo,bs";     ## Expanded Stats
            $sql.=" FROM players_career_pitching_stats as pcp WHERE player_id=$player_id";
            $sql.=" AND league_id=$ootp_league_id AND split_id=1";
            $sql.=" ORDER BY pcp.year;";
        } else {
            $sql="SELECT pcb.year,pcb.team_id,g,ab,h,d,t,hr,rbi,r,bb,hp,sh,sf,k,sb,cs,pa,vorp";
            $sql.=",pitches_seen,ibb,gdp";
            $sql.=" FROM players_career_batting_stats as pcb WHERE player_id=$player_id";
            $sql.=" AND league_id=$ootp_league_id AND split_id=1";
            $sql.=" ORDER BY pcb.year;";
        } // END if
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            $fields = $query->list_fields();
            foreach($query->result() as $row) {
                $year = array();
                foreach($fields as $field) {
                    $year[$field] = $row->$field;
                } // END foreach
                array_push($career_stats,$year);
            } // END foreach
        } // END if
        $query->free_result();
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $career_stats;
    }

	//---------------------------------------------------------------
	
	/**
     *	GET RECENT GAME STATS.
     *	Returns statistics for recent games.
     *
     *	@param	$league_id			League ID value
     *	@param	$player_id			Player Id, defaults to current player id if empty
     *	@return						Stat Array in key = value format
     *	@since						1.0
     *	@version					1.0.2
     *
     */
    public function get_recent_game_stats($league_id = false, $last_date = false, $year = false, $days = 7, $player_id = false) {

		if ($player_id === false) 	{ $player_id = $this->player_id; }
		if ($last_date === false) 	{ $last_date = date('Y-m-d 00:00:00'); }
		if ($year === false) 		{ $year = date('Y'); }

        // GET ALL TEAMS
        if (!$this->use_prefix) $this->db->dbprefix = '';
        $teams = array();
        $this->db->select("team_id, abbr");
        $this->db->where("league_id",$league_id);
        $query = $this->db->get("teams");
        if ($query->num_rows() > 0) {
            foreach($query->result() as $row) {
                $teams[$row->team_id] = $row->abbr;
            }
        }
        $query->free_result();

        $stats = array();
        $pos = 0;

        // GET PLAYER TEAM & POSITION
        $team_id = $this->get_player_team($player_id);
		$pos = $this->get_player_position($player_id);

        $select = "games.date,games.home_team,games.away_team,";
        if (!$this->use_prefix) $this->db->dbprefix = '';
        if ($pos == 1) {
            $this->db->select($select.'w,l,s,ip,ha,er,bb,k');
            $table = 'players_game_pitching_stats';
        } else {
            $this->db->select($select.'ab,r,h,hr,rbi,bb,sb');
            $table = 'players_game_batting';
        }
        $this->db->from($table);
        $this->db->join('games',$table.".game_id = games.game_id",'left');
        $this->db->where($table.'.player_id',$player_id);
        $this->db->where($table.'.year',$year);
        $this->db->where($table.'.level_id',1);
        $this->db->where('games.game_type',0);
        $this->db->where("(games.home_team = ".$team_id." OR games.away_team = ".$team_id.")");
        $this->db->where("DATEDIFF('".$last_date."',games.date) > ",0);
        $this->db->order_by("games.date",'desc');
        $query = $this->db->get();
        $fields = $query->list_fields();
        if ($query->num_rows() > 0) {
            $count = 0;
            foreach($query->result() as $row) {
                $game = array();
                foreach($fields as $field) {
                    if ($field != 'home_team' && $field != 'away_team') {
                        $game[$field] = $row->$field;
                    }
                }
                if ($row->home_team == $team_id) {
                    if (isset($teams[$row->away_team])) {
                        $game['opp'] = $teams[$row->away_team];
                    } else {
                        $game['opp'] = "?";
                    }
                } else if ($row->away_team == $team_id) {
                    if (isset($teams[$row->home_team])) {
                        $game['opp'] = $teams[$row->home_team];
                    } else {
                        $game['opp'] = "?";
                    }
                } else {
                    $game['opp'] = "?";
                }
                array_push($stats,$game);
                $count++;
                if ($count >= $days) break;
            }
        }
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $stats;

    }	

	//---------------------------------------------------------------
	
	/**
     *	GET PLAYER SCHEDULES.
     *	Returns a list of upcoming games for one or more players.
     *
     *	@param	$players_list		Player id list
     *	@param	$start_date			The first day to show games for
     *	@param	$sim_len			Number of days to display
     *	@return						Schedule Array in key = value format
     *
     */
    public function get_player_schedules($players_list = false, $start_date = false ,$sim_len = 7) 
	{
		if (!function_exists('getVisibleDays')) {
			$this->load->helper('open_sports_toolkit/datalist');
		}
		
		$daysInPeriod = getVisibleDays($start_date, $sim_len);  
		$schedules = array();
		//$schedules = array('players_active'=>array(),'players_reserve'=>array(),'players_injured'=>array());
		// LOAD PLAYERS
        if (!$this->use_prefix) $this->db->dbprefix = '';
        if (is_array($players_list) && sizeof($players_list) > 0) {
			foreach ($players_list as $arr_id => $players_arr) {
				//if ($arr_id == 0) { $list = 'players_active'; } else if ($arr_id == 1) { $list = 'players_reserve'; } else { $list = 'players_injured'; }
				if (is_array($players_arr) && sizeof($players_arr) > 0) {
					foreach ($players_arr as $id => $data) {
						$projStarts = array();
						/**
						 * TODO  GET PITCHING PROJECTED STARTS
						 */
						//if ($data['position'] == 1 && $data['role'] == 11) {
							// GET START PROJECTIONS
						//}
						$this->db->flush_cache();
						$this->db->select('game_id,home_team,away_team,games.date AS game_date,time AS game_time');
						$this->db->where("DATEDIFF('".$start_date."',games.date)<=",0);
						$this->db->where("DATEDIFF('".$start_date."',games.date)>-",$sim_len);
						$this->db->where('(home_team = '.$data['team_id'].' OR away_team = '.$data['team_id'].')');
						$this->db->order_by('games.date','asc');
						$query = $this->db->get('games');
						$player_schedule = array();
						$offDay = 0;
						if ($query->num_rows() > 0) {
							$dateCount = 0;
							$prevDate = -1;
							foreach ($query->result() as $row) {
								$game_date = strtotime($row->game_date);
								// HANDLE MULTIPLE GAMES FOR A SINGLE DAY
								if (($prevDate != -1 && $prevDate == $game_date)) {
									$dateCount -= 1;
								}
								$calendar_date = strtotime($daysInPeriod[$dateCount]);
								$date_diff = $game_date - $calendar_date;
								$diffDays = floor($date_diff/(60*60*24));
								// IF AN OFF DAY IS FOUND, ADD AN EMPTY FIELD
								if ($diffDays != 0) {
									$player_schedule = $player_schedule + array(($offDay-=1)=>array('home_team'=>-1,
															   'away_team'=>-1));
									$dateCount++;
								}
								$player_schedule = $player_schedule + array($row->game_id=>array('home_team'=>$row->home_team,
															   'away_team'=>$row->away_team,'game_date'=>$row->game_date,
															   'game_time'=>$row->game_time));
								// SAVE LAST DATE USED TO CORRETLY HANDLE MUTLTIPLE GAMES ON A SINGLE DAY
								$prevDate = $game_date;
								$dateCount++;
							} // END foreach
							if (sizeof($player_schedule) < intval($sim_len)) {
								$player_schedule = $player_schedule + array(($offDay-=1)=>array('home_team'=>-1,
												   'away_team'=>-1));
							}
						} // END if
						$query->free_result();
						$schedules = $schedules + array($id=>$player_schedule);
					} // END foreach
				} // END if
			} // END foreach
		} // END if
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $schedules;
	}
	//---------------------------------------------------------------
	
	
	public function get_current_stats($position = 1, $league_id = false, $league_year = false, $team_id = false) {
       
		$players_list = array();
        if (!$this->use_prefix) $this->db->dbprefix = '';
        $this->db->flush_cache();
        $table = '';
		$select = 'first_name, last_name, players.position, players.role, age, throws, bats, ';
		if ($position == 1) {
            $select .= "w,l,if((ip+(ipf/3))=0,0,9*er/(ip+(ipf/3))) as era,g,gs,(ip+(ipf/3)) as ip,k,bb,hra,s";
            $table = 'players_career_pitching_stats';
        } else {
            $select .= "if(ab=0, 0,(h/ab) ) as avg,g,ab,r,h,hr,rbi,bb,k,sb,cs";
            $table = 'players_career_batting_stats';
        }
		$this->db->select($select, false)
				 ->join('players','players.player_id = '.$table.'.player_id', 'left outer');
		if ($team_id !== false)
		{
			$this->db->where('players.team_id',(int)$team_id);
        }
		$this->db->where('split_id',1);
        if ($league_year !== false)
		{
			$this->db->where('year',(int)$league_year);
		}
        if ($league_id !== false)
        {
            $this->db->where('players.league_id',(int)$league_id);
        }
        if ($position == 1) {
			$this->db->where('players.position',1);
		} else {
			$this->db->where('players.position <> 1');
		}
        $this->db->order_by('players.position','ASC');
        $query = $this->db->get($table);
        $fields = $query->list_fields();
        if ($query->num_rows() > 0) {
            foreach($query->result() as $row) {
				$stats = array();
				foreach($fields as $field) {
					$stats[$field] = $row->$field;
				}
				array_push($players_list,$stats);
			}
        }
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $players_list;
    }
}  
/* End of players_model.php */
/* Location: ./open_sports_toolkit/models/players_model.php */
