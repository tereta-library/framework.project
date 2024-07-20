#!/bin/bash

ROOTDIR=$(realpath "$(dirname "$0")/../..")

source "$ROOTDIR/dev/repositories/include/config.sh"

for REPO in "${REPOSITORIES[@]}"; do
  echo "+ $REPO"
  echo "cd \"$ROOTDIR/dev/repositories/git/$REPO\"; git tag" | bash
  echo "- $REPO"
done
