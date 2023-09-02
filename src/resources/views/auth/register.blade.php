@extends('layouts.app')
@section('title', 'Register')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-center mt-5">
            <div class="form-group col-xs-12 col-md-6 border-purple rounded border p-4">
                <h1 class="bg-purple text-light mb-4 rounded p-3 text-center">Kenamental.com|Register</h1>
                <form action="register" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="name" name="name" id="name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </div>
                    <div class="mb-3">
                        <a href="/login" class="btn btn-secondary w-100">Back to Login</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
