
		<?php if (isset($records) && is_array($records) && count($records)) :  
			echo ('<h3>'.$teamname.' '.$type.' Stats</h3>');
			?>
			<div class="stats-table">
			<table class="table table-striped table-bordered">
			<thead>
			<tr>
				<?php 
				foreach ($headers as $header) :
					$class = ' stat';
					if ($header == 'PN' || $header == 'TN') { $class = ''; }
					echo (' <th class="headline'.$class.'">'.lang("acyn_".$header).'</th> '."\n");
				endforeach;
				?>
			</tr>
			</thead>
			<tbody>
			<?php 
			foreach ($records as $player) : ?>
			<?php $player = (array)$player;?>
			<tr>
				<?php foreach($player as $field => $value) :  
					if ($field != 'id' && $field != 'role') :
						$class = 'stat';
						if ($field == 'player_name' || $field == 'team_name') { $class = ''; }
						echo('<td class="'.$class.'">'.$value.'</td>');
					endif;
				endforeach; ?>
			</tr>
			<?php endforeach; ?>
			</tbody>
			
			<?php if (isset($totals) && is_array($totals) && count($totals)) :  ?>
			<tfoot>
			<tr>
				<?php foreach($totals[0] as $field => $value) :  
					if ($field != 'id' && $field != 'role') :
						$class = 'stat';
						if ($field == 'player_name' || $field == 'team_name') { $class = ''; }
						echo('<td class="totals '.$class.'">'.(($field != 'player_name' && $field != 'team_name') ? $value : (($field == 'player_name') ? 'Totals' : '')).'</td>');
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