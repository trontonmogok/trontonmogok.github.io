#!/bin/bash

> /w3g/tronton/todays_urls

query="select p.url, ' === ', p.title, ' === ', p.preview_image_url from moz_historyvisits as h, moz_places as p where substr(h.visit_date, 0, 11) >= strftime('%s', date('now')) and p.id == h.place_id order by h.visit_date;"

find ${HOME}/.mozilla/firefox/ -name 'places.sqlite' -print0 | \
while IFS= read -r -d '' adb; do
	printf "=--- $adb \n"
	cp $adb /tmp/temp.sqlite
	urls=$(sqlite3 "/tmp/temp.sqlite" "${query}")
	echo "${urls}" >> /w3g/tronton/todays_urls
done

sed -i 's/https:/http:/g' /w3g/tronton/todays_urls
sed -i 's!/www\.!/!g' /w3g/tronton/todays_urls
sed -i 's!client=firefox-b-d\&!!g' /w3g/tronton/todays_urls
cat /w3g/tronton/todays_urls | sort -u > /tmp/aa
mv /tmp/aa /w3g/tronton/todays_urls

find /w3g/tronton -iname "bookmarks-*.html" -type f -mtime +30
