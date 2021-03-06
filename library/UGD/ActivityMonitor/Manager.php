<?php

class UGD_ActivityMonitor_Manager {
	
	const IDLE     = "IDL";
	const DONE     = "DUN";
	const GROUP    = "GRP";
    
	private $handle;
    private $workerCount;
	private $groups              = array();
	private $workers             = array();
	private $workerPipes;//         = array();
	
	
	private $pipeSpec 	         = array(
                						0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
                						1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
                						2 => array("pipe", "w") // stderr is a file to write to
                						);
	
	private $done                = 0;
	private $logger;
	/**
	 * 
	 */
	public function __construct() {
		$this->handle = Util_Guid::generate();
		$this->logger = Util_Log::get()->UGAMON_Mng();
	}
	
	public function init(){

	    try{
	        //Get Limit
    		$this->workerCount = (int) $this->getWorkerCount();

    		//Pickup Groups
    		$this->groups = $this->getGroups();
    		
    		if ( count($this->groups) > 0){
        		//Spawn Workers
        		$this->spawnWorkers();
        		
        		//Monitor Worker and Push Groups
        		$this->processQueue();
    		}
    		
	    }catch (Exception $e){
	        $this->logger->err($e->getMessage());
	        echo("BOT Failure:". $e->getMessage());
	    }
	    
	    $this->killWorkers();
		
	}
	
	private function getWorkerCount(){
		
		//Get Total
		$gTable = Doctrine::getTable('Group');
		$totalGroups = $gTable->count();
		
		//Get Alerting Groups
		$afterDate = date('Y-m-d',strtotime("-" . Zend_Registry::get('config')->amon->alerts->days_between . " days"));
		$q = Doctrine_Query::create()
			->from('Group g')
			->where('g.state = ? AND (g.last_check <= ? OR g.last_check IS NULL)', array('2', $afterDate));
		$alerting = $q->count();
		
		//Get regular number for today
		//Total Groups / (days in month -1)
		//This leaves one day for us to pickup any left overs in the month
		$groupsPerDay = ceil( $totalGroups / (date('t')-1) );
		
		//Add pending alerting groups for this execution
		$totalForProcess = $groupsPerDay + $alerting;
		
		return $totalForProcess;
	}
	
	private function getGroups(){
		
		
	    //Cut off date for alerting groups
	    $afterDate = date('Y-m-d',strtotime("-" . Zend_Registry::get('config')->amon->alerts->days_between . " days"));
		
	    //Month for regular groups
	    $actualMonth = date('m');
	    
	    //Build Query
	    $q = Doctrine_Query::create()
			->from('Group g')
			->where('(MONTH(g.last_check) != ? OR g.last_check IS NULL) OR (g.state = ? AND (g.last_check <= ? OR g.last_check IS NULL))', array($actualMonth, '2', $afterDate))
			->orderBy('g.last_check ASC')
			->limit($this->workerCount);
		$groups = $q->execute();
		
		//overwrite worker count for custom tailored value
		$this->workerCount = count($groups);
		
		return $groups->toArray(false);
	    
	}
	
	private function spawnWorkers(){

	    for ($i=0; $i < $this->workerCount; $i++){
			
	        $processName = CMDLINE.' -m cli -c ActivityMonitor -a worker '.$this->handle.':'.$i;
	        $this->logger->debug($processName);
	        $this->workers[$i] = proc_open("php ".$processName, $this->pipeSpec, $this->workerPipes[$i]);
	        stream_set_blocking($this->workerPipes[$i][1],0);
	        stream_set_blocking($this->workerPipes[$i][2],0);
			
			if ($this->workers[$i] === false || gettype($this->workerPipes[$i][1]) != 'resource'){
			    $this->logger->err( 'Error at Worker creation' );
			    throw new Exception('Unable to create Worker Bot');
			}
		}
		
	}
	
	private function processQueue(){
	    
	    while (!$this->isDone()){
	        $this->logger->debug('Begin Listening');
	        
	        foreach($this->workers as $pipeId => $worker){
	            
	            $this->logger->debug('Listening to '.$pipeId);
	            
                $signal = $this->readWorker($pipeId);
                $this->logger->debug('Received: '.$signal->code);
	            switch ($signal->code){
	                case self::IDLE:
	                    
	                    //Pickup Group
	                    $group = array_shift($this->groups);
	                    
	                    if ($group === null){
	                        break;
	                    }
	                    
	                    $groupId = $group['id'];
	                    
	                    //Push to a worker
	                    try{
	                        $this->sendGroupToWorker($pipeId,$groupId);
	                    }catch (Exception $e){
	                        //return group to Queue
	                        $this->groups[] = $group;
	                    }
	                    break;
	                case self::DONE:
	                    $this->done++;
	                    break;
	            }
	            
	        }
	        
	        sleep(5);
	        
	    }
	    
	}
	
	private function isDone(){

	    if (count($this->workers) == 0){
	        throw new Exception('Catastrophic Worker Failures');
	    }
	    
	    return ($this->done >= count($this->workers) && count($this->groups) == 0);
	}
	
	private function readWorker($pipeId){
	    
	    $signal = new stdClass();
	    
	    //Get STDOUT pipe
	    $pipe = $this->workerPipes[$pipeId][1];
	    
	    $read = fgets($pipe);
	    
	    if (!$read){
	        return null;
	    }else{
    	    //Process received command
    	    $in = trim($read);
    	    $this->logger->debug(var_export($in,true));
    		$signal->code = substr($in,0,3);
    		$signal->data = substr($in,4);
	    }
	    
	    return $signal;
	}
	
	private function sendGroupToWorker($pipeId,$groupId){
	    
	    $pipe = $this->workerPipes[$pipeId][0];
	    $msg = self::GROUP ." ". $groupId. "\n";
	    if ( !fwrite($pipe, $msg) ){
	        $err = 'Unable to send Group ('. $groupId .') to Worker ('.$pipeId.')';
	        $this->logger->err($err);
	        throw new Exception($err);
	    }
	    
	}
	
	private function killWorkers(){
	    
	    foreach ($this->workers as $worker) {
	    	proc_close($worker);
	    }
	    
	    
	}
}

?>