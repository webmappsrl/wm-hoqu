# wm-hoqu
HOKU is a software library made up of various components: 
a PHP library (REST API) dedicated to listening to external hooks, 
a database for queue management, a series of support scripts (CLI).


## Architettura
![Hoqu Architecture image](https://raw.githubusercontent.com/webmappsrl/wm-hoqu/master/resources/HOQU_architecture.jpeg)

## Repo structure

|- /resources Some useful resources like images and other staff
|- /tests Unit (phpunit) and E2E (Selenium) tests
|- /src Classes and API
|- /scripts Some useful script (install and other)


## Unit test
If you want to run unit test you must use phpunit

phpunit --color=always --bootstrap src/autoload.php --whitelist src tests/unit/hoquTest.php
