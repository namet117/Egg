const { default: Axios } = require("axios");

new Vue({
  el: '#egg',
  data() {
    return {
      loginForm: {
        name: '',
        password: '',
      },
      loginRules: {
        name: [{ required: true, trigger: 'blur', message: '请输入用户名' }],
        password: [{ required: true, trigger: 'blur', message: '请输入密码' }],
      },
      loading: false,
    };
  },
  methods: {
    handleLogin() {
      this.$refs['loginForm'].validate(valid => {
        if (!valid) {
          return '';
        }
        this.loading = true;
        axios.post(window.loginUrl, this.loginForm)
          .then(() => {
            location.replace('/');
          })
          .catch(e => {
            this.loading = false;
            let msg = '登录失败';
            if (e.response) {
              msg = e.response.status === 422 ? '帐号或密码错误' : e.response.statusText;
            }
            this.$message.error(msg);
          })
      });
    }
  },
});
