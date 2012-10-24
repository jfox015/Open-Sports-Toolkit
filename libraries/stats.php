<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Stats Class
 *
 * The Stats class provides an abstract interface to sports statistics pulled from either a database or 
 * remotes data source like JSON or XML.
 *
 * @package    Open Sports Toolkit
 * @subpackage Libraries
 * @category   Libraries
 * @author     Jeff Fox
 * @link       http://www.aeoliandigital.com/
 * @version    0.1
 *
 */
class Stats
{

	/**
	 * Whether or not debug messages should be displayed.
	 *
	 * @access private
	 *
	 * @var bool
	 */
	private static $debug = FALSE;

	/**
	 * Stores the CodeIgniter core object.
	 *
	 * @access protected
	 *
	 * @var object
	 */
	protected static $ci;

	/**
	 * The base folder (relative to the module root) that all of the individual sport 
	 * and stats source drivers are stored in.
	 *
	 * @access private
	 *
	 * @var string
	 */
	private static $driver_library		= 'helpers/stats_drivers/';

	/**
	 * The names of the sports supported by the class.
	 *
	 * @access public
	 *
	 * @var array
	 */
	public static $sports 	= array(
										0 		=> 'baseball',
										1		=> 'football',
										2		=> 'backetball',
										3		=> 'hockey',
										4		=> 'soccer'
									);
									
	/**
	 * The sport and data source specific stats listing.
	 *
	 * @access protected
	 *
	 * @var int
	 */
	protected static $sport		= false;
	/**
	 * The sport and data source specific stats listing.
	 *
	 * @access protected
	 *
	 * @var int
	 */
	protected static $source	= false;

	/**
	 * Tyhe sport and data source specific stats listing.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $stat_list		= array();
	
	/**
	 * Tyhe sport and data source specific position listing.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $position_list		= array();
	
	/**
	 * Mapping of source data sources for the stat scope type (career, season, game).
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $table_list		= array();

	
	//--------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * This if here solely for CI loading to work. Just calls the init( ) method.
	 *
	 * @return void
	 */
	public function __construct()
	{
		self::$ci =& get_instance();

		self::init();

	} //end __construct()

	//--------------------------------------------------------------------

    /**
     * Load the assets config file, and inserts the base
     * css and js into our array for later use. This ensures
     * that these files will be processed first, in the order
     * the user is expecting, prior to and later-added files.
     *
     * @static
     * @param bool $sport
     * @param bool $source
     * @return void
     */

	public static function init($sport = false, $source = false)
	{
        if ($sport !== false)
        {
            self::$sport = $sport;
			self::load_sport_helper();
			self::$ci->lang->load('open_sports_toolkit/'.self::$sport.'_acyn');
			self::$ci->lang->load('open_sports_toolkit/'.self::$sport.'_full');
			self::$stat_list = stat_list();
			self::$position_list = position_list();

        }
		if ($source !== false)
        {
            self::$source = $source;
			self::load_source_helper();

			$map =field_map();
			self::$stat_list = array_merge(self::$stat_list,$map['stats']);
			self::$position_list = array_merge(self::$position_list,$map['positions']);
        }
	} //end init()
	
	//--------------------------------------------------------------------
	// !GLOBAL METHODS
	//--------------------------------------------------------------------

	//---------------------------------------------------------------

    /**
     * Get League Stats.
     * Accepts a League ID and returns stats for the league based on passed params
     *
     * @static
     * @param bool $league_id           League ID
     * @param int $stats_type           Stats Type (offense, defense, specialty, injury)
     * @param int $stats_class          Stats Class definition
     * @param int $stat_scope           Scope of stats to return
     * @param array $params		        Array of arguments in (key => value) format
     * @return array|bool		        Array of stats
     */
	public static function get_league_stats($league_id = false, $stats_type = TYPE_OFFENSE, $stats_class = array(), $stat_scope = STATS_CAREER, $params = array())
	{
		if ($league_id === false)
		{
			return false;
		}
		
		$stats = array();

		$where = array();
		$identifier = identifier_map();
        $where[$identifier['league']] = $league_id;

        $params['identifier'] = $identifier['league'];
        if (isset($params['where']))
		{
			$params['where'] = $params['where'] + $where;
		}
		else 
		{
			$params['where'] = $where;
		}

        $stats = self::_get_stats_query($stats_type, $stats_class, $stat_scope, $params);
        return $stats;
	}

	//---------------------------------------------------------------

