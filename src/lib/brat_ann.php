<?php

class BratAnn{

    private $path;
    private $data;

    public function __construct($path){
        $this->path=$path;
        $this->data=[];    
    }
    
    public function load(){
        $this->data=[];
        if(!is_file($this->path))return ;
        
        foreach(explode("\n",file_get_contents($this->path)) as $line){
            $ldata=explode("\t",$line);
            if(count($ldata)!==3)continue;
            
            $ldata[1]=explode(" ",$ldata[1]);
            
            $this->data[]=$ldata;            
        }
    }
    
    public function save(){
        $fout=fopen($this->path,"w");
        foreach($this->data as $ldata){
            fwrite($fout,"${ldata[0]}\t".$ldata[1][0]." ".$ldata[1][1]." ".$ldata[1][2]."\t${ldata[2]}\n");
        }
        fclose($fout);
    }
    
    public function getForBrat(){
        $ret=[];
        foreach($this->data as $ldata){
             $ret[]=[$ldata[0],$ldata[1][0],[[$ldata[1][1],$ldata[1][2]]]];
        }
        return $ret;
    }
    
    public function deleteById($id){
        $found=false;
        foreach($this->data as $k=>$ldata){
            if($ldata[0]==$id){$found=$k;break;}
        }
        if($found!==false){
            unset($this->data[$k]);
            $this->data=array_values($this->data);
        }
    }
    
    public function addAnnotation($type,$offsets,$text){
        $newid="";
        if(empty($this->data)){
            $newid="T1";
        }else{
            $oldid=$this->data[count($this->data)-1][0];
            $oldid=intval(substr($oldid,1));
            $newid="T".($oldid+1);
        }
        
        $textann=mb_substr($text,$offsets[0][0],($offsets[0][1]-$offsets[0][0]));        
        $this->data[]=[$newid,[$type,$offsets[0][0],$offsets[0][1]],$textann];
    }

}

?>