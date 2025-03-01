@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-muted">Add Supplier</h2>
                            <form method="POST" class="custom-validation" action="{{ route('store.supplier')}}" novalidate="">
                                @csrf
                                <div class="mb-3 mt-3">
                                    <input type="text" id="name" name="name" class="form-control" required="" placeholder="Supplier Name"
                                        data-parsley-required-message="Supplier Name is required" >
                                </div>

                                <div class="mb-3">
                                    <div>
                                        <input type="email" class="form-control" name="email" id="email" parsley-type="email"
                                            placeholder="Enter a valid email">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div>
                                        <input type="tel" id="phone" name="phone" class="form-control"
                                            required="" placeholder="Phone Number"
                                            data-parsley-required-message="Phone Number is required.">
                                    </div>
                                </div>


                                <div class="mb-3">
                                    <div>
                                        <textarea required="" data-parsley-required-message="Address is required." name="address" id="address"
                                            class="form-control" rows="5" placeholder="Enter Your Supplier Address"></textarea>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <div>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light me-1">
                                            Add Supplier
                                        </button>
                                        <button type="reset" class="btn btn-secondary waves-effect">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
