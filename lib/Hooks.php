<?

namespace SlothStars;

class Hooks
{
  //---------------------------------------------------------------------------------------------
  // ~ Public static methods
  //---------------------------------------------------------------------------------------------

  /**
   * @param mixed $class
   */
  public static function registerClasses($classes)
  {
    if (!is_array($classes)) $classes = array($classes);
    foreach ($classes as $class) self::handleHookRegistration('add', $class, $class);
  }

  /**
   * @param mixed $class
   */
  public static function unregisterClasses($classes)
  {
    if (!is_array($classes)) $classes = array($classes);
    foreach ($classes as $class) self::handleHookRegistration('remove', $class, $class);
  }

  //---------------------------------------------------------------------------------------------
  // ~ Private static methods
  //---------------------------------------------------------------------------------------------

  /**
   * @param string $action
   * @param mixed $class
   * @param mixed $target
   */
  private static function handleHookRegistration($action, $class, $target)
  {
    # get reflection
    $reflection = new \ReflectionClass($class);

    # get class comment
    $comment = $reflection->getDocComment();

    # scan class comment for @nohooks
    if (preg_match('/@no_hooks[ \t\*\n]+/', $comment)) return;
    # scan class comment for @no_filter_hooks or @no_action_hooks
    $filters = !(preg_match('/@no_filter_hooks[ \t\*\n]+/', $comment));
    $actions = !(preg_match('/@no_action_hooks[ \t\*\n]+/', $comment));

    # scan method comments
    foreach ($reflection->getMethods() as $method) {
      # ignore constructor or non public methods
      if (!$method->isPublic() || $method->isConstructor()) continue;
      # get comment
      $comment = $method->getDocComment();
      if ($filters) self::handleHook($action, 'filter', $comment, $method, $target);
      if ($actions) self::handleHook($action, 'action', $comment, $method, $target);
    }

    # register parent defined hooks
    if (null != $parentClass = \get_parent_class($class)) self::handleHookRegistration($action, $parentClass, $target);
  }

  /**
   * @param string $action
   * @param string $type
   * @param string $comment
   * @param object $method
   * @param mixed $class
   */
  private static function handleHook($action, $type, $comment, $method, $class)
  {
    # scan for @add_action
    preg_match_all('/@add_' . $type . '?\s+(.*?)\n/s', $comment, $matches);

    # return if nothing found
    if (empty($matches[1])) return;

    #\trigger_error('   --> ' . $type . ' - ' . $comment . ' - ' . $method);

    $filterHooks = $matches[1];
    foreach ($filterHooks as $filterHook) {
      $filterHook = trim(preg_replace('!\s+!', ' ', $filterHook));
      $parts = explode(' ', $filterHook);
      $name = (string) array_shift($parts);
      $priority = (!empty($parts)) ? intval(array_shift($parts)) : 10;
      $arguments = (!empty($parts)) ? intval(array_shift($parts)) : $method->getNumberOfParameters();
      call_user_func($action .'_' . $type , $name, array($class, $method->name), $priority, $arguments);
    }
  }
}