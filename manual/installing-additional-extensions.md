Installing additional extensions
================================

meza comes pre-built with many extensions, but if you need additional extensions you can add them to any configuration file. The recommended method for adding extensions to all wikis is to use your "postLocalSettings_allWikis.php" file in `/opt/meza/config/local`. This file may not already exist, but if you add it meza will automatically start using it.

An example file is located at `/opt/meza/config/template/more-extensions.php`. This shows the following method to load extensions for all wikis or just for select wikis.
```
<?php
 #
 # Extension:WhateverExtension
 #
 require_once $egExtensionLoader->registerLegacyExtension(
    "WhateverExtension",
    "https://github.com/jamesmontalvo3/WhateverExtension.git",
    "master"
 );
 ```
*"WhateverExtenstion" is the name of the extension that you would like to install
*You can find the URL from the git link off of the relevant Mediawiki extension page for the extension you want to install
*"master" is the branch that you want to pull from the git link you entered. You may want to change this to the branch on git specific to the current version of your wiki. As of October 2016 the current version is Mediawiki 1.25. See Special:Version to determine the current version.


After you've moved this file into `postLocalSettings_allWikis.php`, or included it from `postLocalSettings_allWikis.php`, you need to perform the installation. To do that run 'updateExtensions.php' as shown below:

```
sudo WIKI=<wiki-id> php /opt/meza/htdocs/mediawiki/extensions/ExtensionLoader/updateExtensions.php
```

Replace `<wiki-id>` with any wiki ID. If the extensions you are installing require database updates (e.g. if their install instructions tell you to run `update.php`) then you will need to run `update.php` for **all wikis**. To do that, run the following:

```
sudo WIKI=<wiki-id> php /opt/meza/htdocs/mediawiki/maintenance/update.php
```

Do the command above for all wiki IDs.
