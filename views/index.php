<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport"
        content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <link rel="canonical" href="">
  <meta name="keywords" content=""/>
  <meta name="description" content=""/>
  <meta property="og:title" content="">
  <meta property="og:description" content="">
  <meta property="og:url" content="">
  <meta property="og:image" content="">
  <meta property="og:site_name" content=""/>
  <meta property="og:type" content="website"/>
  <!-- Twitter tags -->
  <meta name="twitter:title" content=""/>
  <meta name="twitter:card" content=""/>
  <meta name="twitter:site" content=""/>
  <meta name="twitter:description" content=""/>
  <meta name="twitter:image" content=""/>
  <title>Title</title>
  <script src="<?php echo getBasePath(); ?>/assets/js/vue@2.6.12.js" type="text/javascript"></script>
  <script src="<?php echo getBasePath(); ?>/assets/js/axios.min.js" type="text/javascript"></script>
</head>
<body>
index
<div id="indexApp">


</div>
<script>
    const apiBaseUrl = '<?php echo getBasePath(true); ?>'
    const app = new Vue({
        el: '#indexApp',
        created() {
            const data = new FormData();
            axios.post(apiBaseUrl + '/registration', data)
                .then(({data, status}) => {
                    alert(data)
                })
                .catch((error) => {
                    alert(error)
                });
        }
    });
</script>
</body>
</html>
