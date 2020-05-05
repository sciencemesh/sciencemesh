# Dockerfile -- Database for GOCDB based on MariaDB5
#
# Author:   Daniel Mueller/University of Muenster (daniel.mueller@uni-muenster.de)
# Date:     2020-04-23
# Version:  1.0
FROM    mariadb:5
LABEL   maintainer "daniel.mueller@uni-muenster.de"
LABEL   description "Database container for GOCDB"

# All we need to do is to copy the initialization scripts to the correct container directory
COPY    sql/ /docker-entrypoint-initdb.d
