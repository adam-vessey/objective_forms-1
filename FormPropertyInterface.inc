<?php

/**
 * @file
 * Interface for converting FormElement properties.
 *
 * Converts into/between object/scalar values.
 */

/**
 * An interface for FormProperties that want to be treated as objects.
 */
interface FormPropertyInterface {

  /**
   * Function __constructor.
   *
   * Creates the FormProperty object from its scalar or array Drupal Form
   * Property value.
   *
   * @param mixed $value
   *   The value.
   */
  public function __construct($value);

  /**
   * Converts this object to a Drupal Form Property value.
   *
   * @return mixed
   *   Returns a scalar or array repersentation of this class, which can be used
   *   within a Drupal Form.
   */
  public function toDrupalForm();

}
