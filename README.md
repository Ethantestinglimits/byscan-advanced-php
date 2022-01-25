<h1 align="center" font-weight="bold">Byscan</h1>


<p align="center">
    <a href="http://forthebadge.com/" target="_blank">
    	<img src="https://forthebadge.com/images/badges/powered-by-electricity.svg"
    </a>
</p>
<p align="center">
    <a href="http://forthebadge.com/" target="_blank">
    	<img src="https://forthebadge.com/images/badges/built-with-love.svg"
    </a>
    <a href="http://forthebadge.com/" target="_blank">
    	<img src="https://forthebadge.com/images/badges/gluten-free.svg"
    </a>
</p>

------
####  An unofficial website using a Nautiljon Scrapping API made by [@barthofu](https://github.com/barthofu), made 4 school

#### Table of content:

* **[Installation](#installation)**
* **[Uses](#uses)**
* **[Tech](#tech)**
* **[Author](#author)**
 
------

## Installation 

To run the project, you'll need to run 

```bash
  composer install
  php bin/console doctrine:migrations:migrate
  npm install
  npm run build
```

_Launch dev server_

```bash
  symfony server:start
```

And also you can find the file `byscan_manga.sql` in the project's root. You'll need to install it and update your .env file to use it.

------

## Uses

To add manga you have to go through the route : `/manga/add` or to use a csv file `manga/add/csv`.

If the manga already exists It'll just update it.

When you add a manga it will search it through the API on <a href="https://www.nautiljon.com">Nautiljon</a> and complete all the data we'll need.

Also, the suppress code is `1234`.

------

## Tech

**Client:** SCSS

**Server:** Symfony, MariaDB, Twig, Webpack & Encore

**API:** Nautiljon Scrapper by <a href="https://github.com/barthofu/nautiljon-scraper">Barthofu</a>.

------

## Author

- [@Ethanito](https://github.com/Etantestingmlimits) G1S3