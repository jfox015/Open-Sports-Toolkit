<?php if (validation_errors()) : ?>
<div class="notification error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>

<div class="admin-box">

    <h3><?php echo lang('st_settings_manage') ?></h3>

    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>

    <fieldset>
        <legend><?php echo lang('st_select_source') ?></legend>

			<!-- Baseball -->		
		<?php
		if (isset($sources_baseball) && is_array($sources_baseball) && count($sources_baseball)) :
			$selection = ( isset ($settings['sportstoolkit.source_baseball']) ) ? (int) $settings['sportstoolkit.source_baseball'] : 1;
			echo form_dropdown('source_baseball', $sources_baseball, $selection , lang('st_source_baseball'), 'class="chzn-select" id="source_baseball"');
		else:
			echo('<div class="well">'.lang('st_no_sources_found').'</div>');
		endif;
		?>
			<!-- Football -->		
		<?php
		if (isset($sources_football) && is_array($sources_football) && count($sources_football)) :
			$selection = ( isset ($settings['sportstoolkit.source_football']) ) ? (int) $settings['sportstoolkit.source_football'] : 1;
			echo form_dropdown('source_football', $sources_football, $selection , lang('st_source_football'), 'class="chzn-select" id="source_football"');
		else:
			echo('<div class="well">'.lang('st_no_sources_found').'</div>');
		endif;
		?>
			<!-- Basketball -->		
		<?php
		if (isset($sources_basketball) && is_array($sources_basketball) && count($sources_basketball)) :
			$selection = ( isset ($settings['sportstoolkit.source_basketball']) ) ? (int) $settings['sportstoolkit.source_basketball'] : 1;
			echo form_dropdown('source_basketball', $sources_basketball, $selection , lang('st_source_basketball'), 'class="chzn-select" id="source_basketball"');
		else:
			echo('<div class="well">'.lang('st_no_sources_found').'</div>');
		endif;
		?>
			<!-- Hockey -->		
		<?php
		if (isset($sources_basketball) && is_array($sources_basketball) && count($sources_basketball)) :
			$selection = ( isset ($settings['sportstoolkit.source_basketball']) ) ? (int) $settings['sportstoolkit.source_basketball'] : 1;
			echo form_dropdown('source_basketball', $sources_basketball, $selection , lang('st_source_basketball'), 'class="chzn-select" id="source_basketball"');
		else:
			echo('<div class="well">'.lang('st_no_sources_found').'</div>');
		endif;
		?>
		
	</fieldset>
	
	<div class="form-actions">
		<input type="submit" name="submit" class="btn btn-primary" value="<?php echo lang('bf_action_save') .' '. lang('bf_context_settings') ?>" /> <?php echo lang('bf_or') ?>
        <?php echo anchor(SITE_AREA .'/settings/users', '<i class="icon-refresh icon-white">&nbsp;</i>&nbsp;' . lang('bf_action_cancel'), 'class="btn btn-warning"'); ?>
	</div>

<?php echo form_close(); ?>

