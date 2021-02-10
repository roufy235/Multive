const app = new Vue({
    el: '#indexApp',
    created() {
        const formData = new FormData();
        axios.get(apiBaseUrl + '/registration')
            .then(({data, status}) => {
                if (status === 200) {
                    alert(data.statusStr)
                } else {
                    alert('error')
                }
            })
            .catch((error) => {
                alert(error)
            });
    }
});
