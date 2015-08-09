=== StatsFC Fixtures ===
Contributors: willjw
Donate link:
Tags: widget, football, soccer, fixtures, premier league, fa cup, league cup
Requires at least: 3.3
Tested up to: 4.2.2
Stable tag: 1.8.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This widget will display a list of football fixtures on your website, for a chosen competition or team.

== Description ==

Add a list of football fixtures to your WordPress website. To request a key sign up for your free trial at [statsfc.com](https://statsfc.com/sign-up).

For a demo, check out [wp.statsfc.com/fixtures/](http://wp.statsfc.com/fixtures/).

== Installation ==

1. Upload the `statsfc-fixtures` folder and all files to the `/wp-content/plugins/` directory
2. Activate the widget through the 'Plugins' menu in WordPress
3. Drag the widget to the relevant sidebar on the 'Widgets' page in WordPress
4. Set the StatsFC key and any other options. If you don't have a key, sign up for free at [statsfc.com](https://statsfc.com)

You can also use the `[statsfc-fixtures]` shortcode, with the following options:

- `key` (required): Your StatsFC key
- `competition` (required*): Competition key, e.g., `EPL`
- `team` (required*): Team name, e.g., `Liverpool`
- `from` (optional): Date to show fixtures from, e.g., `2014-01-01`
- `to` (optional): Date to show fixtures to, e.g., `2014-01-07`
- `limit` (optional): Maximum number of fixtures to show, e.g., `4`, `10`
- `highlight` (optional): The name of the team you want to highlight, e.g., `Liverpool`
- `show_badges` (optional): Display team badges, `true` or `false`
- `show_dates` (optional): Display match dates, `true` or `false`
- `timezone` (optional): The timezone to convert match times to, e.g., `Europe/London` ([complete list](https://php.net/manual/en/timezones.php))
- `default_css` (optional): Use the default widget styles, `true` or `false`

*Only one of `competition` or `team` is required.

== Frequently asked questions ==



== Screenshots ==



== Changelog ==

**1.0.1**: Fixed syntax error.

**1.0.2**: Fixed 'Team' setting bug.

**1.0.3**: If showing fixtures for a single team, highlight that team.

**1.1**: Added a setting for number of fixtures. Applies to single team only. Choose '0' to display all fixtures.

**1.1.1**: Fixed a bug when selecting a specific team.

**1.2**: Automatically adjust kick-off times according to your site's timezone setting.

**1.2.1**: Fixed timezone adjustment bug in old versions of PHP. If using an old version, you'll need to choose your own UTC offset in the options. Added Community Shield fixtures.

**1.2.2**: Use cURL to fetch API data if possible.

**1.2.3**: Fixed possible cURL bug.

**1.2.4**: Added fopen fallback if cURL request fails.

**1.2.5**: Fixed possible Timezone bug.

**1.2.6**: Tweaked error message.

**1.3**: Allow an actual timezone to be selected, and use the new API.

**1.4**: Updated to use the new API.

**1.4.2**: Tweaked CSS.

**1.5**: Added `[statsfc-fixtures]` shortcode.

**1.5.2**: Updated team badges.

**1.5.3**: Default `default_css` parameter to `true`

**1.5.4**: Added badge class for each team

**1.6**: Enabled ad-support

**1.6.1**: Allow more discrete ads for ad-supported accounts

**1.7**: Re-use the same JS as the standard widget

**1.8**: Added `highlight`, `show_badges` and `show_dates` options

**1.8.1**: Fixed bug with multiple widgets on one page

**1.8.2**: Fixed "Invalid domain" bug caused by referal domain

== Upgrade notice ==

