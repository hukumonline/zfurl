<?php
// application/controllers/UrlController.php

/**
 * UrlController Controlls all aspects of the URLS
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class UrlController extends Zend_Controller_Action 
{

    public function init(){
    	
		//Init the DB
		$this->db = Zend_Registry::get('db');
		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);

    }

    public function indexAction() 
    {
	//This is the main function of this controller
	//If this action is hit we should look up the url then
	//do a redirect


    }

    public function shortenAction()
    {
    	//$ip = $_SERVER['REMOTE_ADDR']; 
		
		//A new URL to make short
		$f = new Zend_Filter_StripTags();
		$url = $this->_request->getPost('url');
	
		//strip off the %0A 
		$url = trim(preg_replace('/%0A/', '', $url));
	
		//see if the url is in the db if it is return the id
		$shortid = $this->db->fetchCol("select * from urls where url = ?", $url);
		$return;
		
		$config = Zend_Registry::getInstance();
		$sh = $config->get('configuration');
		
		if (isset($shortid[0]))
		{
			$hex = dechex($shortid[0]);
			$short = $sh->siteroot.$hex;
			$return = array('shorturl' => $short);
			
		}else{
			//if not insert then return the id
			$data = array('url' => $url,
			 			  'createdate' => date("Y-m-d h:i:s"),
						  'remoteip' => Pandamp_Lib_Formater::getRealIpAddr());
			$insert = $this->db->insert('urls', $data);
			
			$id = $this->db->lastInsertId('urls', 'id');
			$hex = dechex($id);
			$short = $sh->siteroot.$hex;
			$return = array('shorturl' => $short);
		}
		$this->_helper->json->sendJson($return);
    }
	
	public function redirectAction()
	{
		$request = $this->getRequest();
    	$urlid = $request->getParam('urlid'); 
		$id = hexdec($urlid);
		
		$url = $this->db->fetchRow("select * from urls where id = ?", $id);
		if ($url->id)
		{
			//record the hit and redirect
			$data = array(
						'urlid' => $id,
						'date' => date("Y-m-d h:i:s")		
					);
			$insert = $this->db->insert('clicks', $data);
			$redirecturl = $url->url;
			
			if (!preg_match('/http/', $redirecturl))
			{
				$redirecturl = "http://" . $redirecturl;
			}
			
			$this->_redirect($redirecturl);
		}else{
			$this->view->message = "We were not able to determine the url you were looking for. urlid $urlid : $id";
		}
		
	}
	
	public function topurlAction()
	{
		$dataarray = $this->db->fetchAssoc("select b.url as link, count(*) as count
										 	from clicks as a,
     									 	urls as b
     									 	where a.urlid = b.id 
     									 	group by a.urlid, b.url
											having count(*) > 1
									 		order by count desc"
										);
		$this->view->data = $dataarray;
		
		
		
	}
	
}
