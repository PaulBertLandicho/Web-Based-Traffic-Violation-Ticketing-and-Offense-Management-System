@extends('layouts.layout')
@section('title', 'Past Fines | traffic Enforcers')

@section('content')
@include('layouts.components.enforcer.topNav')
@include('layouts.components.enforcer.leftsideNavbar')
@include('layouts.components.footer')

<!-- Dashboard main content start here -->
<div class="dashwrapper animated fadeIn">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <div class="container-fluid pt-4">
            <h1 class="mt-3">Search Driver Past Violations</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('enforcer.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Search Driver Past Fines</li>
            </ol>

            <div class="card mb-4">
                <div class="card-body p-lg-5">
                    <form method="GET" action="{{ route('fine.past') }}">
                        <div class="form-row align-items-center">
                            <div class="col-sm-9 my-1">
                                <input type="text" class="form-control" name="licenseid" placeholder="Enter License ID" value="{{ $licenseId ?? '' }}">
                            </div>
                            <div class="form-group col-xs-3">
                                <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-search"></i>Search</button>
                            </div>
                        </div>
                    </form>

                    @if(isset($results) && count($results) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Reference No</th>
                                    <th>Provisions</th>
                                    <th>Vehicle No</th>
                                    <th>Place</th>
                                    <th>Status</th>
                                    <th>Issued Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $fine)
                                <tr>
                                    <td>{{ $fine->id }}</td>
                                    <td>{{ $fine->violation_type }}</td>
                                    <td>{{ $fine->vehicle_no }}</td>
                                    <td>{{ $fine->place }}</td>
                                    <td>{{ ucfirst($fine->status) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($fine->issued_date)->format('F d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @elseif(isset($licenseId))
                    <div class="alert alert-warning mt-3">No fines found for license ID <strong>{{ $licenseId }}</strong>.</div>
                    @endif
                </div>
                @endsection