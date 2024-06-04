# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 2.0.1 2024-06-04
### Added 
- Updated `BearerPassportAuthenticator::onAuthenticationFailure` response message

## 2.0.0 2024-02-08
### Added 
- Added support for Symfony ^5.4
- Dropped support for PHP 5.5 and Symfony 2.x and 3.x
- Renamed Paysera\BearerAuthenticationBundle\DependencyInjection\Security\Factory\BearerFactory to Paysera\BearerAuthenticationBundle\DependencyInjection\Security\Factory\BearerSecurityFactory
- Added Paysera\BearerAuthenticationBundle\DependencyInjection\Security\Factory\BearerAuthenticatorFactory for Symfony 5
- Changed src/Security/Authentication/Token/BearerTokenInterface::getToken(), added return type

## 1.0.2
### Added
- Deprecated \Symfony\Component\DependencyInjection\DefinitionDecorator class removed and 
  used \Symfony\Component\DependencyInjection\ChildDefinition

## 1.0.1
### Added
- added support for Symfony 4.x

## 1.0.0
### Changed
- `\Paysera\BearerAuthenticationBundle\Listener\BearerListener` not does not return 403 response after failing to authenticate user
