@extends('layouts.app')
@section('title', 'Login Admin')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-center mt-5">
            <div class="form-group col-xs-12 col-md-6 border-primary rounded border p-4">
                <h2 class="bg-primary text-light mb-4 rounded p-3 text-center">Kenamental.com | Login | Admin</h2>
                <form action="/admin/login" method="POST">
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
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </div>
                    <div class="mb-3">
                        <a href="/admin/register" class="btn btn-secondary w-100">Register</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection
