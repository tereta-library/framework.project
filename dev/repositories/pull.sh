#!/bin/bash

ROOTDIR=$(realpath "$(dirname "$0")/../..")

source "$ROOTDIR/dev/repositories/include/config.sh"
source "$ROOTDIR/dev/repositories/include/version.sh"

mkdir -p "$ROOTDIR/dev"
mkdir -p "$ROOTDIR/dev/repositories"
mkdir -p "$ROOTDIR/dev/repositories/git"

for REPO in "${REPOSITORIES[@]}"; do
  echo "-----$REPO-----"

  echo "cd \"$ROOTDIR/vendor/tereta/$REPO\"; git config pull.rebase false" | bash
  echo "cd \"$ROOTDIR/vendor/tereta/$REPO\"; git pull" | bash
done
