<?php

class CONLLUPSentence {

    private $conllup;
    private $from;
    private $to;

    public function __construct($conllup,$from,$to){
        $this->conllup=$conllup;
        $this->from=$from;
        $this->to=$to;    
    }
    
    public function getTokenIterator(){
        return new CONLLUPTokenIterator($this->conllup,$this->from,$this->to);
    }

}

?>