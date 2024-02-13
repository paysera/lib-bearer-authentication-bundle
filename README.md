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
```

Sample security.yml for Symfony 5
```yaml
security:
    enable_authenticator_manager: true
    providers:
      bearer_user:
            id: paysera_bearer_authentication.security_user.bearer_user_provider    

    firewalls:
        jwt:
            pattern: ^/api
            stateless: true
            custom_authenticators:
                - paysera_bearer_authentication.authenticator.bearer_passport
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
