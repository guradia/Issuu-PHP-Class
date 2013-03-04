<?php
/**
 * Issuu Client API (http://www.issuu.com)
 * 
 * @link		http://github.com/Astronuts/Issuu-PHP-Class
 * @copyright	Copyright (c) 2013 Theta Design AS (http://www.thetadesign.no)
 * @author      Chris Magnussen <chris at thetadesign dot no>
 * @license		See LICENSE - New BSD License
 * @package		Core\Issuu
 */
 
namespace Issuu\Http;

/**
 * @category 	Core
 * @package		Core\Issuu
 * @subpackage  Http\Request
 */
class Request {
    
    const METHOD_GET     = 'GET';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_DELETE  = 'DELETE';
    
    /**
     * @var string
     */
    protected $method = self::METHOD_GET;
    
    /**
     * @var string
     */
    protected $uri = null;
    
    /**
     * Set the method for this request
     * 
     * @param string $method
     * @throws \InvalidArgumentException
     * @return Request
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);
        if (!defined('static::METHOD_' . $method)) {
            throw new \InvalidArgumentException('Invalid HTTP method passed');
        }
        $this->method = $method;
        return $this;
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function setUri($uri)
    {
        if (is_string($uri))
            $this->uri = $uri;
        
        return $this;
    }
    
    public function send($data = null)
    {
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $this->uri);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        
        switch($this->method)
        {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, 1);
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                break;
            case 'DELETE':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        
        $response = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        
        return $response;
    }
}