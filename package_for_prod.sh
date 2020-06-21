#!/bin/bash

# Package the contents of wp-app, stripping the wp-app/ prefix from the archived filenames and excluding
# local-config.php and wp-config.php.
tar --create --file=public_html.tar --exclude local-config.php --exclude wp-config.php --transform=s,wp-app/,, wp-app

# Add wantsum brewery theme.
tar --append --file=public_html.tar --transform=s,wantsum-brewery-theme,wp-content/themes/wantsum-brewery, wantsum-brewery-theme

# Add prod environment specific version of wp-config.php to the archive.
tar --append --file=public_html.tar --transform=s,package/wantsumbrewery.co.uk/,, package/wantsumbrewery.co.uk
tar --delete --file=public_html.tar package

# Compress the archive
gzip public_html.tar
