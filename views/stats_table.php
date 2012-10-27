		<?php 
		switch ($player_type) {
			case TYPE_OFFENSE:
				$title = 'Batting';
				break;
			case TYPE_SPECIALTY:
				$title = 'Pitching';
				break;
		}
		?>
        <?php if (isset($records) && is_array($records) && count($records)) :  
			if (isset($teamname) && !empty($teamname)) :
				echo ('<h3>'.$teamname.' '.$title.' Stats</h3>');
			endif;
			?>
			<div class="stats-table">
			<table class="table table-striped table-bordered">
			<thead>
			<tr>
				<?php
                $exceptions = array('PN','TN','PID','TID');
				foreach ($stats_class as $field) :
					if ($field != 'PID' && $field != 'TID') {
                        $class = ' stat';
                        if (in_array($field, $exceptions)) { $class = ''; }
                        if (isset($stats_list['general'][$field]['lang']))
                        {
                            $label = lang("acyn_".$stats_list['general'][$field]['lang']);
                        }
                        else if (isset($stats_list[$player_type][$field]['lang']))
                        {
                            $label = lang("acyn_".$stats_list[$player_type][$field]['lang']);
                        }
					    echo (' <th class="headline'.$class.'">'.$label.'</th> '."\n");
                    }
				endforeach;
				?>
			</tr>
			</thead>
			<tbody>
			<?php 
			foreach ($records as $player) : ?>
			<?php $player = (array)$player;?>
			<tr>
				<?php
                $exceptions = array('player_name','team_name','player_id','team_id');
                foreach($player as $field => $value) :
					if ($field != 'id' && $field != 'role') :
						$class = 'stat';
                        if (in_array($field, $exceptions)) { $class = ''; }
						if ($field != 'player_id' && $field !=  'team_id') {
                            echo('<td class="'.$class.'">'.$value.'</td>');
                        }
					endif;
				endforeach; ?>
			</tr>
			<?php endforeach; ?>
			</tbody>
			
			<?php if (isset($totals) && is_array($totals) && count($totals)) :  ?>
			<tfoot>
			<tr>
				<?php
                $exceptions = array('player_name','team_name','player_id','team_id');
                foreach($totals[0] as $field => $value) :
					if ($field != 'id' && $field != 'role') :
						$class = 'stat';
                        if (in_array($field, $exceptions)) { $class = ''; }
                        if ($field != 'player_id' && $field !=  'team_id') {
						    echo('<td class="totals '.$class.'">'.(($field != 'player_name' && $field != 'team_name') ? $value : (($field == 'player_name') ? 'Totals' : '')).'</td>');
                        }
					endif;
				endforeach; ?>
			</tr>
			</tfoot>
			<?php
			endif; ?>
			</table>
			</div>
			<?php 
		endif; 
		?>