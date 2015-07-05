<?php
/*
Plugin Name: StatsFC Fixtures
Plugin URI: https://statsfc.com/widgets/fixtures
Description: StatsFC Fixtures
Version: 1.7
Author: Will Woodward
Author URI: http://willjw.co.uk
License: GPL2
*/

/*  Copyright 2013  Will Woodward  (email : will@willjw.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('STATSFC_FIXTURES_ID',      'StatsFC_Fixtures');
define('STATSFC_FIXTURES_NAME',    'StatsFC Fixtures');
define('STATSFC_FIXTURES_VERSION', '1.7');

/**
 * Adds StatsFC widget.
 */
class StatsFC_Fixtures extends WP_Widget
{
    public $isShortcode = false;

    protected static $count = 0;

    private static $defaults = array(
        'title'       => '',
        'key'         => '',
        'competition' => '',
        'team'        => '',
        'from'        => '',
        'to'          => '',
        'limit'       => 0,
        'timezone'    => 'Europe/London',
        'default_css' => true
    );

    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(STATSFC_FIXTURES_ID, STATSFC_FIXTURES_NAME, array('description' => 'StatsFC Fixtures'));
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {
        $instance    = wp_parse_args((array) $instance, self::$defaults);
        $title       = strip_tags($instance['title']);
        $key         = strip_tags($instance['key']);
        $competition = strip_tags($instance['competition']);
        $team        = strip_tags($instance['team']);
        $from        = strip_tags($instance['from']);
        $to          = strip_tags($instance['to']);
        $limit       = strip_tags($instance['limit']);
        $timezone    = strip_tags($instance['timezone']);
        $default_css = strip_tags($instance['default_css']);
        ?>
        <p>
            <label>
                Title
                <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
            </label>
        </p>
        <p>
            <label>
                Key
                <input class="widefat" name="<?php echo $this->get_field_name('key'); ?>" type="text" value="<?php echo esc_attr($key); ?>">
            </label>
        </p>
        <p>
            <label>
                Competition
                <input class="widefat" name="<?php echo $this->get_field_name('competition'); ?>" type="text" value="<?php echo esc_attr($competition); ?>" placeholder="e.g., EPL, CHP, FAC">
            </label>
        </p>
        <p>
            <label>
                Team
                <input class="widefat" name="<?php echo $this->get_field_name('team'); ?>" type="text" value="<?php echo esc_attr($team); ?>" placeholder="e.g., Liverpool, Manchester City">
            </label>
        </p>
        <p>
            <label>
                From
                <input class="widefat" name="<?php echo $this->get_field_name('from'); ?>" type="text" value="<?php echo esc_attr($from); ?>" placeholder="e.g., <?php echo date('Y-m-d'); ?>, today">
            </label>
        </p>
        <p>
            <label>
                To
                <input class="widefat" name="<?php echo $this->get_field_name('to'); ?>" type="text" value="<?php echo esc_attr($to); ?>" placeholder="e.g., <?php echo date('Y-m-d'); ?>, +2 weeks, next Monday">
            </label>
        </p>
        <p>
            <label>
                Limit
                <input class="widefat" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo esc_attr($limit); ?>" min="0" max="99"><br>
                <small>Applies to single team only. Choose '0' for all fixtures.</small>
            </label>
        </p>
        <p>
            <label>
                Timezone
                <select class="widefat" name="<?php echo $this->get_field_name('timezone'); ?>">
                    <?php
                    $zones = timezone_identifiers_list();

                    foreach ($zones as $zone) {
                        $selected = ($zone == $timezone ? ' selected' : '');

                        echo '<option value="' . esc_attr($zone) . '"' . $selected . '>' . esc_attr($zone) . '</option>' . PHP_EOL;
                    }
                    ?>
                </select>
            </label>
        </p>
        <p>
            <label>
                Use default styles?
                <input type="checkbox" name="<?php echo $this->get_field_name('default_css'); ?>"<?php echo ($default_css == 'on' ? ' checked' : ''); ?>>
            </label>
        </p>
    <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance                = $old_instance;
        $instance['title']       = strip_tags($new_instance['title']);
        $instance['key']         = strip_tags($new_instance['key']);
        $instance['competition'] = strip_tags($new_instance['competition']);
        $instance['team']        = strip_tags($new_instance['team']);
        $instance['from']        = strip_tags($new_instance['from']);
        $instance['to']          = strip_tags($new_instance['to']);
        $instance['limit']       = strip_tags($new_instance['limit']);
        $instance['timezone']    = strip_tags($new_instance['timezone']);
        $instance['default_css'] = strip_tags($new_instance['default_css']);

        return $instance;
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        extract($args);

        $title       = apply_filters('widget_title', $instance['title']);
        $whitelist   = array('competition', 'team', 'from', 'to', 'limit', 'highlight', 'showBadges', 'showDates', 'timezone');
        $unique_id   = ++static::$count;
        $key         = $instance['key'];
        $referer     = (array_key_exists('HTTP_REFERER', $_SERVER) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : '');
        $default_css = filter_var($instance['default_css'], FILTER_VALIDATE_BOOLEAN);

        $options = array(
            'competition' => $instance['competition'],
            'team'        => $instance['team'],
            'from'        => $instance['from'],
            'to'          => $instance['to'],
            'limit'       => (int) $instance['limit'],
            'timezone'    => $instance['timezone']
        );

        $html  = $before_widget;
        $html .= $before_title . $title . $after_title;
        $html .= '<div id="statsfc-fixtures-' . $unique_id . '"></div>' . PHP_EOL;
        $html .= $after_widget;

        // Enqueue CSS
        if ($default_css) {
            wp_register_style(STATSFC_FIXTURES_ID . '-css', plugins_url('all.css', __FILE__), null, STATSFC_FIXTURES_VERSION);
            wp_enqueue_style(STATSFC_FIXTURES_ID . '-css');
        }

        // Enqueue base JS
        wp_register_script(STATSFC_FIXTURES_ID . '-js', plugins_url('fixtures.js', __FILE__), array('jquery'), STATSFC_FIXTURES_VERSION, true);
        wp_enqueue_script(STATSFC_FIXTURES_ID . '-js');

        // Enqueue widget JS
        $object = 'statsfc_fixtures_' . $unique_id;

        $GLOBALS['statsfc_fixtures_init'] = '<script>' . PHP_EOL;
        $GLOBALS['statsfc_fixtures_init'] .= 'var ' . $object . ' = new StatsFC_Fixtures(' . json_encode($key) . ');' . PHP_EOL;
        $GLOBALS['statsfc_fixtures_init'] .= $object . '.referer = ' . json_encode($referer) . ';' . PHP_EOL;

        foreach ($whitelist as $parameter) {
            if (! array_key_exists($parameter, $options)) {
                continue;
            }

            $GLOBALS['statsfc_fixtures_init'] .= $object . '.' . $parameter . ' = ' . json_encode($options[$parameter]) . ';' . PHP_EOL;
        }

        $GLOBALS['statsfc_fixtures_init'] .= $object . '.display("statsfc-fixtures-' . $unique_id . '");' . PHP_EOL;
        $GLOBALS['statsfc_fixtures_init'] .= '</script>';

        add_action('wp_print_footer_scripts', function()
        {
            global $statsfc_fixtures_init;

            echo $statsfc_fixtures_init;
        });

        if ($this->isShortcode) {
            return $html;
        } else {
            echo $html;
        }
    }

    public static function shortcode($atts)
    {
        $args = shortcode_atts(self::$defaults, $atts);

        $widget              = new self;
        $widget->isShortcode = true;

        return $widget->widget(array(), $args);
    }
}

// Register StatsFC widget
add_action('widgets_init', function()
{
    register_widget(STATSFC_FIXTURES_ID);
});

add_shortcode('statsfc-fixtures', STATSFC_FIXTURES_ID . '::shortcode');
