<?php

class Runner {
		private $runnerId;
		private $runnerFolder;
		
		public function __construct($runnerId,$runnerFolder){
				$this->runnerId=$runnerId;
				$this->runnerFolder=$runnerFolder;
		}
		
		public function getRunnerId(){return $this->runnerId;}
		
		public function getRunnerFolder(){return $this->runnerFolder;}
}

?>