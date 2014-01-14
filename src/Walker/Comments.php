<?

namespace SlothStars\Walker;

class Comments extends \Walker_Comment
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
    return \SlothStars\Template::render('walkers/comments', $template, $vars);
  }
}