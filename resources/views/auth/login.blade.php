@extends('layout.index')

@section('body')
  <div class="layui-container">
    <div class="layui-row" style="margin-top: 80px;">
      <form class="layui-form login-form" lay-filter="loginForm" action="{{ route('login') }}" method="post">
        @csrf
        <div class="layui-form-item">
          <input type="text" name="name" value="" required lay-verify="required" placeholder="用户名" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-item">
          <input type="password" name="password" value="" required lay-verify="required" placeholder="密码" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-item login-form-button">
          <input type="hidden" name="remember" value="true">
          <button class="layui-btn" lay-submit lay-filter="loginForm">登录</button>
        </div>
      </form>
    </div>
  </div>
@endsection
