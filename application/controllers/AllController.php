<?php
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';
ini_set("memory_limit", "2048M");
ini_set("max_execution_time", "0");

class allController extends Zend_Controller_Action
{   
  public function indexAction(){
    //$this->_helper->viewRenderer->setNoRender();
    
    $requestParams =  $this->_request->getParams();
    if(isset($requestParams['page'])){
      $page = $requestParams['page'];
    }
    else{
      $page = 1;
    }
    
    $host = OpenContext_OCConfig::get_host_config();
    
    $archiveFeed = new ArchiveFeed;
    $archiveFeed->set_up_feed_page($page);
    $archiveFeed->getItemList();
    $this->view->archive =  $archiveFeed;
    
  }//end function
    
  
  public function atomAction(){
    $this->_helper->viewRenderer->setNoRender();
    
    //check for referring links
    OpenContext_SocialTracking::update_referring_link('all_feed', $this->_request->getRequestUri(), @$_SERVER['HTTP_USER_AGENT'], @$_SERVER['HTTP_REFERER']);

    $requestParams =  $this->_request->getParams();
    if(isset($requestParams['page'])){
      $page = $requestParams['page'];
    }
    else{
      $page = 1;
    }
    
    $host = OpenContext_OCConfig::get_host_config();
    mb_internal_encoding( 'UTF-8' );
    $host = OpenContext_OCConfig::get_host_config();
    $archiveFeed = new ArchiveFeed;
    $archiveFeed->set_up_feed_page($page);
    if(!$archiveFeed->feedItems){
      $this->view->requestURI = $host.$this->_request->getRequestUri();
		return $this->render('404error'); // page not found
    }
    
    if($page > 1 && count($archiveFeed->feedItems) < 1){
      $this->view->requestURI = $host.$this->_request->getRequestUri();
		return $this->render('404error'); // page not found
    }
    else{
      header('Content-type: application/atom+xml; charset=utf-8', true);
      echo $archiveFeed->generateFeed();
    }
    
  }//end function
  
  
  public function personAction(){
    $this->_helper->viewRenderer->setNoRender();
    $person = New Person;
    echo $person->getItemEntry("642_DT_Person");
    
    
    
  }
  
  public function spaceAction(){
    $this->_helper->viewRenderer->setNoRender();
    $requestParams =  $this->_request->getParams();
    if(isset($requestParams['batch'])){
      $batch = $requestParams['batch'];
    }
    else{
      $batch = 0;
    }
    mb_internal_encoding( 'UTF-8' );
    $archiveFeed = new ArchiveFeed;
    $archiveFeed->set_up_feed_page(1);
    echo "Start Batch: ".$batch;
    $batch = $archiveFeed->insertSpatial($batch);
    echo "<br/>Done: ".$batch;
    echo "<br/><a href='http://opencontext.org/all/space?batch=".$batch."'>next...</a>";
  }
  
  public function mediaAction(){
    $this->_helper->viewRenderer->setNoRender();
    $requestParams =  $this->_request->getParams();
    if(isset($requestParams['batch'])){
      $batch = $requestParams['batch'];
    }
    else{
      $batch = 0;
    }
    $archiveFeed = new ArchiveFeed;
    $archiveFeed->set_up_feed_page(1);
    echo "Start Batch: ".$batch;
    $batch = $archiveFeed->insertMedia();
    echo "<br/>Done: ".$batch;
    //echo "<br/><a href='http://opencontext.org/all/space?batch=".$batch."'>next...</a>";
  }
  
  
  public function siteMapAction(){
    $this->_helper->viewRenderer->setNoRender();
    
    $siteMapObj = new SiteMap;
    
    /*
    $siteMapObj->startDB();
    $siteMapObj->getMaxRankings();
    $siteMapObj->get_items();
    $siteMapObj->adjust_rankings();
    */
    
    
    //header('Content-Type: application/javascript; charset=utf8');
    //echo Zend_Json::encode($siteMapObj->itemList);
    
    header('Content-Type: application/xml; charset=utf-8');
    echo $siteMapObj->get_make_sitemap();
  }
   
}//end of class
