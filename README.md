XMySQL
======

Set of CLI tools for managing multiple mysql instances / clusters.

## Installation:

```sh
$ git clone ...
$ cd xmysql
$ composer install
$ cp xmysql.yaml.dist xmysql.yaml
```

## Usage

```sh
./bin/xmysql show # output the parsed configuration, server details and detected databases
./bin/xmysql backup # loop over all servers and all (non-excluded) databases, and create a mysqldump (gzipped) into the configured target backup directory
```

## Roadmap / future enhancement ideas

* [ ] detect identically named databases across servers
* [ ] upload mechanism for external backups (or use something like rclone in tandem)
* [ ] setup and/or restore mysql replication
* [ ] (re)build a slave using mariabackup xstream / nc
* [ ] migrate databases between servers

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).
By the way, we're hiring!
