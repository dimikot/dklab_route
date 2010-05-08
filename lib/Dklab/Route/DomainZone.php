<?php
/**
 * Dklab_Route_DomainZone: domain name router with fixed zone binding.
 * 
 * You specify a list of zones (domain name suffixes) which must be ignored
 * while match/assemble procedures. E.g. you may have "example.com" domein
 * and two zones:
 *   - "*.dev.local"
 *   - "test.local"
 * 
 * So, Dklab_Route_DomainZone will correctly process domain names like:
 *   - "example.com"
 *   - "example.com.LGN.dev.local" 
 *   - "example.com.test.local" 
 * 
 * It will strip "*.dev.local" and "test.local" while match() operation
 * and add them back while assemble() operation. Note that wildcards
 * in zone names are okay; they are expanded automatically to proper 
 * values during the assemble() procedure. 
 * 
 * @version 1.16
 */
require_once "Dklab/Route/Exception.php";

class Dklab_Route_DomainZone
{
	/**
	 * List of apps and their domain names.
	 */
	private $_apps;

	/**
	 * This is the suffix which is stripped from all domain names passed
	 * to match() and appended to all names after assemble().
	 *
	 * @var string
	 */
	private $_curZone;

	/**
	 * Current domain passed via the constructor.
	 * 
	 * @var string
	 */
	private $_curDomain;
	
	/**
	 * Match of the current domain. Filled on demand.
	 *
	 * @var string
	 */
	private $_curMatch = null;
	
	/**
	 * If set, only this App name is considered while match().
	 * 
	 * @var string
	 */
	private $_curApp = null;
	
	/**
	 * Create a new domain group parser/builder.
	 *
	 * @param array $apps         Array(appName => array(domain1.tld, domain2.tld, ...))
	 * @param string $curDomain   Current hostname (must be matched by $zoneMask).
	 * @param array $zoneMask     Remove one of these zone masks from all domains before match() and insert after assemble().
     * @param string $curApp      If specified, only this app is considered while match().
	 */
	public function __construct(array $apps, $curDomain, array $zoneMasks, $curApp = null)
	{
		$this->_curApp = $curApp;
		// This is the suffix which is virtually stripped from all domain names 
		// passed to match() and appended to all names after assemble().
		$curZone = "";
		// Match the tail of $curDomain by each of $zoneMasks.
		foreach ($zoneMasks as $zoneMask) {
			$m = null;
			if (preg_match('/(\.' . $this->_mask2re($zoneMask) . ')$/s', $curDomain, $m)) {
				$curZone = $m[1];
				break;
			}
		}
		$this->_curZone = $curZone;
		// Append current zone to each app domain. So, all domains passed
		// to match() must end with this suffix, and all domains returned
		// by assemble() will contain this suffix too.
		$this->_apps = array();
		foreach ($apps as $app => $domains) {
			foreach ($domains as $domain) {
				// We use ltrim(..., "."), because $domain may be empty (case of default domain).
				// ATTENTION: in this match "*" matches MULTIPLE domain parts.
				$this->_apps[$app][ltrim($domain . $curZone, ".")] = $this->_mask2re($domain . $curZone, true);
			}
		}
		// Save current domain for later processing.
        $this->_curDomain = $curDomain;
	}
	
	/**
	 * Search for the longest full domain name in apps.
	 *
	 * @param string $domain
	 * @return array
	 */
	public function match($domain)
	{
		$best = null;
 		foreach ($this->_apps as $app => $masks) {
 			if ($this->_curApp && $app !== $this->_curApp) continue;
			foreach ($masks as $mask => $re) {
				$m = null;
				// $re is always started with '\.'.
				if (preg_match("/^(.*?)($re)$/s", $domain, $m)) {
					$ret = array(
						"app" => $app, 
						"subDomain" => rtrim($m[1], '.'),
						// Additional params.
						"baseDomain" => ltrim($m[2], '.'),
					);

					// Find a match with longest base domain.
					if (!$best || strlen($best['baseDomain']) < strlen($ret['baseDomain'])) {
						$best = $ret;
					}
				}
			}
		}
		return $best;
	}
	
	/**
	 * Build full domain name based on subdomain and app names.
	 * 
	 * @param array $parsed
	 * @return string
	 */
	public function assemble(array $parsed)
	{
		$app = $parsed['app'];
		$subDomain = $parsed['subDomain'];
		// Get this app masks.
		if (!isset($this->_apps[$app])) {
			throw new Dklab_Route_Exception("No such app: $app, available are: (" . join(", ", array_keys($this->_apps)) . ")");
		}		
        // Parse current domain to detect current group (to stay on the same
        // domain if we assemble() pathes of the same app).
        if (!$this->_curMatch) {
            $this->_curMatch = $this->match($this->_curDomain);
            if (!$this->_curMatch) {
                throw new Dklab_Route_Exception("Cannot find a match for the current domain: \"{$this->_curDomain}\", zone \"{$this->_curZone}\"");
            }
        }
		// We are building URL on the same app as the current (fast and used in most cases)?
		if ($this->_curMatch['app'] === $app) {
			$baseDomain = $this->_curMatch['baseDomain'];
		} else {
			// Else get the first domain of a new app.
			reset($this->_apps[$app]);
			$baseDomain = key($this->_apps[$app]);
		}
		return ($subDomain? $subDomain . ($baseDomain? '.' : '') : '') . $baseDomain;
	}
	
	/**
	 * Returns current domain matched zone.
	 *
	 * @return string
	 */
	public function getCurZone()
	{
		return $this->_curZone;
	}

	/**
	 * Convert mask with "*" to regexp.
	 *
	 * @param string $mask
	 * @param bool $wildcardMatchesDots  If true, "*" in domain wildcard matches 
	 *                                   multiple domain parts (e.g. *.ru matches
	 *                                   the whole "aaa.bbb.ccc.ru"). If false, only one 
	 *                                   domain part is matches.
	 * @return string
	 */
	private function _mask2re($mask, $wildcardMatchesDots = false)
	{
		$re = $wildcardMatchesDots? '(.+)' : '([^.]+)';
		return str_replace("\\*", $re, preg_quote($mask, '/'));
	}
}
