#!/bin/bash

if [ -z "$1" ] || [ -z "$2" ]; then

    echo "Syntax:"
    echo "   curlicat_xml_all.sh <venv> <corpus>"
    exit -1;

fi

source "$1/bin/activate"
rm -fr "$2/curlicat-xml"
mkdir -p "$2/curlicat-xml"


find "$2/basic_tagging" -name '*conllup' | parallel -j 20 "python conllup2xml.py < {} > \"$2/curlicat-xml/\$(basename {}).xml\""
