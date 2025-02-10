@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Admin Dashboard</h2>

    <div class="row">
        <!-- Total Users Card -->
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-users fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text fs-4">{{ \App\Models\User::count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Posts Card -->
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-file-alt fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Total Posts</h5>
                        <p class="card-text fs-4">{{ \App\Models\Post::count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
