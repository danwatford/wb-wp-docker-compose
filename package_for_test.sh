#!/bin/bash

# Package the contents of wp-app, stripping the wp-app/ prefix from the archived filenames and excluding
# local-config.php and wp-config.php.
tar --create --file=public_html.tar --exclude local-config.php --exclude wp-config.php --transform=s,wp-app/,, wp-app

# Add test environment specific version of wp-config.php to the archive.
tar --append --file=public_html.tar --transform=s,package/wb.dev.watfordconsulting.com/,, package/wb.dev.watfordconsulting.com

# Compress the archive
gzip public_html.tar
