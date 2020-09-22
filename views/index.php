<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="<?php echo getBasePath(); ?>/assets/js/vue@2.6.12.js" type="text/javascript"></script>
    <script src="<?php echo getBasePath(); ?>/assets/js/axios.min.js" type="text/javascript"></script>
</head>
<body>
index
<div id="indexApp">

</div>
<script>
    const baseUrl = '<?php echo getBasePath(true); ?>'
    const app = new Vue({
        el: '#indexApp',
        created() {
            const data = new FormData();
            axios.post(baseUrl + '/registration', data)
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
