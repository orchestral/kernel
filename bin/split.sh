#!/usr/bin/env bash

set -e
set -x

CURRENT_BRANCH="4.x"

function split()
{
    SHA1=`splitsh-lite --prefix=$1`
    git push $2 "$SHA1:refs/heads/$CURRENT_BRANCH" -f
}

function add_remote()
{
    git remote add $1 $2 || true
}

function remove_remote()
{
    git remote remove $1 || true
}

git pull origin $CURRENT_BRANCH

add_remote config git@github.com:orchestral/config.git
add_remote database git@github.com:orchestral/database.git
add_remote hashing git@github.com:orchestral/hashing.git
add_remote http git@github.com:orchestral/http.git
add_remote notifications git@github.com:orchestral/notifications.git
add_remote routing git@github.com:orchestral/routing.git

split 'src/Config' config
split 'src/Database' database
split 'src/Hashing' hashing
split 'src/Http' http
split 'src/Notifications' notifications
split 'src/Routing' routing

remove_remote config
remove_remote database
remove_remote hashing
remove_remote http
remove_remote notifications
remove_remote routing
