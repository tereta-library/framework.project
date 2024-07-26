#!/bin/bash

ROOTDIR=$(cd "$(dirname "$0")/../.." && pwd)

source "$ROOTDIR/dev/repositories/include/config.sh"

for REPO in "${REPOSITORIES[@]}"; do
  echo "+ $REPO"
  echo "cd \"$ROOTDIR/vendor/tereta/$REPO\"; git status" | bash
  echo "- $REPO"
done
