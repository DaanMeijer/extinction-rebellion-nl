## Docker setup

**Requirements:**
- [Docker](https://www.docker.com/products/docker-desktop)
- [Node.js](https://nodejs.org/en/)

Run
```sh
cp .env.example .env
# copy commercial plugins to web/app/plugins directory @todo
docker-compose up -d
# docker-compose logs -f # to view the logs if you want
# You may see that in the logs, "sh: npm: not found" this is because
# in the composer image there's no npm or nodejs
cd web/app/themes/xrnl
npm i
npm run watch # compiles scss files as you make changes
```

## Sync production data

1. Visit http://localhost:8000
2. Setup wordpress and login
3. Navigate to **Plugins** page (http://localhost:8000/wp/wp-admin/plugins.php)
4. Activate **WP Sync DB** and **WP Sync DB Media Files** plugins
5. Get connection info to sync the production database by logging in to extinctionrebellion.nl/wp/wp-admin/. To get the connection string:
    - Go to _Tools > MigrateDB_ and
    - Select the _Settings_ tab.
    - Copy the connection string. It should look like `https://extinctionrebellion.nl/wp{{HASH}}` (contact the **Tech team** if you don't have a login)
6. Navigate to _Tools > MigrateDB_ (http://localhost:8000/wp/wp-admin/tools.php?page=wp-sync-db) on your **local instance**, select _pull_ and enter the connection string you got in the previous step.
    Replace:
    - `//extinctionrebellion.nl` => `//localhost:8000`
    - `/var/www/extinction-rebellion-nl/web` => `/var/www/html/web`
    - `extinction-rebellion-nl.daan-mac` => `localhost:8000`

    and select "Media Files" checkbox at the bottom and hit "Migrate DB" button. It will take a minute or so and then you will be prompted to login.
7. Use your extinctionrebellion.nl login to login at http://localhost:8000/wp/wp-admin

---

# [Bedrock](https://roots.io/bedrock/)
[![Packagist](https://img.shields.io/packagist/v/roots/bedrock.svg?style=flat-square)](https://packagist.org/packages/roots/bedrock)
[![Build Status](https://img.shields.io/travis/roots/bedrock.svg?style=flat-square)](https://travis-ci.org/roots/bedrock)

Bedrock is a modern WordPress stack that helps you get started with the best development tools and project structure.

Much of the philosophy behind Bedrock is inspired by the [Twelve-Factor App](http://12factor.net/) methodology including the [WordPress specific version](https://roots.io/twelve-factor-wordpress/).

## Features

* Better folder structure
* Dependency management with [Composer](https://getcomposer.org)
* Easy WordPress configuration with environment specific files
* Environment variables with [Dotenv](https://github.com/vlucas/phpdotenv)
* Autoloader for mu-plugins (use regular plugins as mu-plugins)
* Enhanced security (separated web root and secure passwords with [wp-password-bcrypt](https://github.com/roots/wp-password-bcrypt))

## Requirements

* PHP >= 7.1
* Composer - [Install](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

## Installation

1. Create a new project:
    ```sh
    $ composer create-project roots/bedrock
    ```
2. Update environment variables in the `.env` file:
  * Database variables
    * `DB_NAME` - Database name
    * `DB_USER` - Database user
    * `DB_PASSWORD` - Database password
    * `DB_HOST` - Database host
    * You can also use a variable `DATABASE_URL` instead of using all the database variables above, that contains a DSN (ex: `mysql://user:password@127.0.0.1:3306/db_name`)
  * `WP_ENV` - Set to environment (`development`, `staging`, `production`)
  * `WP_HOME` - Full URL to WordPress home (https://example.com)
  * `WP_SITEURL` - Full URL to WordPress including subdirectory (https://example.com/wp)
  * `AUTH_KEY`, `SECURE_AUTH_KEY`, `LOGGED_IN_KEY`, `NONCE_KEY`, `AUTH_SALT`, `SECURE_AUTH_SALT`, `LOGGED_IN_SALT`, `NONCE_SALT`
    * Generate with [wp-cli-dotenv-command](https://github.com/aaemnnosttv/wp-cli-dotenv-command)
    * Generate with [our WordPress salts generator](https://roots.io/salts.html)
3. Add theme(s) in `web/app/themes/` as you would for a normal WordPress site
4. Set the document root on your webserver to Bedrock's `web` folder: `/path/to/site/web/`
5. Access WordPress admin at `https://example.com/wp/wp-admin/`

## Documentation

Bedrock documentation is available at [https://roots.io/bedrock/docs/](https://roots.io/bedrock/docs/).

## Contributing

Contributions are welcome from everyone. We have [contributing guidelines](https://github.com/roots/guidelines/blob/master/CONTRIBUTING.md) to help you get started.

## Bedrock sponsors

Help support our open-source development efforts by [becoming a patron](https://www.patreon.com/rootsdev).

<a href="https://kinsta.com/?kaid=OFDHAJIXUDIV"><img src="https://cdn.roots.io/app/uploads/kinsta.svg" alt="Kinsta" width="200" height="150"></a> <a href="https://k-m.com/"><img src="https://cdn.roots.io/app/uploads/km-digital.svg" alt="KM Digital" width="200" height="150"></a> <a href="https://www.itineris.co.uk/"><img src="https://cdn.roots.io/app/uploads/itineris.svg" alt="itineris" width="200" height="150"></a>

## Community

Keep track of development and community news.

* Participate on the [Roots Discourse](https://discourse.roots.io/)
* Follow [@rootswp on Twitter](https://twitter.com/rootswp)
* Read and subscribe to the [Roots Blog](https://roots.io/blog/)
* Subscribe to the [Roots Newsletter](https://roots.io/subscribe/)
* Listen to the [Roots Radio podcast](https://roots.io/podcast/)
