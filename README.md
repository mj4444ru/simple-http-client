# Simple Http Client

Version: 2025-09-12

> [!NOTE]
> To use this software, make a copy of this package into your project.

> [!WARNING]
> The software is under development.
> It is strongly not recommended to connect it to yourself via composer or any other way.

## Installation

### Via composer

```shell
composer require --dev --prefer-dist mj4444/simple-http-client:dev-master
```

### By cloning the code

```shell
git clone https://github.com/mj4444ru/simple-http-client.git
```

Add the following lines to the `composer.json` file:

```
"autoload": {
    "psr-4": {
        "Mj4444\\SimpleHttpClient\\": "simple-http-client/src/"
    }
}
```

## Examples

## Run tests

```shell
vendor/bin/codecept run
```
