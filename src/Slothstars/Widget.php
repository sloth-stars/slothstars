<?php

namespace SlothStars;

/**
 *
 */
class Widget
{
  //---------------------------------------------------------------------------------------------
  // ~ Static variables
  //---------------------------------------------------------------------------------------------

  private static $classes = array();

  //---------------------------------------------------------------------------------------------
  // ~ Public static methods
  //---------------------------------------------------------------------------------------------

  /**
   * @param mixed $class
   */
  public static function registerClasses($classes)
  {
    \SlothStars\Hooks::registerClasses(get_class());
    if (!is_array($classes)) $classes = array($classes);
    self::$classes = array_unique(array_merge(self::$classes, $classes));
  }

  /**
   * @param mixed $class
   */
  public static function unregisterClasses($classes)
  {
    if (!is_array($classes)) $classes = array($classes);
    self::$classes = array_diff(self::$classes, $classes);
  }

  // --------------------------------------------------------------------------------------------
  // ~ Internal static methods
  // --------------------------------------------------------------------------------------------

  /**
   * @add_action widgets_init
   */
  public static function _init()
  {
    foreach (self::$classes as $class) register_widget($class);
  }
}