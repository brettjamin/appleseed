<!DOCTYPE html>
<html lang="en">

<head>
    
    <!-- Meta -->
    <meta charset="utf-8" />

    <!-- Title -->
    <title><?php echo __("Login Component | Appleseed"); ?></title>
    
    <!-- Links -->
    <?php $zApp->Theme->UseStyles (); ?>
    
    <!-- Javascript --> 
    <!--[if IE]>
    <script src="/themes/default/style/html5.js"></script>
    <![endif]-->
</head>

<body id="www-website-com">

	<?php $zApp->Components->Go ( "login", "logout", "logout", "logout" ); ?>

</html>
