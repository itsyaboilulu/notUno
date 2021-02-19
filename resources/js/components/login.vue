<template>
    <div id="login">
        <div class="login">
            <h4>LOGIN</h4>
            <form id="login-form" method="post">
                <input id="_token" type="hidden" name="_token" value="">
                <input type="text" name="username" v-model="input.username" placeholder="Username" /><br>
                <input type="password" name="password" v-model="input.password" placeholder="Password" /><br>
                <input id="checkbox-remember" type="checkbox" name="remember" :checked="remember">
                <label for="remember">Remember me</label>
            </form>
            <button type="button" v-on:click="login()">Login</button><button v-on:click="register()" >Register</button>
        </div>
    </div>
</template>

<script>
    export default {
        props:['action','crf_token','remember'],
        mounted: function() {
            document.getElementById('login-form').setAttribute('action',this.action);
            document.getElementById('_token').value = this.crf_token;
        },
        name: 'Login',
        data: function() {
            return {
                input: {
                    username: "",
                    password: ""
                }
            }
        },
        methods: {
            login: function() {
                var username = this.$data.input.username;
                var password = this.$data.input.password;
                if(username == "" || password == "") {
                        console.log("The username and / or password is incorrect");
                } else {
                    document.getElementById('login-form').submit();
                }
            },
            register (){
                document.location.href = 'register'
            }
        }
    }
</script>

