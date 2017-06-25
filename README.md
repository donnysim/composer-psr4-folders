# Composer PSR-4 folders

This plugin allows adding `psr-4-folders` to composer `autoload` and all subfolders
will be added to PSR-4 as roots, for example having directory such as:

    modules
        Users
        Pages
    
and adding `"psr-4-folders": ["modules/"]` will generate PSR-4 entries:

    "psr-4": {
        "Users//": "modules/Users/",
        "Pages//": "modules/Pages/",
    }
    
If a directory contains `src` subdirectory, then it will be marked as root e.g. `"Pages//": "modules/Pages/src"`.
