<?php
/*
Plugin Name: StatsFC Fixtures
Plugin URI: https://statsfc.com/docs/wordpress
Description: StatsFC Fixtures
Version: 1.2.6
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
		'league-cup'		=> 'League Cup',
		'community-shield'	=> 'Community Shield'
	);

	private static $_offsets = array('-12:00', '-11:00', '-10:00', '-09:30', '-09:00', '-08:00', '-07:00', '-06:00', '-05:00', '-04:30', '-04:00', '-03:30', '-03:00', '-02:00', '-01:00', '00:00', '+01:00', '+02:00', '+03:00', '+03:30', '+04:00', '+04:30', '+05:00', '+05:30', '+05:45', '+06:00', '+06:30', '+07:00', '+08:00', '+08:45', '+09:00', '+09:30', '+10:00', '+10:30', '+11:00', '+11:30', '+12:00', '+12:45', '+13:00', '+14:00');

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
			'limit'			=> __(5, STATSFC_FIXTURES_ID),
			'tz_offset'		=> __('00:00', STATSFC_FIXTURES_ID),
			'default_css'	=> __('', STATSFC_FIXTURES_ID)
		);

		$instance		= wp_parse_args((array) $instance, $defaults);
		$title			= strip_tags($instance['title']);
		$api_key		= strip_tags($instance['api_key']);
		$competition	= strip_tags($instance['competition']);
		$team			= strip_tags($instance['team']);
		$limit			= strip_tags($instance['limit']);
		$tz_offset		= strip_tags($instance['tz_offset']);
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
				try {
					$data = $this->_fetchData('https://api.statsfc.com/premier-league/teams.json?key=' . (! empty($api_key) ? $api_key : 'free'));

					if (empty($data)) {
						throw new Exception('There was an error connecting to the StatsFC API');
					}

					$json = json_decode($data);
					if (isset($json->error)) {
						throw new Exception($json->error);
					}
					?>
					<select class="widefat" name="<?php echo $this->get_field_name('team'); ?>">
						<option></option>
						<?php
						foreach ($json as $row) {
							echo '<option value="' . esc_attr($row->path) . '"' . ($row->path == $team ? ' selected' : '') . '>' . esc_attr($row->name) . '</option>' . PHP_EOL;
						}
						?>
					</select>
				<?php
				} catch (Exception $e) {
				?>
					<input class="widefat" name="<?php echo $this->get_field_name('team'); ?>" type="text" value="<?php echo esc_attr($team); ?>">
				<?php
				}
				?>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Number of fixtures', STATSFC_FIXTURES_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo esc_attr($limit); ?>" min="0" max="99"><br>
				<small>Applies to single team only. Choose '0' for all fixtures.</small>
			</label>
		</p>
		<?php
		if (! self::_timezone()) {
		?>
			<p>
				<label>
					<?php _e('UTC offset', STATSFC_FIXTURES_ID); ?>:
					<select class="widefat" name="<?php echo $this->get_field_name('tz_offset'); ?>">
						<?php
						foreach (self::$_offsets as $offset) {
							echo '<option value="' . esc_attr($offset) . '"' . ($offset == $tz_offset ? ' selected' : '') . '>' . esc_attr($offset) . '</option>' . PHP_EOL;
						}
						?>
					</select>
				</label>
			</p>
		<?php
		}
		?>
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
		$instance['limit']			= strip_tags($new_instance['limit']);
		$instance['tz_offset']		= strip_tags($new_instance['tz_offset']);
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
		$limit			= (int) $instance['limit'];
		$tz_offset		= $instance['tz_offset'];
		$default_css	= $instance['default_css'];

		if (empty($team)) {
			$limit = 20;
		}

		echo $before_widget;
		echo $before_title . $title . $after_title;

		try {
			$data = $this->_fetchData('https://api.statsfc.com/' . esc_attr($competition) . '/fixtures.json?key=' . $api_key . (! empty($limit) ? '&limit=' . $limit : '') . (! empty($team) ? '&team=' . esc_attr($team) : ''));

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
					$limit		= (! empty($team) ? $limit : 10);
					$previous	= null;

					foreach ($json as $fixture) {
						$total++;

						if (date('Y-m-d', strtotime($fixture->date)) !== $previous) {
							if ($total > 1) {
								echo '</tbody>' . PHP_EOL;
							}

							if ($limit > 0 && $total > $limit) {
								break;
							}

							$previous = date('Y-m-d', strtotime($fixture->date));
							?>
							<thead>
								<tr>
									<th colspan="3"><?php echo self::_convertDate($fixture->date, 'l, j F Y', $tz_offset); ?></th>
								</tr>
							</thead>
							<tbody>
						<?php
						}
						?>
						<tr>
							<td class="statsfc_home<?php echo ($team == $fixture->home ? ' statsfc_highlight' : ''); ?>">
								<span class="statsfc_status"><?php echo self::_convertDate($fixture->date, 'H:i', $tz_offset); ?></span>
								<?php echo esc_attr($fixture->homeshort); ?>
							</td>
							<td class="statsfc_vs">-</td>
							<td class="statsfc_away<?php echo ($team == $fixture->away ? ' statsfc_highlight' : ''); ?>"><?php echo esc_attr($fixture->awayshort); ?></td>
						</tr>
					<?php
					}
					?>
				</table>

				<p class="statsfc_footer"><small>Powered by StatsFC.com</small></p>
			</div>
		<?php
		} catch (Exception $e) {
			echo '<p style="text-align: center;"><img src="//statsfc.com/i/icon.png" width="64" height="64" alt="Football widgets and API"><br><a href="https://statsfc.com" title="Football widgets and API" target="_blank">StatsFC.com</a> – ' . esc_attr($e->getMessage()) .'</p>' . PHP_EOL;
		}

		echo $after_widget;
	}

	private function _fetchData($url) {
		if (function_exists('curl_exec')) {
			return $this->_curlRequest($url);
		} else {
			return $this->_fopenRequest($url);
		}
	}

	private function _curlRequest($url) {
		$ch = curl_init();

		curl_setopt_array($ch, array(
			CURLOPT_AUTOREFERER		=> true,
			CURLOPT_HEADER			=> false,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 5,
			CURLOPT_URL				=> $url
		));

		$data = curl_exec($ch);
		if (empty($data)) {
			$data = $this->_fopenRequest($url);
		}

		curl_close($ch);

		return $data;
	}

	private function _fopenRequest($url) {
		return file_get_contents($url);
	}

	private static function _convertDate($timestamp, $format, $offset) {
		if (! ($timezone = self::_timezone())) {
			return date($format, strtotime($timestamp . ' ' . ($offset[0] == '-' ? '+' : '-') . substr($offset, 1)));
		}

		$datetime = new DateTime($timestamp, new DateTimeZone('GMT'));
		$datetime->setTimezone(new DateTimeZone($timezone));

		return $datetime->format($format);
	}

	private static function _timezone() {
		if (! class_exists('DateTime')) {
			return false;
		}

		$timezone = get_option('timezone_string');

		try {
			$dtz = new DateTimeZone($timezone);
		} catch (Exception $e) {
			return false;
		}

		return $timezone;
	}
}

// register StatsFC widget
add_action('widgets_init', create_function('', 'register_widget("' . STATSFC_FIXTURES_ID . '");'));