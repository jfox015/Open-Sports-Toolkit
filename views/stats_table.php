
		<?php if (isset($records) && is_array($records) && count($records)) :  
			echo ('<h3>'.$teamname.' '.$type.' Stats</h3>');
			?>
			<table class="table table-striped table-bordered">
			<thead>
			<tr>
				<?php 
				foreach ($headers as $header) :
					echo (' <th class="headline">'.lang("acyn_".$header).'</th> '."\n");
				endforeach;
				?>
			</tr>
			</thead>
			<tbody>
			<?php 
			$totals = array();
			
			foreach ($records as $player) : ?>
			<?php $player = (array)$player;?>
			<tr>
				<?php foreach($player as $field => $value) :  
					if ($field != 'id' && $field != 'role') :
						echo('<td>'.$value.'</td>');
					endif;
				endforeach; ?>
			</tr>
			<?php endforeach; ?>
			</tbody>
			
			<?php if (isset($totals) && is_array($totals) && count($totals)) :  ?>
			<tfoot>
			<tr class="totals">
				<?php foreach($totals as $field => $value) :  
					if ($field != 'id' && $field != 'role') :
						echo('<td>'.$value.'</td>');
					endif;
				endforeach; ?>
			</tr>
			</tfoot>
			<?php
			endif; ?>
			</table>
			<?php 
		endif; 
		?>