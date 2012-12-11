<?php
namespace Vivo\Indexer\Adapter\Solr;
/**
 * Copyright (c) 2007-2009, Conduit Internet Technologies, Inc.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *  - Neither the name of Conduit Internet Technologies, Inc. nor the names of
 *    its contributors may be used to endorse or promote products derived from
 *    this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright Copyright 2007-2009 Conduit Internet Technologies, Inc. (http://conduit-it.com)
 * @license New BSD (http://solr-php-client.googlecode.com/svn/trunk/COPYING)
 *
 * @author Donovan Jimenez <djimenez@conduit-it.com>
 */

/**
 * Starting point for the Solr API. Represents a Solr server resource and has
 * methods for pinging, adding, deleting, committing, optimizing and searching.
 *
 * Example Usage:
 * <code>
 * ...
 * $solr = new Apache_Solr_Service(); //or explicitly new Apache_Solr_Service('localhost', 8180, '/solr')
 *
 * if ($solr->ping())
 * {
 * 		$solr->deleteByQuery('*:*'); //deletes ALL documents - be careful :)
 *
 * 		$document = new Document();
 * 		$document->id = uniqid(); //or something else suitably unique
 *
 * 		$document->title = 'Some Title';
 * 		$document->content = 'Some content for this wonderful document. Blah blah blah.';
 *
 * 		$solr->addDocument($document); 	//if you're going to be adding documents in bulk using addDocuments
 * 										//with an array of documents is faster
 *
 * 		$solr->commit(); //commit to see the deletes and the document
 * 		$solr->optimize(); //merges multiple segments into one
 *
 * 		//and the one we all care about, search!
 * 		//any other common or custom parameters to the request handler can go in the
 * 		//optional 4th array argument.
 * 		$solr->search('content:blah', 0, 10, array('sort' => 'timestamp desc'));
 * }
 * ...
 * </code>
 *
 * @todo Investigate using other HTTP clients other than file_get_contents built-in handler. Could provide performance
 * improvements when dealing with multiple requests by using HTTP's keep alive functionality
 */
class Service
{
	/**
	 * Response version we support
	 */
	const SOLR_VERSION = '1.2';

	/**
	 * Response writer we'll request - JSON
     * See http://code.google.com/p/solr-php-client/issues/detail?id=6#c1 for reasoning
	 */
	const SOLR_WRITER = 'json';

	/**
	 * NamedList Treatment constants
	 */
	const NAMED_LIST_FLAT = 'flat';
	const NAMED_LIST_MAP = 'map';

	/**
	 * Search HTTP Methods
	 */
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';

	/**
	 * Servlet mappings
	 */
	const PING_SERVLET = 'admin/ping';
	const UPDATE_SERVLET = 'update';
	const UPDATE_EXTRACT_SERVLET = 'update/extract';
	const SEARCH_SERVLET = 'select';
	const THREADS_SERVLET = 'admin/threads';

	/**
	 * Server identification string - host
	 * @var string
	 */
	protected $host;

    /**
     * Server identification string - port
     * @var string
     */
    protected $port;

    /**
     * Server identification string - path
     * @var string
     */
    protected $path;

	/**
	 * Whether {@link Response} objects should create {@link Document}s in the returned parsed data
	 * @var boolean
	 */
	protected $createDocuments = true;

	/**
	 * Whether {@link Response} objects should have multivalue fields with only a single value
	 * collapsed to appear as a single value would.
	 * @var boolean
	 */
	protected $collapseSingleValueArrays = true;

	/**
	 * How NamedLists should be formatted in the output.  This specifically effects facet counts. Valid values
	 * are {@link Service::NAMED_LIST_MAP} (default) or {@link Service::NAMED_LIST_FLAT}.
	 * @var string
	 */
	protected $namedListTreatment = self::NAMED_LIST_MAP;

	/**
	 * Query delimiter. Someone might want to be able to change it (to use &amp; instead of & for example)
	 * @var string
	 */
	protected $queryDelimiter = '?';

