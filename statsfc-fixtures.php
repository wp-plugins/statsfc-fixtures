<?php
/*
Plugin Name: StatsFC Fixtures
Plugin URI: https://statsfc.com/developers
Description: StatsFC Fixtures
Version: 1.0.1
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

define('STATSFC_FIXTURES_ID',	'StatsFC_Fixtures');
define('STATSFC_FIXTURES_NAME',	'StatsFC Fixtures');

/**
 * Adds StatsFC widget.
 */
class StatsFC_Fixtures extends WP_Widget {
	private static $_competitions = array(
		'premier-league'	=> 'Premier League',
		'fa-cup'			=> 'FA Cup',
		'league-cup'		=> 'League Cup'
	);

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(STATSFC_FIXTURES_ID, STATSFC_FIXTURES_NAME, array('description' => 'StatsFC Fixtures'));
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {
		$defaults = array(
			'title'			=> __('Fixtures', STATSFC_FIXTURES_ID),
			'api_key'		=> __('', STATSFC_FIXTURES_ID),
			'competition'	=> __(current(array_keys(self::$_competitions)), STATSFC_FIXTURES_ID),
			'team'			=> __('', STATSFC_FIXTURES_ID),
			'default_css'	=> __('', STATSFC_FIXTURES_ID)
		);

		$instance		= wp_parse_args((array) $instance, $defaults);
		$title			= strip_tags($instance['title']);
		$api_key		= strip_tags($instance['api_key']);
		$competition	= strip_tags($instance['competition']);
		$highlight		= strip_tags($instance['highlight']);
		$default_css	= strip_tags($instance['default_css']);
		?>
		<p>
			<label>
				<?php _e('Title', STATSFC_FIXTURES_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('API key', STATSFC_FIXTURES_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo esc_attr($api_key); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Competition', STATSFC_FIXTURES_ID); ?>:
				<select name="<?php echo $this->get_field_name('competition'); ?>">
					<?php
					foreach (self::$_competitions as $id => $name) {
						echo '<option value="' . esc_attr($id) . '"' . ($id == $competition ? ' selected' : '') . '>' . esc_attr($name) . '</option>' . PHP_EOL;
					}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Team', STATSFC_FIXTURES_ID); ?>:
				<?php
				$data = file_get_contents('https://api.statsfc.com/premier-league/teams.json?key=' . (! empty($api_key) ? $api_key : 'free'));

				try {
					if (empty($data)) {
						throw new Exception('There was an error connecting to the StatsFC API');
					}

					$json = json_decode($data);
					if (isset($json->error)) {
						throw new Exception($json->error);
					}
					?>
					<select class="widefat" name="<?php echo $this->get_field_name('highlight'); ?>">
						<option></option>
						<?php
						foreach ($json as $team) {
							$value = str_replace(' ', '-', strtolower($team->name));
							echo '<option value="' . esc_attr($value) . '"' . ($value == $highlight ? ' selected' : '') . '>' . esc_attr($team->name) . '</option>' . PHP_EOL;
						}
						?>
					</select>
				<?php
				} catch (Exception $e) {
				?>
					<input class="widefat" name="<?php echo $this->get_field_name('highlight'); ?>" type="text" value="<?php echo esc_attr($highlight); ?>">
				<?php
				}
				?>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Use default CSS?', STATSFC_FIXTURES_ID); ?>
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
	public function update($new_instance, $old_instance) {
		$instance					= $old_instance;
		$instance['title']			= strip_tags($new_instance['title']);
		$instance['api_key']		= strip_tags($new_instance['api_key']);
		$instance['competition']	= strip_tags($new_instance['competition']);
		$instance['team']			= strip_tags($new_instance['team']);
		$instance['default_css']	= strip_tags($new_instance['default_css']);

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
	public function widget($args, $instance) {
		extract($args);

		$title			= apply_filters('widget_title', $instance['title']);
		$api_key		= $instance['api_key'];
		$competition	= $instance['competition'];
		$team			= $instance['team'];
		$default_css	= $instance['default_css'];

		echo $before_widget;
		echo $before_title . $title . $after_title;

		$data = file_get_contents('https://api.statsfc.com/' . esc_attr($competition) . '/fixtures.json?key=' . $api_key . (! empty($team) ? '&team=' . esc_attr($team) : ''));

		try {
			if (empty($data)) {
				throw new Exception('There was an error connecting to the StatsFC API');
			}

			$json = json_decode($data);
			if (isset($json->error)) {
				throw new Exception($json->error);
			}

			if (count($json) == 0) {
				throw new Exception('No fixtures found');
			}

			if ($default_css) {
				wp_register_style(STATSFC_FIXTURES_ID . '-css', plugins_url('all.css', __FILE__));
				wp_enqueue_style(STATSFC_FIXTURES_ID . '-css');
			}
			?>
			<div class="statsfc_fixtures">
				<table>
					<?php
					$total		= 0;
					$limit		= (! empty($team) ? 5 : 10);
					$previous	= null;

					foreach ($json as $fixture) {
						$total++;

						if (date('Y-m-d', strtotime($fixture->date)) !== $previous) {
							if ($total > 1) {
								echo '</tbody>' . PHP_EOL;
							}

							if ($total > $limit) {
								break;
							}

							$previous = date('Y-m-d', strtotime($fixture->date));
							?>
							<thead>
								<tr>
									<th colspan="3"><?php echo date('l, j F Y', strtotime($fixture->date)); ?></th>
								</tr>
							</thead>
							<tbody>
						<?php
						}
						?>
						<tr>
							<td class="statsfc_home">
								<span class="statsfc_status"><?php echo date('H:i', strtotime($fixture->date)); ?></span>
								<?php echo esc_attr($fixture->homeshort); ?>
							</td>
							<td class="statsfc_vs">-</td>
							<td class="statsfc_away"><?php echo esc_attr($fixture->awayshort); ?></td>
						</tr>
					<?php
					}
					?>
				</table>

				<p class="statsfc_footer"><small>Powered by <a href="https://statsfc.com" target="_blank" title="Football widgets and API">StatsFC.com</a></small></p>
			</div>
		<?php
		} catch (Exception $e) {
			echo '<p class="statsfc_error">' . esc_attr($e->getMessage()) .'</p>' . PHP_EOL;
		}

		echo $after_widget;
	}
}

// register StatsFC widget
add_action('widgets_init', create_function('', 'register_widget("' . STATSFC_FIXTURES_ID . '");'));
?>