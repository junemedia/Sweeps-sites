# June Media Daily Sweepstakes


## Project Setup

1. Clone from GitHub

        git clone git@github.com:resolute/dailysweeps.git /srv/sites/dailysweeps

2. Create project config file from template and edit acordingly

        cd /srv/sites/dailysweeps/app/config
        cp project.php.template project.php

3. Create the database and import fixture data

        mysql -e 'DROP DATABASE IF EXISTS `dailysweeps`;
        CREATE DATABASE `dailysweeps`' && \
        time cat \
        doc/schema.sql \
        doc/views.sql \
        doc/reports.sql \
        doc/triggers.sql \
        doc/routines.sql \
        doc/fixture.sql \
        | mysql dailysweeps

4. Ensure that packages needed to run the build process are in place:

        yum install nodejs npm ruby ruby-devel
        npm install -g grunt-cli
        gem update --system
        gem install compass

5. Project should be accessible at http://betterrecipes.dailysweeps.ENVIRONMENT.resolute.com/


## URLs

| Production                                                     | Staging (white)                                                                                          |
|:---------------------------------------------------------------|---------------------------------------------------------------------------------------------------------:|
| [win.**betterrecipes**.com](http://win.betterrecipes.com/)     | [**betterrecipes**.dailysweeps.white.resolute.com](http://betterrecipes.dailysweeps.white.resolute.com/) |
| [win.**recipe4living**.com](http://win.recipe4living.com/)     | [**recipe4living**.dailysweeps.white.resolute.com](http://recipe4living.dailysweeps.white.resolute.com/) |


## SystemD (CentOS 7) Timers

    # symlink unit files and timers
    cd /etc/systemd/system && \
    ln -s /srv/sites/dailysweeps/etc/jds-reports.service && \
    ln -s /srv/sites/dailysweeps/etc/jds-reports.timer && \
    ln -s /srv/sites/dailysweeps/etc/jds-daily.service && \
    ln -s /srv/sites/dailysweeps/etc/jds-daily.timer && \
    ln -s /srv/sites/dailysweeps/etc/clean-tokens.service && \
    ln -s /srv/sites/dailysweeps/etc/clean-tokens.timer

    # install and enable timers
    systemctl enable jds-reports.timer && systemctl start jds-reports.timer
    systemctl enable jds-daily.timer && systemctl start jds-daily.timer
    systemctl enable clean-tokens.timer && systemctl start clean-tokens.timer


## Simple Templates

All template files `app/templates` are replaced by controllers using CI’s [parser](https://ellislab.com/codeigniter/user-guide/libraries/parser.html) library.


## System Generated Emails

Email templates are located in `app/templates` and are sent during the daily winner selection cron job.  **On staging environments, this daily winner selection cron job should be disabled OR at a minimum, the sending of email should be disabled.**

On the server, Postfix is configured to use JangoSMTP as the exclusive transport relay.  This means that in the app code, we simply use PHP’s native sendmail methods, and rely on postfix/sendmail to transport the message to JangoSMTP.  It is advised that SPF/DKIM records are created for each domain configured in `app/config/project.php`.  This will give the best chances of avoiding spam folders and hitting the winner’s inbox.


## Routing

There are three main components of this project (as defined in `app/config/routes.php`):

1. Frontend
2. Admin
3. Cron

All controllers are defined in `app/controllers/*`.  Both Frontend and Admin controllers use extended classes of the default CI_Controller, which are defined in `app/core/MY_Controller.php`.


## Solve Media

We use Solve Media as our captcha to deter against bot spam both for new signups as well as for daily entries.  In every session there exists an `is_human` timestamp, which once `human_ttl` has been reached (defined in `app/config/project.php`), the user must correctly solve a captcha in order to signup or enter a contest.


## Sessions

Sessions are provided by the default settings in `/etc/php.ini` and the Resolute Digital’s rewritten version of CI’s Session.php class.  In the php.ini, you’ll notice the `session_handler` is set to use memcache on port 11211.  It is critical that memcached is running in order for sessions to work.  This changes the native PHP `session_start()` behavior.

Additional performance enhancements revolve around thrifty garbage collection and the prevention of lingering empty sessions.  This logic is encapsulated in the Resolute Digital version of Session.php found in `/srv/lib/php/ci-2.1/libraries/Session.php`.


## Build Tools: grunt, compass

Please refer to the grunt configuration in `etc/gruntfile.js` and the compass configuration in `etc/config.rb`.  Grunt and compass are responsible for three things:

1. Generate an AMD-clean, asynchronous version of the project’s **JavaScript** for each site defined in `etc/gruntfile.js`;

    *Each site configuration may use any combination of source files.  Meaning, the JS for betterrecipes could be completely different than the JS for recipe4living.  It all depends on how you `define()` and link dependencies in `src/js`. At the time of this writing, both sites use generally the exact same JS, with the exception of some betterrecipes-specific JS code for the Better Recipes shell.*

2. Compile production ready CSS from source **SCSS** `src/scss` files into `web/css`;

3. Generate `etc/assets.json` so that PHP can reference the correct CSS/JS version.

## Routine Maintenance

We use [grunt-filerev](https://www.npmjs.com/package/grunt-filerev) and [grunt-filerev-assets](https://www.npmjs.com/package/grunt-filerev-assets) to manage the versioning of our CSS/JS files (look for config in `etc/gruntfile.js`).  Every time you make a change to any CSS/JS, new revisions are created (based on md5 of output) and placed in `web/css` and `web/js`, respectively.  Since we want to leave references to older versions, eventually the old versions become obsolete and may safely be deleted.

In a pinch, you can quickly remove these old compiled CSS/JS files with the following commands:

    find /srv/sites/dailysweeps/web/css -type f -name "*.css" -mtime +7 -exec git rm "{}" \;
    find /srv/sites/dailysweeps/web/js -type f -name "*.js" -mtime +7 -exec git rm "{}" \;

Then, just to make sure you haven't deleted assets currently being used:

    cd  /srv/sites/dailysweeps/etc && grunt build

Add any files that were inadvertently deleted with the `find` commands and commit/push.


## After Launch

1. Remove conditional on `app/controllers/main.php:77`.
2. Remove `temporarilyAddMeredithWinners()` method in `app/models/prizemodel.php:86-213`.
3. Update `getWinnersByDateRange()` to simply return $winners in `app/models/prizemodel.php:80-83`.