    /**
     * Query delimiter
     * @var string
     */
    protected $queryStringDelimiter = '&';

	/**
	 * Constructed servlet full path URL
	 * @var string
	 */
	protected $pingUrl;

    /**
     * Constructed servlet full path URL
     * @var string
     */
    protected $updateUrl;

    /**
     * Constructed servlet full path URL
     * @var string
     */
    protected $updateExtractUrl;

    /**
     * Constructed servlet full path URL
     * @var string
     */
    protected $searchUrl;

    /**
     * Constructed servlet full path URL
     * @var string
     */
    protected $threadsUrl;

	/**
	 * Keep track of whether our URLs have been constructed
	 * @var boolean
	 */
	protected $urlsInited = false;

	/**
	 * Escape a value for special query characters such as ':', '(', ')', '*', '?', etc.
	 * NOTE: inside a phrase fewer characters need escaping, use {@link Service::escapePhrase()} instead
	 * @param string $value
	 * @return string
	 */
    //TODO - remove 'static'
	static public function escape($value)
	{
		//list taken from http://lucene.apache.org/java/docs/queryparsersyntax.html#Escaping%20Special%20Characters
		$pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
		$replace = '\\\$1';

		return preg_replace($pattern, $replace, $value);
	}

	/**
	 * Escape a value meant to be contained in a phrase for special query characters
	 * @param string $value
	 * @return string
	 */
    //TODO - remove 'static'
	static public function escapePhrase($value)
	{
		$pattern = '/("|\\\)/';
		$replace = '\\\$1';

		return preg_replace($pattern, $replace, $value);
	}

	/**
	 * Convenience function for creating phrase syntax from a value
	 * @param string $value
	 * @return string
	 */
    //TODO - remove 'static'
	static public function phrase($value)
	{
		return '"' . self::escapePhrase($value) . '"';
	}

    /**
     * Constructor. All parameters are optional and will take on default values
     * if not specified.
     * @param string $host
     * @param int $port
     * @param string $path
     */
	public function __construct($host = 'localhost', $port = 8983, $path = '/solr/')
	{
		$this->setHost($host);
		$this->setPort($port);
		$this->setPath($path);
		$this->initUrls();
	}

    /**
     * Return a valid http URL given this server's host, port and path and a provided servlet name
     * @param string $servlet
     * @param array $params
     * @return string
     */
	protected function constructUrl($servlet, $params = array())
	{
		if (count($params)) {
			//escape all parameters appropriately for inclusion in the query string
			$escapedParams = array();
			foreach ($params as $key => $value)
			{
				$escapedParams[] = urlencode($key) . '=' . urlencode($value);
			}
			$queryString = $this->queryDelimiter . implode($this->queryStringDelimiter, $escapedParams);
		} else {
			$queryString = '';
		}
		return 'http://' . $this->host . ':' . $this->port . $this->path . $servlet . $queryString;
	}

	/**
	 * Construct the Full URLs for the three servlets we reference
	 */
	protected function initUrls()
	{
		//Initialize our full servlet URLs now that we have server information
		$this->pingUrl = $this->constructUrl(self::PING_SERVLET);
		$this->updateUrl = $this->constructUrl(self::UPDATE_SERVLET, array('wt' => self::SOLR_WRITER ));
		$this->updateExtractUrl = $this->constructUrl(self::UPDATE_EXTRACT_SERVLET);
		$this->searchUrl = $this->constructUrl(self::SEARCH_SERVLET);
		$this->threadsUrl = $this->constructUrl(self::THREADS_SERVLET, array('wt' => self::SOLR_WRITER ));
		$this->urlsInited = true;
	}

