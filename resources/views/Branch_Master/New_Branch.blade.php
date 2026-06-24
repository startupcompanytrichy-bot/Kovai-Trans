@extends('layouts.app')

@section('content')
<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="mb-0"><i class="icofont icofont-home mr-2 text-primary"></i>Add Branch</h4>
                                <small class="text-muted">Create a new branch under a company</small>
                            </div>
                            <a href="{{ route('branch') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="icofont icofont-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-block p-3">
                                <form method="POST" action="{{ route('branch.store') }}" id="branchForm">
                                    @csrf
                                    @include('Branch_Master._branch_fields')

                                    <hr class="my-4">

                                    <div class="d-flex justify-content-between">
                                        <button type="reset" class="btn btn-outline-danger">
                                            <i class="icofont icofont-refresh mr-1"></i> Reset
                                        </button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="icofont icofont-check mr-1"></i> Save Branch
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection