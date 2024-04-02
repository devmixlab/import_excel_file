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

