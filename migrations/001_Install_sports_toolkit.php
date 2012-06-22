<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_sports_toolkit extends Migration {

    private $permission_array = array(
        'SportsToolkit.Settings.View' => 'View Sports Toolkit Settings Screens.',
        'SportsToolkit.Settings.Manage' => 'Manage Sports Toolkit Settings.',
        'SportsToolkit.Settings.Delete' => 'Delete Sports Toolkit Settings.',
    );

    public function up()
	{
		$prefix = $this->db->dbprefix;

        foreach ($this->permission_array as $name => $description)
        {
            $this->db->query("INSERT INTO {$prefix}permissions(name, description) VALUES('".$name."', '".$description."')");
            // give current role (or administrators if fresh install) full right to manage permissions
            $this->db->query("INSERT INTO {$prefix}role_permissions VALUES(1,".$this->db->insert_id().")");
        }
		
        $default_settings = "
			INSERT INTO `{$prefix}settings` (`name`, `module`, `value`) VALUES
			 ('', 'sportsToolkit', ''),
			 
		";
        $this->db->query($default_settings);
	}
	
	//--------------------------------------------------------------------
	
	public function down() 
	{
        $prefix = $this->db->dbprefix;

        $this->db->query("DELETE FROM {$prefix}settings WHERE (module = 'sportsToolkit')");

        foreach ($this->permission_array as $name => $description)
        {
            $query = $this->db->query("SELECT permission_id FROM {$prefix}permissions WHERE name = '".$name."'");
            foreach ($query->result_array() as $row)
            {
                $permission_id = $row['permission_id'];
                $this->db->query("DELETE FROM {$prefix}role_permissions WHERE permission_id='$permission_id';");
            }
            //delete the role
            $this->db->query("DELETE FROM {$prefix}permissions WHERE (name = '".$name."')");
        }
    }
	
	//--------------------------------------------------------------------
	
}