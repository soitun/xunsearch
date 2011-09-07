#!/bin/sh
# Generate a constant definition file for PHP
# INPUT FILES: $1, ../config.h
# $Id: $ 

# check argument
if test -z $1; then
  echo "USAGE: $0 <C define file>" 1>&2
  exit -1
fi

# check file exists or not
if ! test -f $1; then
  echo "ERROR: input file not found: $1" 1>&2
  exit -1
fi

echo "<?php"
echo "/* Automatically generated from <$1> in "`date +"%Y/%m/%d %H:%M" `" */"
grep "^#define[	 ]CMD_" $1 | grep -v "[\"(]" | awk '{ print "define(\047" $2 "\047,\t" $3 ");" }'
if test -f ../config.h ; then
  grep "PACKAGE_" ../config.h | grep -v "STRING" | awk '{ print "define(\047" $2 "\047,\t" $3 ");" }'
fi
echo "/* end the cmd defination */"
echo
