<?php

namespace Drupal\address\Plugin\Field\FieldWidget;

use Drupal;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\File;

/**
 * Plugin implementation of the 'AddressDefaultWidget' widget.
 *
 * @FieldWidget(
 *   id = "AddressDefaultWidget",
 *   label = @Translation("Address select"),
 *   field_types = {
 *     "Address"
 *   }
 * )
 */
class AddressDefaultWidget extends WidgetBase {

    /**
     * Define the form for the field type.
     *
     * Inside this method we can define the form used to edit the field type.
     *
     * Here there is a list of allowed element types: https://goo.gl/XVd4tA
     */
    public function formElement(
        FieldItemListInterface $items,
        $delta,
        Array $element,
        Array &$form,
        FormStateInterface $formState
    ) {

        // Spelling

        $element['spelling'] = [
            '#type' => 'textfield',
            '#title' => t('Spelling'),
            '#description' => t('输入单词拼写'),
            '#default_value' => isset($items[$delta]->spelling) ?
                $items[$delta]->spelling : null,
            '#empty_value' => '',
            '#placeholder' => t('Spelling'),
        ];

        // Pronunciation

        $element['pronunciation'] = [
            '#type' => 'textfield',
            '#title' => t('Pronunciation'),
            '#description' => t('输入音标 '),
            '#default_value' => isset($items[$delta]->pronunciation) ?
                $items[$delta]->pronunciation : null,
            '#empty_value' => '',
            '#placeholder' => t('Pronunciation'),
        ];

        // Audio
        //必须在Address.php 里面处理，上传的是临时文件，吧临时文件改成永久性文件
        $element['audio'] = [
            '#title' => t('Audio file'),
            '#type' => 'managed_file',
            '#prefix'	=> 'Dummy prefix',
            '#suffix'	=> 'Dummy suffix',
            '#description' => t('请上传音频文件，必须是MP3文件 '),
            '#default_value' => isset($items[$delta]->audio) ?
                [(int)$items[$delta]->audio] : null,
            '#upload_validators'  => array(
                'file_validate_extensions' => array('mp3'),
                'file_validate_size' => array(25600000),
            ),
            '#upload_location' => 'public://Audio/',
            '#required' => FALSE,
        ];



        // Meaning

        $element['meaning'] = [
            '#type' => 'textfield',
            '#title' => t('Meaning'),
            '#description' => t('输入中文意思'),
            '#default_value' => isset($items[$delta]->meaning) ?
                $items[$delta]->meaning : null,
            '#empty_value' => '',
            '#placeholder' => t('Chinese meaning'),
        ];

        // Partofspeechc

        $element['partofspeech'] = [
            '#type' => 'checkboxes',
            '#options' => array('V' => $this->t('V'), 'n' => $this->t('n'),'adv' => $this->t('adv'),
                'adj' => $this->t('adj'),'prep' => $this->t('prep'),'conj' => $this->t('conj')),
            '#title' => $this->t('Part of speach'),
            '#default_value' =>isset($items[$delta]->partofspeech) ?
                $this->str_explode($items[$delta]->partofspeech):array('Verb'),
            '#description' => t('选择词性，只选一个，文章中表示的词性'),
        ];


        // Sentence

        $element['sentence'] = [
            '#type' => 'textarea',
            '#title' => t('Sentence'),
            '#default_value' => isset($items[$delta]->sentence) ?
                $items[$delta]->sentence : null,
            '#empty_value' => '',
            '#placeholder' => t('Sentence'),
            '#description' => t('例句，包含中文，英文两个例句，每一个要独立一行。'),
        ];

        // Reference

        $element['reference'] = [
            '#type' => 'textfield',
            '#title' => t('Reference'),
            '#default_value' => isset($items[$delta]->reference) ?
                $items[$delta]->reference : null,
            '#empty_value' => '',
            '#placeholder' => t('Reference'),
            '#description' => t('文章中要是有变体，从文章中摘取输入变体，重点词汇放在英文小括号里面，比如：i (love) you'),
        ];



        return $element;
    }

    /**
     * @param $vv
     * @return array
     */
    public function str_explode($vv){
        $st=explode('+',$vv);
        return $st;
    }


    /**
     * @param array $values
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array
     */
    public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
        $str='';
        $i=0;
        foreach($values[0]['partofspeech'] as $k=>$v){
            if($v!='0') {
                if ($i == 0) {
                    $str = $v;
                } else {
                    $str = $str . '+' . $v;
                }
            }
            $i++;

        }
        $values[0]['partofspeech']=$str;
        $values[0]['audio']=$values[0]['audio'][0];
        return $values;
    }


} // class