    /**
     * Central method for making a get operation against this Solr Server
     * @param string $url
     * @param bool|float $timeout Read timeout in seconds
     * @throws Exception\HttpStatusNotOkException
     * @return Response
     */
	protected function sendRawGet($url, $timeout = FALSE)
	{
		//set up the stream context so we can control
		// the timeout for file_get_contents
		$context = stream_context_create();
		// set the timeout if specified, without this I assume that the default_socket_timeout ini setting is used
		if ($timeout !== FALSE && $timeout > 0.0) {
			// timeouts with file_get_contents seem to need to be halved to work as expected
			$timeout = (float) $timeout / 2;
			stream_context_set_option($context, 'http', 'timeout', $timeout);
		}
		//$http_response_header is set by file_get_contents
		$response = new Response(@file_get_contents($url, false, $context),
                                 $http_response_header,
                                 $this->createDocuments,
                                 $this->collapseSingleValueArrays);
		if ($response->getHttpStatus() != 200) {
			throw new Exception\HttpStatusNotOkException(
                sprintf("%s: '%s' Status: %s",
                        __METHOD__, $response->getHttpStatus(), $response->getHttpStatusMessage()),
                $response->getHttpStatus());
		}
		return $response;
	}

    /**
     * Central method for making a post operation against this Solr Server
     * @param string $url
     * @param string $rawPost
     * @param bool|float $timeout Read timeout in seconds
     * @param string $contentType
     * @return Response
     * @throws Exception\HttpStatusNotOkException
     */
	protected function sendRawPost($url, $rawPost, $timeout = FALSE, $contentType = 'text/xml; charset=UTF-8')
	{
		//set up the stream context for posting with file_get_contents
		$context = stream_context_create(
			array(
				'http' => array(
					// set HTTP method
					'method' => 'POST',
					// Add our posted content type
					'header' => "Content-Type: $contentType",
					// the posted content
					'content' => $rawPost
				)
			)
		);
		// set the timeout if specified, without this I assume
		// that the default_socket_timeout ini setting is used
		if ($timeout !== FALSE && $timeout > 0.0) {
			// timeouts with file_get_contents seem to need
			// to be halved to work as expected
			$timeout = (float) $timeout / 2;
			stream_context_set_option($context, 'http', 'timeout', $timeout);
		}
		//$http_response_header is set by file_get_contents
		$response = new Response(@file_get_contents($url, false, $context),
                                 $http_response_header,
                                 $this->createDocuments,
                                 $this->collapseSingleValueArrays);
		if ($response->getHttpStatus() != 200 && $response->getHttpStatus() != 0) {
            throw new Exception\HttpStatusNotOkException(
                sprintf("%s: '%s' Status: %s",
                    __METHOD__, $response->getHttpStatus(), $response->getHttpStatusMessage()),
                $response->getHttpStatus());
		}
		return $response;
	}

	/**
	 * Returns the set host
	 * @return string
	 */
	public function getHost()
	{
		return $this->host;
	}

    /**
     * Set the host used; fallback to constants if empty
     * @param string $host
     * @throws Exception\EmptyHostException
     */
	public function setHost($host)
	{
		//Use the provided host or use the default
		if (empty($host)) {
			throw new Exception\EmptyHostException(sprintf('%s: Host parameter is empty', __METHOD__));
		} else {
			$this->host = $host;
		}
		if ($this->urlsInited) {
			$this->initUrls();
		}
	}

	/**
	 * Get the port number
	 * @return integer
	 */
	public function getPort()
	{
		return $this->port;
	}

    /**
     * Set the port used. Fall back to constants if empty
     * @param integer $port
     * @throws Exception\InvalidPortException
     */
	public function setPort($port)
	{
		//Use the provided port or use the default
		$port = (int) $port;
		if ($port <= 0) {
			throw new Exception\InvalidPortException(sprintf("%s: Port number '%s' is not valid", __METHOD__, $port));
		} else {
			$this->port = $port;
		}
		if ($this->urlsInited) {
			$this->initUrls();
		}
	}

	/**
	 * Get the path.
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set the path used. Fallback to constants if empty
	 * @param string $path
	 */
	public function setPath($path)
	{
		$path = trim($path, '/');
		$this->path = '/' . $path . '/';
		if ($this->urlsInited) {
			$this->initUrls();
		}
	}

