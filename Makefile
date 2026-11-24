zip-share:
	zip -r share.zip . -x "Makefile" -x "share.zip" -x "to_share/*" -x ".git/*" -x "backend/vendor/*" -x "frontend/node_modules/*" -x "frontend/dist/*"
	unzip -o share.zip -d to_share

clean:
	rm -rf share.zip to_share
