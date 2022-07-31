#!/bin/sh
#

DIR=$(dirname $0)
if [ "${DIR}" = "." ]; then
	DIR=$(pwd)
fi

DIR=$(realpath "${DIR}")

DOC_ROOT="${DIR}/"
export DOC_ROOT

export REQUEST_URI='/admin/index.php?route=tool/kupivip&key=ib0MZr41nFx1IFRCmWtVUBUZhfHCpyZw&call=sync'
export REQUEST_METHOD='GET'

cd ${DOC_ROOT}
php admin.php
