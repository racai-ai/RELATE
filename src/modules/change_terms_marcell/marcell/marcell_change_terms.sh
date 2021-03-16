#!/bin/bash

if [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ]; then

    echo "Syntax:"
    echo "   marcell_change_terms.sh <venv> <fnameIn> <fnameOut>"
    exit -1;

fi

source "$1/bin/activate"

python marcell_pipe_v3.py "$2" "$3" 
