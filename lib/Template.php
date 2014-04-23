<?

namespace SlothStars;

class Template
{
  // --------------------------------------------------------------------------------------------
  // ~ Static variables
  // --------------------------------------------------------------------------------------------

  /**
   * @string
   */
  private static $template;

  // --------------------------------------------------------------------------------------------
  // ~ Public methods
  // --------------------------------------------------------------------------------------------

  /**
   * @param string $context
   * @param string $template
   * @param array $vars
   * @return string
   */
  public static function render($context, $template, $vars)
  {
    self::$template = locate_template('templates/' . $context . '/' . $template . '.php');
    if (!self::$template) trigger_error('Template does not exist: ' . 'templates/' . $context . '/' . $template . '.php', E_USER_ERROR);
    return self::renderTemplate($vars);
  }

  //---------------------------------------------------------------------------------------------
  // Private static methods
  //---------------------------------------------------------------------------------------------

  /**
   * @param array $vars
   * @return string
   */
  private static function renderTemplate(array $vars=array())
  {
    ob_start();
    extract($vars);
    include self::$template;
    $rendering = ob_get_clean();
    return $rendering;
  }
}