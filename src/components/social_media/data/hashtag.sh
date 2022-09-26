#!/bin/bash

if [ -z "$1" ] || [ -z "$2" ]; then

    echo "Syntax:"
    echo "   marcell_xml_all.sh <venv> <hashtag>"
    exit -1;

fi

source "$1/bin/activate"

python hashtags.py --frequency_file /data/RELATE/resources/microblogging/word_freq_corola_ro.tsv --remove_diacritics Yes --lower Yes --hashtag "$2"
