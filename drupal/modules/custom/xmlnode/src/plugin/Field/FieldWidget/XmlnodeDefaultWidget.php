<?php

namespace Drupal\xmlnode\Plugin\Field\FieldWidget;

use Drupal;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'XmlnodeDefaultWidget' widget.
 *
 * @FieldWidget(
 *   id = "XmlnodeDefaultWidget",
 *   label = @Translation("Xmlnode select"),
 *   field_types = {
 *     "Xmlnode"
 *   }
 * )
 */
class XmlnodeDefaultWidget extends WidgetBase {

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

        // Audio
        //必须在Address.php 里面处理，上传的是临时文件，吧临时文件改成永久性文件
        $element['xml'] = [
            '#title' => t('Xml file'),
            '#type' => 'managed_file',
            '#prefix'	=> 'Dummy prefix',
            '#suffix'	=> 'Dummy suffix',
            '#description' => t('place upload xml file,file extension name must be .xml '),
            '#default_value' => isset($items[$delta]->xml) ?
                [(int)$items[$delta]->xml] : null,
            '#upload_validators'  => array(
                'file_validate_extensions' => array('xml'),
                'file_validate_size' => array(25600000),
            ),
            '#upload_location' => 'public://Audio/',
            '#required' => FALSE,
        ];




        return $element;
    }




    /**
     * @param array $values
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array
     */
    public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
        $values[0]['xml']=$values[0]['xml'][0];
        return $values;
    }

} // class