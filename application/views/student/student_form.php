<?php 
	echo ! empty($h2_title) ? '<h2>' . $h2_title . '</h2>': '';
	echo ! empty($message) ? '<p class="message">' . $message . '</p>': '';

	$flashmessage = $this->session->flashdata('message');
	echo ! empty($flashmessage) ? '<p class="message">' . $flashmessage . '</p>': '';
?>

<form name="absen_form" method="post" action="<?php echo $form_action; ?>">
	<p>
		<label for="nis">NIS:</label>
		<input type="text" class="form_field" name="nis" size="30" value="<?php echo set_value('nis', isset($default['nis']) ? $default['nis'] : ''); ?>" />
	</p>
	<?php echo form_error('nis', '<p class="field_error">', '</p>');?>
	
	<p>
		<label for="tanggal">Nama:</label>
		<input type="text" class="form_field" name="nama" size="30" value="<?php echo set_value('nama', isset($default['nama']) ? $default['nama'] : ''); ?>" />
		
	</p>
	<?php echo form_error('nama', '<p class="field_error">', '</p>');?>	

	<p>
		<label for="id_kelas">Kelas:</label>
        <?php echo form_dropdown('id_kelas', $options_kelas, isset($default['id_kelas']) ? $default['id_kelas'] : ''); ?>
	</p>
	<?php echo form_error('id_kelas', '<p class="field_error">', '</p>');?>

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