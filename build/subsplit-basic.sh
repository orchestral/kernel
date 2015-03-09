#!/bin/sh

if [ -d .subsplit ]; then
    git subsplit update
else
    git subsplit init git@github.com:orchestral/kernel.git
fi

git subsplit publish --no-tags src/Config:git@github.com:orchestral/config.git
git subsplit publish --no-tags src/Contracts:git@github.com:orchestral/contracts.git
git subsplit publish --no-tags src/Database:git@github.com:orchestral/database.git
git subsplit publish --no-tags src/Http:git@github.com:orchestral/http.git
git subsplit publish --no-tags src/Routing:git@github.com:orchestral/routing.git
