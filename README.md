# Wantsum Brewery -  WordPress Docker Compose

Tools for local development of the Wantsum Brewery Website.

Based on https://github.com/nezhar/wordpress-docker-compose

Based on retrieved copies of the Wantsum Brewery website files and database, this docker-compose project can be used
to work on the website's custom theme and plugin, while using the latest wordpress files (including plugins) as used on the 
production site.

## Requirements

Docker and Docker Compose must be installed.

## Usage

Download the SQL dump of the live wordpress database and place in the wp-data directory.

Download the website files and place in the wp-app directory.

To run the website:

```
docker-compose up
```

To stop the website:

```
docker-compose down
```

To remove the volumes associated with the project:

```
docker-compose down -v
```

## Changes to the upstream project (https://github.com/nezhar/wordpress-docker-compose)

The Wantsum Brewery website uses HTTPS. The wordpress image used by this project uses apache, but doesn't include
any SSL certificates. To work around this certificates will be installed at start up based on the technique described by
https://github.com/ogierschelvis in PR https://github.com/docker-library/wordpress/issues/46#issuecomment-358266189

When starting the wordpress container docker has been configured to run a bespoke script file, wp-init.sh, which installs
the certificates before launching apache.

## Fixing data

The database dump placed in wp-data will contain references to the live https://wantsumbrewery.co.uk website.

For development we want to modify that data to use references to localhost.

When the mysql container starts for the first time it will look for files in /docker-entrypoint-initdb.d, which is 
bind mounted from the wp-data directory. Any .sh files are treated as scripts and sourced. Any .sql files are executed
against the configured database.

A new script, 01-replace-urls.sh, has been added to wp-data to replace https://wantsumbrewery.co.uk in any .sql
files with http://localhsot.

By prefixing the script with 01 we should ensure it is run before the .sql file is imported into the database.

## Loading data into an existing db container

### Remove existing data:
* Ensure db container is not running
* docker-compose run --no-deps db sh -c 'rm -r /var/lib/mysql/*'

### Load data into existing database - TODO
If the db container already exists, it is possible to import a database dump using something similar to the following:

* Place the SQL file in wp-data.
* Run any transformation scripts needed on the SQL file - e.g. run wp-data/01-replace-urls
* Bring up the db service:
** docker-compose up db
* Once the db service is running, import the dump:
** TODO - need variant of command to specify target database - docker-compose exec db sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD"' < wp-data/dump.sql