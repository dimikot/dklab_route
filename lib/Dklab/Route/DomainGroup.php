<?php
/**
 * Dklab_Route_DomainGroup: domain name router which supports named domain groups.
 * 
 * @version 1.13
 */
require_once "Dklab/Route/Exception.php";

class Dklab_Route_DomainGroup
{
	const DOMAIN_PART_RE = '([^.]+)';
	
	private $_apps;
	private $_curDomain;
	private $_curMatch = null;
	
	/**
	 * Create new domain group parser/builder.
	 *
	 * @param array $apps   Array(appName => array(groupName => array(domain1.tld, domain2.tld, ...)))
	 * @param string $curDomain
	 */
	public function __construct(array $apps, $curDomain)
	{
		$this->_apps = array();
		foreach ($apps as $app => $groups) {
			// Translate masks into regexps.
			foreach ($groups as $group => $masks) {
				if (is_numeric($group) || !strlen($group)) {
					throw new Dklab_Route_Exception("DomainGroup name must be alphanumeric, \"$group\" given for app $app");
				}
				foreach ((array)$masks as $mask) {
					$this->_apps[$app][$group][$mask] = str_replace("\\*", self::DOMAIN_PART_RE, preg_quote($mask, '/'));
				}
			}
			// Check that group set is the same for each app.
			if (count($this->_apps) > 1) {
				reset($this->_apps);
				$first = key($this->_apps);
				if (array_diff_key($this->_apps[$app], $this->_apps[$first]) || array_diff_key($this->_apps[$first], $this->_apps[$app])) {
					throw new Dklab_Route_Exception(sprintf(
						"App $app contains different groups set than app $first: (%s) != (%s)",
						join(", ", array_keys($this->_apps[$app])), join(", ", array_keys($this->_apps[$first]))
					));
				}
			}
		}
		// Parse current domain to detect current group.
		$this->_curDomain = $curDomain;
		$this->_curMatch = $this->match($curDomain);
		if (!$this->_curMatch) {
			throw new Dklab_Route_Exception("Cannot find a match for the current domain: \"$curDomain\". It is needed for group detection.");
		}
	}
	
	/**
	 * Search for the full domain name in apps.
	 *
	 * @param string $domain
	 * @return array
	 */
	public function match($domain)
	{
		foreach ($this->_apps as $app => $masks) {
			foreach ($masks as $group => $regexps) {
				foreach ($regexps as $mask => $re) {
					$m = null;
					if (preg_match("/^((?:.*(?:\\.|$))?)($re)$/s", $domain, $m)) {
						return array(
							"app" => $app, 
							"subDomain" => rtrim($m[1], '.'),
							// Additional params.
							"group" => $group,
							"mask" => $mask,
							"baseDomain" => $m[2],
							"matches" => array_slice($m, 3),
						);
					}
				}
			}
		}
		return null;
	}
	
	/**
	 * Build full domain name based on subdomain and app names.
	 * 
	 * This method tries to use $curDomain passed to the constructor
	 * to deduce the best domain name. E.g. if ew are at a developer's
	 * domain, it tries to return full hostname for the same developer.
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
		// We are building URL on the same app as the current (fast and used in most cases)?
		if ($this->_curMatch['app'] === $app) {
			$baseDomain = $this->_curMatch['baseDomain'];
		} else {
			// Else build URL of another app.
			$regexps = $this->_apps[$app][$this->_curMatch['group']];
			$baseDomain = key($regexps);
			foreach ($this->_curMatch['matches'] as $m) {
				$baseDomain = str_replace('*', $m, $baseDomain);
			}
		}
		return ($subDomain? $subDomain . ($baseDomain? '.' : '') : '') . $baseDomain;
	}
}