	/**
	 * Set the create documents flag; This determines whether {@link Response} objects will
	 * parse the response and create {@link Document} instances in place
	 * @param boolean $createDocuments
	 */
	public function setCreateDocuments($createDocuments)
	{
		$this->createDocuments = (bool) $createDocuments;
	}

	/**
	 * Get the current state of the create documents flag
	 * @return boolean
	 */
	public function getCreateDocuments()
	{
		return $this->createDocuments;
	}

	/**
	 * Set the collapse single value arrays flag.
	 * @param boolean $collapseSingleValueArrays
	 */
	public function setCollapseSingleValueArrays($collapseSingleValueArrays)
	{
		$this->collapseSingleValueArrays = (bool) $collapseSingleValueArrays;
	}

	/**
	 * Get the current state of the collapse single value arrays flag
	 * @return boolean
	 */
	public function getCollapseSingleValueArrays()
	{
		return $this->collapseSingleValueArrays;
	}

    /**
     * Set how NamedLists should be formatted in the response data
     * This mainly affects the facet counts format
     * @param string $namedListTreatment
     * @throws Exception\InvalidArgumentException
     */
	public function setNamedListTreatment($namedListTreatment)
	{
		switch ((string) $namedListTreatment) {
			case self::NAMED_LIST_FLAT:
				$this->namedListTreatment = self::NAMED_LIST_FLAT;
				break;
			case self::NAMED_LIST_MAP:
				$this->namedListTreatment = self::NAMED_LIST_MAP;
				break;
			default:
				throw new Exception\InvalidArgumentException(
                    sprintf("%s: '%s' is not a valid named list treatment option", __METHOD__, $namedListTreatment));
		}
	}

	/**
	 * Get the current setting for named list treatment.
	 * @return string
	 */
	public function getNamedListTreatment()
	{
		return $this->namedListTreatment;
	}


	/**
	 * Set the string used to separate the path form the query string.
	 * Defaults to '?'
	 * @param string $queryDelimiter
	 */
	public function setQueryDelimiter($queryDelimiter)
	{
		$this->queryDelimiter = $queryDelimiter;
	}

	/**
	 * Set the string used to separate the parameters in the query string
	 * Defaults to '&'
	 * @param string $queryStringDelimiter
	 */
	public function setQueryStringDelimiter($queryStringDelimiter)
	{
		$this->queryStringDelimiter = $queryStringDelimiter;
	}

    /**
     * Call the /admin/ping servlet, can be used to quickly tell if a connection to the server is possible
     * @param float $timeout maximum time to wait for ping in seconds, -1 for unlimited (default is 2)
     * @return float Actual time taken to ping the server, FALSE if timeout or HTTP error status occurs
     */
	public function ping($timeout = 2)
	{
		$start = microtime(true);
		// when using timeout in context and file_get_contents it seems to take twice the timeout value
		$timeout = (float) $timeout / 2;
		if ($timeout <= 0.0) {
			$timeout = -1;
		}
		$context = stream_context_create(
			array(
				'http' => array(
					'method' => 'HEAD',
					'timeout' => $timeout
				)
			)
		);
		// attempt a HEAD request to the solr ping page
		$ping = @file_get_contents($this->pingUrl, false, $context);
		// result is false if there was a timeout or if the HTTP status was not 200
		if ($ping !== false) {
			return microtime(true) - $start;
		} else {
			return false;
		}
	}

	/**
	 * Call the /admin/threads servlet and retrieve information about all threads in the
	 * Solr servlet's thread group. Useful for diagnostics.
	 * @return Response
	 */
	public function threads()
	{
		return $this->sendRawGet($this->threadsUrl);
	}

	/**
	 * Raw Add Method - Takes a raw post body and sends it to the update service
     * Post body should be a complete and well formed "add" xml document
	 * @param string $rawPost
	 * @return Response
	 */
	public function add($rawPost)
	{
		return $this->sendRawPost($this->updateUrl, $rawPost);
	}
	
	public function addExtract($get, $content_stream, $mime_type)
	{
		// echo $this->updateExtractUrl.'?'.$get."<br />";
		return $this->sendRawPost($this->updateExtractUrl.'?'.$get, $content_stream, false, $mime_type);
	}

