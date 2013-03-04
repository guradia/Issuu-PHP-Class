<?php
/**
 * Issuu Client API (http://www.issuu.com)
 *
 * @link		http://github.com/Astronuts/Issuu-PHP-Class
 * @copyright	Copyright (c) 2013 Theta Design AS (http://www.thetadesign.no)
 * @author      Chris Magnussen <chris at thetadesign dot no>
 * @license		See LICENSE - New BSD License
 * @package		Core\Issuu\Adapter
 */

namespace Core\Issuu\Adapter;
use Core\Issuu\Client;

/**
 * @category 	Core
 * @package		Core\Issuu\Adapter
 * @subpackage  Documents
 */

class Documents {

    protected $appSettings;
    protected $client;
    protected $sort;
    protected $preSearch = true;
    
    public function __construct($appSettings, Client $client)
    {
        $this->appSettings = $appSettings;
        
        if (!$client instanceof Client)
            throw new \Exception("Parameter 'client' needs to be an instance of Issue\\Client");
        
        $this->client = $client;
    }
    
    /**
     * Get latest document from specific folder
     * 
     * @param string $folder
     * @param string $sort
     * @param boolean $pre
     * @throws \Exception
     * @return array
     */
    public function latestDocInFolder($folder, $sort = 'publishDate', $pre = true)
    {
        $this->sort = $sort;
        $this->preSearch = $pre;
        
        if (false == $folderId = $this->getFolderId($folder))
            throw new \Exception("Unable to retrieve folder '".$folder."'");
        
        if (false == $document = $this->getDocInFolder($folderId))
            throw new \Exception("No documents matched your search");
        
        return $document;
    }
    
    /**
     * @param string $folder
     * @return Ambigous <boolean, NULL>
     */
    protected function getFolderId($folder)
    {
        $this->client->setOptions(array(
                "action" => "issuu.folders.list",
                "format" => 'json',
                "responseParams" => "folderId,name"
        ));
        
        $query = $this->client->request()->rsp->_content->result->_content;
        $result = null;
        
        foreach ($query as $res)
            if ($res->folder->name == $folder)
                $result = $res->folder->folderId;
        
        return !empty($result) ? $result : false;
    }
    
    protected function getDocInFolder($id)
    {
        $this->client->setOptions(array(
                "action" => "issuu.documents.list",
                "resultOrder" => "desc",
                "documentSortBy" => "publishDate",
                "format" => 'json'
        ));
        
        $query = $this->client->request()->rsp->_content->result->_content;
        $result = false;
        
        foreach ($query as $res) {
            if (isset($res->document->folders) && in_array($id, $res->document->folders)) {
                $sort = $this->sort;
                if ($this->preSearch == true) {
                    echo date("d.m.Y",strtotime($res->document->$sort));
                    if (strtotime($res->document->$sort) <= time()) {
                        $result = $res->document;
                        break;
                    }
                } else {
                    $result = $res->document;
                    break;
                }
            }
        }
        return $result;
    }
    
}