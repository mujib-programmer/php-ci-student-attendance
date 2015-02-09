<?php 
	echo ! empty($h2_title) ? '<h2>' . $h2_title . '</h2>': '';
	echo ! empty($message) ? '<p class="message">' . $message . '</p>': '';

	$flashmessage = $this->session->flashdata('message');
	echo ! empty($flashmessage) ? '<p class="message">' . $flashmessage . '</p>': '';
?>

<form name="absen_form" method="post" action="<?php echo $form_action; ?>">
	<p>
		<label for="nis">N I S:</label>
		<input type="text" class="form_field" name="nis" size="30" value="<?php echo set_value('nis', isset($default['nis']) ? $default['nis'] : ''); ?>" />
	</p>
	<?php echo form_error('nis', '<p class="field_error">', '</p>');?>
	
	<p>
		<label for="tanggal">Tanggal (dd-mm-yyyy):</label>
		<input type="text" class="form_field" name="tanggal" size="30" value="<?php echo set_value('tanggal', isset($default['tanggal']) ? $default['tanggal'] : ''); ?>" />
		
	</p>
	<?php echo form_error('tanggal', '<p class="field_error">', '</p>');?>	

	<p>
		<label for="Absen">Absen:</label>
		<input name="absen" type="radio" value="S" <?php echo set_radio('absen', 'S', isset($default['absen']) && $default['absen'] == 'S' ? TRUE : FALSE); ?> />Sakit
		<input name="absen" type="radio" value="I" <?php echo set_radio('absen', 'I', isset($default['absen']) && $default['absen'] == 'I' ? TRUE : FALSE); ?> />Ijin
		<input name="absen" type="radio" value="A" <?php echo set_radio('absen', 'A', isset($default['absen']) && $default['absen'] == 'A' ? TRUE : FALSE); ?> />Alpha
		<input name="absen" type="radio" value="T" <?php echo set_radio('absen', 'T', isset($default['absen']) && $default['absen'] == 'T' ? TRUE : FALSE); ?> />Terlambat
	</p>
	<?php echo form_error('absen', '<p class="field_error">', '</p>');?>

	<p>
		<input type="submit" name="submit" id="submit" value=" Simpan " />
	</p>
</form>

<?php
	if ( ! empty($link))
	{
		echo '<p id="bottom_link">';
		foreach($link as $links)
		{
			echo $links . ' ';
		}
		echo '</p>';
	}
?>