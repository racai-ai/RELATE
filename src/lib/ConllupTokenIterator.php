<?php

class CONLLUPTokenIterator implements Iterator {

    private $conllup;
    private $pos;
    private $from;
    private $to;
    private $current;

    public function __construct($conllup,$from,$to){
        $this->conllup=$conllup;
        $this->from=$from;
        $this->to=$to;
        
        $this->rewind();
    }
    
    public function findCurrent(){
        for($line=$this->conllup->getLine($this->pos);$line!==false && $line['type']!=='data' && $this->pos<=$this->to;$this->pos++,$line=$this->conllup->getLine($this->pos));
        if($this->pos<=$this->to){
            $this->current=new CONLLUPToken($this->conllup,$this->pos);
        }
    }
    
    public function rewind(){
        $this->pos=$this->from;
        $this->current=false;
        $this->findCurrent();
    }

    public function current(){
        if($this->pos>$this->to)return false;
        return $this->current;
    }
    
    public function key(){
        return $this->pos;
    }
    
    public function valid(){
        return ($this->pos<=$this->to);
    }
    
    public function next(){
        $this->pos++;
        $this->findCurrent();
    }
}

?>