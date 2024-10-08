#!/bin/bash

ROOTDIR=$(cd "$(dirname "$0")/../.." && pwd)

source "$ROOTDIR/dev/repositories/include/config.sh"

mkdir -p "$ROOTDIR/dev"
mkdir -p "$ROOTDIR/dev/repositories"
mkdir -p "$ROOTDIR/dev/repositories/git"

read -p "Did you make push before reinit? [Yes]: " AGREE

if [ "$AGREE" != "Yes" ]; then
  echo "You must make push before reinit"
  exit 1
fi

for REPO in "${REPOSITORIES[@]}"; do
  rm -Rf "$ROOTDIR/dev/repositories/git/$REPO"
  mkdir -p "$ROOTDIR/dev/repositories/git/$REPO"

  GIT_SSH_COMMAND="ssh -i ~/.ssh/tereta.alexander@gmail.com.rsa" git clone "git@github.com:tereta-library/$REPO.git" "$ROOTDIR/dev/repositories/git/$REPO"
  git config core.sshCommand "ssh -i ~/.ssh/builder.rsa"
  rm -Rf "$ROOTDIR/vendor/tereta/$REPO/.git"
  cp -R "$ROOTDIR/dev/repositories/git/$REPO/.git" "$ROOTDIR/vendor/tereta/$REPO/.git"

  echo "cd \"$ROOTDIR/dev/repositories/git/$REPO\"; git pull origin master" | bash
done
