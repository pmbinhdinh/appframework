<?php

/**
 * ownCloud - App Framework
 *
 * @author Bernhard Posselt, Thomas Tanghus, Bart Visscher, 
 * Evert Pot (http://www.rooftopsolutions.nl/)
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


namespace OCA\AppFramework\Http;


class Http {

	const STATUS_CONTINUE = 100;
	const STATUS_SWITCHING_PROTOCOLS = 101;
	const STATUS_PROCESSING = 102;
	const STATUS_OK = 200;
	const STATUS_CREATED = 201;
	const STATUS_ACCEPTED = 202;
	const STATUS_NON_AUTHORATIVE_INFORMATION = 203;
	const STATUS_NO_CONTENT = 204;
	const STATUS_RESET_CONTENT = 205;
	const STATUS_PARTIAL_CONTENT = 206;
	const STATUS_MULTI_STATUS = 207;
	const STATUS_ALREADY_REPORTED = 208;
	const STATUS_IM_USED = 226;
	const STATUS_MULTIPLE_CHOICES = 300;
	const STATUS_MOVED_PERMANENTLY = 301;
	const STATUS_FOUND = 302;
	const STATUS_SEE_OTHER = 303;
	const STATUS_NOT_MODIFIED = 304;
	const STATUS_USE_PROXY = 305;
	const STATUS_RESERVED = 306;
	const STATUS_TEMPORARY_REDIRECT = 307;
	const STATUS_BAD_REQUEST = 400;
	const STATUS_UNAUTHORIZED = 401;
	const STATUS_PAYMENT_REQUIRED = 402;
	const STATUS_FORBIDDEN = 403;
	const STATUS_NOT_FOUND = 404;
	const STATUS_METHOD_NOT_ALLOWED = 405;
	const STATUS_NOT_ACCEPTABLE = 406;
	const STATUS_PROXY_AUTHENTICATION_REQUIRED = 407;
	const STATUS_REQUEST_TIMEOUT = 408;
	const STATUS_CONFLICT = 409;
	const STATUS_GONE = 410;
	const STATUS_LENGTH_REQUIRED = 411;
	const STATUS_PRECONDITION_FAILED = 412;
	const STATUS_REQUEST_ENTITY_TOO_LARGE = 413;
	const STATUS_REQUEST_URI_TOO_LONG = 414;
	const STATUS_UNSUPPORTED_MEDIA_TYPE = 415;
	const STATUS_REQUEST_RANGE_NOT_SATISFIABLE = 416;
	const STATUS_EXPECTATION_FAILED = 417;
	const STATUS_IM_A_TEAPOT = 418;
	const STATUS_UNPROCESSABLE_ENTITY = 422;
	const STATUS_LOCKED = 423;
	const STATUS_FAILED_DEPENDENCY = 424;
	const STATUS_UPGRADE_REQUIRED = 426;
	const STATUS_PRECONDITION_REQUIRED = 428;
	const STATUS_TOO_MANY_REQUESTS = 429;
	const STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	const STATUS_INTERNAL_SERVER_ERROR = 500;
	const STATUS_NOT_IMPLEMENTED = 501;
	const STATUS_BAD_GATEWAY = 502;
	const STATUS_SERVICE_UNAVAILABLE = 503;
	const STATUS_GATEWAY_TIMEOUT = 504;
	const STATUS_HTTP_VERSION_NOT_SUPPORTED = 505;
	const STATUS_VARIANT_ALSO_NEGOTIATES = 506;
	const STATUS_INSUFFICIENT_STORAGE = 507;
	const STATUS_LOOP_DETECTED = 508;
	const STATUS_BANDWIDTH_LIMIT_EXCEEDED = 509;
	const STATUS_NOT_EXTENDED = 510;
	const STATUS_NETWORK_AUTHENTICATION_REQUIRED = 511;

	private $server;
	private $protocolVersion;
	protected $headers;

	/**
	 * @param $_SERVER $server
	 * @param string $protocolVersion the http version to use defaults to HTTP/1.1
	 */
	public function __construct($server, $protocolVersion='HTTP/1.1') {
		$this->server = $server;
		$this->protocolVersion = $protocolVersion;

		// taken from Sabre_HTTP_Response
		$this->headers = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authorative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status', // RFC 4918
			208 => 'Already Reported', // RFC 5842
			226 => 'IM Used', // RFC 3229
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',
			400 => 'Bad request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot', // RFC 2324
			422 => 'Unprocessable Entity', // RFC 4918
			423 => 'Locked', // RFC 4918
			424 => 'Failed Dependency', // RFC 4918
			426 => 'Upgrade required',
			428 => 'Precondition required', // draft-nottingham-http-new-status
			429 => 'Too Many Requests', // draft-nottingham-http-new-status
			431 => 'Request Header Fields Too Large', // draft-nottingham-http-new-status
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version not supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage', // RFC 4918
			508 => 'Loop Detected', // RFC 5842
			509 => 'Bandwidth Limit Exceeded', // non-standard
			510 => 'Not extended',
			511 => 'Network Authentication Required', // draft-nottingham-http-new-status
		);
	}


	public function getHeader($status, $ETag, $lastModified) {
		
		// if etag or lastmodified have not changed, return a not modified
		if ((isset($this->server['HTTP_IF_NONE_MATCH']) &&
			trim($this->server['HTTP_IF_NONE_MATCH']) === $ETag) 
			||
			(isset($this->server['HTTP_IF_MODIFIED_SINCE']) &&
			trim($this->server['HTTP_IF_MODIFIED_SINCE']) === $lastModified)) {

			$status = $this->headers[self::STATUS_NOT_MODIFIED];
		}
		
		return $this->protocolVersion . ' ' . $status . ' ' . $this->headers[$status];
	}


}


