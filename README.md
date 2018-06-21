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

Sample `security.yml`
```yml
security:
    providers:
        bearer_user:
            id: paysera_bearer_authentication.security_user.bearer_user_provider
    
    firewalls:
        bearer_secured:
            patter: ^/api/
            stateless: true
            bearer: true
```

All bearer handlers must be tagged with:
```xml
<tag name="paysera_bearer_authentication.handler" />
```
and implement `\Paysera\BearerAuthenticationBundle\Security\User\HandlerInterface` 
