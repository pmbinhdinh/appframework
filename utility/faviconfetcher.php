<?php

/**
 * ownCloud - App Framework
 *
 * @author Bernhard Posselt
 * @copyright 2012 Bernhard Posselt nukeawhale@gmail.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */


namespace OCA\AppFramework\Utility;


class FaviconFetcher {

	private $apiFactory;


	/**
	 * Inject a factory to build a simplepie file object. This is needed because
	 * the file object contains logic in its constructor which makes it
	 * impossible to inject and test
	 */
	public function __construct(SimplePieAPIFactory $apiFactory) {
		$this->apiFactory = $apiFactory;
	}


	/**
	 * Fetches a favicon from a given URL
	 * @param string|null $url the url where to fetch it from
	 */
	public function fetch($url) {
		list($httpURL, $httpsURL) = $this->buildURL($url);

		if(!$httpURL) {
			return null;
		}

		// first check if the page defines an icon in its html
		$httpURLFromPage = $this->extractFromPage($httpURL);
		$httpsURLFromPage = $this->extractFromPage($httpsURL);

		if($this->isImage($httpURLFromPage)) {
			return $httpURLFromPage;
		} elseif ($this->isImage($httpsURLFromPage)) {
			return $httpsURLFromPage;
		}

		// try the /favicon.ico as a last resort but use the base url
		// since they are always at the base url, remove the path
		while(parse_url($httpURL, PHP_URL_PATH)){
			$httpURL = dirname($httpURL);
			$httpsURL = dirname($httpsURL);
		}

		$httpURL .= '/favicon.ico';
		$httpsURL .= '/favicon.ico';

		if($this->isImage($httpURL)) {
			return $httpURL;
		} elseif($this->isImage($httpsURL)) {
			return $httpsURL;
		} else {
			return null;
		}

	}


	/**
	 * Tries to get a favicon from a page
	 * @param string $url the url to the page
	 * @return string the full url to the page
	 */
	protected function extractFromPage($url) {
		if(!$url) {
			return null;
		}

		$file = $this->apiFactory->getFile($url);

		if($file->body !== '') {
			$document = new \DOMDocument();
			@$document->loadHTML($file->body);

			if($document) {
				$xpath = new \DOMXpath($document);
				$elements = $xpath->query("//link[contains(@rel, 'icon')]");

				if ($elements->length > 0) {
					$iconPath = $elements->item(0)->getAttribute('href');
					$absPath = \SimplePie_Misc::absolutize_url($iconPath, $url);
					return $absPath;
				}
			}
		}
	}


	/**
	 * Test if the file is an image
	 * @param string $url the url to the file
	 * @return bool true if image
	 */
	protected function isImage($url) {
		// check for empty urls
		if(!$url) {
			return false;
		}

		$file = $this->apiFactory->getFile($url);
		$sniffer = new \SimplePie_Content_Type_Sniffer($file);
		return $sniffer->image() !== false;
	}


	/**
	 * Get HTTP and HTTPS adresses from an incomplete URL
	 * @param string $url the url that should be built
	 * @return array an array containing the http and https address
	 */
	protected function buildURL($url) {
		$result = array();

		// trim the right / from the url
		$url = trim($url);
		$url = rtrim($url, '/');
		
		// dont build empty urls
		if(!$url) {
			$result[0] = null;
			$result[1] = null;
		} elseif (strpos($url, 'http://') === 0) {
			$result[0] = $url;
			$result[1] = substr($url, 0, 4) . 's' . substr($url, 4);
		} elseif (strpos($url, 'https://') === 0 ) {
			$result[0] = substr($url, 0, 4) . substr($url, 5);
			$result[1] = $url;
		} else {
			$result[0] = 'http://' . $url;
			$result[1] = 'https://' . $url;
		}

		return $result;
	}
 

}
