<?

namespace SlothStars\Walker;

class NavMenu extends \Walker_Nav_Menu
{

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
    return \SlothStars\Template::render('walkers/nav-menus', $template, $vars);
  }
}