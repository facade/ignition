# Changelog

All notable changes to `ignition` will be documented in this file

## 1.13.0 - 2019-11-27

- Allow custom grouping types

## 1.12.1 - 2019-11-25

- Detect multibyte position offsets when adding linenumbers to the blade view - Fixes #193

## 1.12.0 - 2019-11-14

- Add exception to html (#206)
- Add a clear exception when passing no parameters to ddd (#205)
- Ignore JS tests (#215)
- Fix share report route bug

## 1.11.2 - 2019-10-13

- simplify default Laravel installation (#198)

## 1.11.1 - 2019-10-08

- add conditional line number (#182)

## 1.11.0 - 2019-10-08

- add better error messages for missing validation rules (#125)

## 1.10.0 - 2019-10-07

- Add `ignition:make-solution` command
- Add default for query binding option (Fixes #183)

## 1.9.2 - 2019-10-04

- Fix service provider registration (Fixes #177)

## 1.9.1 - 2019-10-01

- collapse vendor frames on windows fix (#176)

## 1.9.0 - 2019-09-27

-  add ability to send logs to flare
-  add `ddd` function

## 1.8.4 - 2019-09-27

-  Resolve configuration from the injected app instead of the helper ([#168](https://github.com/facade/ignition/pull/168))

## 1.8.3 - 2019-09-25

-   Remove `select-none` from error message
-   Change line clamp behaviour for longer error messages

## 1.8.2 - 2019-09-20

-   fix for `TypeError: Cannot set property 'highlightState' of undefined`

## 1.8.1 - 2019-09-20

-   Revert javascript assets via URL - Fixes #161

## 1.8.0 - 2019-09-18

-   added solution for running Laravel Dusk in production ([#121](https://github.com/facade/ignition/pull/121))
-   Automatically fix blade variable typos and optional variables ([#38](https://github.com/facade/ignition/pull/38))

## 1.7.1 - 2019-09-18

-   Use url helper to generate housekeeping endpoints

## 1.7.0 - 2019-09-18

-   Add the ability to define a query collector max value ([#153](https://github.com/facade/ignition/pull/153))

## 1.6.10 - 2019-09-18

-   fix `__invoke` method name in solution ([#151](https://github.com/facade/ignition/pull/151))

## 1.6.9 - 2019-09-18

-   Add noscript trace information - fixes [#146](https://github.com/facade/ignition/issues/146)

## 1.6.8 - 2019-09-18

-   Use javascript content type for asset response - fixes [#149](https://github.com/facade/ignition/issues/149)

## 1.6.7 - 2019-09-18

-   Load javascript assets via URL. Fixes [#16](https://github.com/facade/ignition/issues/16)

## 1.6.6 - 2019-09-16

-   Prevent undefined index exception in `TestCommand`

## 1.6.5 - 2019-09-13

-   Ignore invalid characters in JSON encoding. Fixes [#138](https://github.com/facade/ignition/issues/138)

## 1.6.4 - 2019-09-13

-   add no-index on error page

## 1.6.3 - 2019-09-12

-   Fix `RouteNotDefinedSolutionProvider` in Laravel 5

## 1.6.2 - 2019-09-12

-   updated publishing tag from default config

## 1.6.1 - 2019-09-12

-   Resolve configuration from the injected application instead of the helper - Fixes [#131](https://github.com/facade/ignition/issues/131)

## 1.6.0 - 2019-09-09

-   add `RouteNotDefined` solution provider ([#113](https://github.com/facade/ignition/pull/113))

## 1.5.0 - 2019-09-09

-   suggest running migrations when a column is missing ([#83](https://github.com/facade/ignition/pull/83))

## 1.4.19 - 2019-09-09

-   Remove quotation from git commit url ([#89](https://github.com/facade/ignition/pull/89))

## 1.4.18 - 2019-09-09

-   Fix open_basedir restriction when looking up config file. Fixes ([#120](https://github.com/facade/ignition/pull/120))

## 1.4.17 - 2019-09-06

-   Remove Inter, Operator from font stack. Fixes [#74](https://github.com/facade/ignition/issues/74)

## 1.4.15 - 2019-09-05

-   Use previous exception trace for view exceptions. Fixes [#107](https://github.com/facade/ignition/issues/107)

## 1.4.14 - 2019-09-05

-   Use DIRECTORY_SEPARATOR to fix an issue with blade view lookups in Windows

## 1.4.13 - 2019-09-05

-   Use Laravel style comments

## 1.4.12 - 2019-09-04

-   Use a middleware to protect ignition routes ([#93](https://github.com/facade/ignition/pull/93))

## 1.4.11 - 2019-09-04

-   Use exception line number as fallbacks for view errors

## 1.4.10 - 2019-09-04

-   Wrap solution provider lookup in a try-catch block

## 1.4.9 - 2019-09-04

-   Lookup the first exception when linking to Telescope

## 1.4.8 - 2019-09-04

-   pass an empty string to query if no connection name is available - fixes [#86](https://github.com/facade/ignition/issues/86)

## 1.4.7 - 2019-09-04

-   Match whoops minimum version constraint with Laravel 6

## 1.4.6 - 2019-09-04

-   Use empty array for default ignored solution providers

## 1.4.5 - 2019-09-03

-   fix for new Laravel 6 installs

## 1.4.4 - 2019-09-03

-   Suggest default database name in Laravel 6
-   Add void return type to FlareHandler::write()

## 1.4.3 - 2019-09-03

-   allow monolog v2

## 1.4.2 - 2019-09-03

-   style fixes

## 1.4.1 - 2019-09-03

-   Change `remote-sites-path` and `local-sites-path` config keys to us snake case

## 1.4.0 - 2019-09-03

-   add `enable_runnable_solutions` key to config file

## 1.3.0 - 2019-09-02

-   add `MergeConflictSolutionProvider`

## 1.2.0 - 2019-09-02

-   add `ignored_solution_providers` key to config file

## 1.1.1 - 2019-09-02

-   Fixed context tab crash when not using git ([#24](https://github.com/facade/ignition/issues/24))

## 1.1.0 - 2019-09-02

-   Fixed an error that removed the ability to register custom blade directives.
-   Fixed an error that prevented solution execution in Laravel 5.5 and 5.6
-   The "Share" button can now be disabled in the configuration file
-   Fixes an error when trying to log `null` values

## 1.0.4 - 2019-09-02

-   Check if the authenticated user has a `toArray` method available, before collecting user data

## 1.0.3 - 2019-09-02

-   Corrected invalid link in config file

## 1.0.2 - 2019-09-02

-   Fixed an error in the `DefaultDbNameSolutionProvider` that could cause an infinite loop in Laravel < 5.6.28

## 1.0.1 - 2019-08-31

-   add support for L5.5 & 5.6 ([#21](https://github.com/facade/ignition/pull/21))

## 1.0.0 - 2019-08-30

-   initial release
