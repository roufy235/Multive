<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="<?php echo getBasePath(); ?>/assets/js/vue@2.6.12.js">
</head>
<body>
    <h1>
        hello <?php echo $name; ?>
    </h1>
    <a href="<?php print htmlspecialchars(getBasePath()); ?>/">contact us</a>
</body>
</html>