	/**
	 * Add a Solr Document to the index
	 * @param Document $document
	 * @param boolean $allowDups
	 * @param boolean $overwritePending
	 * @param boolean $overwriteCommitted
	 * @return Response
	 * @throws Exception If an error occurs during the service call
	 */
	public function addDocument(Document $document,
                                $allowDups = false,
                                $overwritePending = true,
                                $overwriteCommitted = true)
	{
		$dupValue = $allowDups ? 'true' : 'false';
		$pendingValue = $overwritePending ? 'true' : 'false';
		$committedValue = $overwriteCommitted ? 'true' : 'false';
		$rawPost = '<add allowDups="' . $dupValue . '" overwritePending="'
                   . $pendingValue . '" overwriteCommitted="' . $committedValue . '">';
		$rawPost .= $this->documentToXmlFragment($document);
		$rawPost .= '</add>';
		return $this->add($rawPost);
	}

	/**
	 * Add an array of Solr Documents to the index all at once
	 * @param array $documents Should be an array of Document instances
	 * @param boolean $allowDups
	 * @param boolean $overwritePending
	 * @param boolean $overwriteCommitted
	 * @return Response
	 */
	public function addDocuments($documents, $allowDups = false, $overwritePending = true, $overwriteCommitted = true)
	{
		$dupValue = $allowDups ? 'true' : 'false';
		$pendingValue = $overwritePending ? 'true' : 'false';
		$committedValue = $overwriteCommitted ? 'true' : 'false';
		$rawPost = '<add allowDups="' . $dupValue . '" overwritePending="'
                   . $pendingValue . '" overwriteCommitted="' . $committedValue . '">';
		foreach ($documents as $document) {
			if ($document instanceof Document) {
				$rawPost .= $this->documentToXmlFragment($document);
			}
		}
		$rawPost .= '</add>';
		return $this->add($rawPost);
	}
	
	public function addExtractDocument(Document $document,
                                       $contentStream,
                                       $contentField = null,
                                       $ignored = array(),
                                       $mimeType)
    {
		return $this->addExtract($this->documentToGetFragment($document, $contentField, $ignored),
                                 $contentStream, $mimeType);
	}

    /**
     * Create an XML fragment from a {@link Document} instance appropriate for use inside a Solr add call
     * @param Document $document
     * @return string
     */
	protected function documentToXmlFragment(Document $document)
	{
		$xml = '<doc';
		if ($document->getBoost() !== false) {
			$xml .= ' boost="' . $document->getBoost() . '"';
		}
		$xml .= '>';
		foreach ($document as $key => $value) {
			$key = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
			$fieldBoost = $document->getFieldBoost($key);
			if (is_array($value[0])) {
				foreach ($value[0] as $multivalue) {
					$xml .= '<field name="' . $key . '"';
					if ($fieldBoost !== false) {
						$xml .= ' boost="' . $fieldBoost . '"';
						// only set the boost for the first field in the set
						$fieldBoost = false;
					}
					$multivalue = htmlspecialchars($multivalue, ENT_NOQUOTES, 'UTF-8');
					$xml .= '>' . $multivalue . '</field>';
				}
			} else {
				$xml .= '<field name="' . $key . '"';
				if ($fieldBoost !== false) {
					$xml .= ' boost="' . $fieldBoost . '"';
				}
				$value = htmlspecialchars($value[0], ENT_NOQUOTES, 'UTF-8');
				$xml .= '>' . $value . '</field>';
			}
		}
		$xml .= '</doc>';
		// echo $xml;
		// replace any control characters to avoid Solr XML parser exception
		return $this->stripCtrlChars($xml);
	}
	
