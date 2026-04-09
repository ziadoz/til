# Docker
docker run --rm -it --network="host" --add-host host.docker.internal:host-gateway dimitri/pgloader:latest pgloader --verbose --debug --client-min-messages debug --log-min-messages debug mysql://<user>:<password>@127.0.0.1:3306/dbname postgresql://<user>:<password>@127.0.0.1:5432/dbname

# Brew
# @see: https://github.com/dimitri/pgloader/issues/962
brew install pgloader
pgloader --verbose --debug --client-min-messages debug --log-min-messages debug --dynamic-space-size 262144 -v pgloader.load

# Config (pgloader.load)
LOAD DATABASE
    FROM mysql://root:somepass@127.0.0.1:3306/fusions_local
    INTO postgresql://root:somepass@127.0.0.1:5432/fusions_local

WITH prefetch rows = 100,
    max parallel create index = 1

SET MySQL PARAMETERS
    net_read_timeout  = '3600',
    net_write_timeout = '3600';