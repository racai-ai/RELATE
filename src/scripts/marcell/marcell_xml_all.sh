#!/bin/bash

if [ -z "$1" ] || [ -z "$2" ]; then

    echo "Syntax:"
    echo "   marcell_xml_all.sh <venv> <corpus>"
    exit -1;

fi

source $1/bin/activate
mkdir -p $2/marcell-xml

find $2/basic_tagging -name '*conllup' | parallel --citation -j 20 "python conllup2xml.py < {} > $2/ marcell-xml/\$(basename {}).xml"
