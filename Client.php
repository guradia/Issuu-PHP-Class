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
 
/**
 * @uses        Core\Issuu\Options
 */
namespace Core\Issuu;
use Core\Issuu\Options;
use Core\Issuu\Http\Request;
 
/**
 * @category 	Core
 * @package		Core\Issuu
 * @subpackage  Client
 */
class Client extends Options {
    
    /**
     * Adapter options
     * @var mixed
     */
    protected $options;
    
    /**
     * API Endpoint
     * @var string $_endpoint
     */
    private $_endpoint = 'http://api.issuu.com/1_0';
    
    /**
     * Signed request as built by API request
     * @var MD5 $_signature
     */
    private $_signature;
    
    /**
     * Application settings
     * @var array $_appSettings
     */
    private $_appSettings = array();
    
    /**
     * Construct with authorization keys
     * 
     * @param string $key API key
     * @param string $secret API secret key
     * @return void
     */
    public function __construct($key, $secret)
    {
        $this->_appSettings['apiKey'] = $key;
        $this->_appSettings['secretKey'] = $secret;
    }
    
    /**
     * Set API options
     * 
     * @param array $options
     * @return void
     */
    public function setOptions($options)
    {
        if (!$options instanceof Options)
            $options = new Options($options);
        
        $this->options = $options;
        
        if (!empty($this->_appSettings))
            $this->createSignature();
        
    }
    
    /**
     * Get all options
     * 
     * @return \Core\Issuu\Options
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    public function request($data = null)
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->setUri($this->buildQuery());
        $response = $request->send($data);

        return json_decode($response);
    }
    
    /**
     * Build query
     * 
     * @return string
     */
    protected function buildQuery()
    {
        $query = array("signature" => $this->_signature);
        $options = (array) $this->options;
        
        array_map(function($key, $val) use (&$query) {
            if (strstr($key, '*'))
                $key = substr($key, 3);
            
            $query[$key] = $val;
        }, array_keys($options), $options);
        
        ksort($query);
        
        $query['apiKey'] = $this->_appSettings['apiKey'];
        $query = http_build_query(array_filter($query));
        
        return $this->_endpoint."?".$query;
        
    }
    
    /**
     * Create signature to use with every request
     * 
     * @return void
     */
    protected function createSignature()
    {
        $signature = $this->_appSettings['secretKey'];
        $ph = array();
        $options = (array) $this->options;
        $options['apiKey'] = $this->_appSettings['apiKey'];
        
        array_map(function($key, $val) use (&$ph) {
            if (strstr($key, '*'))
                $key = substr($key, 3);
            
            if ($val !== '' && $val !== 0 && $val !== null)
                $ph[$key] = $val;
        }, array_keys($options), $options);
        
        ksort($ph);
        
        foreach ($ph as $key => $val)
            $signature .= $key.$val;
        
        $this->_signature = md5($signature);
    }
    
    /**
     * Get option by key
     * 
     * @param string $method
     * @param mixed $args
     * @throws \Exception
     * @return string|boolean
     */
    public function __call($method, $args)
    {
        $key = strtolower(substr($method, 3, 1)) . substr($method, 4);
        $value = isset($this->options->$key) ? $this->options->$key : null;
        switch (substr($method, 0, 3)) {
            case 'get':
                if (property_exists($this->options, $key)) {
                    return $this->options->$key;
                }
                break;
            case 'has':
                return property_exists($this->options, $key);
                break;
        }

        throw new \Exception('Method "' . $method . '" does not exist and was not trapped in __call()');
    }
    
    public function setApiKey($key) { $this->_appSettings['secret'] = $key; }
    public function setApiSecret($secret) { $this->_appSettings['secret'] = $secret; }
}

