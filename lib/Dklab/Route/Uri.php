<?php
/**
 * Dklab_Route_Uri: router for internal URIs.
 * 
 * Each parsable URI must be in format:
 *   - "<domain_prefix>/<path_info>"
 *   - "/<path_info> 
 * where:
 *   - <domain_prefix> is the prefix of application's domain (if present);
 *   - <path_info> is the relative URI.
 * 
 * URL map file parsing is a quite heavy operation, but this class does not 
 * implement any caching behaviour. To implement caching, just cache the
 * whole Dklab_Route_Uri object from outside the class.
 * 
 * @version 1.13
 */
require_once "Dklab/Route/Exception.php";

class Dklab_Route_Uri
{
    /**
     * Router map by page name.
     *
     * @var array
     */
    private $_map;

    /**
     * Constructor.
     *
     * @param mixed $iniFile    INI file path or 2d array of routes.
     */
    public function __construct($iniFile)
    {
        $this->_map = $this->_readMap($iniFile);
    }

    /**
     * Gets map of route
     *
     * @return array
     */
    public function getRoute()
    {
        return $this->_map['byName'];
    }

    /**
     * Parse the URI and return its parts according to found URL map.
     *
     * @param string $uri  URI in form of: "domain/part1/part2" or "/part1/part2"
     * @return array  Parameters or null if nothing is found in the map.
     */
    public function match($uri)
    {
        $uri = rtrim($uri, '/');

        $chunks = explode('/', $uri);
        $chunks_count = count($chunks);
        foreach ($this->_map['byName'] as $name => $parts) {
            $parsed = $parts;
            $matched = true;
            $i = 0;
            foreach ($parts['url'] as $key => $pattern) {
                if (!isset($chunks[$i])) {
                    $matched = false;
                    break;
                } elseif (is_numeric($key)) {
                    // Plain value to compare.
                    if ($pattern !== $chunks[$i]) {
                        $matched = false;
                        break;
                    }
                } else {
                    // Regular expression.
                    $m = null;
                    if (!preg_match("/^{$pattern}$/s", $chunks[$i], $m)) {
                        $matched = false;
                        break;
                    }
                    $parsed[$key] = $m[0];
                }
                $i++;
            }
            if ($matched && $i == $chunks_count) {
                unset($parsed['url']);
                $parsed['name'] = $name;
                return $parsed;
            }
        }
        return null;
    }


    /**
     * Builds an URI from its parsed representation.
     * This method is reverses match().
     *
     * @param array $parsed
     * @return string
     */
    public function assemble($parsed)
    {
        if (!is_array($parsed) || !strlen(@$parsed['name'])) {
            throw new Dklab_Route_Exception("No 'name' parameter found");
        }
        $name = $parsed['name'];
        if (!isset($this->_map['byName'][$name])) {
            throw new Dklab_Route_Exception("No URL map item '{$name}' found");
        }
        $parts = $this->_map['byName'][$name]['url'];
        foreach ($parts as $key => $value) {
            if (!is_numeric($key)) {
                if (!isset($parsed[$key])) {
                    throw new Dklab_Route_Exception("No parameter '$key' found in parsed URL (name  '" . $parsed['name'] . "')");
                }

                if (!is_scalar($parsed[$key])) {
                    throw new Dklab_Route_Exception("Parameter '$key' must be scalar, given: " . var_export($parsed[$key], 1) . "' (name  '" . $parsed['name'] . "')");
                }
                $parts[$key] = $parsed[$key];
            }
        }
        return join('/', $parts) . ($value == '' ? null : '/');
    }


    /**
     * Returns routing option from the INI config.
     *
     * @param string $name
     * @return string
     */
    public function getOption($name)
    {
        return @$this->_map['options'][$name];
    }


    /**
     * Loads an INI file.
     *
     * @param unknown_type $iniFile
     * @return unknown
     */
    private function _readMap($iniFile)
    {
    	if (is_string($iniFile)) {
	        $ini = parse_ini_file($iniFile, true);
	    } else {
	    	$ini = $iniFile;
	    }
        return array(
            'byName'  => $this->_buildMapByName($ini),
            'options' => $this->_buildOptions($ini)
        );
    }


    /**
     * Extracts options from INI data.
     *
     * @param array $ini
     * @return array
     */
    private function _buildOptions($ini)
    {
        $options = array();
        foreach ($ini as $k => $v) {
            if (is_scalar($v)) {
                $options[$k] = $v;
            }
        }
        return $options;
    }


    /**
     * Builds the full URL map from INI configuration.
     *
     * @param array $ini
     * @return array
     */
    public function _buildMapByName($ini)
    {
        $mapByName = array();
        foreach ($ini as $name => $route) {
            if (!is_array($route)) {
                continue;
            }
            if (!strlen(@$route['url'])) {
                throw new Dklab_Route_Exception("No 'url' parameter in [$name] route");
            }
            $urlParts = $this->_buildPartsByUriMask($route['url']);
            $route['url'] = $urlParts;
            $mapByName[$name] = $route;
        }
        return $mapByName;
    }


    /**
     * Parses URL mask into array.
     * Array keys may be:
     * - plain integer (anonymous URL part)
     * - identifier (named part)
     *
     * @param string $url
     * @return array
     */
    public function _buildPartsByUriMask($url)
    {
        $url = preg_replace('{/+$}s', '', $url);
        $chunks = explode("/", $url);
        $parts = array();
        foreach ($chunks as $chunk) {
        	$m = null;
            if (preg_match('/^([a-z]\w*)\s*=\s*(.*)$/si', $chunk, $m)) {
                $parts[$m[1]] = $m[2];
            } else {
                $parts[] = $chunk;
            }
        }
        return $parts;
    }
}
