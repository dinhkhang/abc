<?php

if (!empty($data)) {
        foreach ($data as $k => $v) {
                if (is_array($v) && !empty($v)) {
                        $option_tag = '';
                        foreach ($v as $kk => $vv) {
                                $option_tag .= $this->Html->tag('option', $vv, array(
                                    'value' => $kk,
                                ));
                        }
                        echo $this->Html->tag('optgroup', $option_tag, array('label' => $k));
                } elseif (!is_array($v)) {
                        echo $this->Html->tag('option', $v, array(
                            'value' => $k,
                        ));
                }
        }
}