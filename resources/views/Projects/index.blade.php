@extends('layouts.default')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Projects List</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('projects.create') }}"> Create New Project</a>
                
                <a class="btn btn-info" href="{!! url('/getMavenlinkProject'); !!}"> Sync with Mavenlink</a>
            </div>
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Address</th>
            <th>Client Name</th>
            <th width="280px">Action</th>
        </tr>
    @foreach ($projects as $project)
    <tr>
        <td>{{ $project->project_no }}</td>
        <td>{{ $project->address}}</td>
        <td>{{ $project->client_name}}</td>
        <td>
            <a class="btn btn-info" href="{{ route('projects.show',$project->id) }}">Show</a>
            <a class="btn btn-primary" href="{{ route('projects.edit',$project->id) }}">Edit</a>
            {!! Form::open(['method' => 'DELETE','route' => ['projects.destroy', $project->id],'style'=>'display:inline']) !!}
            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
    </table>
    {!! $projects->render() !!}
@endsection