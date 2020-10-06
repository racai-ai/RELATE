#!/usr/bin/python3

# convert MARCELL CONLL-U+ format into simple xml

# stdin to stdout

import sys, re
import xml.etree.ElementTree as et

def conllup2xml():
    header = None
    par_el = None
    text_el = et.Element('text')
    text_el.text = text_el.tail = '\n'
    for line in sys.stdin:
        l = line.strip()
        if l.startswith('# global.columns '):
            if header:
                raise ValueError('Double header line '+repr(l))
            header = line
            column_names = l.split('=',1)[1].strip().split()
            column_names = [x.replace(':', '_').lower() for x in column_names]
            continue
        if l.startswith('#') and '=' in l or l=='# newpar':
            tag = None
            if l=='# newpar':
                key, value = 'newpar', ''
                tag = 'newpar'
                attrib = None
            else:
                key, value = l.split('=', 1)
                key = re.sub('^# *', '', key.strip())
                value = value.strip()
            if ' ' in key:
                tag, attrib = key.split(None, 1)
                tag = tag.strip()
                attrib = attrib.strip()
            if tag:
                if attrib:
                    attrib_xml = {attrib:value}
                else:
                    attrib_xml = {}
                if tag=='newdoc':
                    last_el = doc_el = et.SubElement(text_el, 'doc', attrib=attrib_xml)
                    doc_el.text = doc_el.tail='\n'
                elif tag=='newpar':
                    last_el = par_el = et.SubElement(doc_el, 'p', attrib=attrib_xml)
                    par_el.text = par_el.tail='\n'
                else:
                    raise
            elif key == 'sent_id':
                if par_el is not None:
                    parent_el = par_el
                else:
                    parent_el = doc_el
                last_el = sent_el = et.SubElement(parent_el, 's', attrib={'id':value})
                sent_el.text = sent_el.tail='\n'
            else:
                last_el.attrib[key] = value

        elif l.startswith('#'): # a comment
            continue
        elif l=='':
            continue
        else:
            columns = l.split('\t')
            token_el = et.SubElement(sent_el, 'token')
            token_el.tail = '\n'
            for coltag, colval in zip(column_names, columns):
                col_el = et.SubElement(token_el, coltag)
                col_el.text = colval
    print('<?xml version="1.0" encoding="UTF-8" ?>')
    print(et.tostring(text_el, encoding='unicode'))


if __name__=='__main__':
    conllup2xml()

