# wp-adaptive-images

## Preliminaries
This has to be added to the main `composer.json`
```
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "dan-coulter/phpflickr",
                "version": "1.0.0",
                "source": {
                    "type": "git",
                    "reference": "origin/master",
                    "url": "git@github.com:dan-coulter/phpflickr.git"
                }
            }
        },
        {
            "type": "package",
            "package": {
                "name": "syamilmj/aqua-resizer",
                "version": "1.0.0",
                "source": {
                    "type": "git",
                    "reference": "origin/master",
                    "url": "git@github.com:syamilmj/Aqua-Resizer.git"
                }
            }
        }
    ],
    "autoload": {
        "classmap": [
            "../vendor/dan-coulter/phpflickr/", 
            "../vendor/syamilmj/aqua-resizer"
        ]
    }
```