const app=new Vue({el:"#indexApp",created:function(){new FormData;axios.get(apiBaseUrl+"/registration").then(({data:t,status:a})=>{200===a?alert(t.statusStr):alert("error")}).catch(t=>{alert(t)})}});