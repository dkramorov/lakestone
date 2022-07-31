#!/bin/sh
#
find . -type f -name \*.jpg -exec convert {} -sampling-factor 4:2:0 -strip -quality 85 -interlace JPEG -colorspace sRGB {} \;
#find . -type f -name \*.png -exec convert {} -strip -alpha off {} \;
