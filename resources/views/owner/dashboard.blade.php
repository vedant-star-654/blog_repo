@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Owner Dashboard</h2>

    <div class="row">
        <!-- Total Posts Card -->
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-file-alt fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">My Posts</h5>
                        <p class="card-text fs-4">{{ Auth::user()->posts->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shared Posts Count Card -->
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-share-alt fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Shared Posts</h5>
                        <p class="card-text fs-4">{{ Auth::user()->sharedPosts->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
