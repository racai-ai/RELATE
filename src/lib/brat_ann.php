<?php

class BratAnn{

    private $path;
    private $data;

    public function __construct($path){
        $this->path=$path;
        $this->data=[];    
    }
    
    // ID<tab>TYPE start end;start end<tab>text
    
    public function load(){
        $this->data=[];
        if(!is_file($this->path))return ;
        
        foreach(explode("\n",file_get_contents($this->path)) as $line){
            $ldata=explode("\t",$line);
            if(count($ldata)!==3)continue;
            
            $frags=explode(" ",$ldata[1],2);
            $type=$frags[0];
            $frags=explode(";",$frags[1]);
            $fragArr=[];
            foreach($frags as $v)$fragArr[]=explode(" ",$v);
            
            $this->data[]=["id"=>$ldata[0], "type"=>$type, "frags"=>$fragArr,"text"=>$ldata[2]];            
        }
    }
    
    public function save(){
        $fout=fopen($this->path,"w");
        foreach($this->data as $ldata){
            $ann=[];
            foreach($ldata['frags'] as $a)$ann[]=implode(" ",$a);
            $ann=implode(";",$ann);
            fwrite($fout,"${ldata['id']}\t${ldata['type']} $ann\t${ldata['text']}\n");
        }
        fclose($fout);
        
        file_put_contents($this->path.".log",date("Ymd His")."\n",FILE_APPEND);

        file_put_contents(dirname($this->path)."/../changed_standoff.json","{\"changed\":".time()."}");        
    }
    
    public function getForBrat(){
        $ret=[];
        foreach($this->data as $ldata){
             $ret[]=[$ldata['id'],$ldata['type'],$ldata['frags']];
        }
        return $ret;
    }
    
    public function deleteById($id){
        $found=false;
        foreach($this->data as $k=>$ldata){
            if($ldata['id']==$id){$found=$k;break;}
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

        $textann="";
        for($i=0;$i<count($offsets);$i++){
            $textann.=mb_substr($text,$offsets[$i][0],($offsets[$i][1]-$offsets[$i][0]));
        }        
        $this->data[]=['id'=>$newid,'type'=>$type,'frags'=>$offsets,'text'=>$textann];
    }

}

?>