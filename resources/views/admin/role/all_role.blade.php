@extends('admin.admin_master')
@section('admin')
    <!-- Begin Page Content -->
    <div class="page-content">
        <!--breadcrumb-->
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Role</h6>
            <h6 class="m-0 font-weight-bold text-primary">
                <a href="{{ route('add.role') }}">
                    <button class="btn btn-info">Add Role</button>
                </a>
            </h6>
        </div>
        <!--end breadcrumb-->
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Role Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Sl</th>
                                <th>Role Name</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($allRole as $key => $role)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $role->name }}</td>

                                    <td style="display:flex">
                                        <a title="Edit Role" href="{{ route('edit.role', $role->id) }}"
                                            class="btn btn-dark text-light">
                                            <i class="fas fa-edit    "></i>
                                        </a>
                                        <a id="delete" href="{{ route('delete.role', $role->id) }}"
                                            class="ml-2 btn btn-danger" id="delete" title="Delete Role">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    <!-- End Page Content -->
@endsection
