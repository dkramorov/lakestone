#!/bin/sh
#

DIR=$(dirname $0)
if [ "${DIR}" = "." ]; then
	DIR=$(pwd)
fi

DIR=$(realpath "${DIR}")

DOC_ROOT="${DIR}/"
export DOC_ROOT

export REQUEST_URI='/admin/index.php?route=tool/ozon/sync&key=GMbRZckWzLm4yjQEcPZ0fFSx1EKwwTV7'
export REQUEST_METHOD='GET'

cd ${DOC_ROOT}
php admin.php
