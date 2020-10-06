<?php

class CONLLUPSentenceIterator implements Iterator {

    private $conllup;
    private $pos;
    private $currentSent;
    private $currentSentNum;
    private $nextPos;

    public function __construct($conllup){
        $this->conllup=$conllup;
        
        $this->rewind();
    }
    
    private function findCurrent(){
        if($this->pos>=$this->conllup->getNumLines()){
            $this->currentSent=false;
            return ;
        }
        
        for($i=$this->pos;$i<$this->conllup->getNumLines();$i++){
            $line=$this->conllup->getLine($i);
            if($line['type']=='new_sent'){
                  $this->currentSent=new ConllupSentence($this->conllup,$this->pos,$i-1);
                  $this->nextPos=$i+1;
                  return ;
            }
        }
        
        $this->currentSent=new ConllupSentence($this->conllup,$this->pos,$this->conllup->getNumLines()-1);
        $this->nextPos=$this->conllup->getNumLines();
    }
    
    public function rewind(){
        $this->pos=0;
        $this->currentSentNum=0;
        $this->currentSent=false;
        $this->nextPos=-1;
        
        $this->findCurrent();
    }

    public function current(){
        return $this->currentSent;
    }
    
    public function key(){
        return $this->currentSentNum;
    }
    
    public function valid(){
        return ($this->currentSent!==false);
    }
    
    public function next(){
        $this->pos=$this->nextPos;
        $this->findCurrent();
    }
}

?>