#!/bin/bash

ROOTDIR=$(cd "$(dirname "$0")/../.." && pwd)

source "$ROOTDIR/dev/repositories/include/config.sh"
source "$ROOTDIR/dev/repositories/include/version.sh"

mkdir -p "$ROOTDIR/dev"
mkdir -p "$ROOTDIR/dev/repositories"
mkdir -p "$ROOTDIR/dev/repositories/git"

for REPO in "${REPOSITORIES[@]}"; do
  echo "-----$REPO-----"

  echo "cd \"$ROOTDIR/vendor/tereta/$REPO\"; git config core.sshCommand \"ssh -i ~/.ssh/personal.rsa\"" | bash
  echo "cd \"$ROOTDIR/vendor/tereta/$REPO\"; git config pull.rebase false" | bash
  echo "cd \"$ROOTDIR/vendor/tereta/$REPO\"; git pull" | bash
  echo "cd \"$ROOTDIR/vendor/tereta/$REPO\"; git add ." | bash
  echo "cd \"$ROOTDIR/vendor/tereta/$REPO\"; git commit -am \"$COMMENT\"; git push" | bash
  echo "cd \"$ROOTDIR/vendor/tereta/$REPO\"; git tag $VERSION; git push --tags" | bash
done
