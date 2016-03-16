# [WooCommerce Subscriptions](http://www.woothemes.com/products/woocommerce-subscriptions/)

[![Build Status](https://magnum.travis-ci.com/Prospress/woocommerce-subscriptions.svg?token=7qcKG8toQcpjnZpuJrFT&branch=master)](https://magnum.travis-ci.com/Prospress/woocommerce-subscriptions) [![codecov.io](http://codecov.io/github/Prospress/woocommerce-subscriptions/coverage.svg?token=SZMiHxYlfh&branch=master)](http://codecov.io/github/Prospress/woocommerce-subscriptions?branch=master)

## Repositories

* The `/woothemes/woocommerce-subscriptions/` repository is treated as a _deployment_ repository: all code committed to the `master` branch of this repository is considered stable and ready for release. Commit history from 2.0.0 onwards include only complete change sets for a new version.
* The `/prospress/woocommerce-subscriptions/` repository is treated as a _development_ repository: this includes development assets, like unit tests and configuration files. Commit history for this repository includes all commits for all changes to the code base, not just for new versions.

## Deployment

The [`deploy.sh`](https://github.com/Prospress/woocommerce-subscriptions/blob/master/deploy.sh) script is used to deploy from the `/prospress/woocommerce-subscriptions/` development repository to the `/woothemes/woocommerce-subscriptions/` _deployment_ repository.

In order to run this script, you first need to have installed [`makepot` - a bash script to make .pot files for WordPress plugins](https://gist.github.com/johnpbloch/3436835).

Once that script is installed, deployment is as simple as running `.deploy.sh` from within the `/woocommerce-subscriptions/` repository.

A "_deployment_" in our sense means:
 * validating the version in the header and `WC_Subscriptions::$version` variable match
 * generating a `.pot` file for all translatable strings in the development repository
 * tagging a new version in the main development repository and pushing it to `/prospress` (which must have a remote with the name `prospress`)
 * cloning a copy of the `/woothemes/woocommerce-subscriptions/` repo into a temporary directory
 * exporting a copy of all the files in the `/prospress/woocommerce-subscriptions/` repo to the `/woothemes/` repo
 * removing all development related assets, like this file, unit tests and configuration files from that repo
 * committing those files to the `/woothemes/` repo, then tagging a new version in it and pushing the changes and new tag to `/woothemes/`
 * the changes will be pushed to a branch with the name `release/{version}` so that a PR can be issued on `/woothemes/woocommerce-subscriptions/`

## Branches

* [`woothemes/master`](https://github.com/woothemes/woocommerce-subscriptions/tree/master) is used for the current version. It does not include files for development and can be considered stable for production.
* [`prospress/master`](https://github.com/prospress/woocommerce-subscriptions/tree/master) includes all code for the current version and any new pull requests merged that will be released with the next version. It can be considered stable for staging and development sites but not for production.
* [`prospress/1.5-branch`](https://github.com/prospress/woocommerce-subscriptions/tree/1.5-branch) has the latest version of the codebase prior to 2.0.
* `prospress/issue_{id}` branches are used for creating patches for specific issue reported on the development repository and can not be considered stable.

## Additional resources

* [Translation readme](https://github.com/Prospress/woocommerce-subscriptions/blob/master/.tx/readme.md)
* [Testing readme](https://github.com/Prospress/woocommerce-subscriptions/blob/master/tests/readme.md)