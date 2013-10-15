Facebook PHP SDK Changelog
==========================

__v4.0.0-community__ (2013-XX-XX)

* Updated `composer.json`
* Tests use composer for bootstraping
* Add PHPUnit to composer
* Add PHP 5.5 to Travis CI
* Renamed `bootstrap.php` to `bootstrap.php.dist`
* Add `phpunit.xml.dist` file
* PSR-2 refactoring
* Make `$READ_ONLY_CALLS` a static class member
* Refactor BaseFacebook to a concrete class Facebook using sessions
* Refactor Facebook to SharedFacebook, inherit from Facebook
  that manage shared session