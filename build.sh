#! /bin/sh
set -e

gcurrentbranch () {
    git branch 2> /dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/ \1/'
}
GITBRANCH=$(gcurrentbranch)
GITLASTCOMMITHASH=$(git rev-parse HEAD | cut -c1-8)
IT=${GITBRANCH:1}-$GITLASTCOMMITHASH
#IMAGETAG="${IT//\//_}"
IMAGETAG="latest"


echo "Starting build image flash-app:$IMAGETAG ..."
docker build . -f ./devops/Dockerfile -t harbor.deligram.com/flash-admin/flash-app:$IMAGETAG

echo "Pushing image to harbor.deligram.com/flash-admin/flash-app:$IMAGETAG ..."
docker push harbor.deligram.com/flash-admin/flash-app:$IMAGETAG

echo "Removing harbor.deligram.com/flash-admin/flash-app:$IMAGETAG ..."
#docker rmi harbor.deligram.com/flash/flash-app:$IMAGETAG


echo "\nImage $IMAGETAG Build Completed for kaiju!"

