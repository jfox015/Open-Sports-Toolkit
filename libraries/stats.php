<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Stats Class
 *
 * The Stats class provides an abstract interface to querying sports statistics from either a database or 
 * remotes data source like JSON or XML.
 *
 *	Right now, only MySQL databases are supported.
 *
 * @package    Open Sports Toolkit
 * @subpackage Libraries
 * @category   Libraries
 * @author     Jeff Fox
 * @link       http://www.aeoliandigital.com/
 * @version    0.3
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
	 * The sport and data source specific stats listing.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $stat_list		= array();

	/**
	 * The sport and data source specific splits listing.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $splits_list	= array();

	/**
	 * The sport and data source specific position listing.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $position_list		= array();
	
	/**
	 * The sport and data source specific hands listing.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $hands_list		= array();
		
	/**
	 * The sport and data source specific awards listing.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $award_list		= array();
	
	/**
	 * The sport and data source specific level listing.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $level_list		= array();
	
	/**
	 * Mapping of source data sources for the stat scope type (career, season, game).
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $table_list		= array();

	/**
	 * Attriute for settign and retieving error information.
	 *
	 * @access public
	 *
	 * @var String $error
	 */
	public static $error		= '';

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
     * Init().
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
			if (!function_exists('sports_map')) 
			{
				self::$ci->load->helper('open_sports_toolkit/general');
			}
			$sports = sports_map();
			self::$sport = $sports[$sport];
			self::load_sport_helper();
			self::$ci->lang->load('open_sports_toolkit/'.self::$sport.'_acyn');
			self::$ci->lang->load('open_sports_toolkit/'.self::$sport.'_full');
			self::$stat_list = stat_list();
			self::$position_list = position_list();
			self::$award_list = award_list();
			self::$level_list = level_list();
			self::$splits_list = split_list();
			self::$hands_list = hands_list();
        }
		if ($source !== false)
        {
            self::$source = $source;
			self::load_source_helper();

			$map = field_map();
			self::$stat_list = array_merge_recursive(self::$stat_list,$map['stats']);
			self::$position_list = array_merge_recursive(self::$position_list,$map['positions']);
			self::$award_list = array_merge_recursive(self::$award_list,$map['awards']);
			self::$level_list = array_merge_recursive(self::$level_list,$map['levels']);
			self::$splits_list = array_merge_recursive(self::$splits_list,$map['splits']);
			self::$hands_list = array_merge_recursive(self::$hands_list,$map['hands']);
        }
	} //end init()
	
	//--------------------------------------------------------------------
	// !GLOBAL METHODS
	//--------------------------------------------------------------------

	//---------------------------------------------------------------

	/**
     * Get Stats.
     * Accepts a data type and ID and returns stats for the type based on passed params.
	 *
	 * To get a SQL string returned, set <code>$debug</code> to true.
	 *
	 * <b>Supported Param Arguments:</b>
	 *
	 * <ul>
	 *	<li>select_data - (Array) 	key => value array of dynamic select replacements</li>
	 *	<li>totals 		- (Boolean)	TRUE for the stats engine to query for an aggregate total of the type passedrather than individual results</li>
	 *	<li>where 		- (Array)	key => value array of custom where clause definitions</li>
	 *	<li>where_in 	- (String)	A comma delimited string of values to apply to a WHERE IN clause</li>
	 *	<li>year 		- (int)		Year value for season and season range queries</li>
	 *	<li>id_list 	- (Array)	Array of numeric id values based on the specified type</li>
	 *	<li>split 		- (int)		Specify a split value for the data</li>
	 *	<li>order_by 	- (Array)	Array of stat classes to order results by</li>
	 *	<li>order_dir 	- (String)	'asc' for ascending results, 'desc' for descending</li>
	 *	<li>limit 		- (int)		Max # of results to return</li>
	 *	<li>offset 		- (int)		Starting result offset if other than 0</li>
     * </ul>
	 *
     * @static
     * @param 	String 		$type					Stat type (league, team, player, game)
     * @param 	int 		$id						ID param
     * @param 	int 		$stats_type           	Stats Type (offense, defense, speciality, injury, team)
     * @param 	Array 		$stats_class          	Stats Class definition
     * @param 	int 		$stat_scope          	Scope of stats to return (career, season, game, season avg)
     * @param 	int 		$range           		Range for data values
     * @param 	Array 		$params		        	Array of additonal arguments in (key => value) format
     * @param 	Boolean 	$debug           	 	TRUE to display debug trace info, FALSE to disable
     * @return 	mixed		        				Array of stats, SQL query on debug, FALSE on error
     */
    public static function get_stats($type = false, $id = false, $stats_type = TYPE_OFFENSE, $stats_class = false, $stat_scope = STATS_CAREER, $range = RANGE_SEASON, $params = array(), $debug = false)
	{
		if ($type === false)
		{
			self::$error = "Type parameter not found.";
			return false;
		} // END if
		if ($stats_class === false)
		{
			self::$error = "Stat class not specified.";
			return false;
		} // END if
		
		$stats = array();
		$headers = array();
        $totals = array();
		$where = array();

		if ($id !== false) 
		{
			if (is_string($id))
			{
				 $id = intval($id);
			}
			else if (!is_integer($id))
			{
				return false;
			}
			$identifier = identifier_map();
			$where[$identifier[$type]] = $id;
			$params['identifier'] = $identifier[$type];
		} // END if
		
		if (isset($params['where']))
		{
			$params['where'] = $params['where'] + $where;
		}
		else 
		{
			$params['where'] = $where;
		} // END if
		
		$sql = self::_get_stats_query($type, $stats_type, $stats_class, $stat_scope, $range, $params, $debug);
		
		if ($debug === true)
		{
			return $sql;
		}
		else 
		{
			$query = self::$ci->db->query($sql);
			if ($query->num_rows() > 0)
			{
				$stats_data = $query->result_array();
				$fields = $query->list_fields();
			} // END if
			$query->free_result();

            $headers = self::make_headers($stats_type, $stats_class, self::$stat_list);
			
			if (!function_exists('format_stats'))
			{
				self::$error .= "Stats library is not properly loaded.";
				return false;
			} 
			else
			{
				$stats = format_stats($stats_type, $stats_data, $stats_class, self::$stat_list, self::$position_list,
                    self::$hands_list, self::$level_list, $debug);
			} // END if

            if (isset($params['totals'])) {
                $params['totals_row'] = true;
                $sql = self::_get_stats_query($type, $stats_type, $stats_class, $stat_scope, $range, $params, $debug);
                $query = self::$ci->db->query($sql);
                if ($query->num_rows() > 0)
                {
                    $totals = $query->result_array();
                } // END if
                $query->free_result();
            }
		} // END if
        echo(self::$ci->db->last_query()."<br />");
		return array('stats'=>$stats, 'headers'=>$headers, 'totals' =>$totals);
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
     * Get Stats List.
     * Returns the internal splits_list object.
     *
     * @static
     * @return array            splits_list Array
     */
    public static function get_splits_list()
    {
        return self::$splits_list;
    }

    //--------------------------------------------------------------------

    /**
     * Get Position Array.
     * Returns an id => value array of positions.
     *
     * @static
     * @return array            stats_list Array
     */
    public static function get_splits_array($only_invisible = true)
    {
        $arr_out = array();
        foreach (self::$splits_list as $split => $details) {
            if(($details['visible'] === false && !$only_invisible) ||$details['visible'] == true)  {
               $arr_out = $arr_out + array($details['id'] => $split);
            }
        }
        return $arr_out;
    }

	//--------------------------------------------------------------------

	/**
     * Get Hands List.
     * Returns the internal hands_list object.
     *
     * @static
     * @return array            hands_list Array
     */
    public static function get_hands_list()
    {
        return self::$hands_list;
    }

    //--------------------------------------------------------------------

    /**
     * Get Hands Array.
     * Returns an id => value array of hands.
     *
     * @static
     * @return array            hands_list Array
     */
    public static function get_hands_array($only_invisible = true)
    {
        $arr_out = array();
        foreach (self::$hands_list as $hand => $details) {
            if(($details['visible'] === false && !$only_invisible) ||$details['visible'] == true)  {
               $arr_out = $arr_out + array($details['id'] => $hand);
            }
        }
        return $arr_out;
    }

	//--------------------------------------------------------------------

	/**
     * Get Award List.
     * Returns the internal award_list object.
     *
     * @static
     * @return array            award_list Array
     */
    public static function get_award_list()
    {
        return self::$award_list;
    }
	
	//--------------------------------------------------------------------

	/**
     * Get Position List.
     * Returns the internal position_list object.
     *
     * @static
     * @return array            stats_list Array
     */
    public static function get_position_list()
    {
        return self::$position_list;
    }

    //--------------------------------------------------------------------

    /**
     * Get Position Array.
     * Returns an id => value array of positions.
     *
     * @static
     * @return array            stats_list Array
     */
    public static function get_position_array($include_group = false)
    {
        $arr_out = array();
        $types = array(TYPE_OFFENSE, TYPE_DEFENSE, TYPE_SPECIALTY);
        foreach ($types as $type) {
           $arr_out[$type] = array();
            foreach (self::$position_list as $pos => $details) {
                if ($details['type'] == $type) {
                    if(($details['group'] == true && $include_group) ||$details['group'] == false )  {
                        $arr_out[$type] = $arr_out[$type] + array($details['id'] => $pos);
                    }
                }
            }
        }
        return $arr_out;
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
     * Get Stats Fields.
     * Returns the value of a meta field value for the stat type and categories passed.
     *
     * @static
     * @param 	string 		$stat_type		Stats category type (TYPE_OFFENSE.TYPE_DEFENSE.TYPE_SPECIALTY.TYPE_INJURY,TYPE_TEAM)
     * @param 	Array 		$fields			Array of Stat categories (usually derived from sport_helper::tats_class())
     * @param 	string 		$statField		field, formula or lang
	 * @return 	Array         				List of stats field meta values
     */
    public function get_stats_fields($stat_type = '', $fields = array(), $statField = 'field') {
		$stat_fields = array();
		$stat_list = self::$stat_list;
        foreach($fields as $field) {
			$val = '';
			$cat = '';
			if (isset($stat_list['general'][$field][$statField]))
			{
				$cat = $stat_list['general'][$field][$statField];
			} 
			else if (isset($stat_list[$stat_type][$field][$statField])) 
			{
				$cat = $stat_list[$stat_type][$field][$statField];
			} 
			else 
			{
				$cat = $field;
			} // END if
			if (strstr($cat, ".") !== false) 
			{
				$tmpCat = explode(".",$cat);
				$val = $tmpCat[1];
			} 
			else 
			{
				$val = $cat;
			} // END if
            array_push($stat_fields, $val);
		}
		return $stat_fields;
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
    protected  static function make_headers($stats_type = false, $stats_class = false)
    {
        $stats_list = self::$stat_list;
        $headers = array();
        foreach($stats_class as $field)
        {
            $exceptions = array('PID','TID');
            foreach ($stats_class as $field) :
                if (!in_array($field, $exceptions)) {
                    if (isset($stats_list['general'][$field]['lang']))
                    {
                        $label = lang("acyn_".$stats_list['general'][$field]['lang']);
                    }
                    else if (isset($stats_list[$stats_type][$field]['lang']))
                    {
                        $label = lang("acyn_".$stats_list[$stats_type][$field]['lang']);
                    }
                    $headers = $headers + array($field => $label);
                }
            endforeach;
        }
        return $headers;
    }

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
	 *	Get Stats Query.
	 *	Accepts the player type ans stats class (and custom field defs) and builds a SQL query.
	 *	@param		$stat_type	int			1 = Batter, All Else = Pitcher
	 *	@param		$stats_class	int			The class of stats to use
	 *	@return						Array		Array of stat definitions
	 */
    /**
     * Get Stats Class.
     * Accepts the player type ans stats class (and custom field defs) and builds a SQL query.
     * @static
	 * @access	private
     * @param 	int 	$stats_type           	Stats Type (offense, defense, speciality, injury)
     * @param 	int 	$stats_class          	Stats Class definition
     * @param 	int 	$stat_scope           	Scope of stats to return
     * @param 	int 	$range           	 	Range type of stats
     * @param 	Array 	$params           	 	Array of optional parameters
     * @param 	Boolean $debug           	 	TRUE to display debug trace info, FALSE to disable
     * @param 	array 	$params
     * @return  array|string
     */
	protected static function _get_stats_query($query_type = false, $stats_type = TYPE_OFFENSE, $stats_class = array(), $stat_scope = STATS_CAREER, $range = RANGE_SEASON, $params = array(), $debug = false)
	{
		// Fail safe to prevent SQL errors when using game list for range
		if ($range == RANGE_GAME_ID_LIST && $stat_scope != STATS_GAME) {
			$stat_scope = STATS_GAME;
		}
		
		// For straight data pulls (no sum or avg) turn off the operator
		$no_operator = false;
		if (isset($params['no_operator']) && !empty($params['no_operator'])) {
			$no_operator = $params['no_operator'];
		}

		$_table_def = table_map();
        $query = self::build_stats_select($stats_type, $stats_class, self::$stat_list, $stat_scope, $no_operator);
		 
		/*------------------------------------
		/	DYNAMIC SELECT DATA
		/-----------------------------------*/ 
        // DYNAMICALLY REPLACE PLACEHOLDERS IN SELECT WITH LIVE DATA
		if (isset($params['select_data']) && is_array($params['select_data']) && count($params['select_data']))
		{
			foreach($params['select_data'] as $key => $val)
			{
				$query = str_replace("[".$key ."]", $val, $query);
			}
		}

		$tbl = $_table_def[$stats_type][$stat_scope];
        $query .= " FROM ".$tbl;

		/*-----------------------------------------------------------
		/	JOINS 
		/	JOIN IN THE PLAYERS TABLE FOR META INFORMATION ACCESS
		/----------------------------------------------------------*/ 
		$identifier = identifier_map();
		$query .= " RIGHT OUTER JOIN ".$_table_def['players']." ON ".$_table_def['players'].".".$identifier['player']." = ".$tbl.".".$identifier['player'];
		$query .= " RIGHT OUTER JOIN ".$_table_def['team']." ON ".$_table_def['team'].".".$identifier['team']." = ".$tbl.".".$identifier['team'];

		if (($query_type != 'player' && $query_type != 'team') && !empty($params['identifier']) && !isset($params['totals_row'])) {
			$query .= " RIGHT OUTER JOIN ".$_table_def[$query_type]." ON ".$_table_def[$query_type].".".$params['identifier']." = ".$tbl.".".$params['identifier'];
		}
        /*------------------------------------
		/	WHERE CLAUSES
		/-----------------------------------*/ 
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
				
		/*------------------------------------
		/	POSITION FILTERS FOR OFFENSE AND DEFENSE
		/-----------------------------------*/
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
		
		/*------------------------------------
		/	DATA RANGE
		/-----------------------------------*/
		switch ($range) {
			case RANGE_SEASON:
				if (isset($params['year']) && !empty($params['year'])) {
					if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
					$where_str .= $_table_def[$stats_type][$stat_scope].".".$identifier['year']."	= ".$params['year'];
				}
				break;
			case RANGE_YEARS:
				if (isset($params['year']) && !empty($params['year']) && isset($params['offset']) && !empty($params['offset'])) {
					if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
					$where_str .= "DATEDIFF('".$params['year']."',".$_table_def[$stats_type][$stat_scope].".".$identifier['date'].")<=0";
					$where_str .= "DATEDIFF('".$params['year']."',".$_table_def[$stats_type][$stat_scope].".".$identifier['date'].")>-".$params['offset'];
				}
				break;
			case RANGE_GAME_ID_LIST:
				if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
				$where_str .= self::id_array_to_where($params['id_list'], $_table_def[$stats_type][$stat_scope], $identifier['game']);
				break;
			case RANGE_DATE_LIST:
				if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
				$where_str .= self::id_array_to_where($params['id_list'], $_table_def[$stats_type][$stat_scope], $identifier['date']);
				break;
			case RANGE_TEAM_LIST:
				if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
				$where_str .= self::id_array_to_where($params['id_list'], $_table_def[$stats_type][$stat_scope], $identifier['team']);
				break;
			case RANGE_PLAYER_LIST:
				if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
				$where_str .= self::id_array_to_where($params['id_list'], $_table_def[$stats_type][$stat_scope], $identifier['player']);
				break;
			case RANGE_LEAGUE_LIST:
				if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
				$where_str .= self::id_array_to_where($params['id_list'], $_table_def[$stats_type][$stat_scope], $identifier['league']);
				break;
			case RANGE_CAREER:
			default:
				break;
		} // END switch
		
		/*------------------------------------
		/	LEVEL
		/-----------------------------------*/
        if (!isset($params['level']) || empty($params['level']))
        {
			$params['level'] = LEVEL_MAJOR;
		}
		if (isset($params['level']) && !empty($params['level']))
		{
            if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
            $where_str .= 'level_id = '.$params['level'];
        }

		/*------------------------------------
		/	SPLITS
		/-----------------------------------*/
        if (!isset($params['split']) || empty($params['split']))
        {
			$params['split'] = SPLIT_SEASON;
		}
		if (isset($params['split']) && !empty($params['split']))
		{
            $split = get_split($params['split'], $tbl);
            if ($where_str != ' WHERE ') { $where_str .= ' AND '; }
            $where_str .= $split;
        }

        $query .= $where_str;

        /*------------------------------------
		/	WHERE IN AND NOT IN CLAUSES
		/-----------------------------------*/
        if (isset($params['where_in']) && $params['where_in'] != "()")
        {
            $query .= ' AND '.$_table_def[$stats_type][$stat_scope].'.'.$params['identifier'].' IN '.$params['where_in'];
        }
        if (isset($params['where_not_in']) && $params['where_not_in'] != "()")
        {
            $query .= ' AND '.$_table_def[$stats_type][$stat_scope].'.'.$params['identifier'].' NOT IN '.$params['where_not_in'];
        }

        /*------------------------------------
		/	GROUPING FOR SUM AND AVG
		/-----------------------------------*/
        if ($stat_scope != STATS_CAREER) {
            if (isset($params['totals_row']) && $params['totals_row'] == 1) {
                if (!empty($identifier['team']))
                {
                    $query .= " GROUP BY ".$_table_def[$stats_type][$stat_scope].'.'.$identifier['team'];
                }
                else if (!empty($identifier['league']))
                {
                    $query .= " GROUP BY ".$_table_def[$stats_type][$stat_scope].'.'.$identifier['league'];
                }
            }
            else if (!empty($identifier['player']))
            {
                $query .= " GROUP BY ".$_table_def[$stats_type][$stat_scope].'.'.$identifier['player'];
            }
        }
		/*------------------------------------
		/	ORDERING AND SORTING
		/-----------------------------------*/
		if (isset($params['order_by']))
        {
            $order_by_arr = array();
			$order_str = '';
			if (is_string($params['order_by']) && strlen($params['order_by']) > 0)
			{
				array_push($order_by_arr , $params['order_by']);
			}
			else if (is_array($params['order_by']) && count($params['order_by']))
			{
				$order_by_arr = $params['order_by'];
			}
			
			$order_by_arr = self::get_stats_fields($stats_type, $order_by_arr, 'field');
				
			foreach($order_by_arr as $field)
			{
				if (!empty($order_str)) { $order_str .= ","; }
				$order_str .= $field;
			}
			$query .= " ORDER BY ".$order_str." ";
			if (isset($params['order_dir']) && is_string($params['order_dir']) &&!empty($params['order_dir']))
			{
				$query .= strtolower($params['order_dir'])." ";
			}
        }
		
        /*------------------------------------
		/	LIMITS AND OFFSET
		/-----------------------------------*/
        if (isset($params['limit']))
        {
            if ($params['limit'] != -1 && (!isset($params['offset']) || (isset($params['offset']) && $params['offset'] == 0)))
            {
                $query.="LIMIT ".$params['limit'];
            }
            else if ($params['limit'] != -1 && isset($params['offset']) && $params['offset'] > 0)
            {
                $query.="LIMIT ".$params['offset'].", ".$params['limit'];
            }
        }
		if ($debug === true)
		{
			echo ("query = ".$query."<br /><br />");
		}
        return $query;

	}	
	
	private function id_array_to_where($id_list = false, $table = '', $field = '') {
		
		$idStr = '';
		if ($id_list !== false && is_array($id_list) && count($id_list)) 
		{
			foreach ($id_list as $id) 
			{
				if (!empty($idStr)) { $idStr .= ' OR '; }
				$idStr .= $table.'.'.$field. ' = '.$id;
			} // END foreach
		} // END if
		return "(".$idStr.")";
	}
	//---------------------------------------------------------------

    /**
     * GET Extended Fields.
     * Returns a list of extended information fields that accompany stats. These are divided into groups or can be queried
     * together (group = all).
     * @static
	 * @access	private
     * @param 	int $stat_type    Stat Type
     * @param 	bool $group       Field Group type
     * @return 	array            Field List Array
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
	 *	@static
	 * 	@access		private
     * 	@param		$stat_type			int			1 = Batter, All Else = Pitcher
	 *	@param		$categories			Array		An array of stats fields to include in the query
	 *	@param		$stat_list			Array		The full table of stats available
	 *	@param		$scope				int			Stat scope (season, career, range, etc.)
	 *	@param		$no_operator		Boolean		TRUE to use no operator, FALSE to use one
	 *	@return							String		SQL Query String
	 */

	private static function build_stats_select($stat_type = '', $categories = array(), $stat_list = array(), $scope = STATS_CAREER,
	$no_operator = false)
	{
		// ERROR HANDLING
		if ((!is_array($categories) || count($categories) == 0) || (!is_array($stat_list) || count($stat_list) == 0))
		{	
			return false;
		}
		$sql = '';
		$sqlOperator = 'SUM';
		if ($scope === STATS_SEASON_AVG) { $sqlOperator = 'AVG'; }
		
		if ($no_operator === true) { $sqlOperator = ''; }

		foreach ($categories as $cat) 
		{
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
					if ($no_operator === true) {
                        $clean_formula = str_replace('[OPERATOR]','',$stat_list[$stat_type][$cat]['formula']);
                        $sql .= preg_replace('/[OPERATOR][\s]?\([\w]*\)/', '%1', $clean_formula);
                    } else {
                        $sql .= str_replace('[OPERATOR]',$sqlOperator,$stat_list[$stat_type][$cat]['formula']);
                    }
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

define('ID_TEAM','team');
define('ID_LEAGUE','league');
define('ID_PLAYER','player');
define('ID_GAME','game');
define('ID_SUB_LEAGUE','sub_league');

define('TYPE_OFFENSE', 'offense');
define('TYPE_DEFENSE', 'defense');
define('TYPE_SPECIALTY', 'speciality');
define('TYPE_INJURY', 'injury');
define('TYPE_TEAM', 'team');
define('TYPE_LEAGUE', 'league');
define('TYPE_PLAYER', 'player');

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
define('CLASS_RECENT', 7);

define('SPLIT_SEASON', 0);
define('SPLIT_PRESEASOM', 1);
define('SPLIT_PLAYOFFS', 2);
define('SPLIT_NONE', 3);
define('SPLIT_DEFENSE', 4);

define('LEVEL_MAJOR', 1);
define('LEVEL_MINOR', 2);
define('LEVEL_INT', 3);

define('RANGE_GAME_ID_LIST', 0);
define('RANGE_DATE_LIST', 1);
define('RANGE_LEAGUE_LIST', 2);
define('RANGE_TEAM_LIST', 3);
define('RANGE_PLAYER_LIST', 4);
define('RANGE_SEASON', 5);
define('RANGE_CAREER', 6);
define('RANGE_YEARS', 7);

/* End of file stats.php */
/* Location: ./open_sports_toolkit/libraries/stats.php */
