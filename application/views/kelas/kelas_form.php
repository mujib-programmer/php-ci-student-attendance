<?php 
	echo ! empty($h2_title) ? '<h2>' . $h2_title . '</h2>': '';
	echo ! empty($message) ? '<p class="message">' . $message . '</p>': '';

	$flashmessage = $this->session->flashdata('message');
	echo ! empty($flashmessage) ? '<p class="message">' . $flashmessage . '</p>': '';
?>

<form name="kelas_form" method="post" action="<?php echo $form_action; ?>">
	<p>
		<label for="nis">Kode Kelas:</label>
		<input type="text" class="form_field" name="id_kelas" size="30" value="<?php echo set_value('id_kelas', isset($default['id_kelas']) ? $default['id_kelas'] : ''); ?>" />
	</p>
	<?php echo form_error('id_kelas', '<p class="field_error">', '</p>');?>
	
	<p>
		<label for="tanggal">Kelas:</label>
		<input type="text" class="form_field" name="kelas" size="30" value="<?php echo set_value('kelas', isset($default['kelas']) ? $default['kelas'] : ''); ?>" />
		
	</p>
	<?php echo form_error('kelas', '<p class="field_error">', '</p>');?>	

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