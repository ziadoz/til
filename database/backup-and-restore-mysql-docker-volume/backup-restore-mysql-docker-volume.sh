#!/usr/bin/env bash
# @see: https://docs.docker.com/storage/volumes/#backup-restore-or-migrate-data-volumes
# @see: https://jareklipski.medium.com/backup-restore-docker-named-volumes-350397b8e362
# @see: https://www.spherex.dev/backing-up-docker-volumes/

# The name of the volume to backup/restore:
VOLUME="my-db"

# Backup the Docker Volume to an archive:
docker run --rm --volume $VOLUME:/var/lib/mysql -v $(pwd):/backup alpine:latest ash -c "tar cvf /backup/$VOLUME.tar /var/lib/mysql"

# Restore the archive back to a Docker Volume:
docker run --rm -v $VOLUME:/var/lib/mysql -v $(pwd):/backup alpine:latest ash -c "cd /var/lib/mysql && tar xvf /backup/$VOLUME.tar --strip 3"