#!/bin/bash

fileDir=$(dirname "$0")
rootDir=$(realpath "$(dirname "$0")/../..")

cd "$fileDir"

source "$rootDir/dev/repositories/include/config.sh"

mkdir -p "$rootDir/dev"
mkdir -p "$rootDir/dev/repositories"
mkdir -p "$rootDir/dev/repositories/git"

for repo in "${REPOSITORIES[@]}"; do
  # Создаем директорию
  mkdir -p "$rootDir/dev/repositories/git/$repo"

  # Клонируем репозиторий
  git clone "git@github.com:tereta-library/$repo.git" "$rootDir/dev/repositories/git/$repo"

  # Переходим в директорию и делаем pull
  cd "$rootDir/dev/repositories/git/$repo"
  git pull origin master
done

cd "$fileDir"
