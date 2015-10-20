# Auth
PHPixie Authentication library

[![Build Status](https://travis-ci.org/PHPixie/Auth.svg?branch=master)](https://travis-ci.org/PHPixie/Auth)
[![Test Coverage](https://codeclimate.com/github/PHPixie/Auth/badges/coverage.svg)](https://codeclimate.com/github/PHPixie/Auth)
[![Code Climate](https://codeclimate.com/github/PHPixie/Auth/badges/gpa.svg)](https://codeclimate.com/github/PHPixie/Auth)
[![HHVM Status](https://img.shields.io/hhvm/phpixie/auth.svg?style=flat-square)](http://hhvm.h4cc.de/package/phpixie/auth)

[![Author](http://img.shields.io/badge/author-@dracony-blue.svg?style=flat-square)](https://twitter.com/dracony)
[![Source Code](http://img.shields.io/badge/source-phpixie/auth-blue.svg?style=flat-square)](https://github.com/phpixie/auth)
[![Software License](https://img.shields.io/badge/license-BSD-brightgreen.svg?style=flat-square)](https://github.com/phpixie/auth/blob/master/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/phpixie/auth.svg?style=flat-square)](https://packagist.org/packages/phpixie/auth)

This is the base package of the PHPixie authentication subsystem, which is split into several components. This manual covers all of them for.

Authentication is the most critical part of any application, implementing it the right way is hard, and any errors can compromise a lot of user, especially in opensource projects. Using old hash functions, cryptographically unsecure random generators and the misues of cookies are sadly things we still encounter frequently. This is why I spent a lot of time to carefully implement authentication in PHPixie.

## What makes it secure

 * Using the secure password_hash() in PHP 5.5 and a compatibilty package for older PHP versions
 * Same for the crryptographically secure random_bytes() from PHP 7
 * Following the [best practices](http://jaspan.com/improved_persistent_login_cookie_best_practice) for persisted login

The last point is the most interesting and currently no other framework supports it out of the box. The idea behind it lies
in the use of a special table for storing auth tokens.

 1. When a user first logs in a random *series* identiefier and a *passphrase* are generated. These are then sent to the user as a cookie.
 2. The *series* and *passphrase* are hashed, and then the *series*, the resulting hash, user id and expiration date are saved in the database
 3. When a user enters the site (and the session is not already present) his cookie is rehashed and compared to the hash in the database.
    If those match, the user is logged in, a session is started and a new token is generated for the user.
 4. If the hashes don't match a theft is assumed and any token with the same series identifier is deleted from the table
 
This approach has huge benefits when compared to the usual approach of storing a single token in the users table:

 * Users can have multiple persistent session on multiple devices (each device will get its own *series*)
 * Tokens are of one time use, and if stolen using a MITM attack cannot be reused.
 * Tokens cannot be bruteforced, since the first unsuccessful attempt removes the series
 * If a database is ever compromised, only token hashes are exposed, so the attacker still cannot login.
 
And basically if your framework is storing the paristent token as-is in the database without hashing it, it is comparable
to storing an unhashed password there. And there are still a lot of popular frameworks doing this, just take a look.
 
 
## Initializing

The initialization might seem a bit overwhelming, but that is because the architecture is highly modular
and tries to minimize any unneeded dependencies. If you don't need a particular extension, feel free to not build it.
Of course if you are using the PHPixie framework all of this is handled automatically.

```php
$slice = new \PHPixie\Slice();

// The database component is only required if you need
// the token storage functionality
$database = new \PHPixie\Database($slice->arrayData(array(
    'default' => array(
        'driver' => 'pdo',
        'connection' => 'sqlite::memory:'
    )
)));

// the security component handles hashing, random numbers and tokens
$security = new \PHPixie\Security($database);

// This plugin allows using the login/password auth
$authLogin = new \PHPixie\AuthLogin($security);

// To use HTTP authorization we must first
// build an HTTP context
$http = new \PHPixie\HTTP();
$request = $http->request();
$context = $http->context($request);
$contextContainer = $http->contextContainer($context);

$authHttp = new \PHPixie\AuthHTTP($security, $contextContainer);


$authConfig = $slice->arrayData(array(
    // config options
));

// This is your class that must impplement the
// \PHPixie\Auth\Repositories\Registry interface
$authRepositories = new AuthRepositories();

// Initialize the Auth system with both extensions
$auth = new \PHPixie\Auth($authConfig, $authRepositories, array(
    $authLogin->providers(),
    $authHttp->providers()
));
```

## Repositories

The first thing you need is a user repository. The most basic one is `PHPixie\Auth\Repositories\Repository` which only provides fetching users
by their id. But for any practical use you will probably need the `\PHPixie\AuthLogin\Repository` interface, which allows for the password based
login. You will need a repostory builder to pass to the Auth component:

```php
class AuthRepositories extends \PHPixie\Auth\Repositories\Registry\Builder
{
    protected function buildUserRepository()
    {
        return new YourRepository();
    }
}

// that is the second parameter we passed to Auth
$authRepositories = new AuthRepositories();
```

### Framework support

If you are using the PHPixie ORM all you need is to extend the premade wrappers:

```php
namespace Project\App\ORMWrappers\User;

// Repository wrapper
class Repository extends \PHPixie\AuthORM\Repositories\Type\Login
{
    // You can supply multiple login fields,
    // in this case its both usernam and email
    protected function loginFields()
    {
         return array('username', 'email');
    }
}
```

```php
namespace Project\App\ORMWrappers\User;

// Entity wrapper
class Entity extends \PHPixie\AuthORM\Repositories\Type\Login\User
{
    // get hashed password value
    // from the field in the database
    public function passwordHash()
    {
         return $this->passwordHash;
    }
}
```

Don't forget to register these wrappers with the ORM:

```php

namespace Project\App;

class ORMWrappers extends \PHPixie\ORM\Wrappers\Implementation
{
    protected $databaseEntities = array('user');
    protected $databaseRepositories = array('user');

    public function userEntity($entity)
    {
        return new ORMWrappers\User\Entity($entity);
    }
    
    public function userRepository($repository)
    {
        return new ORMWrappers\User\Repository($repository);
    }
}
```

And register an AuthRepositories class in your bundle

```php
namespace Project\App;

class AuthRepositories extends \PHPixie\Auth\Repositories\Registry\Builder
{
    protected $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    protected function buildUserRepository()
    {
        $orm = $this->builder->components()->orm();
        return $orm->repository('user');
    }
}
```

```php
namespace Project\App;

class Builder extends \PHPixie\DefaultBundle\Builder
{
    protected function buildAuthRepositories()
    {
        return new AuthRepositories($this);
    }
}
```

## Configuration options

The configuration is split into domains. A domain is a context that consists of a repository and authentication providers. Usually your app will have only a single domain, but sometimes you may need more. E.g. imagine you have some sort of the social login for site users, but site administrators are logged in on a separate page using their database accounts.

```php
// /assets/auth.php

return array(
    'domains' => array(
        'default' => array(

            // using the 'user' repository from the 'app' bundle
            'repository' => 'app.user',
            'providers'  => array(

                // include session support
                'session' => array(
                    'type' => 'http.session'
                ),

                // include persistent cookies (remember me)
                'cookie' => array(
                    'type' => 'http.cookie',
                    
                    // when a cookie is used to login
                    // persist login using session too
                    'persistProviders' => array('session'),
                    
                    // token storage
                    'tokens' => array(
                        'storage' => array(
                            'type'            => 'database',
                            'table'           => 'tokens',
                            'defaultLifetime' => 3600*24*14 // two weeks
                        )
                    )
                ),
                
                // password login suport
                'password' => array(
                    'type' => 'login.password',
                    
                    // remember the user in session
                    // note that we did not add 'cookies' to this array
                    // because we don't want every login to be persistent
                    'persistProviders' => array('session')
                )
            )
        )
);
```

As you can see all providers are entirely independent of each other, whcih means we can alter the behavior easily. For example let's
assume that we don't want to use sessions at all, just the cookie based login, and turn off token regeneration on each request:

```php
// /assets/auth.php

return array(
    'domains' => array(
        'default' => array(
               'cookie' => array(
                    'type' => 'http.cookie',

                    // token storage
                    'tokens' => array(
                        'storage' => array(
                            'type'            => 'database',
                            'table'           => 'tokens',
                            'defaultLifetime' => 3600*24*14,
                            
                            // don't refresh tokens
                            'refresh'         => false
                        )
                    )
                ),
                
                'password' => array(
                    'type' => 'login.password',
                    
                    // persist lgoin with cookie
                    'persistProviders' => array('cookie')
                )
            )
        )
);
```

## Token storage

In both examples we referenced a database table used to store tokens. In fact this can also be a MongoDB collection. The SQL for
the table creation would be as follows:

```sql
CREATE TABLE `tokens` (
  `series` varchar(50) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `challenge` varchar(50) DEFAULT NULL,
  `expires` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`series`)
);
```

## Usage example

Now that we have everything configured, lets test how it all works together. Here is a simple processor:

```php
namespace Project\App\HTTPProcessors;

class Auth extends \PHPixie\DefaultBundle\Processor\HTTP\Actions
{
    protected $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    // Check if the user is logged in
    public function defaultAction($request)
    {
        $user = $this->domain()->user();

        return $user ? $user->username : 'not logged';
    }
    
    // Action for adding user to the database
    public function addAction($request)
    {
        $query = $request->query();
        $username = $query->get('username');
        $password = $query->get('password');

        $orm = $this->builder->components()->orm();
        $provider = $this->domain()->provider('password');

        $user = $orm->createEntity('user');

        $user->username     = $username;

        // Hash password using the password provider
        $user->passwordHash = $provider->hash($password);

        $user->save();

        return 'added';
    }
    
    // Attempt to login user using his password
    public function loginAction($request)
    {
        $query = $request->query();
        $username = $query->get('username');
        $password = $query->get('password');

        $provider = $this->domain()->provider('password');

        $user = $provider->login($username, $password);
        
        if($user) {
        
              // Generate persistent login cookie
              $provider = $this->domain()->provider('cookie');
              $provider->persist();
        }
        return $user ? 'success' : 'wrong password';
    }
    
    // logout action
    public function logoutAction($request)
    {
        $this->domain()->forgetUser();
        return 'logged out';
    }
     
    protected function domain()
    {
        $auth = $this->builder->components()->auth();
        return $auth->domain();
    }
}
```

To test it try hitting these URLs:

 1. /auth - user is not logged in
 2. /auth/add?username=dracony&password=5 - add user to the database
 3. /auth/login?username=dracony&password=5 - log in
 4. /auth - check login
 5. /auth/logout - logout
 

### Adding your own providers

At some point you will probably need to add your own login providers (e.g. for social networks), to do that you need to satisfy a `PHPixie\Auth\Providers\Builder` interface and pass it along with the other extensions. Try looking at the [AuthLogin component](https://github.com/PHPixie/Auth-Login/blob/master/src/PHPixie/AuthLogin/Providers.php) for an example. If you are using the PHPixie Framework
you can pass your custom extensions to the Auth component by overloading [this method](https://github.com/PHPixie/Framework/blob/master/src/PHPixie/Framework/Extensions.php#L29).
