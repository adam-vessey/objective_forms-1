<?php
namespace Drupal\objective_forms;

use Drupal\Core\Form\FormStateInterface;

use Drupal\objective_forms\FormValueTracker;

/**
 * This class stores all submitted values. It provides a mechanism for
 * accessing submitted values with the FormElements hashes.
 */
class FormValues {

  /**
   * A map of submitted form values where the key is the FormElement's hash.
   *
   * @var array
   */
  protected $values;

  /**
   * A helper class that matches values to elements.
   *
   * @var FormValueTracker
   */
  protected $tracker;

  /**
   * Checks to see if any form values were submitted to the server.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The Drupal Form State.
   *
   * @return bool
   *   TRUE if $form_state['values'] exists, FALSE otherwise.
   */
  public static function Exists(FormStateInterface $form_state) {
    return $form_state->getValues() ? TRUE : FALSE;
  }

  /**
   * Create a FormValues instance.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Drupal Form state.
   *
   * @param array $root
   *   The elements to associate with submitted values.
   */
  public function __construct(FormStateInterface $form_state, array &$root, FormElementRegistry $registry) {
    $this->values = array();
    if (self::Exists($form_state)) {
      $this->tracker = new FormValueTracker($form_state->getValues(), $registry);
      $this->setValues($root, $this->tracker);
    }
  }

  /**
   * Utilizes the FormValueTracker to store all the submitted values.
   *
   * @param array $element
   *   An element in the Drupal Form.
   *
   * @param FormValueTracker $tracker
   *   A helper class for extracting submitted values.
   */
  protected function setValues(array &$element, FormValueTracker $tracker) {
    $this->setValue($element, $tracker);
    $children = \Drupal\Core\Render\Element::children($element);
    foreach ($children as $key) {
      $child = &$element[$key];
      $this->setValues($child, clone $tracker);
    }
  }

  /**
   * Associate the given array &, with a submitted value.
   *
   * @param array $element
   *   An element in the Drupal Form.
   *
   * @param FormValueTracker $tracker
   *   A helper class for extracting submitted values.
   */
  protected function setValue(array &$element, FormValueTracker $tracker) {
    $value = $tracker->getValue($element);
    if (isset($element['#hash'])) {
      $this->values[$element['#hash']] = $value;
    }
  }

  /**
   * Is there a value associated with the given form element.
   *
   * @param hash $hash
   *   The unique #hash property that identifies the FormElement.
   *
   * @return bool
   *   TRUE if there is a value for the given FormElement.
   */
  public function hasValue($hash) {
    return isset($this->values[$hash]);
  }

  /**
   * Get the value associated with the given element.
   *
   * @param hash $hash
   *   The unique #hash property that identifies the FormElement.
   *
   * @return mixed
   *   The submitted value for the given element if found, NULL otherwise.
   */
  public function getValue($hash) {
    if ($this->hasValue($hash)) {
      $value = $this->values[$hash];
      if (is_array($value)) {
        foreach ($value as &$v) {
          if (is_string($v)) {
            $v = trim($v);
          }
        }
      }
      elseif (is_string($value)) {
        $value = trim($value);
      }
      return $value;
    }
    return NULL;
  }

  /**
   * Returns all values.
   *
   * @return array
   *   The keys of the array are the FormElement's hashs, and the values are the
   *   submitted form values.
   */
  public function getValues() {
    return $this->values;
  }

}
