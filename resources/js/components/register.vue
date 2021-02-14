<template>
    <div id="login">
        <div class="login">
            <h4>Register</h4>
            <form id="login-form" method="post" autocomplete="false">
                <input id="_token" type="hidden" name="_token" value="">
                <input type="text" name="username" v-model="input.username" placeholder="Username" />
                <input type="password" name="password" v-model="input.password" placeholder="Password" />
                <input type="password" name="password_confirmation" v-model="input.c_password" placeholder="Confirm Password" />
            </form>
            <p class="error" v-if="error" >{{error}}</p>
            <button :disabled=" !input.username && !input.password && !input.c_password " v-on:click="register()">Register</button>
        </div>
    </div>
</template>

<script>
    import { ajax }         from "../ajax.min.js";
    export default {
        props:['action','crf_token'],
        mounted: function() {
            document.getElementById('login-form').setAttribute('action',this.action);
            document.getElementById('_token').value = this.crf_token;
        },
        name: 'Register',
        data: function() {
            return {
                error:0,
                input: {
                    username:   "",
                    password:   "",
                    c_password: ""
                }
            }
        },
        methods: {
            register: function() {
                if (this.input.password !== this.input.c_password){
                    this.error = 'Password did not match';
                    return;
                }
                this.ajax();
            },
            ajax(){
                return ajax( this.checkResponse, 'api/register', {
                    name  :     this.input.username
                }, 1 );
            },
            checkResponse($r){
                if ($r.available){
                    document.getElementById('login-form').submit();
                } else {
                    this.error = 'Username already in use';
                    return;
                }
            }
        }
    }
</script>

