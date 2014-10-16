git subsplit init git@github.com:orchestral/kernel.git
git subsplit publish --no-tags src/Contracts:git@github.com:orchestral/contracts.git
git subsplit publish --no-tags src/Http:git@github.com:orchestral/http.git
git subsplit publish --no-tags src/Routing:git@github.com:orchestral/routing.git
rm -rf .subsplit/
