[![GPLv3 License](https://img.shields.io/badge/License-GPL%20v3-yellow.svg)](https://opensource.org/licenses/)


# About Home Economics

This project is build to make better health Home Economics.

Is made as personal needs, so i decided to share with community.
The propose is primary to make another devs to use, so needs to be self-hosted PHP.

I expect you like this and enjoy.

Is made with Laravel, TailwindCSS and InertiaJS with ReactJs.

I use this project to study InertiaJS and ReactJs.





## Deploy

To run this project you need to satisfy some requeriments:

    1 - PHP 8
    2 - composer
    3 - node
    4 - MySQL

The environment must be configured to support a Laravel installation. Specific requirements can be found at: https://laravel.com/docs/10.x/deployment#server-requirements


So now we can start installation:

### 1. Start cloning repo:
```bash
  git clone https://github.com/camolezerafael/home-economics.git
```

### 2. Then let's install PHP dependencies:

```bash
  composer install
```

### 3. And install node dependencies:

```bash
  npm install
```

### 4. Build React interface:

```bash
  npm build
```

### 5. Configure .env file:

Setup your MySQL database and generate a .env file using the .env.example as base, changing the mysql params to your MySQL database previous created.

### 6. Now it's time to create database tables:

```bash
  php artisan migrate
```

### 7. Now it's time to create database tables:

```bash
  php artisan migrate
```

### 8. The final step is create your user:

```bash
  php artisan db:seed
```

The user credentials created is

    username: admin@admin.com
    password: 123456

So you need to change on first login to make your system secure.

### 9. Then access your application on Locahost or your domain

## Existing Features

- Create/Manage Accounts
- Create/Manage Account Types
- Create/Manage Categories
- Create/Manage Payment Types
- Create/Manage From & To Payments
- Create/Manage Transactions (Income, Fixed Expenses, Variable Expenses, People, Taxes and Transfers)


## This project is still in development.

Some future features:
- A Complete Dashboard
- Possibility of Creating Goals
- Manage Credit Cards
- Manage Crypto Wallets making currency conversion
- Many others
