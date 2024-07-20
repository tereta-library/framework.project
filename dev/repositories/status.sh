#!/bin/bash

ROOTDIR=$(realpath "$(dirname "$0")/../..")

source "$ROOTDIR/dev/repositories/include/config.sh"

for REPO in "${REPOSITORIES[@]}"; do
  echo "+ $REPO"
  echo "cd \"$ROOTDIR/vendor/tereta/$REPO\"; git status" | bash
  echo "- $REPO"
done
