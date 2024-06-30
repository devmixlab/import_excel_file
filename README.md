
************************************************************************
Requirements for application
Framework to use – http://www.slimframework.com/

Working with database only via PHP PDO.
It is prohibited to use any libraries or ORM to work with database.

Архів поштових індиків - https://www.ukrposhta.ua/files/shares/out/postindex.zip

API documentation: https://swagger.io/

1. Design the structure and create DB
   As unique key for all operations must be this one "Поштовий індекс відділення зв'язку (Post code of post office)"
   If in archive of post indexes field of column "Поштовий індекс відділення зв'язку (Post code of post office)" is empty, ignore it.

2. Create script to upload list of post indexes from archive.
- Assume that the script will run daily on a schedule.
- Archive contains about 28k rows, but script must be able to handle much more rows, for example 1mln.
- Mandatory configuration for ini.php
    memory_limit = 128M
- The script`s running time, as well as the load on the database, are crucial - the less, the better.

Script must do:
- Unpack
- Process data
- Add the rows into database that are not there.
- Update modified rows
- Remove rows, that are not in the archive, except those that were added via api(see below).

3. Create three api interfaces:
- To retrieve the post indexes from database. There can be specified or not some parameters for filtering the result. (see below)
- If no parameters were specified, should be returned 50 rows per page with pagination, sorted by the index in ascending order.
- If post index specified, returning found row.
- If the address or part of it is specified, returning 50 rows per page with pagination sorted by address in ascending order.
- Addition a post index (one or several at once)
- Removing a post index

---
Documentation must be implemented in phpdoc format (show example), also code should be documented where it is necessary.
---

As bonus, create a simple interface for API with Svelte or VueJS

************************************************************************

Setup:

<ul>
    <li>
        composer install
    </li>
    <li>
        docker-compose up --build
    </li>
    <li>
        npm install
    </li>
    <li>
        npm run build
    </li>
    <li>
        php migrate.php
    </li>
</ul>


************************************************************************
Creates table for import data from file<br><br>
<b>php migrate.php</b>


************************************************************************
Creates table for import data from file<br>
And inserts in table fake data<br><br>

<b>php migrate.php -f<br>
php migrate.php -f100<br>
php migrate.php -f1000<br></b>


************************************************************************
Makes import of data from excel files<br>
Test files are located at files folder<br><br>

<b>cron.php</b>


************************************************************************
Test written for api<br>
located in folder tests<br><br>

<b>php vendor/bin/phpunit ./tests/ApiPostindexControllerTest.php</b>

************************************************************************
Api documentation<br>

<b>/documentation</b>

