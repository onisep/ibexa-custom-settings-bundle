IbexaCustomSettingsBundle
==========

Symfony bundle for configuring custom parameters linked to an Ibexa location.

**Details**:

* Author: ONISEP / Florian Bouché
* Licence: [MIT]([https://opensource.org/licenses/MIT](https://opensource.org/licenses/MIT))

**Available translations**:

* en (English): partial support
* fr (French): full support

## Requirements

* php: >=7.4
* ibexa: ^3.3

## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require onisep/ibexa-custom-settings-bundle
```

This command requires you to have Composer installed globally, as explained in
the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

### Step 2: Enable the Bundle

Add `Onisep\IbexaCustomSettingsBundle\IbexaCustomSettingsBundle::class => ['all' => true]`, in the `config/bundles.php` file.

Like this:

```php
<?php

return [
    // ...
    Onisep\IbexaCustomSettingsBundle\IbexaCustomSettingsBundle::class => ['all' => true],
    // ...
];
```

### Step 3: Import bundle routing file

```yaml
# app/config/routing.yml or config/routing.yaml

_ibexa_custom_settings:
    resource: '@IbexaCustomSettingsBundle/Resources/config/routing.yaml'
```

### Step 4: Execute database migration

You can create a migration in your project from this example, then run it:

```php
# migrations/Version20231004124944.php

<?php

namespace Onisep\migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231004124944 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ibexa_custom_settings (id INT AUTO_INCREMENT NOT NULL, location_id INT NOT NULL, setting_key VARCHAR(300) NOT NULL, setting_value LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;');
    }
}
```

Or if you prefer to run the SQL yourself :

```sql
CREATE TABLE ibexa_custom_settings
(
    id            INT AUTO_INCREMENT NOT NULL,
    location_id   INT                NOT NULL,
    setting_key   VARCHAR(300)       NOT NULL,
    setting_value LONGTEXT           NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4
  COLLATE `utf8mb4_unicode_ci`
  ENGINE = InnoDB;
```

## License

This package is licensed under the [MIT license](LICENSE).