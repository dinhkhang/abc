<?php

$opts_name = strtolower(Inflector::pluralize($input_field));
$value = $this->request->data($model_name . '.' . $input_field);
$options = $this->request->data($model_name . '.' . $opts_name);
$text = !empty($options[$value]) ? $options[$value] : '';

$input_options = array(
	'type' => 'select',
	'class' => 'select-ajax form-control',
	'id' => $input_id,
	'div' => false,
	'label' => false,
	'data-ajax--url' => Router::url(array('action' => 'reqLocation', $input_id)),
	'data-value' => $value,
	'data-text' => $text,
	'options' => $options,
	'default' => $value,
	'empty' => '-------',
);
if (!empty($required)) {

	$input_options['required'] = $required;
}

echo $this->Form->input($model_name . '.' . $input_field, $input_options);
