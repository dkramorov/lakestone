#!/bin/sh
#

DIR=$(dirname $0)
if [ "${DIR}" = "." ]; then
  DIR=$(pwd)
fi

DIR=$(realpath "${DIR}")

DOC_ROOT="${DIR}/"
export DOC_ROOT

export REQUEST_URI='/index.php?route=tool/sync_integration&key=FwpEi6R2Rv0yQ2GeUJVd38r384R7VUv3'
export REQUEST_METHOD='GET'

cd ${DOC_ROOT}
php catalog.php
