=== StatsFC Fixtures ===
Contributors: willjw
Donate link:
Tags: widget, football, soccer, fixtures, premier league, fa cup, league cup
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This widget will place list of Premier League, FA Cup or League Cup fixtures in your website.

== Description ==

Add a list of Premier League, FA Cup or League Cup fixtures to your WordPress website. To request an API key sign up for free at [statsfc.com](https://statsfc.com).

For a demo, check out [wp.statsfc.com](http://wp.statsfc.com).

== Installation ==

1. Upload the `statsfc-fixtures` folder and all files to the `/wp-content/plugins/` directory
2. Activate the widget through the 'Plugins' menu in WordPress
3. Drag the widget to the relevant sidebar on the 'Widgets' page in WordPress
4. Set the API key and any other options. If you don't have any API key, sign up for free at statsfc.com

If you want to place the widget into a page rather than a sidebar:

1. Install and activate 'Widgets on Pages' from the 'Plugins' menu in WordPress
2. Add a sidebar named "StatsFC Fixtures" from the 'Settings > Widgets on Pages' menu in WordPress
3. Place the widget anywhere in a page, using the following code:

		[widgets_on_pages id="StatsFC Fixtures"]

== Frequently asked questions ==



== Screenshots ==



== Changelog ==

**1.0.1**:

- Fixed syntax error.

**1.0.2**:

- Fixed 'Team' setting bug.

**1.0.3**:

- If showing fixtures for a single team, highlight that team.

**1.1**:

- Added a setting for number of fixtures. Applies to single team only. Choose '0' to display all fixtures.

**1.1.1**:

- Fixed a bug when selecting a specific team.

**1.2**:

- Automatically adjust kick-off times according to your site's timezone setting.

**1.2.1**:

- Fixed timezone adjustment bug in old versions of PHP.
- If using an old version, you'll need to choose your own UTC offset in the options.
- Added Community Shield fixtures.

**1.2.2**:

- Use cURL to fetch API data if possible.

**1.2.3**:

- Fixed possible cURL bug.

**1.2.4**:

- Added fopen fallback if cURL request fails.

== Upgrade notice ==

