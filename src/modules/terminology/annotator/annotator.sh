#!/bin/bash

if [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ] || [ -z "$4" ] || [ -z "$5" ] || [ -z "$6" ]; then

    echo "Syntax:"
    echo "   annotator.sh <venv> <terminology> <max> <column_name> <fileIn> <fileOut>"
    exit -1;

fi

source "$1/bin/activate"

python annotate.py "--lemma_path=tbl.wordform.ro" "--terminology_path=$2" "--max_terminology_words=$3" "--column_name=$4" "$5" "$6"
