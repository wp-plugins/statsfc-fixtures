<?php
/*
Plugin Name: StatsFC Fixtures
Plugin URI: https://statsfc.com/docs/wordpress
Description: StatsFC Fixtures
Version: 1.3
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
	private static $competitions = array(
		'EPL'	=> 'England Premier League',
		'ICL'	=> 'UEFA Champions League',
		'IEL'	=> 'UEFA Europa League',
		'EFC'	=> 'England FA Cup',
		'ELC'	=> 'England League Cup',
		'ECS'	=> 'England Community Shield'
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
			'competition'	=> __(current(array_keys(self::$competitions)), STATSFC_FIXTURES_ID),
			'group'			=> __('', STATSFC_FIXTURES_ID),
			'team'			=> __('', STATSFC_FIXTURES_ID),
			'from'			=> __('', STATSFC_FIXTURES_ID),
			'to'			=> __('', STATSFC_FIXTURES_ID),
			'limit'			=> __(0, STATSFC_FIXTURES_ID),
			'timezone'		=> __('Europe/London', STATSFC_FIXTURES_ID),
			'default_css'	=> __('', STATSFC_FIXTURES_ID)
		);

		$instance		= wp_parse_args((array) $instance, $defaults);
		$title			= strip_tags($instance['title']);
		$api_key		= strip_tags($instance['api_key']);
		$competition	= strip_tags($instance['competition']);
		$group			= strip_tags($instance['group']);
		$team			= strip_tags($instance['team']);
		$from			= strip_tags($instance['from']);
		$to				= strip_tags($instance['to']);
		$limit			= strip_tags($instance['limit']);
		$timezone		= strip_tags($instance['timezone']);
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
					<option value="">All</option>
					<?php
					foreach (self::$competitions as $key => $name) {
						echo '<option value="' . esc_attr($key) . '"' . ($key == $competition ? ' selected' : '') . '>' . esc_attr($name) . '</option>' . PHP_EOL;
					}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Group', STATSFC_FIXTURES_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('group'); ?>" type="text" value="<?php echo esc_attr($group); ?>" placeholder="e.g., A, B">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Team', STATSFC_FIXTURES_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('team'); ?>" type="text" value="<?php echo esc_attr($team); ?>" placeholder="e.g., Liverpool, Manchester City">
			</label>
		</p>
		<p>
			<label>
				<?php _e('From', STATSFC_FIXTURES_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('from'); ?>" type="text" value="<?php echo esc_attr($from); ?>" placeholder="e.g., <?php echo date('Y-m-d'); ?>, today">
			</label>
		</p>
		<p>
			<label>
				<?php _e('To', STATSFC_FIXTURES_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('to'); ?>" type="text" value="<?php echo esc_attr($to); ?>" placeholder="e.g., <?php echo date('Y-m-d', '+2 weeks'); ?>, +2 weeks, next Monday">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Limit', STATSFC_FIXTURES_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo esc_attr($limit); ?>" min="0" max="99"><br>
				<small>Applies to single team only. Choose '0' for all fixtures.</small>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Timezone', STATSFC_FIXTURES_ID); ?>:
				<select class="widefat" name="<?php echo $this->get_field_name('timezone'); ?>">
					<?php
					$zones = timezone_identifiers_list();

					foreach ($zones as $zone) {
						echo '<option value="' . esc_attr($zone) . '"' . ($zone == $timezone ? ' selected' : '') . '>' . esc_attr($zone) . '</option>' . PHP_EOL;
					}
					?>
				</select>
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
		$instance['group']			= strip_tags($new_instance['group']);
		$instance['team']			= strip_tags($new_instance['team']);
		$instance['from']			= strip_tags($new_instance['from']);
		$instance['to']				= strip_tags($new_instance['to']);
		$instance['limit']			= strip_tags($new_instance['limit']);
		$instance['timezone']		= strip_tags($new_instance['timezone']);
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
		$group			= $instance['group'];
		$team			= $instance['team'];
		$from			= $instance['from'];
		$to				= $instance['to'];
		$limit			= (int) $instance['limit'];
		$timezone		= $instance['timezone'];
		$default_css	= $instance['default_css'];

		echo $before_widget;
		echo $before_title . $title . $after_title;

		try {
			if (strlen($competition) == 0 && strlen($team) == 0) {
				throw new Exception('Please choose a competition and/or team from the widget options');
			}

			$data = $this->_fetchData('https://api.statsfc.com/widget/fixtures.json.php?key=' . urlencode($api_key) . '&competition=' . urlencode($competition) . '&group=' . urlencode($group) . '&team=' . urlencode($team) . '&from=' . urlencode($from) . '&to=' . urlencode($to) . '&limit=' . urlencode($limit) . '&timezone=' . urlencode($timezone));

			if (empty($data)) {
				throw new Exception('There was an error connecting to the StatsFC API');
			}

			$json = json_decode($data);

			if (isset($json->error)) {
				throw new Exception($json->error);
			}

			$fixtures	= $json->fixtures;
			$customer	= $json->customer;

			if ($default_css) {
				wp_register_style(STATSFC_FIXTURES_ID . '-css', plugins_url('all.css', __FILE__));
				wp_enqueue_style(STATSFC_FIXTURES_ID . '-css');
			}
			?>
			<div class="statsfc_fixtures">
				<div>
					<?php
					foreach ($fixtures as $date => $matches) {
					?>
						<table>
							<thead>
								<tr>
									<th colspan="3"><?php echo esc_attr($date); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($matches as $match) {
								?>
									<tr>
										<td class="statsfc_team statsfc_home statsfc_badge" style="background-image: url(//api.statsfc.com/kit/<?php echo esc_attr($match->homepath); ?>.png);">
											<span class="statsfc_status"><?php echo esc_attr($match->status); ?></span>
											<?php echo esc_attr($match->home); ?>
										</td>
										<td class="statsfc_vs">-</td>
										<td class="statsfc_team statsfc_away statsfc_badge" style="background-image: url(//api.statsfc.com/kit/<?php echo esc_attr($match->awaypath); ?>.png);">
											<?php echo esc_attr($match->away); ?>
											<span class="statsfc_competition">
												<abbr title="<?php echo esc_attr($match->competition); ?>"><?php echo esc_attr($match->competitionkey); ?></abbr>
											</span>
										</td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					<?php
					}
					?>
				</div>

				<?php
				if ($customer->advert) {
				?>
					<p class="statsfc_footer"><small>Powered by StatsFC.com</small></p>
				<?php
				}
				?>
			</div>
		<?php
		} catch (Exception $e) {
		?>
			<p>StatsFC.com – <?php echo esc_attr($e->getMessage()); ?></p>
		<?php
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
}

// register StatsFC widget
add_action('widgets_init', create_function('', 'register_widget("' . STATSFC_FIXTURES_ID . '");'));
