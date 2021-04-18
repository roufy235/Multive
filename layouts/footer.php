<script>
    const domain = '<?php echo $_ENV['PROJECT_WEB_DOMAIN_URL']; ?>'
    const token = localStorage.getItem('token') ? localStorage.getItem('token') : '';
    const axiosInstance = axios.create({
        baseURL: '<?php echo getBasePath(true); ?>',
        time: 10000,
        headers: {
            'Authorization' : 'Bearer ' + token
        }
    });
</script>
<script src="<?php echo getBasePath(); ?>/dist/_9550808079.js?version=<?php echo $_ENV['JAVASCRIPT_VERSION_CONTROL']; ?>"></script>
