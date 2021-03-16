# -*- coding: utf-8 -*-
"""
Created on Wed Jul 29 10:25:29 2020

@author: Maria
"""

import os
import codecs
from collections import defaultdict as dd
import re
import sys



def rep2(buf,dl,ind):
    ll=list(dl.keys())
    ln=len(ll)
    for i in range(0,ln):
        tind=ll[i]
        fbuf=buf.replace(str(tind)+":",str(ind)+":")
        ind+=1
        buf=fbuf
    return buf,ind
    
def rep(ln,rl,kl):
    for r in rl:
        ln=ln.replace(r,'')
    for k in kl:
        ln=ln.replace(k,'')
    return ln
    

def cnt(lis):
    return len(lis)

def del_l(l):
    cd={}
    llen=len(l)
    nl=[]
    rnd={}
    cont=1
    for ri in l:
       for i in ri: 
         try:
             cd[i]=cd[i]+1
         except:
             cd[i]=1
         try:
             rnd[i]=rnd[i]+str(cont)
         except:
             rnd[i]=str(cont)
       cont+=1
    ocd={k: v for k, v in sorted(cd.items(), key=lambda item: item[1])}
    mx=list(ocd.values())
    mxk=list(ocd.keys())
    if llen<=2:
        
        for ki ,vi in cd.items():
            if (vi!=mx[-1]):
                nl.append(ki)
    else:
       for ki ,vi in cd.items():
           try:
            if ((vi!=mx[-1] and vi!=mx[-2]) or (vi==1 and rnd[ki][0] in rnd[mxk[-1]]) ):
                nl.append(ki)
           except:
               print(rnd)
               print(mxk)
               print('----------------------------------------------')
               break
 
    
    return nl

def mbuf(buf,l,l2):
    nbuf=''
    rl=del_l(l)
    #print((rl,l))
    kl=del_l(l2)
    #print((kl,l2))
    broken_buf=buf[:-1].split('\n')
    buft=''
    cont=0
    for line in broken_buf:
        buf2=line
        
        if(cnt(l[cont])>1):
                 
            nbuf=rep(buf2,rl,kl)
                
                
            buft=buft+nbuf+'\n'
            
        else:
            buft=buft+buf2+'\n'
        cont+=1
    buf=buft
    di=len(l)
    for i in range(0,di+1):
        buf=re.sub(';\n','\n',buf)
        buf=re.sub(';\t','\t',buf)
        buf=re.sub('\t;','\t',buf)
        buf=re.sub(';;',';',buf)
    buf=re.sub(';;;;;',';',buf)
    buf=re.sub(';;;;',';',buf)
    buf=re.sub(';;;',';',buf)
    buf=re.sub(';;',';',buf)
    return buf
    



            
if __name__ == "__main__":
    a=sys.argv[1:]
    
    
    if len(a)!=2:
        if len(a)==0:
            f="D:/uky/test/mj_00000G000BDEJN9GU4F0LCLLLIVK34GM.conllup"
            path_out="D:/uky/test/testout.conllup"
        else:    
            print("Must pass exactly two arguments for source file and target dir")
    else:
        f=a[0]
        path_out=a[1]
        
    i=0
    
    file=f
    fi = codecs.open(f, "r",encoding='utf-8')
    fl=fi.readlines()
    fdict=dd(int)
    intro=0
    buf=''
    text=''
    
    for lines in fl:
        line=lines.rstrip()
        ls=line.split('\t')
        
        if(len(ls)>13):
          if(ls[14]!='_' ):
            an=ls[14]
            an2=ls[12]
            intro+=1
            buf=buf+lines
            if (intro==1):
                l=[]
                l2=[]
                
            ans=an.split(";")
            ans2=an2.split(";")
            
            v=[]
            for  sp in ans:
              nr=sp
              v.append(nr)
              
            l.append(v)
            v2=[]
            for  sp2 in ans2:
              nr2=sp2

              v2.append(nr2)
              
            l2.append(v2)
                  
          else:
              if(intro==0):
                text=text+lines
              else:
                 text=text+mbuf(buf,l,l2)+lines
                 intro=0
                 buf=''
              
        else:
             if(intro==0):
                text=text+lines
             else:
                 text=text+mbuf(buf,l,l2)+lines
                 intro=0
                 buf=''
    fl=text.split("\n")
    text1=''
    for lines in fl:
        line=lines.rstrip()
        ls=line.split('\t')
        buf14='nimic'
        if(len(ls)>14):
          
          
          if(ls[14]!='_' ):
            nl='' 
            for j in range(0,12):
              nl=nl+ls[j]+'\t'
            an=ls[14]
            
            ans=an.split(";")
            ofc=len(ans)
            if (ofc>1 ):
                in14=0
                l12=''
                l14=''
                ls12=ls[12].split(";")
                for cod in ans:
                    cs=cod.split(":")[1]
                    if (in14==0):
                        buf14=cs
                        l14=ans[0]
                        l12=ls12[0]
                        in14+=1
                    else:
                        if(cs!=buf14):
                            l14=l14+";"+cod
                            l12=l12+";"+ls12[in14]
                        in14+=1
                addl=nl+l12+'\t'+"_"+"\t"+l14+'\n'
                            
                            
            else:
                addl=lines+'\n'
          else:
             addl=lines+"\n"
        else:
           addl=lines+"\n"
        
        text1=text1+addl
    ind=1
    intro=0
    text2=''
    kc=''
    buf=''
    ni=1
    l={}
    fl=text1.split("\n")
    for lines in fl:
        line=lines.rstrip()
        ls=line.split('\t')
        
        if(len(ls)>14):
          if(ls[14]!='_' ):      
             an=ls[14]
             ans=an.split(';')
             if( len(ans)==1):
            
                intro+=1
                buf=buf+lines+"\n"
                                             
                tind=an.split(':')[0]
                l[tind]=1
                        
             else:
                intro+=1
                buf=buf+lines+"\n"
            
                for c in ans:
                    tind=c.split(':')[0]
                    l[tind]=1
                             

                  
          else:
              if(intro==0):
                text2=text2+lines+"\n"
                
              else:
                 nbuf,ind=rep2(buf,l,ind)
                 text2=text2+nbuf+lines+"\n"
                 intro=0
                 buf=''
                 
                 tind=9999999
                 l={}
              
        else:
             if(intro==0):
                text2=text2+lines+"\n"
             else:
                 nbuf,ind=rep2(buf,l,ind)
                 text2=text2+nbuf+lines+"\n"
                 intro=0
                 buf=''
                 l={}
                 tind=9999999
             if line=='':
                 ind=1
              
    #fname=f.split("/")[-1]
    file =codecs.open(path_out, "w",encoding='utf-8')
    file.write(text2)
    file.close()    
    fi.close()
    
