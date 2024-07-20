#!/bin/bash

fileDir=$(dirname "$0")
rootDir=$(realpath "$(dirname "$0")/../..")

cd "$fileDir"

source "$rootDir/dev/repositories/include/config.sh"
source "$rootDir/dev/repositories/include/version.sh"

for repo in "${REPOSITORIES[@]}"; do
    cd "$rootDir/dev/repositories/git/$repo"
    git tag $VERSION
    git push --tags
done

cd "$fileDir"
