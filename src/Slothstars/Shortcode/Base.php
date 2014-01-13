<?

namespace SlothStars\Shortcode;

class Base
{
  // --------------------------------------------------------------------------------------------
  // ~ Public methods
  // --------------------------------------------------------------------------------------------

  /**
   * @return string[] the shortcode tags
   */
  public static function getTags()
  {
    return array();
  }

  /**
   * @param array $atts    an associative array of attributes
   * @param array $content the enclosed content (if the shortcode is used in its enclosing form)
   * @param array $tag     the shortcode tag, useful for shared callback functions
   * @return string
   */
  public static function render($atts, $content, $tag)
  {
    return '';
  }

  // --------------------------------------------------------------------------------------------
  // ~ Protected static methods
  // --------------------------------------------------------------------------------------------

  /**
   * @param string $template
   * @param array $vars
   * @return string
   */
  protected static function renderTemplate($template, array $vars=array())
  {
    return \SlothStars\Template::render('shortcodes', $template, $vars);
  }
}