	/**
	 * VIVO schema specific!!
	 * @return string
	 */
	protected function documentToGetFragment(Document $document, $contentField = null, $ignored = array())
	{
		$get = '';
		if ($document->getBoost() !== false) {
			$get .= ' boost="' . $document->getBoost() . '"';
		}
		foreach ($document as $key => $value) {
			$key = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
			$fieldBoost = $document->getFieldBoost($key);
			if (is_array($value[0])) {
				foreach ($value[0] as $multivalue) {
					$get .= 'literal.' . $key;
					// echo print_r($value);
					$multivalue = urlencode($multivalue);// htmlspecialchars($multivalue, ENT_NOQUOTES, 'UTF-8');
					$get .= '=' . $multivalue . '&';
				}
				$get = substr($get, 0, strlen($get) - 1);
			} else {
				$get .= 'literal.' . $key;
				$value = urlencode($value[0]); // htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');
				$get .= '=' . $value;
			}
			if ($fieldBoost !== false) {
				$get .= '&boost.' . $key . '=' . $fieldBoost;
			}
			$get .= '&';
		}
		// TODO description
		foreach ($ignored as $original => $mapped) {
			//Extract handler field
			$get .= 'fmap.'.$original.'=ignore_'.$original;
			$get .= '&';
			//Indexer field which should be named according to the scheme
			$get .= 'fmap.'.$mapped.'='.$original;
			$get .= '&';
		}
		if ($contentField != null) {
			$get .= 'fmap.content=' . $contentField;
		} else {
			$get = substr($get, 0, strlen($get) - 1);
		}
		// replace any control characters to avoid Solr XML parser exception
		return $this->stripCtrlChars($get);
	}

	/**
	 * Replace control (non-printable) characters from string that are invalid to Solr's XML parser with a space.
	 * @param string $string
	 * @return string
	 */
	protected function stripCtrlChars($string)
	{
		// See:  http://w3.org/International/questions/qa-forms-utf-8.html
		// Printable utf-8 does not include any of these chars below x7F
		return preg_replace('@[\x00-\x08\x0B\x0C\x0E-\x1F]@', ' ', $string);
	}

    /**
     * Send a commit command; will be synchronous unless both wait parameters are set to false
     * @param boolean $optimize Defaults to true
     * @param boolean $waitFlush Defaults to true
     * @param boolean $waitSearcher Defaults to true
     * @param float $timeout Maximum expected duration (in seconds) of the commit operation on the server
     *                      (otherwise throws a communication exception) Defaults to 1 hour
     * @return Response
     */
	public function commit($optimize = true, $waitFlush = true, $waitSearcher = true, $timeout = 3600)
	{
		$optimizeValue = $optimize ? 'true' : 'false';
		$flushValue = $waitFlush ? 'true' : 'false';
		$searcherValue = $waitSearcher ? 'true' : 'false';
		$rawPost = '<commit optimize="' . $optimizeValue . '" waitFlush="'
                   . $flushValue . '" waitSearcher="' . $searcherValue . '" />';
		return $this->sendRawPost($this->updateUrl, $rawPost, $timeout);
	}

	/**
	 * Raw Delete Method; Takes a raw post body and sends it to the update service
     * Body should be a complete and well formed "delete" xml document
	 * @param string $rawPost Expected to be utf-8 encoded xml document
	 * @param float $timeout Maximum expected duration of the delete operation on the server
     *                      (otherwise throws a communication exception)
	 * @return Response
	 */
	public function delete($rawPost, $timeout = 3600)
	{
		return $this->sendRawPost($this->updateUrl, $rawPost, $timeout);
	}

	/**
	 * Create a delete document based on document ID
	 * @param string $id Expected to be utf-8 encoded
	 * @param boolean $fromPending
	 * @param boolean $fromCommitted
	 * @param float $timeout Maximum expected duration of the delete operation on the server
     *                       (otherwise throws a communication exception)
	 * @return Response
	 */
	public function deleteById($id, $fromPending = true, $fromCommitted = true, $timeout = 3600)
	{
		$pendingValue = $fromPending ? 'true' : 'false';
		$committedValue = $fromCommitted ? 'true' : 'false';
		//escape special xml characters
		$id = htmlspecialchars($id, ENT_NOQUOTES, 'UTF-8');
		$rawPost = '<delete fromPending="' . $pendingValue . '" fromCommitted="' . $committedValue . '"><id>' . $id . '</id></delete>';
		return $this->delete($rawPost, $timeout);
	}

