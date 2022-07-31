#!/bin/sh
#

for file in $(find catalog/_video -name \*.mp4); do
	echo ${file}
	DIR=$(dirname ${file})
	FILENAME=$(basename ${file})
	if [ -z "${DIR}" -o -z "${FILENAME}"]; then
		echo "We had a problem with file: ${file}, skipped"
		continue
	fi
	ffmpeg -i "${file}" -vf "select=eq(n\,0)" -q:v 1 "${file}.jpg"
done

#ffmpeg -i video.mp4 -vf "select=eq(n\,0)" -q:v 1 poster.jpg
