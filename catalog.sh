#!/bin/sh
#

DIR=$(dirname $0)
if [ "${DIR}" = "." ]; then
  DIR=$(pwd)
fi

DIR=$(realpath "${DIR}")

DOC_ROOT="${DIR}/"
export DOC_ROOT

export REQUEST_URI=$1
export REQUEST_METHOD='GET'

cd ${DOC_ROOT}
php catalog.php
