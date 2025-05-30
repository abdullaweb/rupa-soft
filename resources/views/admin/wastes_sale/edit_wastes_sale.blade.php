@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" class="custom-validation" action="{{ route('update.wastes.sale') }}" novalidate=""
                            autocomplete="off">
                            @csrf
                            <input type="hidden" name="id" value="{{ $wastesInfo->id}}">
                            <div class="row mb-3">
                                <div class="col-12 text-secondary">
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Wastes Product" required
                                        data-parsley-required-message="Name is required" value="{{ $wastesInfo->name}}">
                                </div>
                            </div>


                            <div class="row mb-3">
                                <div class="col-12 text-secondary">
                                    <input type="text" name="amount" class="form-control" id="amount"
                                        placeholder="Purchase Amount" required="" data-parsley-trigger="keyup"
                                        data-parsley-validation-threshold="0" data-parsley-type="number"
                                        data-parsley-type-message="Input must be positive number"
                                        data-parsley-required-message="Amount is required" value="{{ $wastesInfo->amount}}" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-9 col-lg-9 text-secondary">
                                    <input type="submit" class="btn btn-primary px-4" value="Update Wastes Sale" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
