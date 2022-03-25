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
       
        $map=[];
        
        foreach(explode("\n",file_get_contents($this->path)) as $line){
            $ldata=explode("\t",$line);
            if(count($ldata)!==3)continue;

            if($ldata[0][0]=="#"){ // comment (note)
                $frags=explode(" ",$ldata[1],2);
                $id=$frags[1];
                if(isset($map[$id]) && isset($this->data[$map[$id]]))
                    $this->data[$map[$id]]['comment']=$ldata[2];
            }else{
                $frags=explode(" ",$ldata[1],2);
                $type=$frags[0];
                $frags=explode(";",$frags[1]);
                $fragArr=[];
                foreach($frags as $v)$fragArr[]=explode(" ",$v);
                
                $map[$ldata[0]]=count($this->data);
                $this->data[]=["id"=>$ldata[0], "type"=>$type, "frags"=>$fragArr,"text"=>$ldata[2]];
            }            
        }
    }
    
    public function save(){
        $fout=fopen($this->path,"w");
        $comments=[];
        foreach($this->data as $ldata){
            $ann=[];
            foreach($ldata['frags'] as $a)$ann[]=implode(" ",$a);
            $ann=implode(";",$ann);
            fwrite($fout,"${ldata['id']}\t${ldata['type']} $ann\t${ldata['text']}\n");
            if(isset($ldata['comment']) && strlen($ldata['comment'])>0){
                $comments[]=["id"=>$ldata['id'],"comment"=>$ldata['comment']];
            }
        }
        
        $cid=0;
        foreach($comments as $comm){
            $cid++;
            fwrite($fout,"#$cid\tAnnotatorNotes ${comm['id']}\t${comm['comment']}\n");
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
    
    public function getCommentsForBrat(){
        $ret=[];
        foreach($this->data as $ldata){
             if(isset($ldata['comment']) && strlen($ldata['comment'])>0)
                $ret[]=[$ldata['id'],"AnnotatorNotes",$ldata['comment']];
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
    
    public function addAnnotation($type,$offsets,$text,$comment){
        $newid="";
        if(empty($this->data)){
            $newid="T1";
        }else{
            $oldid=$this->data[count($this->data)-1]['id'];
            $oldid=intval(substr($oldid,1));
            $newid="T".($oldid+1);
        }

        $textann="";
        for($i=0;$i<count($offsets);$i++){
            $textann.=mb_substr($text,$offsets[$i][0],($offsets[$i][1]-$offsets[$i][0]));
        }        
        $this->data[]=['id'=>$newid,'type'=>$type,'frags'=>$offsets,'text'=>$textann,'comment'=>$comment];
    }

}

?>