    /**
     * Get Team Stats.
     * Accepts a Team ID and returns stats for the Team based on passed params
     *
     * @static
     * @param bool $team_id			    Team ID
     * @param int $stats_type           Stats Type (offense, defense, specialty, injury)
     * @param int $stats_class          Stats Class definition
     * @param int $stat_scope           Scope of stats to return
     * @param array $params		        Array of arguments in (key => value) format
     * @return array|bool		        Array of stats
     */
    public static function get_team_stats($team_id = false, $stats_type = TYPE_OFFENSE, $stats_class = array(), $stat_scope = STATS_CAREER, $params = array())
	{
		if ($team_id === false)
		{
			return false;
		}
		
		$stats = array();
		$where = array();
		$identifier = identifier_map();
        $where[$identifier['team']] = $team_id;

        $params['identifier'] = $identifier['team'];
        if (isset($params['where']))
		{
			$params['where'] = $params['where'] + $where;
		}
		else 
		{
			$params['where'] = $where;
		}

        $stats = self::_get_stats_query($stats_type, $stats_class, $stat_scope, $params);
        return $stats;
	}

    //---------------------------------------------------------------

    /**
     * Get Player Stats.
     * Accepts a Player ID and returns stats for the Player based on passed params.
     *
     * @static
     * @param bool $player_id			Player ID
     * @param int $stats_type           Stats Type (offense, defense, specialty, injury)
     * @param int $stats_class          Stats Class definition
     * @param int $stat_scope           Scope of stats to return
     * @param array $params		        Array of arguments in (key => value) format
     * @return array|bool		        Array of stats
     */
    public static function get_player_stats($player_id = false, $stats_type = TYPE_OFFENSE, $stats_class = array(), $stat_scope = STATS_CAREER, $params = array())
    {
        if ($player_id === false)
        {
            return false;
        }
        else if (is_string($player_id))
        {
             $player_id = intval($player_id);
        }
        else if (!is_integer($player_id))
        {
            return false;
        }

        $stats = array();
        $where = array();
		
		$identifier = identifier_map();
        $where[$identifier['player']] = $player_id;

        $params['identifier'] = $identifier['player'];
        if (isset($params['where']))
		{
			$params['where'] = $params['where'] + $where;
		}
		else 
		{
			$params['where'] = $where;
		}


        $stats = self::_get_stats_query($stats_type, $stats_class, $stat_scope, $params);
        return $stats;
    }

	//---------------------------------------------------------------

    /**
     * Get Players Stats.
     * Accepts an array of Player IDs and returns stats for the Players based on passed params.
     *
     * @static
     * @param array 	$player_ids			Player ID Array
     * @param int 		$stats_type         Stats Type (offense, defense, specialty, injury)
     * @param int 		$stats_class        Stats Class definition
     * @param int 		$stat_scope         Scope of stats to return
     * @param array 	$params		        Array of arguments in (key => value) format
     * @return 	array|bool		        	Array of stats
     */
    public static function get_players_stats($player_ids = array(), $stats_type = TYPE_OFFENSE, $stats_class = array(), $stat_scope = STATS_CAREER, $params = array())
	{
		// Error handling
		if (!isset($player_ids) || !is_array($player_ids) || count($player_ids) == 0)
		{
			return false;
		}
        else if (is_integer($player_ids))
        {
            return get_player_stats($player_ids,$stats_type, $stats_class, $stat_scope, $params);
        }
		
		$identifier = identifier_map();
        $stats = array();
        $where_in = array();
        $player_list = "(";
        foreach($player_ids as $player_id) {
            if ($player_list != "(") { $player_list .= ","; }
            $player_list .= $player_id;
        }
        $player_list .= ")";
        $where_in[$identifier['player']] = $player_list;

        $exclude_list_str = "(";
        if (isset($params['exclude_players']) &&  !is_array($params['exclude_players']) || count($params['exclude_players']) == 0)  {
            foreach($params['exclude_players'] as $player_id) {
                if ($exclude_list_str != "(") { $exclude_list_str .= ","; }
                $exclude_list_str .= $player_id;
            }
        }
        $where_not_in[$identifier['player']] = $exclude_list_str;
		$params['identifier'] = $identifier['player'];
		$params['where_in'] = $where_in;
		$params['where_not_in'] = $where_not_in;

		$stats = self::_get_stats_query($stats_type, $stats_class, $stat_scope, $params);
		return $stats;
	}

	//--------------------------------------------------------------------
	
	/**
     * Get Stats List.
     * Returns the internal Stats_list object.
     *
     * @static
     * @return array            stats_list Array
     */
    public static function get_stats_list()
    {
        return self::$stat_list;
    }

	//--------------------------------------------------------------------
	
	/**
     * Get Sport.
     * Returns the internal Sport var value.
     *
     * @static
     * @return array            stats_list Array
     */
    public function get_sport()
    {
        return self::$sport;
    }
	

	//--------------------------------------------------------------------
	
	/**
     * Get Stat Category.
     * Returns the acronym for a stat category.
     *
     * @static
     * @param 	int 		$cat	Stat Category ID
	 * @return 	string|int          Stat Category Name
     */
    public static function get_stat_cat($cat)
    {
        $cat_str = '';
		if (count(self::$stat_list) > 0)
		{
			foreach(self::$stat_list as $type => $categories)
			{
				foreach(self::$categories as $category => $details)
				{
					if (isset($details['id']) && $details['id'] == $cat)
					{
						$cat_str = $category;
						break;
					}
				}
			}
		}
		return $cat_str;
    }

	//--------------------------------------------------------------------
	
	/**
     * Get Stat Category Number.
     * Returns the ID Number for a stat category.
     *
     * @static
     * @param 	string 		$cat	Stat Category Name
	 * @return 	string|int          Stat Category ID
     */
    public static function get_stat_cat_num($cat)
    {
        $cat_str = '';
		if (count(self::$stat_list) > 0)
		{
			foreach(self::$stat_list as $type => $categories)
			{
				if (isset(self::$categories[$cat]))
				{
					$cat_str = self::$categories[$cat]['id'];
					break;
				}
			}
		}
		return $cat_str;
    }	
	//--------------------------------------------------------------------
	
	/**
     * Get Position.
     * Returns the acronym for a position.
     *
     * @static
     * @param 	string|int	$pos	Position ID
	 * @return 	string          	Position Name
     */
    public static function get_pos($pos)
    {
        $pos_str = '';
		if (count(self::$position_list) > 0)
		{
			foreach(self::$position_list as $position => $details)
			{
				if (isset($details['id']) && $details['id'] == $pos)
				{
					$pos_str = $position;
					break;
				}
			}
		}
		return $pos_str;
    }	

	//--------------------------------------------------------------------
	
	/**
     * Get Position Number.
     * Returns the ID for a position.
     *
     * @static
     * @param 	string 		$pos	Position Name
	 * @return 	string|int          Position ID
     */
    public static function get_pos_num($pos)
    {
        $pos = '';
		if (isset(self::$position_list[$pos]))
		{
			$pos = self::$position_list[$pos]['id'];
		}
		return $pos;
    }
	
	//--------------------------------------------------------------------
	
	public function get_stats_fields($stat_type = '', $fields = array()) {
		$stat_fields = array();
		$stat_list = self::$stat_list;
		foreach($fields as $field) {
			$val = '';
			$cat = '';
			if (isset($stat_list['general'][$field]['field']))
			{
				$cat = $stat_list['general'][$field]['field'];
			} 
			else if (isset($stat_list[$stat_type][$field]['field'])) 
			{
				$cat = $stat_list[$stat_type][$field]['field'];
			} 
			else 
			{
				$cat = $field;
			}
			if (strstr($cat, ".") !== false) {
				$tmpCat = explode(".",$cat);
				$val = $tmpCat[1];
			} else {
				$val = $cat;
			}
			array_push($stat_fields, $val);
		}
		return $stat_fields;
	}
	/**
	 *	FORMAT STATS FOR DISPLAY
	 *
	 *	Based on the stat selected, handles converting the raw data output to display ready HTML.
	 *
	 * 	@static
	 *	@author	Frank Esselink
	 * 	@author	Jeff Fox
	 *	@since	1.0
	 */
	public static function format_stats_for_display($player_stats = array(), $fields = array()) {
		$count = 10;
		$newStats = array();
		foreach($player_stats as $row) {
			$newRow = array();
			foreach ($fields as $col) {
				$col = strtolower($col);
				//echo($col." = ".(isset($row[$col]) ? $row[$col] : 0)."<br />");
				//$newRow['id'] = $id = $row['id'];
				switch ($col) {
					case 'bats':
					case 'throws':
						$newRow[$col] = get_hand($row[$col]);
						break;
					case 'pos':
					case 'positions':
						if (strpos($row[$col],":")) {
							$newRow[$col] = makeElidgibilityString($row[$col]);
						} else {
							$newRow[$col] = self::get_pos($row[$col]);
						}
						break;
					case 'position':
					case 'role':
						$newRow[$col] = self::get_pos($row[$col]);
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
					case 'ip':
					case 'ipi':
					case 'ipl':
					case 'vorp':
						$val=sprintf("%.1f",$row[$col]);
						$newRow[$col] = $val;
						break;
					case 'walk':
					case 'wiff':
						$newRow[$col] = intval($row[$col])."%";
						break;
					case 'add':
					case 'draft':
					case 'player_name':
					case 'teamname':
						$newRow[$col] = self::format_extended_fields($col, $row);
						break;
					default:
						if (!isset($row[$col]) && empty($row[$col])) {
							$newRow[$col] = 0;
						} else {
							$newRow[$col] = intval($row[$col]);
						}
						break;
				} // END switch

				// DEBUGGING
				if ($count < 5) {
					if (isset($newRow[$col])) {
						echo($col." = ".$newRow[$col]."<br />");
					}
				}
			} // END foreach
			array_push($newStats, $newRow);
			$count++;
		} // END foreach
		return $newStats;
	}
	
	//--------------------------------------------------------------------
	
	public static function format_extended_fields($field = false, $row = false, $settings = false, $league_id = 100, $debug = false)
	{
		$newRow = array();
		switch ($field) 
		{
			case 'add':
				if (isset($fields['showTrans']) && $fields['showTrans'] === true)
				{
					$newRow[$field] = '<a href="#" rel="itemPick" id="'.$row['id'].'"><img src="'.$settings['web_root'].'images/icons/add.png" width="16" height="16" alt="Add" title="Add" /></td>';
				}
				break;
			case 'draft':
				if (isset($fields['showDraft']) && $fields['showDraft'] === true) {
					if (($fields['pick_team_id'] == $fields['user_team_id'] && ($fields['draftStatus'] >= 2 && $fields['draftStatus'] < 4)) || (($fields['accessLevel'] == ACCESS_ADMINISTRATE || $fields['isCommish']) && ($fields['draftDate'] != EMPTY_DATE_TIME_STR && time() > strtotime($fields['draftDate'])))) {
						$newRow[$field] = '<a href="#" rel="draft" id="'.$row['id'].'"><img src="'.$settings['web_root'].'images/icons/next.png" width="16" height="16" alt="Draft Player" title="Draft Player" /></a>';
					} else {
						$newRow[$field] = '- -';
					}
				}
				break;
			case 'player_name':

				$name = $row['first_name']." ".$row['last_name'];
				if (isset($row['player_id'])) 
				{
					$val = anchor('/players/profile/' . $row['player_id'],$name,array('target'=>'_blank'));
				}
				else 
				{
					$val = $name;
				}
				$newRow[$field] = $val;
				break;
			case 'teamname':
				
				$name = $row['teamname']." ".$row['teamnick'];
				if (isset($row['team_id'])) 
				{
					$val = anchor('/teams/' . $row['team_id'],$name,array('target'=>'_blank'));
				}
				else 
				{
					$val = $name;
				}
				$newRow[$field] = $val;
				break;
		}
		// DEBUGGING
		if ($debug) {
			echo($field." = ".$newRow[$field]."<br />");
		}
		return $newRow[$field];
	}
		
	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------
	
	//--------------------------------------------------------------------
	
	/**
	 *
	 * Load Sport Helper.
	 * Loads the specific driver for the selected sport.
	 *
	 * @return	void
	 * @access	private
	 */
    private static function load_sport_helper()
    {
        self::$ci->load->helper('open_sports_toolkit/drivers/'.self::$sport.'/sport');
    }

    //--------------------------------------------------------------------

    /**
	 *
	 * Load Source Helper.
	 * Loads the specific driver for the selected data source.
	 *
	 * @return	void
	 * @access	private
	 */
    private static function load_source_helper()
    {
        self::$ci->load->helper('open_sports_toolkit/drivers/'.self::$sport.'/'. self::$source.'/source');
    }

	//--------------------------------------------------------------------
	
	/**
	 *	Get Stats Class.
	 *	Accepts the player type ans stats class (and custom field defs) and builds a SQL query.
	 *	@param		$stat_type	int			1 = Batter, All Else = Pitcher
	 *	@param		$stats_class	int			The class of stats to use
	 *	@return						Array		Array of stat definitions
	 */
    /**
     * Get Stats Class.
     * Accepts the player type ans stats class (and custom field defs) and builds a SQL query.
     * @static
     * @param int $stats_type           Stats Type (offense, defense, specialty, injury)
     * @param int $stats_class          Stats Class definition
     * @param int $stat_scope           Scope of stats to return
     * @param array $params
     * @return  array|string
     */
	private static function _get_stats_query($stats_type = TYPE_OFFENSE, $stats_class = array(), $stat_scope = STATS_CAREER, $params = array())
	{
        $scope = STATS_CAREER;
        if (isset($params['scope']) && !empty($params['scope']) && $params['scope'] != STATS_CAREER)
        {
            $scope = $params['scope'];
        }
        //$class_def = stats_class($stats_type, $stats_class);
        
		$_table_def = table_map();
        $type = '';
        switch ($stats_type)
        {
            case TYPE_INJURY:
                $type = "injury";
                break;
            case TYPE_DEFENSE:
                $type = "defense";
                break;
            case TYPE_SPECIALTY:
                $type = "specialty";
                break;
            case TYPE_OFFENSE:
            default:
                $type = "offense";
                break;
        }
        $query = self::build_stats_query($type, $stats_class, self::$stat_list, $scope);

		$tbl = $_table_def[$type][$stat_scope];
        $query .= " FROM ".$tbl;

		// JOIN IN THE PLAYERS TABLE FOR META INFORMATION ACCESS
		$identifier = identifier_map();
		$query .= " RIGHT OUTER JOIN ".$_table_def['players']." ON ".$_table_def['players'].".".$identifier['player']." = ".$tbl.".".$identifier['player'];
		$query .= " RIGHT OUTER JOIN ".$_table_def['team']." ON ".$_table_def['team'].".".$identifier['team']." = ".$tbl.".".$identifier['team'];
		
		
        // WHERE CLAUSES
        $id_field = '';
        $where_str = ' WHERE ';
        if (isset($params['where']) && is_array($params['where']) && count($params['where']))
        {
            foreach($params['where'] as $col => $val) {
                if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
                if ($col == $params['identifier'])
				{
					$where_str .= $tbl.".";
				}
				$where_str .= $col .' = '.self::$ci->db->escape($val);
            }
        }
				
		// POSITION FILTERS FOR OFFENSE AND DEFENSE
        if ($stats_type == TYPE_SPECIALTY)
		{
			if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
            $where_str .= $_table_def['players'].".".where_clause_speciality();
		} 
		else if ($stats_type == TYPE_OFFENSE) 
		{
			if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
            $where_str .= $_table_def['players'].".".where_clause_offense();
		}
		
		// SPLITS
        if (!isset($params['split']) || empty($params['split']))
        {
			$params['split'] = SPLIT_SEASON;
		}
		if (isset($params['split']) || !empty($params['split']))
		{
            if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
            $where_str .= get_split($params['split'], $tbl);
        }
		
        $query .= $where_str;

        // WHERE IN AND NOT IN CLAUSES
        if (isset($params['where_in']) && $params['where_in'] != "()")
        {
            $query .= ' AND '.$_table_def[$type][$stat_scope].'.'.$params['identifier'].' IN '.$params['where_in'];
        }
        if (isset($params['where_not_in']) && $params['where_not_in'] != "()")
        {
            $query .= ' AND '.$_table_def[$type][$stat_scope].'.'.$params['identifier'].' NOT IN '.$params['where_not_in'];
        }

        // GROUPING FOR SUM AND AVG
        if (isset($params['total']) && $params['total'] == 1) {
			if (!empty($identifier['team'])) 
			{
				$query .= " GROUP BY ".$_table_def[$type][$stat_scope].'.'.$identifier['team'];
			} 
			else if (!empty($identifier['league'])) 
			{
				$query .= " GROUP BY ".$_table_def[$type][$stat_scope].'.'.$identifier['league'];
			}
        }
		else if (!empty($identifier['player']))
		{
            $query .= " GROUP BY ".$_table_def[$type][$stat_scope].'.'.$identifier['player'];
        } 

        // LIMITS AND OFFSET
        if (isset($params['limit']) && isset($params['offset']))
        {
            if ($params['limit'] != -1 && $params['offset'] == 0)
            {
                $query.="LIMIT ".$params['limit'];
            }
            else if ($params['limit'] != -1 && $params['offset'] > 0)
            {
                $query.="LIMIT ".$params['offset'].", ".$params['limit'];
            }
        }
        return $query;

	}	
	
	//---------------------------------------------------------------

