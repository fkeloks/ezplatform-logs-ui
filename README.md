EzPlatformLogsUiBundle
============

Symfony bundle dedicated to eZ Platform, to add a log management interface to the back office.  

![Screenshot of EzPlatformLogsUiBundle](https://i.imgur.com/Dlr1LFs.png)

**Details**:
* Author: Florian Bouché
* Licence: [MIT]([https://opensource.org/licenses/MIT](https://opensource.org/licenses/MIT))

## Requirements

* php: ^7.1.3
* ezsystems/ezplatform: 2.5.*
* ezsystems/ezplatform-admin-ui: ^1.5

:warning: Warning, in its current version, the bundle **only supports** log files in `Monolog/LineFormatter` format. [LineFormatter from Github]([https://github.com/Seldaek/monolog/blob/master/src/Monolog/Formatter/LineFormatter.php](https://github.com/Seldaek/monolog/blob/master/src/Monolog/Formatter/LineFormatter.php))

## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require fkeloks/ezplatform-logs-ui
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new EzPlatformLogsUi\Bundle\EzPlatformLogsUiBundle(),
        ];
    }
}
```

### Step 3: Import EzPlatformLogsUi routing files
Now that you have activated and configured the bundle, all that is left to do is import the EzPlatformLogsUi routing files.

```yaml
# app/config/routing.yml

# EzPlatformLogsUiBundle
_ezplatform_logs_ui:
    resource: "@EzPlatformLogsUiBundle/Resources/config/routing.yml"
    prefix: /
```
