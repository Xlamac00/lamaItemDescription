<?php
  include ('./src/ItemTemplate.class.php');
  $it = new ItemTemplate();
?>

<!DOCTYPE html>
    <html>
    <head>
        <meta charset=utf-8 />
        <title></title>
        <script src="./lib/jquery-3.3.1.min.js"></script>
        <?php echo $it->headers(); ?>
        <script src="./lib/bootstrap-4.1.3-dist/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="./lib/bootstrap-4.1.3-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="./lib/fontawesome-free-5.3.1-web/css/all.min.css">
    </head>
    <body>

    <?php echo $it->draw(); ?>

    </body>
</html>
