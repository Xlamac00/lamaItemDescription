# ItemDescription
Module created for Lama Plus s.r.o. to manage item descriptions and their translations.

Example how to initialize this module:
include ('./src/ItemTemplate.class.php');
$it = new ItemTemplate();
<head>
<?php echo $it->headers(); ?>
</head>
<body>
<?php echo $it->draw(); ?>
</body>

Variables this module is saving:
$_SESSION[template_id]
$_SESSION[template_filter_page]
$_SESSION[template_filter_option]
$_SESSION[template_language]
$_COOKIE[template_count]
$_COOKIE[template_language]

Libraries this module requires to be functional:
JQuery - https://jquery.com/
Bootstrap - https://getbootstrap.com/
Fontawesome - https://fontawesome.com/
Popper - https://popper.js.org

PHP version this module was developed on:
PHP 7.1
PHP version this module was tested for:
PHP 7.1, PHP 5.3

DB Server this module was developed on:
MySQL 8 (MariaDB 10.2.17)
DB Server this module was tested on:
MySQL 8.0, MySQL 5.6.12 (MariaDB 10.0.21)
