# Changelog

All notable changes to `ignition` will be documented in this file

## 1.4.2 - 2019-09-03

- style fixes

## 1.4.1 - 2019-09-03

- Change `remote-sites-path` and `local-sites-path` config keys to us snake case

## 1.4.0 - 2019-09-03

- add `enable_runnable_solutions` key to config file

## 1.3.0 - 2019-09-02

- add `MergeConflictSolutionProvider`

## 1.2.0 - 2019-09-02

- add `ignored_solution_providers` key to config file

## 1.1.1 - 2019-09-02

- Fixed context tab crash when not using git (#24)

## 1.1.0 - 2019-09-02

- Fixed an error that removed the ability to register custom blade directives.
- Fixed an error that prevented solution execution in Laravel 5.5 and 5.6
- The "Share" button can now be disabled in the configuration file
- Fixes an error when trying to log `null` values 

## 1.0.4 - 2019-09-02

- Check if the authenticated user has a `toArray` method available, before collecting user data

## 1.0.3 - 2019-09-02

- Corrected invalid link in config file

## 1.0.2 - 2019-09-02

- Fixed an error in the `DefaultDbNameSolutionProvider` that could cause an infinite loop in Laravel < 5.6.28

## 1.0.1 - 2019-08-31

- add support for L5.5 & 5.6 (#21)

## 1.0.0 - 2019-08-30

- initial release
