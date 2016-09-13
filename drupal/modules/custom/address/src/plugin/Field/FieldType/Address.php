<?php

namespace Drupal\address\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface as StorageDefinition;
use Drupal\file\Entity\File;

/**
 * Plugin implementation of the 'address' field type.
 *
 * @FieldType(
 *   id = "Address",
 *   label = @Translation("Address"),
 *   description = @Translation("Stores an address."),
 *   category = @Translation("Custom"),
 *   default_widget = "AddressDefaultWidget",
 *   default_formatter = "AddressDefaultFormatter"
 * )
 */
class Address extends FieldItemBase {

    /**
     * Field type properties definition.
     *
     * Inside this method we defines all the fields (properties) that our
     * custom field type will have.
     *
     * Here there is a list of allowed property types: https://goo.gl/sIBBgO
     */
    public static function propertyDefinitions(StorageDefinition $storage) {

        $properties = [];

        $properties['spelling'] = DataDefinition::create('string')
            ->setLabel(t('Spelling'));

        $properties['pronunciation'] = DataDefinition::create('string')
            ->setLabel(t('Pronunciation'));

        $properties['audio'] = DataDefinition::create('string')
            ->setLabel(t('Audio'));

        $properties['meaning'] = DataDefinition::create('string')
            ->setLabel(t('Meaning'));

        $properties['partofspeech'] = DataDefinition::create('string')
            ->setLabel(t('Partofspeech'));

        $properties['sentence'] = DataDefinition::create('string')
            ->setLabel(t('Sentence'));

        $properties['reference'] = DataDefinition::create('string')
            ->setLabel(t('Reference'));

        return $properties;
    }

    /**
     * Field type schema definition.
     *
     * Inside this method we defines the database schema used to store data for
     * our field type.
     *
     * Here there is a list of allowed column types: https://goo.gl/YY3G7s
     */
    public static function schema(StorageDefinition $storage) {

        $columns = [];
        $columns['spelling'] = [
            'type' => 'char',
            'length' => 255,
        ];
        $columns['pronunciation'] = [
            'type' => 'char',
            'length' => 255,
        ];
        $columns['audio'] = [
            'type' => 'char',
            'length' => 255,
        ];
        $columns['meaning'] = [
            'type' => 'char',
            'length' => 255,
        ];
        $columns['partofspeech'] = [
            'type' => 'char',
            'length' => 255,
        ];
        $columns['sentence'] = [
            'type' => 'char',
            'length' => 255,
        ];
        $columns['reference'] = [
            'type' => 'char',
            'length' => 255,
        ];
        return [
            'columns' => $columns,
            'indexes' => [],
        ];
    }

    /**
     * Define when the field type is empty.
     *
     * This method is important and used internally by Drupal. Take a moment
     * to define when the field fype must be considered empty.
     */
    public function isEmpty() {

        $isEmpty =
            empty($this->get('spelling')->getValue()) &&
            empty($this->get('pronunciation')->getValue());
            empty($this->get('audio')->getValue());
            empty($this->get('meaning')->getValue());
            empty($this->get('partofspeech')->getValue());
            empty($this->get('sentence')->getValue());
            empty($this->get('reference')->getValue());

        return $isEmpty;
    }


    /**
     * 把临时文件改成永久性文件
     */
    public function preSave() {
        if ($file = File::load((int)$this->audio)) {
            $file->setPermanent();
            $file->save();
        }
    }


} // class


