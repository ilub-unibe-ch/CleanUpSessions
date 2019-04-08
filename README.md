### Description CleanUpSessions Plugin
This Plugin will delete anonymous user sessions after a certain time interval, which can be specified in the Plugin.

## Installation

### Install CleanUpSessions plugin
Start at your ILIAS root directory 

```
mkdir -p Customizing/global/plugins/Services/Cron/CronHook
cd Customizing/global/plugins/Services/Cron/CronHook
git clone git@github.com:okaufman/CleanUpSessions.git
```

### Install dependencies via composer
```
cd CleanUpSessions
composer install
```

If you run composer from vagrant box, remember to run it as user `www-data`.
```
sudo -u www-data composer install
```

### Activate Cron Jobs
This is a Cron Plugin so in order for it to work Cron-jobs need to be activated. This can be done in the folder /etc/cron.d
create a file named ilias in /etc/cron.d with the following content

```
*/15 * * * * www-data /usr/bin/php ILIAS_PATH_ABSOLUTE/cron/cron.php ADMIN-USER ADMIN-USER-PWD ILIAS_CLIENT_ID
```
 Using this configuration, cron jobs will be executed every 15 minutes.
 For more Information visit this link: https://docu.ilias.de/goto_docu_pg_8240_367.html
 
### Configure Cron Job in ILIAS Administration
All the Cron Jobs can be found under Administration->General Settings->Cron Jobs.
Look for pl__CleanUpSession. Make sure that this Cron Job is activated.
Edit the configuration and decide how often the CleanUpSessions-Cron-Job will be executed.


### Configure a max time interval for anonymous Sessions to be deleted
Then the Plugin can then be installed and activated in the ILIAS Administration under Plugins.
In the Plugin configuration: set an interval after which anonymous sessions will be deleted



