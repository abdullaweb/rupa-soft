@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <!--breadcrumb-->
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Product</h6>
            <h6 class="m-0 font-weight-bold text-primary">
                <a href="{{ route('sub.category.add') }}">
                    <button class="btn btn-info"> <i class="fa fa-plus-circle" aria-hidden="true"> Add Sub Category </i></button>
                </a>
            </h6>
        </div>
        <!--end breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="datatable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Unit</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Unit</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($subcatAll as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                {{ $item->name }}
                                            </td>

                                            <td>
                                                {{ $item['category']['name'] }}
                                            </td>
                                            <td>
                                                {{ $item['unit']['name'] }}
                                            </td>

                                            <td style="display:flex">
                                                <a style="margin-left: 5px;"
                                                    href="{{ route('sub.category.edit', $item->id) }}"
                                                    class="btn btn-info">
                                                    <i class="fas fa-edit    "></i>
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
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script src="{{ asset('backend/assets/js/code.js') }}"></script>
@endsection
