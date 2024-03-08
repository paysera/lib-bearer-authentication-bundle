# Bearer Authentication Bundle

## Install

Add bundle to `AppKernel.php`:
```php
$bundles = [
    ...
    new Paysera\BearerAuthenticationBundle\PayseraBearerAuthenticationBundle(),
];
```

## Samples

Sample `security.yml` for symfony 3/4
```yaml
security:
    providers:
        bearer_user:
            id: paysera_bearer_authentication.security_user.bearer_user_provider
    
    firewalls:
        bearer_secured:
            pattern: ^/api/
            stateless: true
            bearer: true
            provider: bearer_user
```

Sample security.yml for Symfony 5
```yaml
security:
    enable_authenticator_manager: true
    providers:
      bearer_user:
            id: paysera_bearer_authentication.security_user.bearer_user_provider    

    firewalls:
      bearer_secured:
            pattern: ^/api
            stateless: true
            provider: bearer_user
            
    access_control:
        # require ROLE_ADMIN for /admin*
        - { path: '^/api', roles: ROLE_ADMIN }
```

All bearer handlers must be tagged with:
```xml
<tag name="paysera_bearer_authentication.handler" />
```
and implement `\Paysera\BearerAuthenticationBundle\Security\User\HandlerInterface` 

Example of a handler services.yaml
```yaml
    paysera_auth_token.security.auth_token_handler:
        class: 'App\Services\BearerHandler'
        tags:
            - { name: 'paysera_bearer_authentication.handler' }
```

## Support matrix 
The below table shows the supported PHP versions for this library, please review the composer.json file for each individual package for additional requirements.

| Package version | Symfony Version | PHP Version |
|-----------------|-----------------|-------------|
| 0.x             | 2.x             | 5.5         |
| 0.x             | 3.x             | 5.5         |
| 1.0.0           | 3.x             | 5.5         |
| 1.0.1           | 3.x             | 5.5         |
| 1.0.1           | 4.x             | 5.5         |
| 2.x             | 4.x             | 7.4         |
| 2.x             | 5.x             | 7.4         |
