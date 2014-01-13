<?

namespace SlothStars\Widget;

class Base extends \WP_Widget
{
  //---------------------------------------------------------------------------------------------
  // ~ Constructor
  //---------------------------------------------------------------------------------------------

  /**
   * @see WP_Widget::__construct
   *
   * @param string $id_base
   * @param string $name
   * @param array $widget_options
   * @param array $control_options
   */
  public function __construct($id_base, $name, $widget_options=array(), $control_options=array())
  {
    parent::__construct($id_base, $name, $widget_options, $control_options);
  }

  //---------------------------------------------------------------------------------------------
  // ~ Public methods
  //---------------------------------------------------------------------------------------------

  /**
   * @param array $new_instance
   * @param array $old_instance
   * @return array
   */
  public function update($new_instance, $old_instance)
  {
    $this->flush_widget_cache();
    $allOptions = wp_cache_get('alloptions', 'options');
    if (isset($allOptions[static::ID])) delete_option(static::ID);
    return parent::update($new_instance, $old_instance);
  }

  /**
   *
   */
  public function flush_widget_cache()
  {
    wp_cache_delete(static::ID, 'widget');
  }

  //---------------------------------------------------------------------------------------------
  // ~ Protected methods
  //---------------------------------------------------------------------------------------------

  /**
   * Use in YourWidget::widget to receive a value
   *
   * @param array $instance
   * @param string $value
   * @param mixed $default
   * @return mixed
   */
  protected function getWidgetArguement($instance, $value, $default)
  {
    return apply_filters('widget_' . $value, !isset($instance[$value]) ? $default : $instance[$value], $instance, $this->id_base);
  }

  /**
   * @param string $template
   * @param array $vars
   * @return string
   */
  protected function renderTemplate($template, array $vars=array())
  {
    return \SlothStars\Template::render('widgets/' . static::ID, $template, $vars);
  }
}