	/**
	 * Create a delete document based on a query and submit it
	 * @param string $rawQuery Expected to be utf-8 encoded
	 * @param boolean $fromPending
	 * @param boolean $fromCommitted
	 * @param float $timeout Maximum expected duration of the delete operation on the server
     *                       (otherwise throws a communication exception)
	 * @return Response
	 */
	public function deleteByQuery($rawQuery, $fromPending = true, $fromCommitted = true, $timeout = 3600)
	{
		$pendingValue = $fromPending ? 'true' : 'false';
		$committedValue = $fromCommitted ? 'true' : 'false';
		// escape special xml characters
		$rawQuery = htmlspecialchars($rawQuery, ENT_NOQUOTES, 'UTF-8');
		$rawPost = '<delete fromPending="' . $pendingValue . '" fromCommitted="'
                   . $committedValue . '"><query>' . $rawQuery . '</query></delete>';
		return $this->delete($rawPost, $timeout);
	}

	/**
	 * Send an optimize command; Will be synchronous unless both wait parameters are set to false
	 * @param boolean $waitFlush
	 * @param boolean $waitSearcher
	 * @param float $timeout Maximum expected duration of the commit operation on the server
     *                       (otherwise, will throw a communication exception)
	 * @return Response
	 */
	public function optimize($waitFlush = true, $waitSearcher = true, $timeout = 3600)
	{
		$flushValue = $waitFlush ? 'true' : 'false';
		$searcherValue = $waitSearcher ? 'true' : 'false';
		$rawPost = '<optimize waitFlush="' . $flushValue . '" waitSearcher="' . $searcherValue . '" />';
		return $this->sendRawPost($this->updateUrl, $rawPost, $timeout);
	}

    /**
     * Simple Search interface
     * @param string $query The raw query string
     * @param int $offset The starting offset for result documents
     * @param int $limit The maximum number of result documents to return
     * @param array $params key / value pairs for other query parameters (see Solr documentation),
     *                      use arrays for parameter keys used more than once (e.g. facet.field)
     * @param string $method
     * @throws Exception\HttpMethodNotSupportedException
     * @return Response
     */
	public function search($query, $offset = 0, $limit = 10, $params = array(), $method = self::METHOD_GET)
	{
		if (!is_array($params))
		{
			$params = array();
		}
		// construct our full parameters
		// sending the version is important in case the format changes
		$params['version'] = self::SOLR_VERSION;
		// common parameters in this interface
		$params['wt'] = self::SOLR_WRITER;
		$params['json.nl'] = $this->namedListTreatment;
		$params['q'] = $query;
		$params['start'] = $offset;
		$params['rows'] = $limit;
		// use http_build_query to encode our arguments because its faster
		// than urlencoding all the parts ourselves in a loop
		$queryString = http_build_query($params, null, $this->queryStringDelimiter);
		// because http_build_query treats arrays differently than we want to, correct the query
		// string by changing foo[#]=bar (# being an actual number) parameter strings to just
		// multiple foo=bar strings. This regex should always work since '=' will be urlencoded
		// anywhere else the regex isn't expecting it
		$queryString = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $queryString);
		// echo $this->searchUrl . $this->queryDelimiter . $queryString . '<br />';
		if ($method == self::METHOD_GET) {
			return $this->sendRawGet($this->searchUrl . $this->queryDelimiter . $queryString);
		} elseif ($method == self::METHOD_POST) {
			return $this->sendRawPost($this->searchUrl, $queryString, FALSE, 'application/x-www-form-urlencoded');
		} else {
			throw new Exception\HttpMethodNotSupportedException(
                sprintf("%s: Unsupported method '%s', please use the "
                        . "Vivo\\Indexer\\Adapter\\Solr\\Service::METHOD_* constants", __METHOD__, $method));
		}
	}
}
