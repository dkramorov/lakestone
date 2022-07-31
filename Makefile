all: styles less js jsl

styles: catalog/view/theme/lakestone/stylesheet/stylesheet.min.css

#js: catalog/view/javascript/lazyload.min.js catalog/view/javascript/locality.min.js catalog/view/javascript/markerclusterer.min.js catalog/view/theme/lakestone/js/common.min.js catalog/view/theme/lakestone/js/simplebar.min.js

JS = catalog/view/javascript
JS_L = catalog/view/theme/lakestone/js
STYLES = catalog/view/theme/lakestone/stylesheet
LESS := catalog/view/javascript/bootstrap/less/custom.min.css $(wildcard $(STYLES)/*.less)
CSS := $(patsubst %.less,%.min.css,$(LESS))
YUI-COMPRESSOR = /home/mic/bin/yui-compressor
js := $(patsubst %.js,%.min.js,$(patsubst %.min.js,,$(wildcard $(JS)/*.js)))
jsl := $(patsubst %.js,%.min.js,$(patsubst %.min.js,,$(wildcard $(JS_L)/*.js)))

js := $(js) catalog/view/javascript/bootstrap/js/bootstrap.min.js

t := $(patsubst %.js,%.min.js,$(patsubst %.min.js,,$(wildcard $(JS)/*.js)))

$(STYLES)/my_variables.min.css: $(STYLES)/my_variables.less
	touch $(LESS)
	touch $@

catalog/view/javascript/bootstrap/less/custom.min.css: catalog/view/javascript/bootstrap/less/custom.less
	touch $(LESS)
	touch $@

catalog/view/theme/lakestone/stylesheet/category.min.css: catalog/view/theme/lakestone/stylesheet/product_cssgrid.less catalog/view/theme/lakestone/stylesheet/category.less
	lessc -x catalog/view/theme/lakestone/stylesheet/category.less > $@ || rm -f $@
#	lessc -x --source-map=$@.map catalog/view/theme/lakestone/stylesheet/category.less > $@ || rm -f $@
#	lessc -x $? > $@ || rm -f $@

$(STYLES)/%.min.css: $(STYLES)/%.less
#	lessc -x --source-map=$@.map $? > $@ || rm -f $@
#	lessc -x --source-map $? > $@ || rm -f $@
	lessc -x $? > $@ || rm -f $@

less: $(CSS)

js: $(js)

jsl: $(jsl)

t: $(t)
	echo "RESULT:\n$?"

catalog/view/theme/lakestone/stylesheet/stylesheet.min.css: catalog/view/theme/lakestone/stylesheet/stylesheet.css
#	$(YUI-COMPRESSOR) catalog/view/theme/lakestone/stylesheet/stylesheet.css > catalog/view/theme/lakestone/stylesheet/stylesheet.min.css
	$(YUI-COMPRESSOR) $? -m $@.map > $@

$(JS)/%.min.js: $(JS)/%.js
	$(YUI-COMPRESSOR) $? -m $@.map > $@

$(JS_L)/%.min.js: $(JS_L)/%.js
	$(YUI-COMPRESSOR) $? -m $@.map > $@
