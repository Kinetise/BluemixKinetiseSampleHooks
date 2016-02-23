AlterApi Skeleton
=================

Welcome to Alterapi Skeleton - a base backend source for `Kinetise`_ apps.

Requirements:
-------------
* PHP >= 5.4.0
* SQLite
* Apache

How to start?
-------------

AlterApi Skeleton uses `Composer`_ to ease the creation of a new project:

.. code-block:: console

    $ composer create-project kinetise/alterapi-skeleton path/to/install -s dev

Composer will create for you a new project under path/to/install

Next you need to setup a Apache Virtual host pointing to path/to/install/web

.. code::

    <VirtualHost *:80>
        ServerName alterapi.skeleton.dev

        ## Vhost docroot
        DocumentRoot "path/to/install/web"

        ## Directories, there should at least be a declaration for path/to/install/web

        <Directory "path/to/install/web">
            Options Indexes MultiViews FollowSymLinks
            AllowOverride All
            Require all granted

            <IfModule mod_rewrite.c>
                Options -MultiViews

                RewriteEngine On
                #RewriteBase /path/to/app
                RewriteCond %{REQUEST_FILENAME} !-f
                RewriteRule ^ index.php [QSA,L]
            </IfModule>

        </Directory>

        ## Logging
        ErrorLog "/var/log/apache2/alterapi.skeleton.error.log"
        ServerSignature Off
        CustomLog "/var/log/apache2/alterapi.skeleton.access.log" combined

        ## Custom fragment
        SetEnv APP_ENV dev
        SetEnv APP_DEBUG true

    </VirtualHost>

Base backend collect logs and create some cache files. You need to create a directories for logs and cache files:

.. code-block:: console

    $ cd path/to/install
    $ mkdir -p var/{cache,logs}

Your apache user must have permissions to write files under "var" directory.

Last step is creating SQLite database:

.. code::

    $ cd path/to/install
    $ php app/console orm:schema-tool:create

For more information you can browse tutorial included inside:

.. code-block:: console

    http://alterapi.skeleton.dev/tutorial

Enjoy!

.. _Kinetise: https://www.kinetise.com/
.. _Composer: http://getcomposer.org/