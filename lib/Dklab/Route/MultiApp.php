<?php
/**
 * Dklab_Route_MultiApp: whole URL router.
 *
 * match(): based on a passed URL, choose the proper app and router. Then
 * match URL's URI using this router.
 * 
 * assemble(): based on a passed app name, choose router and build
 * URI and domain name, then glue them together. Protocol-agnostic:
 * use "//" instead of "http://" or "https://" while building the
 * full URL ("//" is okay in all browsers).  
 * 
 * @version 1.15
 */
require_once "Dklab/Route/Exception.php";

class Dklab_Route_MultiApp
{
    /**
     * List of named URI routers (one for each app).
     *
     * @var array
     */
    private $_uriRouters;

	/**
	 * Domain router.
	 *
	 * @var array
	 */
	private $_domainRouter;

    /**
     * Constructor.
     *
	 * @param mixed $domainRouter  Router to be used to choose the app.
     * @param array $uriRouters    One router for each app.
     */
    public function __construct($domainRouter, array $uriRouters)
    {
		$this->_domainRouter = $domainRouter;
		$this->_uriRouters = $uriRouters;
    }

    /**
     * Parse the full URL and return its parts according to found URL map.
	 * The "app" key is set in the resulting array according to matched domain.
	 * If no match is found, throws exception.
     *
     * @param string $url  Full URL.
     * @return array  Parameters found in the map.
     */
    public function match($url)
    {
		$parsed = @parse_url($url);
		if (empty($parsed) || empty($parsed['host']) || empty($parsed['path'])) {
			throw new Dklab_Route_Exception("Invalid URL passed to " . __CLASS__ . "::match(): \"$url\"");
		}
		$pDomain = $this->_domainRouter->match($parsed['host']);
		if (!$pDomain) {
			throw new Dklab_Route_Exception("Cannot find an App for host: \"{$parsed['host']}\"");
		}
		$app = $pDomain['app'];
		if (!isset($this->_uriRouters[$app])) {
			throw new Dklab_Route_Exception("Cannot find App \"$app\" in the list of available routers: (" . join(", ", array_keys($this->_uriRouters)) . ")");
		}
		$aUri = $pDomain['subDomain'] . $parsed['path'];
		$pUri = $this->_uriRouters[$app]->match($aUri);
		if (!$pUri) {
			// If app is found, but URI is not, return array with only app assigned.
			// This is typically needed to correctly process 404 errors.
			return array(
				'app' => $pDomain['app'],
			);
		}
		$pUri['app'] = $pDomain['app'];
		return $pUri;
    }

    /**
     * Builds an URI from its parsed representation (key "app" is needed in $parsed).
     * This method is reverses match().
     *
     * @param array $parsed
     * @return string
     */
    public function assemble($parsed)
    {
		if (empty($parsed['app'])) {
			throw new Dklab_Route_Exception("Key \"app\" is required at " . __CLASS__ . "::assemble()");
		}
		$app = $parsed['app'];
		if (!isset($this->_uriRouters[$app])) {
			throw new Dklab_Route_Exception("Cannot find App \"$app\" in the list of available routers: (" . join(", ", array_keys($this->_uriRouters)) . ")");
		}
		$aUri = $this->_uriRouters[$app]->assemble($parsed);
		// When no '/' is in $aUri, it's OK: we should ignore right part.
		@list ($parsed['subDomain'], $uri) = explode('/', $aUri, 2);
		$aDomain = $this->_domainRouter->assemble($parsed);
		return "//" . $aDomain . '/' . $uri;
    }
    
    public function getDomainRouter()
    {
    	return $this->_domainRouter;
    }

    public function getUriRouters()
    {
    	return $this->_uriRouters;
    }
}
