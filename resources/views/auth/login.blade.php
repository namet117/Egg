@extends('layout.index')

@section('body')
<div class="t-login">
  <el-form
    ref="loginForm"
    :model="loginForm"
    :rules="loginRules"
    autocomplete="on"
    label-position="left"
  >
    {{ csrf_field() }}
    <el-form-item prop="name">
      <el-input
        v-model="loginForm.name"
        placeholder="请输入用户名"
        type="text"
        tabindex="1"
        autocomplete="on"
      >
        <template slot="prepend"><i class="el-icon-user"></i></template>
      </el-input>
    </el-form-item>

    <el-form-item prop="password">
      <el-input
        v-model="loginForm.password"
        type="password"
        placeholder="请输入密码"
        tabindex="2"
        autocomplete="on"
      >
        <template slot="prepend"><i class="el-icon-coin"></i></template>
      </el-input>
    </el-form-item>

    <el-button :loading="loading" type="primary" class="t-login-submit" @click.native.prevent="handleLogin">Login</el-button>
  </el-form>
</div>
@endsection

@section('custom_footer')
<script>
  window.loginUrl = '{{ route('doLogin') }}';
  window.backUrl = '{{ session('login_redirect', '') }}';
</script>
<script src="{{ mix('js/login.js') }}"></script>
@endsection
