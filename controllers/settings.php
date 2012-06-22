<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends Admin_Controller {

	//--------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict('SportsToolkit.Settings.View');

		$this->lang->load('sportstoolkit');
	}

	//--------------------------------------------------------------------

	public function index()
	{
		if ($this->input->post('submit'))
		{
            $this->auth->restrict('SportsToolkit.Settings.Manage');
            if ($this->save_settings())
			{
				Template::set_message('Your settings were successfully saved.', 'success');
				redirect(SITE_AREA .'/settings/open_sports_toolkit');
			} else
			{
				Template::set_message('There was an error saving your settings.', 'error');
			}
		}
		// Read our current settings
		$settings = $this->settings_model->select('name,value')->find_all_by('module', 'sportstoolkit');
		Template::set('settings', $settings);
		Template::set('toolbar_title', lang('nw_setting_title'));
		Template::set_view('settings/index');
		Template::render();
	}
	
	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

	private function save_settings()
	{
		$this->form_validation->set_rules('source_baseball', lang('st_source_baseball'),
		$this->form_validation->set_rules('source_football', lang('st_source_football'),
		$this->form_validation->set_rules('source_basketball', lang('st_source_basketball'),
		$this->form_validation->set_rules('source_hockey', lang('st_source_hockey'),

		if ($this->form_validation->run() === false)
		{
			return false;
		}

		$data = array(
			array('name' => 'sportstoolkit.source_baseball', 'value' => $this->input->post('source_baseball')),
			array('name' => 'sportstoolkit.source_football', 'value' => $this->input->post('source_football')),
			array('name' => 'sportstoolkit.source_basketball', 'value' => $this->input->post('source_basketball')),
			array('name' => 'sportstoolkit.source_hockey', 'value' => $this->input->post('source_hockey'))
		);

		// Log the activity
		$this->load->model('activities/Activity_model', 'activity_model');

		$this->activity_model->log_activity($this->auth->user_id(), lang('bf_act_settings_saved').': ' . $this->input->ip_address(), 'news');
		// $this->activity_model->log_activity($this->current_user->id, lang('bf_act_settings_saved').': ' . $this->input->ip_address(), 'sportstoolkit');

		// save the settings to the DB
		$updated = $this->settings_model->update_batch($data, 'name');

		return $updated;
	}

	//--------------------------------------------------------------------
}
