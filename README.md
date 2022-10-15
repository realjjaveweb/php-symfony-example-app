# The App
This app runs through a Docker-ized environment using following:
- Apache as a webserver
- PHP8.1 as the backend language interpreter for PHP
- MariaDB as an SQL database

## Prerequisites

### OS
Although this app could be ran on Windows/Mac, Linux(Ubuntu-based) is recommend and is assumed by this README.
Windows note: On Windows, you can use "Windows Subsystem for Linux" WSL/WSL2 with Linux(Ubuntu or compatible)

### Make/Makefile
- `sudo apt install make`

### Docker & Docker Compose
You can either use Docker Desktop https://docs.docker.com/desktop/install/linux-install/,
(fees may apply, but to quote docs.docker.com, "If you have Docker Desktop, you’ve got a full Docker installation, including Compose.")

or you can install them manually:
- install Docker https://docs.docker.com/engine/install/ubuntu/ (it's not just <del>`sudo apt install docker-ce`</del>)
- install Docker Compose: `sudo apt install docker-compose`
- To run the Docker stuff without `sudo`:
   - https://docs.docker.com/engine/install/linux-postinstall/
   - Add a new group called "docker" `sudo groupadd docker`
   - Add the current user to the new group `sudo usermod -aG docker $USER`
   - Activate the group changes - log out and log in

## Run the app
1. clone the repository
2. `cd` into the project root
3. create `.env` file as a copy of `.env.example` and adjust DB container crenditials in `.env` if you need
4. `make up` (`docker-compose up -d`)
  - 4b first time - `make install` (if you don't like make - `docker-compose exec api composer install`)
5. Open http://localhost:9000 in a modern browser

## Models & Migrations
In the PHP container shell:
1. create entity `bin/console make:entity` and follow instructions
2. create migration `bin/console make:migration`
3. run the migrations `bin/console doctrine:migrations:migrate`

**User** is a special case in it's nature and it's recommended to create it with the dedicated command:
`bin/console make:user` - follow instructions and say `yes` to hash/check passwords.

To add additional fields to User use `bin/console make:entity` and just enter the `User` class name you've entered above.

## Database admin
This project uses Adminer, it's running in the `adminer` container and can be accessed at:

http://localhost:8080/ (use host `db` and credentials from .env)

## Troubleshooting
- **Not seeing the app** at all or a different app - make sure you're not running anything (like a webserver) on the port 9000
- "Temporary failure resolving ..." - try restarting the docker service (`sudo service docker restart`)
- DB & Adminer container are being restarted automatically immediately, however the api/PHP container has to be (re)started manually
### Logging
Default logger stores log records/messages into (app root's) `./var/log/X.log` where `X` is the environent.
<br>So for example for the `dev` environment, you will be looking for `./var/log/dev.log`

If you have the `tail` command available (mainly Linux), you can watch the logs live running following in app's root:
<br>`tail --follow ./var/log/dev.log`

## Tools
Install all predefined tools with: `make tools_install`
### PHP Formatting - PHP-CS-Fixer
If you have it installed, you can also leverage some nice Make commands (or see the `Makefile` to find out what they do):
- `make format_dry_run` to see what needs formatting
- `make format_fix` to actually apply the formatting according to the `.php-cs` config
- The default `.php-cs` config used in these makes only formats files in the `src` directory

## Teardown
- stop & remove containers `make down`
- remove (all) unused images `docker system prune --all`
- <small>if you are **absolutely sure** you also want to delete docker volumes (data) `docker system prune --all --volumes`</small>

___
___
# And that's all - Have a great day! 🙂
