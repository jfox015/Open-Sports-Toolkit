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

	}//end __construct()

	//--------------------------------------------------------------------

	/**
	 * Load the assets config file, and inserts the base
	 * css and js into our array for later use. This ensures
	 * that these files will be processed first, in the order
	 * the user is expecting, prior to and later-added files.
	 *
	 * @return void
	 */
	public static function init($sport = false, $source = false)
	{
        if ($sport !== false)
        {
            self::$sport = $sport;
			self::load_sport_helper();
			self::$stat_list = self::$ci->stat_list();
        }
		if ($source !== false)
        {
            self::$source = $source;
			self::load_source_helper();
			self::$table_list = array_merge(self::$stat_list, self::$ci->field_map());
        }
	} //end init()

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// !GLOBAL METHODS
	//--------------------------------------------------------------------
	
	private function load_sport_helper()
	{
		self::$ci->load->helper('open_sports_toolkit/'.$self:$sport.'/sport');	
	}

	//--------------------------------------------------------------------

	private function load_source_helper()
	{
		self::$ci->load->helper('open_sports_toolkit/'.self::$sport.'/'. self::$source.'/source');
	}
	
	//---------------------------------------------------------------
	
	/**
	 *	Get League Stats.
	 *	Accepts a League ID and returns stats for the league based on passed params
	 *.
	 *	@param		$league_id		int			League ID
	 *	@param		$arg_list		Array		Array of arguments in (key => value) format
	 *	@return						Array		Array of stats
	 *
	 *	@access	public
	 *
	 */
	private static function get_league_stats($league_id = false, $stat_scope = STATS_CAREER, $arg_list = array())
	{
		if ($league_id === false)
		{
			return false;
		}
		
		$stats = array();
		
		return $stats;
	}

	//---------------------------------------------------------------
	
	/**
	 *	Get Team Stats.
	 *	Accepts a Team ID and returns stats for the Team based on passed params
	 *.
	 *	@param		$team_id		int			Team ID
	 *	@param		$arg_list		Array		Array of arguments in (key => value) format
	 *	@return						Array		Array of stats
	 *
	 *	@access	public
	 *
	 */
	private static function get_team_stats($team_id = false, $arg_list = array()) 
	{
		if ($team_id === false)
		{
			return false;
		}
		
		$stats = array();
		
		return $stats;
	}

	//---------------------------------------------------------------
	
	/**
	 *	Get Player Stats.
	 *	Accepts a Player ID and returns stats for the Player based on passed params
	 *.
	 *	@param		$player_id		int			Player ID
	 *	@param		$arg_list		Array		Array of arguments in (key => value) format
	 *	@return						Array		Array of stats
	 *
	 *	@access	public
	 *
	 */
	private static function get_player_stats($player_id = false, $arg_list = array()) 
	{
		if ($player_id === false)
		{
			return false;
		}
		
		$stats = array();
		
		return $stats;
	}
	
	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

	/**
	 *	Get Stats Class.
	 *	Accepts the player type ans stats class (and custom field defs) and builds a SQL query.
	 *	@param		$stat_type	int			1 = Batter, All Else = Pitcher
	 *	@param		$stats_class	int			The class of stats to use
	 *	@return						Array		Array of stat definitions
	 */

	private static function _get_stats($stat_type = TYPE_OFFENSE, $stats_class = CLASS_STANDARD, $params = array())
	{
        $scope = STATS_CAREER;
        if (isset($params['scope']) && !empty($params['scope']) && $params['scope'] != STATS_CAREER)
        {
            $scope = $params['scope'];
        }
        self::$class_def = self::get_stats_class($stat_type, $stats_class);
        
		$_table_def = table_map();
		$_db_field_def = field_map() ;
        $query = self::build_stats_query($stat_type, $class_def, $_table_def, $_db_field_def, $scope);
	
	}

	//---------------------------------------------------------------
	
	/**
	 *	Get Stats Class.
	 *	Accepts the player type ans stats class (and custom field defs) and builds a SQL query.
	 *	@param		$stat_type	int			1 = Batter, All Else = Pitcher
	 *	@param		$stats_class	int			The class of stats to use
	 *	@return						Array		Array of stat definitions
	 */

	private static function get_stats_table_list()
	{
        $table_list = array();

        return $table_list;
    }
	//---------------------------------------------------------------

	/**
	 *	Get Stats Class.
	 *	Accepts the player type ans stats class (and custom field defs) and builds a SQL query.
	 *	@param		$stat_type	int			1 = Batter, All Else = Pitcher
	 *	@param		$stats_class	int			The class of stats to use
	 *	@return						Array		Array of stat definitions
	 */

	private static function get_stats_class($stat_type = TYPE_OFFENSE, $stats_class = CLASS_STANDARD)
	{
		$fieldList = array();
		if ($stat_type == TYPE_OFFENSE) {
			// BATTERS
			switch ($stats_class) {
				case CLASS_COMPACT:
					$fieldList = array('AVG','R','HR','RBI','OPS');
					break;
				case CLASS_BASIC:
					$fieldList = array('AVG','R','H','HR','RBI','SB','OBP','OPS');
					break;
				case CLASS_COMPLETE:
					$fieldList = array('AVG','G','AB','R','H','2B','3B','HR','RBI','BB','K','SB','OBP','SLG','OPS');
					break;
				case CLASS_EXPANDED:
					$fieldList = array('AVG','G','AB','R','H','2B','3B','HR','RBI','BB','K','SB','CS','OBP','SLG','OPS','wOBA','XBH','OPSPLUS');
					break;
				case CLASS_EXTENDED:
					$fieldList = array('AVG','G','PA','HP','SF','ISO','TB');
					break;
				case CLASS_STANDARD:
				default:
					$fieldList = array('AVG','AB','R','H','HR','RBI','BB','K','SB','OBP','SLG','OPS');
					break;
			} // END switch
		} else if ($stat_type == TYPE_SPECIALTY){
			switch ($stats_class) {
				case CLASS_COMPACT:
					$fieldList = array('W','L','ERA','BB','K');
					break;
				case CLASS_BASIC:
					$fieldList = array('W','L','SV','ERA','IP','BB','K','WHIP');
					break;
				case CLASS_COMPLETE:
					$fieldList = array('W','L','SV','ERA','G','GS','IP','CG','SHO','HA','RA','ER','HRA','BB','K','WHIP');
					break;
				case CLASS_EXPANDED:
					$fieldList = array('W','L','SV','ERA','G','GS','IP','CG','SHO','HA','RA','ER','BB','K','HRA','BB_9','K_9','HR_9','WHIP','BIFP','ERAPLUS');
					break;
				case CLASS_EXTENDED:
					$fieldList = array('IR','IRA','SO','BS','QS','QS%','CG%','SHO%','GF');
					break;
				case CLASS_STANDARD:
				default:
					$fieldList = array('W','L','SV','ERA','IP','SHO','HA','ER','HRA','BB','K','WHIP');
					break;
			} // END switch
		} else if ($stat_type == TYPE_DEFENSE){
			switch ($stats_class) {
				case CLASS_STANDARD:
				default:
					$fieldList = array("TC","A","PO","ER","IP","E","DP","TP","PB","SBA","RTO","ROE","FP");
					break;
			} // END switch
		}
		return $fieldList;
	}
	
	
	 /**
	 *	GET SPORTS FOR SPORT.
	 *
	 *	Loads the stats categories for the chosen sport.
	 *	
	 *	@param	$catID		int 		Category Int
	 *	@param	$forSQL		boolean		TRUE to use SQL friendly names, FALSE for general names
	 *	@return				String		Category Name
	 *
	 *	@author	Frank Esselink
	 *	@author	Jeff Fox
	 * 	@since	1.0
	 */
	private static function get_stats_for_sport()
	{
		return self::$ci->stat_list();
	}
	
	//---------------------------------------------------------------

		 
	 /**
	 *	GET SPORTS FOR SPORT.
	 *
	 *	Loads the stats categories for the chosen sport.
	 *	
	 *	@param	$catID		int 		Category Int
	 *	@param	$forSQL		boolean		TRUE to use SQL friendly names, FALSE for general names
	 *	@return				String		Category Name
	 *
	 *	@author	Frank Esselink
	 *	@author	Jeff Fox
	 * 	@since	1.0
	 */
	private static function get_source_fields()
	{
		
		return self::$ci->stat_list();
	}	
	
	//---------------------------------------------------------------

	/**
	 *	GET Extended Fields.
	 *	Returns a list of extended ifnormation fields that accompany stats. These are divided into groups or can be queried
	 *	together (group = all).
	 *	@param		$stat_type	int			1 = Batter, All Else = Pitcher
	 *	@param		$group			int			The class of stats to use
	 *	@return						Array		Array of stat definitions
	 */
	private static function get_extended_fields($stat_type = TYPE_OFFENSE, $group = false) 
	{
		switch ($group)
		{
			case 'GROUP_GENERAL':
				$fieldList = array('age','throws','bats');
				break;
			case 'GROUP_TRANSACTION':
				$fieldList = array('add');
				break;
			case 'GROUP_DRAFT':
				$fieldList = array('draft');
				break;
			case 'GROUP_FANTASY_POINTS':
				$fieldList = array('fpts');
				break;
			case 'GROUP_FANTASY_RATINGS':
				$fieldList = array('rating');
				break;
			default:
				$fieldList = array('player_name','teamname','pos','positions');
				break;
		}
		return $fieldList;
	}
	
	//---------------------------------------------------------------
		
	/**
	 *	Build Stats Query.
	 *	Accepts the player type ans stats class (and custom field defs) and builds a SQL query.
	 *	@param		$stat_type	int			1 = Batter, All Else = Pitcher
	 *	@param		$stats			Array		An array of stats fields to include in the query
	 *	@param		$rules			Array		An array of rules for fantasy points calculation
	 *	@param		$operator		int			FALSE = SUM, any other value = AVG
	 *	@return						String		SQL Query String
	 */

	private static function build_stats_query($stat_type = TYPE_OFFENSE, $categories = array(), $tables = array(), $scope = STATS_CAREER)
	{
		// ERROR HANDLING
		if (!is_array($categories) || count($categories) == 0) 
		{	
			return false;
		}
		$sql = '';
		$sqlOperator = 'SUM';
		if ($scope === STATS_SEASON_AVG) { $sqlOperator = 'AVG'; }
		
		foreach ($categories as $cat) {
			if ($sql != '') { $sql .= ','; }
			if ($stat_type == TYPE_OFFENSE) {
				// BATTERS
				switch ($cat) {
					case 'avg':
						$sql .= '';
						break;
					case 'OBP':
						$sql .= break;
					case 'SLG':
						$sql .= break;
					case 'OPS':
						$sql .= break;
					case 'wOBA':
						$sql .= break;
					case 'XBH':
                        $sql .= break;
					case 'WIFF':
						$sql .= break;
					case 'WALK':
						$sql .= break;
					case 'TB':
                        $sql .= break;
					case 'ISO':
						$sql .= break;
					default:
						$sql .= ' '.$sqlOperator.'({$cat}) as {$cat},';
						break;
				} // END switch
			} else if ($stat_type == TYPE_SPECIALTY) {
				// PITCHERS
				switch ($cat) {
					case 'ERA':
						$sql .= break;
					case 'IP':
                        $sql .= break;
					case 'WHIP':
						$sql .= break;
					case 'K_9':
						$sql .= break;
					case 'BB_9':
						$sql .= break;
					case 'HR_9':
						$sql .= break;
					case 'OAVG':
						$sql .= break;
					case 'BABIP':
						$sql .= break;
					default:
						$sql .= ' '.$sqlOperator.'({$cat}) as {$cat},';
						break;
				} // END switch
			} // END if
		} // END foreach
		return $sql;
	}

}//end class


/**
 * Helpers: Assets Helpers
 *
 * The following helpers are related and dependent on the Assets class.
 *
 */

/**
 * Returns full site url to assets base folder.
 *
 * @access public
 *
 * @return string Returns full site url to assets base folder.
 */
function assets_path()
{
	return Assets::assets_url ();

}//end assets_path()

define('TYPE_OFFENSE', 0);
define('TYPE_DEFENSE', 1);
define('TYPE_SPECIALTY', 2);

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

/* End of file stats.php */
/* Location: ./open_sports_toolkit/libraries/stats.php */
