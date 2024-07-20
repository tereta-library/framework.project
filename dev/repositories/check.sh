#!/bin/bash

fileDir=$(dirname "$0")
rootDir=$(realpath "$(dirname "$0")/../..")

cd "$fileDir"

source "$rootDir/dev/repositories/include/config.sh"

for repo in "${REPOSITORIES[@]}"; do
    cd "$rootDir/dev/repositories/git/$repo"
    git tag
done

cd "$fileDir"
