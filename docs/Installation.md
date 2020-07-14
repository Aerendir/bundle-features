How to install Serendipity HQ Features Bundle
=============================================

## Install Serendipity HQ Features Bundle via Composer

    $ composer require serendipity_hq/bundle-features

This library follows the http://semver.org/ versioning conventions.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new SerendipityHQ\Bundle\SHQFeaturesBundle\SHQFeaturesBundle(),
        );

        // ...
    }

    // ...
}
```

([Go back to index](Index.md)) | Next step: [Configure](Configuration.md)
