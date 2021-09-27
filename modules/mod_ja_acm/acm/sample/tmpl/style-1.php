<div class="clearfix">
  <h2>Section 1</h2>
  <h3><?php echo $helper->get ('field1') ?></h3>
  <p><?php echo $helper->get ('list') ?></p>
  <p><?php echo $helper->get ('table') ?></p>
</div>

<div class="clearfix">
  <h2>Table Data</h2>
  <?php
  $rows = $helper->getRows('table.name');
  $cols = $helper->getCols('table.name');
  ?>

	<table>
		<tr>
			<td>Name</td>
			<?php for ($col=0; $col<$cols; $col++): ?>
				<td><?php echo $helper->get('table.name', $col) ?></td>
			<?php endfor ?>
		</tr>
		<?php for ($row=0; $row<$rows; $row++) : ?>
		<tr>
			<?php for ($col=0;$col<$cols+1; $col++):
				$value = $helper->getCell('table', $row, $col);
				$type = $value[0];
				?>
				<?php if ($type == 't'): ?>
				<td><?php echo substr($value, 1) ?></td>
				<?php else: ?>
				<td><span class="<?php echo $value ?>"><?php echo $value ?></span></td>
				<?php endif ?>
			<?php endfor ?>
		</tr>
		<?php endfor ?>
	</table>
</div>
<div class="clearfix">
	<h2>List Data</h2>

	<?php $count = $helper->getRows('list.col1'); ?>
  <?php for ($i=0; $i<$count; $i++) : ?>
  <div class="clearfix">
		<h3>Row <?php echo $i ?></h3>
		<p>Text value: <?php echo $helper->get ('list.col1', $i) ?></p>
		<p>Select list value: <?php echo $helper->get ('list.col2', $i) ?></p>
		<p>Radio check value: <?php echo $helper->get ('list.col31', $i) ?></p>
		<p>Checkbox value: <?php echo $helper->get ('list.col32', $i, false ) ? 'Checked':'Unchecked' ?></p>
		<img src="<?php echo $helper->get ("list.col4", $i) ?>" title="" />
  </div>
  <?php endfor ?>
</div>