<?php
	echo ! empty($h2_title) ? '<h2>' . $h2_title . '</h2>': '';
	echo ! empty($message) ? '<p class="message">' . $message . '</p>': '';
	
	$flashmessage = $this->session->flashdata('message');
	echo ! empty($flashmessage) ? '<p class="message">' . $flashmessage . '</p>': '';
?>

<fieldset>
<legend>Pilih Kelas </legend>	
<form name="rekap_form" method="post" action="<?php echo $form_action; ?>">
	<p>
		<label for="id_kelas">Kelas:</label>
        <?php echo form_dropdown('id_kelas', $options_kelas, isset($default['id_kelas']) ? $default['id_kelas'] : ''); ?>
	</p>
	<?php echo form_error('id_kelas', '<p class="field_error">', '</p>');?>
	
	<p>
		<input type="submit" name="submit" id="submit" value=" O K " />
	</p>
</form>
</fieldset>
<?php echo ! empty($active_class) ? 'Kelas : ' . $active_class . '<br />' : ''; ?>
<?php echo ! empty($semester) ? 'Semester : ' . $semester : ''; ?>
<?php echo ! empty($table) ? $table : ''; ?>
	
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