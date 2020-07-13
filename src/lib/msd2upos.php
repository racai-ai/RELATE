<?php

function MSD2UPOS($msd){

  $punct=array_flip([ "COMMA",
    "PERIOD",
    "EXCL",
    "QUEST",
    "QUESTHELLIP",
    "EXCLHELLIP",
    "HELLIP",
    "DASH",
    "COLON",
    "SCOLON",
    "DBLQ",
    "LPAR",
    "RPAR",
    "LSQR",
    "RSQR",
    "LCURL",
    "RCURL",
    "QUOT",
    "BQUOT",
    "BULLET"]);
    
  $sym=array_flip(["SLASH",
    "BSLASH",
    "AMPER",
    "EQUAL",
    "PLUS",
    "STAR",
    "STAR2",
    "STAR3",
    "TILDA",
    "UNDERSC",
    "POUND",
    "GT",
    "LT",
    "GE",
    "LE",
    "DOLLAR",
    "PLUSMINUS",
    "PERCENT",
    "CAP",
    "OR",
    "EURO",
    "ARROW"]);
  
  if(isset($punct[$msd]))return "PUNCT";
  if(isset($sym[$msd]))return "SYM";
  
  if(startsWith($msd,"Np"))return "PROPN";
  if(startsWith($msd,["Nc","Yn","Etd","Ed","Eqy"]))return "NOUN";
  if(startsWith($msd,["M","En","Eii"]))return "NUM";
  if(startsWith($msd,["A","Ya"]))return "ADJ";
  if(startsWith($msd,["R","Yr"]))return "ADV";
  if(startsWith($msd,"I"))return "INTJ";
  if(startsWith($msd,"S"))return "ADP";
  if(startsWith($msd,["Cc","Cr"]))return "CONJ";
  if(startsWith($msd,"Cs"))return "SCONJ";
  if(startsWith($msd,["D","T"]))return "DET";
  if(startsWith($msd,["P","Yp"]))return "PRON";
  if(startsWith($msd,["Vm","Yv"]))return "VERB";
  if(startsWith($msd,"Va"))return "AUX";
  if(startsWith($msd,"Q"))return "PART";
  
  return "X";
}
