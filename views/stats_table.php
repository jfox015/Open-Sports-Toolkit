		<?php 
		switch ($player_type) {
			case TYPE_OFFENSE:
				$title = 'Batting';
				break;
			case TYPE_SPECIALTY:
				$title = 'Pitching';
				break;
		}
		$totals = array();
			
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
                if (isset($records['headers']) && is_array($records['headers']) && count($records['headers'])) :
                    $exceptions = array('PN','TN','PID','TID');
                    foreach ($records['headers'] as $field => $label) :
                        if ($field != 'PID' && $field != 'TID') {
                            $class = 'stat';
                            if (in_array($field, $exceptions)) { $class = ''; }
                            echo (' <th class="headline'.$class.'">'.$label.'</th> '."\n");
                        }
                    endforeach;
                endif;
				?>
			</tr>
			</thead>
			<tbody>
			<?php
            if (isset($records['stats']) && is_array($records['stats']) && count($records['stats'])) :
                foreach ($records['stats'] as $player) : ?>
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
								if (!in_array($field, $exceptions)) { 
									if (isset($totals[$field])) {
										$totals[$field] += intval($value);
									} else {
										$totals[$field] = intval($value);
									}
								}
							}
                        endif;
                    endforeach; ?>
                </tr>
                <?php endforeach;
            endif;
            ?>
			</tbody>
			
			<?php if (isset($totals) && is_array($totals) && count($totals)) :  ?>
			<tfoot>
			<tr>
				<?php
                $totals = compile_stats($totals);
                $totals = format_stats($player_type, array($totals)	, $stats_class, $stats_list);
				$exceptions = array('player_name','team_name','player_id','team_id','age');
                foreach($totals[0] as $field => $value) :
					if ($field != 'id' && $field != 'role') :
						$class = 'stat';
                        if (in_array($field, $exceptions)) { $class = ''; }
                        if ($field != 'player_id' && $field !=  'team_id') {
						    echo('<td class="totals '.$class.'">'.(($field != 'player_name' && $field != 'team_name' && $field != 'age' && $field != 'bats' && $field != 'throws' && $field != 'year') ? $value : (($field == 'player_name') ? 'Totals' : '')).'</td>');
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