    /**
     * GET Extended Fields.
     * Returns a list of extended information fields that accompany stats. These are divided into groups or can be queried
     * together (group = all).
     * @static
     * @param int $stat_type    Stat Type
     * @param bool $group       Field Group type
     * @return array            Field List Array
     */
	private static function get_extended_fields($group = false) 
	{
		switch ($group)
		{
			case 'GROUP_GENERAL':
				$fieldList = array('AGE','TH','BA');
				break;
			case 'GROUP_TRANSACTION':
				$fieldList = array('ADD');
				break;
			case 'GROUP_DRAFT':
				$fieldList = array('DRAFT');
				break;
			case 'GROUP_FANTASY_POINTS':
				$fieldList = array('FPTS');
				break;
			case 'GROUP_FANTASY_RATINGS':
				$fieldList = array('PR15');
				break;
			case 'GROUP_PLAYERS':
			default:
				$fieldList = array('PN','TN','POS');
				break;
		}
		return $fieldList;
	}

	//---------------------------------------------------------------
		
	/**
	 *	Build Stats Query.
	 *	Accepts the player type ans stats class (and custom field defs) and builds a SQL query.
	 *	@param		$stat_type		int			1 = Batter, All Else = Pitcher
	 *	@param		$stats			Array		An array of stats fields to include in the query
	 *	@param		$rules			Array		An array of rules for fantasy points calculation
	 *	@param		$operator		int			FALSE = SUM, any other value = AVG
	 *	@return						String		SQL Query String
	 */

	private static function build_stats_query($stat_type = '', $categories = array(), $stat_list = array(), $scope = STATS_CAREER)
	{
		// ERROR HANDLING
		if ((!is_array($categories) || count($categories) == 0) || (!is_array($stat_list) || count($stat_list) == 0))
		{	
			return false;
		}
		$sql = '';
		$sqlOperator = 'SUM';
		if ($scope === STATS_SEASON_AVG) { $sqlOperator = 'AVG'; }

		foreach ($categories as $cat) {
			// HANDLE GENERAL FIELDS
			if (isset($stat_list['general'][$cat]))
			{
				if (!empty($sql)) { $sql .= ','; } // END if
				if (isset($stat_list['general'][$cat]['formula']) && !empty($stat_list['general'][$cat]['formula']))
				{
					$sql .= $stat_list['general'][$cat]['formula'];
				} 
				else if (isset($stat_list['general'][$cat]['field']) && !empty($stat_list['general'][$cat]['field'])) 
				{
					$sql .= $stat_list['general'][$cat]['field'];
				}
				else 
				{
					// Let the stat pass through
				} // END if
			}
			if (isset($stat_list[$stat_type][$cat]))
			{
				if (!empty($sql)) { $sql .= ','; } // END if
				if (isset($stat_list[$stat_type][$cat]['formula']) && !empty($stat_list[$stat_type][$cat]['formula']))
				{
					$sql .= str_replace('[OPERATOR]',$sqlOperator,$stat_list[$stat_type][$cat]['formula']);
				} 
				else if (isset($stat_list[$stat_type][$cat]['field']) && !empty($stat_list[$stat_type][$cat]['field'])) 
				{
					$sql .= $sqlOperator.'('.$stat_list[$stat_type][$cat]['field'].') as '.$stat_list[$stat_type][$cat]['field'];
				}
			}
		} // END foreach
		return "SELECT ".$sql;
	}
}//end class

//--------------------------------------------------------------------
// !CONTSANTS
//--------------------------------------------------------------------

define('TYPE_OFFENSE', 0);
define('TYPE_DEFENSE', 1);
define('TYPE_SPECIALTY', 2);
define('TYPE_INJURY', 3);
define('TYPE_TEAM', 4);

define('STATS_CAREER', 0);
define('STATS_SEASON', 1);
define('STATS_GAME', 2);
define('STATS_SEASON_AVG', 3);

define('CLASS_COMPACT', 0);
define('CLASS_BASIC', 1);
define('CLASS_STANDARD', 2);
define('CLASS_COMPLETE', 3);
define('CLASS_EXPANDED', 4);
define('CLASS_EXTENDED', 5);
define('CLASS_ULTRA_COMPACT', 6);

define('SPLIT_SEASON', 0);
define('SPLIT_PRESEASOM', 1);
define('SPLIT_PLAYOFFS', 2);
define('SPLIT_NONE', 3);

/* End of file stats.php */
/* Location: ./open_sports_toolkit/libraries/stats.php */
