<?php

namespace MageSquare\Blog\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use MageSquare\Blog\Helper\Data;
use Magento\Framework\HTTP\Client\Curl;
use \Psr\Log\LoggerInterface;
use Magento\Framework\Notification\NotifierInterface as NotifierPool;
use \Magento\Framework\App\Config\ConfigResource\ConfigInterface;

class VerifyObserver implements \Magento\Framework\Event\ObserverInterface
{

	/**
   * @var \Magento\Framework\App\Config\ScopeConfigInterface
   */
   protected $scopeConfig;

    /**
     * @var Magento\Framework\Controller\Result\JsonFactory
    */ 
    protected $resultJsonFactory;
    
    /**
     * @var \Psr\Log\LoggerInterface;
    */ 
    protected $logger;

 
    /**
     * @var MageSquare\Blog\Helper\Data
     */
    protected $helper;

    /**
    * @var Magento\Framework\HTTP\Client\Curl
    */
    protected $curl;
    

    /**
     *  @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $configWriter;

	/**
     * Notifier Pool
     *
     * @var NotifierPool
     */
    protected $notifierPool;

   public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Data $helper,
        LoggerInterface $logger,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface,
        Curl $curl,
          NotifierPool $notifierPool,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
    	  $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->configInterface = $configInterface;
        $this->curl = $curl;
          $this->notifierPool = $notifierPool;
         $this->scopeConfig = $scopeConfig;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
	    	$connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 

	    	$data = $connection->fetchAll("SELECT * FROM magesquare_blog_license WHERE status = 1 AND is_verify = 1 ORDER BY id desc");

	    	if(sizeof($data) > 0){
	    		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
	     		$is_verify = $this->scopeConfig->getValue('msblog/license_settings/islc', $storeScope);
	     		// check whether flag active set or not
	     		
	     		if($is_verify == 0)	 {
    					return $this;
	     		}else{
	     			/** @var \Magento\Framework\Controller\Result\Json $result */
	     			$result = $this->resultJsonFactory->create();

	     			$last_verify = $data[0]['last_verify'];
	     			$last_verify = strtotime($last_verify);

	  				$verf=0;
	  			 	// check api is executed today or not
	         		if($last_verify > 0){
		               $current_date = date('d');
		               $current_month = date('m');
		               $current_year = date('Y');
		               $verify_date = date('d',$last_verify);
		               $verify_month = date('m',$last_verify);
		               $verify_year = date('Y',$last_verify);

		               if($verify_year == $current_year && $verify_month == $current_month && $verify_date == $current_date){
		                  $run_api = 0;
		               }else{
		               	if($is_verify == 0){
		                  	$run_api = 0;
		               	}elsE{
		               		$run_api = 1;
		               	}
		               }
		            }else{
		               if($is_verify == 0){
	                  	$run_api = 0;
	               	}elsE{
	               		$run_api = 1;
	               	}
		            }
		         // check api is executed today or not

		         // if API for verify license is not run today
		            if($run_api == 1){
		               // call api for verify licanse
		             	$api_url = "http://license.typo3marketplace.ch:8000/api/license/verify_license";
		                $api_key = '5ed35a38caed4f5776fe39c7awaewaea';
		                $this_server_name = getenv('SERVER_NAME')?:$_SERVER['SERVER_NAME']?:getenv('HTTP_HOST')?:$_SERVER['HTTP_HOST'];
		                $this_http_or_https = (((isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on")) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://');
		                $this_url = $this_server_name;
		                $this_ip = $this->get_ip_from_third_party();

		                $data_array =  array(
		                     "license_code" => $data[0]['c_lc'],
		                     "email" => $data[0]['email'],
		                    );

		                $this->curl->addHeader("crayon_lc_api_key",$api_key);
		                $this->curl->addHeader("crayon_lc_api_url",$this_url);
		                $this->curl->addHeader("crayon_lc_api_ip",$this_ip);
		                $this->curl->post($api_url, $data_array);
		                $response = $this->curl->getBody();
		                $response = json_decode($response);
		                
		                $statusCode = $this->curl->getStatus();
		               if($statusCode === 200){
		             		$sql = "INSERT INTO magesquare_blog_license (email,c_lc,status,is_verify,log,version) VALUES('".$data[0]['email']."','".$data[0]['c_lc']."',".$data[0]['status'].",1,'".$response->msg."','".$data[0]['version']."')";
		             	 	$connection->query($sql);
		             	 	$this->configInterface->saveConfig('msblog/license_settings/islc',1,'default',0);
										return $this;
		               }else{
		             		$sql = "INSERT INTO magesquare_blog_license (email,c_lc,status,is_verify,log,version) VALUES('".$data[0]['email']."','".$data[0]['c_lc']."',".$data[0]['status'].",0,'".$response->msg."','".$data[0]['version']."')";
		             		$connection->query($sql);
		             		$this->configInterface->saveConfig('msblog/license_settings/islc',0,'default',0);
			    				$html = "<div style='color:#ff0000;font-weight:500;text-align:center;font-size:20px;'>MageSquare Blog has License issue : ".$response->msg."</div>";
			    				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
								$state =  $objectManager->get('Magento\Framework\App\State');
					    		if($state->getAreaCode() === "adminhtml"){
					    			// echo $html;
					    			$this->notifierPool->addNotice('MageSquare_Blog', 'MageSquare Blog has License issue : Please Activate License!');
			    					return $this;
					    		}else{
			    					echo $html;
			    					return $this;
					    		}
		             	}
		            }
		         // if API for verify license is not run today
	     		}

	    	}else{
	    		// Must be Activate License First
	    		$this->configInterface->saveConfig('msblog/license_settings/islc',0,'default',0);
	    		return $this;
	    	}
	}

	 private function get_ip_from_third_party(){
      $ch = curl_init ();
      curl_setopt ($ch, CURLOPT_URL, "http://ipecho.net/plain");
      curl_setopt ($ch, CURLOPT_HEADER, 0);
      curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec ($ch);
      curl_close ($ch);
      return $response;
  }
}