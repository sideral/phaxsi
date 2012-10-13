<div id="phaxsi-console" style="display:<?= $popup?'none':'block' ?>">
	<?= $html->css('/phaxsi/console/console.css'); ?>
	<div id="phaxsi-trace">
		<table>
			<thead>
				<tr>
					<th class="controller">Location</th>
					<th >Content</th>
					<th >Time(ms)</th>		
				</tr>
			</thead>
			<tbody>
				<?php foreach($lines as $line): ?>

					<tr class="row <?= $line['type']; ?>">
						<td>
							<?= $line['location']; ?>
						</td>
						<td>
							<?php if(in_array($line['type'], array('info', 'no-important'))): ?>
								<?= $html->pre($line['name']); ?>
							<?php else: ?>
								<?= $line['name']; ?>
							<?php endif; ?>

						</td>
						<td class="duration">
							<?php if($line['duration'] != ''): ?>
								<?= round($line['duration']*1000, 3);?>
							<?php else: ?>
								-
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">
						<?= date('r'); ?>
					</td>
				</tr>
			</tfoot>

		</table>
	</div>
</div>

<?php if($popup):?>
	<span id="phaxsi-opener">
		<a onclick="window.open('', 'console', 'height=450,width=980,scrollbars=yes').document.write(document.getElementById('phaxsi-console').innerHTML);return false;" class="view-details" href="#">&raquo; See Details</a>
		<?php foreach($lines as $i => $line): ?>
			<?php if($line['type'] == 'error' || $i == count($lines)-1):?>
				<?= $line['name']; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</span>
<?php endif; ?>

