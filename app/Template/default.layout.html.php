<?php
use TinyFw\Lang;
?>
<html lang="en">
<!-- html Head -->
<head>
    <!-- Environment: <?php echo _ENV; ?> : Version 1.1.3.5.2240-->
    <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>PubliBike - v2</title>
    <!-- Custom styles -->
    <link rel="stylesheet" href="web/public/assets/css/style.css" />
    <link rel="stylesheet" href="web/public/assets/css/style2.css" />
    <!-- Fontawesome styles -->
    <link
      rel="stylesheet"
      href="web/public/assets/vendor/fontawesome-free/css/all.min.css"
    />
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media
        queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file://
        -->
    <!--[if IE 9]>
      <link
        href="https://cdn.jsdelivr.net/gh/coliff/bootstrap-ie8/css/bootstrap-ie9.min.css"
        rel="stylesheet"
      />
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!--[if lte IE 8]>
      <link
        href="https://cdn.jsdelivr.net/gh/coliff/bootstrap-ie8/css/bootstrap-ie8.min.css"
        rel="stylesheet"
      />
      <script src="https://cdn.jsdelivr.net/g/html5shiv@3.7.3"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <!-- End of html Head -->
<!-- End of html Head -->
      <script>
        var _langData = <?php echo jsLang() ?>;
      </script>
      <script>
        var _lang = "<?php echo Lang::instance()->getLang() ?>";
      </script>
<body>
    <?php echo $output; ?>


     <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
    <script
      src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
      integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
      integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
      crossorigin="anonymous"
    ></script>
    <script src="web/public/assets/js/script.js"></script>
    <script src="web/public/assets/js/svg4everybody.min.js"></script>
    <script src="web/public/assets/js/svg4everybody.legacy.min.js"></script>
    <script>
      svg4everybody();
    </script>

    <script defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCg0-frHnM59_GK8tZvBZe_m4UJwlfH6Y0&callback=initMap">
    </script>

    <script src="<?php echo \TinyFw\Asset::js('main.js'); ?>"></script>
</body>
</html>
