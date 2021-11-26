<?php
 
namespace MageSquare\Blog\Controller\Adminhtml\System\Config;
 
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use MageSquare\Blog\Helper\Data;
use Magento\Framework\HTTP\Client\Curl;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Config\ConfigResource\ConfigInterface;

class Collect extends Action
{
    
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
     * @param Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param Data $helper
     * @param Logger $logger
     * @param Curl $curl
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Data $helper,
        LoggerInterface $logger,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface,
        Curl $curl
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->curl = $curl;
        $this->configInterface = $configInterface;
        parent::__construct($context);
    }
 
    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();  
        $request = $objectManager->get('Magento\Framework\App\Request\Http');  
        $email = $request->getParam('email');
        $license_key = $request->getParam('license_key');

        if($email == null || $email == "" || $license_key == null || $license_key == "")
        {
            return $result->setData(['status' => 0, 'Msg' => 'Please Enter Valid Email and License Key']);
        }else{


            try {
                $api_url = "http://license.typo3marketplace.ch:8000/api/license/activate_license";
                $api_key = '5ed35a38caed4f5776fe39c7awaewaea';
                $this_server_name = getenv('SERVER_NAME')?:$_SERVER['SERVER_NAME']?:getenv('HTTP_HOST')?:$_SERVER['HTTP_HOST'];
                $this_http_or_https = (((isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on")) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https://' : 'http://');
                $this_url = $this_server_name;
                $this_ip = $this->get_ip_from_third_party();

                $data_array =  array(
                     "license_code" => $license_key,
                     "email" => $email,
                    );
                $this->curl->addHeader("crayon_lc_api_key",$api_key);
                $this->curl->addHeader("crayon_lc_api_url",$this_url);
                $this->curl->addHeader("crayon_lc_api_ip",$this_ip);
                $this->curl->post($api_url, $data_array);
                $response = $this->curl->getBody();
                $response = json_decode($response);
                $statusCode = $this->curl->getStatus();
                if($statusCode == 200){
                    if($response->status){
                        // add Activation success msg 
                        $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
                        $connection= $this->_resources->getConnection();
                        $table = $this->_resources->getTableName('magesquare_blog_license');
                        $activate = "INSERT INTO " . $table . "(email,c_lc,status,is_verify,log,version,last_verify) VALUES ('".$email."','".$license_key."',1,0,'".$response->msg."','".$response->version."',NULL)";
                        $connection->query($activate);

                        // Add verfification success
                        $this->ress = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
                        $connection= $this->ress->getConnection();
                        $table = $this->ress->getTableName('magesquare_blog_license');
                        $verify = "INSERT INTO " . $table . "(email,c_lc,status,is_verify,log,version) VALUES ('".$email."','".$license_key."',1,1,'".$response->msg."','".$response->version."')";
                        $connection->query($verify);

                        $this->configInterface->saveConfig('msblog/license_settings/email',$email,'default',0);
                        $this->configInterface->saveConfig('msblog/license_settings/license_key',$license_key,'default',0);
                        $this->configInterface->saveConfig('msblog/license_settings/islc',1,'default',0);
                        return $result->setData(['data' => $response]);
                        
                    }else{
                        $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
                        $connection= $this->_resources->getConnection();

                        $table = $this->_resources->getTableName('magesquare_blog_license');
                        $sql = "INSERT INTO " . $table . "(email,c_lc,status,is_verify,log,last_verify) VALUES ('".$email."','".$license_key."',1,0,'".$response->msg."',null)";
                        $connection->query($sql);
                        $this->configInterface->saveConfig('msblog/license_settings/email',NULL,'default',0);
                        $this->configInterface->saveConfig('msblog/license_settings/license_key',NULL,'default',0);
                        $this->configInterface->saveConfig('msblog/license_settings/islc',1,'default',0);
                        return $result->setData(['data' => $response]);

                    }
                }else{
                    $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
                    $connection= $this->_resources->getConnection();

                    $table = $this->_resources->getTableName('magesquare_blog_license');
                    $sql = "INSERT INTO " . $table . "(email,c_lc,status,is_verify,log,last_verify) VALUES ('".$email."','".$license_key."',0,0,'".$response->msg."',NULL)";
                    $connection->query($sql);
                    $this->configInterface->saveConfig('msblog/license_settings/email',NULL,'default',0);
                    $this->configInterface->saveConfig('msblog/license_settings/license_key',NULL,'default',0);
                    $this->configInterface->saveConfig('msblog/license_settings/islc',0,'default',0);
                    return $result->setData(['data' => $response]);

                }
                
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->logger->critical($e);
                return $result->setData(['error' => $e->getMessage()]);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                return $result->setData(['error' => $e->getMessage()]);
            }
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
?>