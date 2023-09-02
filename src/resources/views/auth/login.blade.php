@extends('layouts.app')
@section('title', 'Login')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-center mt-5">
            <div class="form-group col-xs-12 col-md-6 border-purple rounded border p-4">
                <h1 class="bg-purple text-light mb-4 rounded p-3 text-center">Kenamental.com | Login</h1>
                <form action="login" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-purple w-100">Login</button>
                    </div>
                    <div class="mb-3">
                        <a href="/register" class="btn btn-info w-100">Register</a>
                    </div>
                </form>
                <a href="/auth/google/redirect" class="btn btn-secondary w-100">Login with Google <i
                        class="bi bi-google"></i></a>
            </div>
        </div>
    </div>
@endsection
