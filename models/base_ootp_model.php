<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *	Base OOTP DATA MODEL CLASS.
 *
 *	@author			Jeff Fox <jfox015 (at) gmail (dot) com>
 *  @copyright   	(c)2009-12 Jeff Fox/Aeolian Digital Studios
 *	@version		1.0
 *
 *	This model provides a basic set of function used by all models in 
 *	the league_manager module. To use:
 *	<ul>
 *		<li>Require this model before the class declaration:<br />
 *		<code>
 *      require_once(dirname(dirname(__FILE__)).'/models/base_ootp_model.php');
 *      </code></li>
 *      <.li>extend the class using Base_ootp_model</li>
 *  </ul>
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
class Base_ootp_model extends BF_Model {

	//---------------------------------------------------------------
	protected $use_prefix 	= true;
	protected $dbprefix 	= '';

	public function __construct()
	{
		parent::__construct();
        $this->dbprefix = $this->db->dbprefix;
		$this->use_prefix = ($this->settings_lib->item('ootp.use_db_prefix') == 1) ? true : false;
	}

    /*--------------------------------------------------
     /
     /	PUBLIC FUNCTIONS
     /
     /-------------------------------------------------*/
    public function find($id) {
        $data = array();
		if (!$this->use_prefix) $this->db->dbprefix = '';
        if ($this->db->table_exists($this->table)) {
            $data = parent::find($id);
        }
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $data;
    }

    public function find_all() {
		$data = array();
		if (!$this->use_prefix) $this->db->dbprefix = '';
        if ($this->db->table_exists($this->table)) {
            $data = parent::find_all();
        }
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $data;
    }
    public function find_by($field = '', $value='', $type='') {
		$data = array();
		if (!$this->use_prefix) $this->db->dbprefix = '';
        if ($this->db->table_exists($this->table)) {
            $data = parent::find_by($field, $value, $type);
        }
		if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $data;
    }
    public function find_all_by($field = '', $value='') {
		$data = array();
		if (!$this->use_prefix) $this->db->dbprefix = '';
        if ($this->db->table_exists($this->table)) {
            $data = parent::find_all_by($field, $value);
        }
        if (!$this->use_prefix) $this->db->dbprefix = $this->dbprefix;
        return $data;
    }

}