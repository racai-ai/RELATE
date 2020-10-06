<?php

class CONLLUPToken {
    private $conllup;
    private $pos;

    public function __construct($conllup,$pos){
        $this->conllup=$conllup;
        $this->pos=$pos;
    }
    
    public function get($column){return $this->conllup->getTokenData($this->pos,$column);}
    public function set($column,$value){$this->conllup->setTokenData($this->pos,$column,$value);}
    public function getNumColumns(){return count($this->conllup->getLine($this->pos)['content']);}
    
    public function getWordSeq($n){
        $seq="";
        for($i=0;$i<$n;$i++){
            $w=$this->conllup->getTokenData($this->pos+$i,"FORM");
            if($w===false)return false;
            if(!empty($seq))$seq.=" ";
            $seq.=$w;
        }
        return $seq;
    }
}

?>