#!/bin/sh

if [ -d .subsplit ]; then
    git subsplit update
else
    git subsplit init git@github.com:orchestral/kernel.git
fi

git subsplit publish --heads="master" src/Config:git@github.com:orchestral/config.git
git subsplit publish --heads="master" src/Database:git@github.com:orchestral/database.git
git subsplit publish --heads="master" src/Http:git@github.com:orchestral/http.git
git subsplit publish --heads="master" src/Notifications:git@github.com:orchestral/notifications.git
git subsplit publish --heads="master" src/Routing:git@github.com:orchestral/routing.git
