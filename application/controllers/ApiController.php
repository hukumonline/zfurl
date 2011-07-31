<?php
// application/controllers/ApiController.php

/**
 * ApiController is the default controller for any ur /about
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class ApiController extends Zend_Controller_Action 
{
    /**
     * The "index" action is the default action for all controllers. This 
     * will be the landing page of your application.
     *
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /index/
     *   /index/index
     *
     * @return void
     *
     */
	
	public function init(){
    	
		//Init the DB
		$this->db = Zend_Registry::get('db');
		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);

    }
	
    public function indexAction() 
    {
		$url = $this->getRequest()->getParam('url');
		
		$xmlString = '<?xml version="1.0" standalone="yes"?><response></response>';
		$xml = new SimpleXMLElement($xmlString);
		
		if (strlen($url) < 1)
		{
			$xml->addChild('status', 'failed no url passed');
		}else
		{
			$shortid = $this->db->fetchCol("select * from urls where url = ?", $url);
			
			$config = Zend_Registry::getInstance();
			$sh = $config->get('configuration');
			
			if ($shortid[0])
			{
				$hex = dechex($shortid[0]);
				$short = $sh->siteroot.$hex;
				
			}else{
				//if not insert then return the id
				$data = array('url' => $url,
				 			  'createdate' => date("Y-m-d h:i:s"),
							  'remoteip' => Pandamp_Lib_Formater::getRealIpAddr());
				$insert = $this->db->insert('urls', $data);
				
				$id = $this->db->lastInsertId('urls', 'id');
				$hex = dechex($id);
				$short = $sh->siteroot.$hex;
			}
			$xml->addChild('holurl', $short);
			$xml->addChild('status', 'success');
		}
		
		$out = $xml->asXML();
		
		//This returns the XML xmlreponse should be key value pairs
	    $this->getResponse()->setHeader('Content-Type', 'text/xml')
	                                ->setBody($out);
									
		$this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender();

    }
	
	public function aboutAction() 
	{
		//THis only prints the about page.
	}
	
}