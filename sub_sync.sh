#!/bin/sh
#

DIR=$(dirname $0)
if [ "${DIR}" = "." ]; then
        DIR=$(pwd)
fi

DIR=$(realpath "${DIR}")

DOC_ROOT="${DIR}/" 
export DOC_ROOT

if [ "$1" = "--full" ]; then
	export REQUEST_URI='/admin/index.php?route=tool/sub_sync&key=f0EuzCTJ5RTQ0RQMSWuIVQHVGA1V0pRMN04GVR0cTteUziCe&full'
else
	export REQUEST_URI='/admin/index.php?route=tool/sub_sync&key=f0EuzCTJ5RTQ0RQMSWuIVQHVGA1V0pRMN04GVR0cTteUziCe'
fi

export REQUEST_METHOD='GET'

cd ${DOC_ROOT}
php admin.php
