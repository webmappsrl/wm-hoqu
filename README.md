# wm-hoqu
HOKU is a software library made up of various components: 
a PHP library (REST API) dedicated to listening to external hooks, 
a database for queue management, a series of support scripts (CLI).


## Architecture
![Hoqu Architecture image](https://raw.githubusercontent.com/webmappsrl/wm-hoqu/master/resources/HOQU_architecture.jpeg)

## Repo structure

/resources Some useful resources like images and other staff

/tests Unit (phpunit) and E2E (Selenium) tests

/src Classes and API

/scripts Some useful script (install and other)



## Automatic Tests
Directory tests is composed by subdirectories with different types of automatic test: unit and e2e_api.
In order to run all test locally (see below detailed instruction) you need to install and configure a 
mysql client and server (for IOS https://mariadb.com/resources/blog/installing-mariadb-10-1-16-on-mac-os-x-with-homebrew/)
and edit file src/config.json with your connection test data. 
Example:

```
{
  "mysql" : {
    "host" : "localhost",
    "db" : "hoqutest",
    "user" : "hoqutest",
    "password" : "hoqu"
  }
}
```

### Unit test
If you want to run a specific unit test (phpunit must be installed):

```
phpunit --color=always --bootstrap src/autoload.php --whitelist src tests/unit/hoquTest.php
```

If you want to run a all unit test:

```
phpunit --color=always --bootstrap src/autoload.php --whitelist src tests/unit
```

### E2E API test using PHPUNIT and GUZZLE
Guzzle acts as a powerful HTTP client which we can use to simulate HTTP Requests against our API. 
Though PHPUnit acts as a Unit Test framework (based on XUnit), in this case we will be using this powerful testing 
framework to test the HTTP responses we get back from our APIs using Guzzle.
Useful resources:
 - https://github.com/IcyApril/Test-the-REST
 - https://blog.cloudflare.com/using-guzzle-and-phpunit-for-rest-api-testing/

If you want to run 2e2 api test locally, you must configure your computer before, by following steps:
- Configure local server (APACHE, PHP, MYSQL)
- Add following Virtual HOST to your APACHE configuration file
```
<VirtualHost *:80>
    DocumentRoot "/path/to/hoqu/src/"
    ServerName hoqutest.webmapp.it   
</VirtualHost>
```
- Add the following line to your /etc/hosts file
```
127.0.0.1 hoqutest.webmapp.it
```

If you want to run a specific e2e_api test (phpunit must be installed):

```
phpunit --color=always --bootstrap src/autoload.php --whitelist src tests/e2e_api/indexTest.php
```

If you want to run a all e2e_api test:

```
phpunit --color=always --bootstrap src/autoload.php --whitelist src tests/e2e_api
```

