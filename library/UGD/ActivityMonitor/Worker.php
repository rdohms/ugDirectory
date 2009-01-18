<?php

class UGD_ActivityMonitor_Worker
{

    private $handle;
    private $logger;
    
    /**
     * 
     */
    public function __construct ($handle)
    {
        $this->handle = $handle;
        $this->logger = Util_Log::get()->UGAMON_Wrk();
        
        stream_set_blocking(STDIN,0);
    }
    
    public function init(){
        
        $this->logger->debug($this->handle . " Online");
        $this->listen();
        
        
    }
    
    public function sendResponse($code, $data = null){
        
        $msg = $code . " " . $data;
        
        if (!fwrite(STDOUT,$msg . PHP_EOL)){
            $this->logger->err($this->handle . " unable to send response: ".$msg);
            throw new Exception('Unable to send response: '.$msg);
        }
    }
    
    private function listen(){
        while (!feof(STDIN)) {
            
            $this->sendResponse(UGD_ActivityMonitor_Manager::IDLE);
            
            $order = $this->getOrder();
            
            if ($order !== null){
                $this->processOrder($order);
            }
                        
            sleep(20);
            
        }
    }
    
    private function getOrder(){
	    
        $signal = new stdClass();
	    $read = fgets(STDIN);
	    
	    if (!$read){
	        return null;
	    }
	    
	    //Process received command
	    $in = trim($read);
	    $this->logger->debug(var_export($in,true));
		$signal->code = substr($in,0,3);
		$signal->data = substr($in,4);
		
		return $signal;
    }
    
    private function processOrder($order){
        
        switch ($order->code){
            case UGD_ActivityMonitor_Manager::GROUP:
                $this->monitorGroup($order->data);
                break;
        }
        
        $this->sendResponse(UGD_ActivityMonitor_Manager::DONE);
        
    }
    
    private function monitorGroup($id){
        sleep(15);
        //TODO implement group handling
    }
}

?>