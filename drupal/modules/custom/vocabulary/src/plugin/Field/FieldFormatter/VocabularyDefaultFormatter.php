<?php
namespace Drupal\vocabulary\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal;

/**
 * Plugin implementation of the 'VocabularyDefaultFormatter' formatter.
 *
 * @FieldFormatter(
 *   id = "VocabularyDefaultFormatter",
 *   label = @Translation("Vocabulary"),
 *   field_types = {
 *     "Vocabulary"
 *   }
 * )
 */
class VocabularyDefaultFormatter extends FormatterBase {

    /**
     * Define how the field type is showed.
     *
     * Inside this method we can customize how the field is displayed inside
     * pages.
     */
    public function viewElements(FieldItemListInterface $items, $langcode) {

        $elements = [];
        foreach ($items as $delta => $item) {
            $elements[$delta] = [
                '#type' => 'markup',
                '#markup' => $item->street . ', ' . $item->city
            ];
        }

        return $elements;
    }

} // class