const pageApp = {
    data() {
        return {

        }
    },
    created() {
        // noinspection JSUnresolvedVariable
        axiosInstance.get('/registration')
            .then(({data, status}) => {
                if (status === 200) {
                    console.log('Connected to server!')
                } else {
                    console.error('Unable to connect to the server')
                }
            })
            .catch((error) => {
                alert(error)
            });
    }
}
Vue.createApp(pageApp).mount('#indexApp');
