<?php

 $environment = 'development';


// application/bootstrap.php
// 
// Step 1: APPLICATION CONSTANTS - Set the constants to use in this application.
// These constants are accessible throughout the application, even in ini 
// files. We optionally set APPLICATION_PATH here in case our entry point 
// isn't index.php (e.g., if required from our test suite or a script).
defined('APPLICATION_PATH')
    or define('APPLICATION_PATH', dirname(__FILE__));

defined('APPLICATION_ENVIRONMENT')
    or define('APPLICATION_ENVIRONMENT', 'development');


$router = new Zend_Controller_Router_Rewrite();
//$route = new Zend_Controller_Router_Route(
$route = new Zend_Controller_Router_Route_Regex(
		'([A-Fa-f0-9]+)', 
		array(
				'controller' => 'url', 
				'action' => 'redirect',
		),
		array(
			'urlid' => 1
		)
 );
 $router->addRoute('index', $route);

// Step 2: FRONT CONTROLLER - Get the front controller.
// The Zend_Front_Controller class implements the Singleton pattern, which is a
// design pattern used to ensure there is only one instance of
// Zend_Front_Controller created on each request.
$frontController = Zend_Controller_Front::getInstance();

// Step 3: CONTROLLER DIRECTORY SETUP - Point the front controller to your action
// controller directory.
$frontController->setControllerDirectory(APPLICATION_PATH . '/controllers');

// Step 4: APPLICATION ENVIRONMENT - Set the current environment.
// Set a variable in the front controller indicating the current environment --
// commonly one of development, staging, testing, production, but wholly
// dependent on your organization's and/or site's needs.
$frontController->setParam('env', APPLICATION_ENVIRONMENT);
$frontController->setRouter($router);


// LAYOUT SETUP - Setup the layout component
// The Zend_Layout component implements a composite (or two-step-view) pattern
// With this call we are telling the component where to find the layouts scripts.
Zend_Layout::startMvc(APPLICATION_PATH . '/layouts/scripts');



// VIEW SETUP - Initialize properties of the view object
// The Zend_View component is used for rendering views. Here, we grab a "global" 
// view instance from the layout object, and specify the doctype we wish to 
// use. In this case, XHTML1 Strict.
$view = Zend_Layout::getMvcInstance()->getView();
$view->doctype('XHTML1_STRICT');

$view->addHelperPath(APPLICATION_PATH . '/../library/Pandamp/Controller/Action/Helper', 'Pandamp_Controller_Action_Helper');

// CONFIGURATION - Setup the configuration object
// The Zend_Config_Ini component will parse the ini file, and resolve all of
// the values for the given section.  Here we will be using the section name
// that corresponds to the APP's Environment
$configuration = new Zend_Config_Ini(
    APPLICATION_PATH . '/config/app.ini', 
    APPLICATION_ENVIRONMENT
);

// REGISTRY - setup the application registry
// An application registry allows the application to store application 
// necessary objects into a safe and consistent (non global) place for future 
// retrieval.  This allows the application to ensure that regardless of what 
// happends in the global scope, the registry will contain the objects it 
// needs.
$registry = Zend_Registry::getInstance();
$registry->configuration = $configuration;
//$registry->dbAdapter     = $dbAdapter;

//The DB 
 //Create db object
 Zend_Loader::loadClass('Zend_Db_Table');
 $db = Zend_Db::factory($configuration->db);
 Zend_Db_Table::setDefaultAdapter($db);
 Zend_Registry::set('db', $db);

Zend_Registry::set('configuration', $configuration);

// Step 5: CLEANUP - Remove items from global scope.
// This will clear all our local boostrap variables from the global scope of 
// this script (and any scripts that called bootstrap).  This will enforce 
// object retrieval through the applications's registry.
unset($frontController, $view, $configuration, $registry, $db);

