<?php
namespace Drupal\xmlnode\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'XmlnodeDefaultFormatter' formatter.
 *
 * @FieldFormatter(
 *   id = "XmlnodeDefaultFormatter",
 *   label = @Translation("Xmlnode"),
 *   field_types = {
 *     "Xmlnode"
 *   }
 * )
 */
class XmlnodeDefaultFormatter extends FormatterBase {

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