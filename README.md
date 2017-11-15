ViraCMS
=======

The **ViraCMS** is a content management system software. It's written in PHP
language, with Yii Framework and support all of it's features along with
convenient management interface of administrators, static pages, images and
other files.

This repository is represents **COMMUNITY** edition of the **ViraCMS**
software so you can use it absolutely free of charge.

It's have modular structure and can be used as base for developing very
huge web applications.

And it's have a very nice visual editor of the site static pages :-)

[Don't forget to visit ViraCMS website](http://www.viracms.ru/)

### Requirements, installation, configuration and database initialization

#### Requirements

* PHP 5.2 or later
* Apache/Nginx + PHP-FPM/other web server with PHP support
* MySQL/PostgreSQL/sqlite database engine

#### Installation

[Download zip-file](https://github.com/ViraTech/ViraCMS/archive/master.zip) and extract it
contents, OR clone the repository to the server directory which is indended to be a website
storage directory.

```
git clone https://github.com/ViraTech/ViraCMS.git .
```

Note that the directories `public/assets`, `public/files`, and `runtime/` must be
allowed to write for the web server process.

Make sure your server configured properly and points to `public/`
directory as website root, and have `index.php` as root index.

#### Configuration

To configure **ViraCMS** you need to edit `protected/config/local.php`
file. Configuration is provided as PHP array.

As the usual, you have to update database server address, database name,
username, and password for correct database access.

An example for database configuration file for the database server
which runs on `localhost`, database name is `viracms`, username is
`username`, and password is `password`:

```PHP
<?php
return array(
  'components' => array(
    'db' => array(
      'connectionString' => 'mysql:host=localhost;dbname=viracms',
      'username' => 'username',
      'password' => 'password',
    ),
  ),
);
```

#### Database initialization

To initialize the database with default data you need to execute console
command from `protected` directory on the web server:

```
php console.php migrate
```

In case of everything is configured correctly the command will proceed
with migrations immidiately.

After the database initialization you may need to add administrator
account. This can be done with the command like this (replace the demo
data with your own):

```
php console.php user add --username=admin --email=admin@yourdomain.com --password=123456 --language=en
```

After all, you can now login to the administrator's interface simply
going to `/admin` URL of your new website (e. g. if your domain is
`example.org` then you have to go to `example.org/admin`).

### Copyrights and licenses

ViraCMS is copyrighted software by [Vira Technologies](http://viratechnologies.ru/) in 2015.

The **ViraCMS** community edition is licensed under GPLv3.
