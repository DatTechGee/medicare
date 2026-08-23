@extends('backend.admin-master')
@section('site-title')
    {{__('Create Gift')}}
@endsection
@section('style')
    @include('backend.partials.datatable.style-enqueue')
    <x-media.css/>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-msg.error/>
                <x-msg.success/>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('Create Donation Gift')}}</h4>
                        <div class="bulk-delete-wrapper">
                            @can('donation-gift-list')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="btn-wrapper pull-right mb-3">
                                            <a href="{{route('admin.donations.gift.all')}}"
                                               class="btn btn-info text-white">{{__('All Gifts')}}</a>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        </div>


                        <form action="{{route('admin.donations.gift.store')}}" method="post">
                            <div class="modal-body">
                                @csrf
                                <div class="form-group">
                                    <label for="name">{{__('Title')}}</label>
                                    <input type="text" class="form-control" name="title" placeholder="{{__('title')}}">
                                </div>

                                <div class="form-group">
                                    <label for="name">{{__('Amount')}}</label>
                                    <input type="number" class="form-control" name="amount" placeholder="{{__('Amount')}}">
                                </div>

                                <div class="iconbox-repeater-wrapper">
                                    <div class="all-field-wrap">
                                        <div class="form-group">
                                            <label for="name">{{__('Gift ')}}</label>
                                            <input type="text" class="form-control" name="gifts[]" placeholder="{{__('Gift')}}">
                                        </div>
                                        <div class="action-wrap">
                                            <span class="add"><i class="ti-plus"></i></span>
                                            <span class="remove"><i class="ti-trash"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">{{__('Description')}}</label>
                                    <textarea name="description" class="form-control" cols="30" rows="4"
                                              placeholder="{{__('Description')}}"></textarea>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label for="status">{{__('Status')}}</label>
                                        <select name="status" class="form-control" id="status">
                                            <option value="publish">{{__("Publish")}}</option>
                                            <option value="draft">{{__("Draft")}}</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <label for="edit_designation">{{__('Delivery Date')}}</label>
                                        <input type="text" class="form-control date-flat" name="delivery_date" placeholder="{{__('Delivery Date')}}">
                                    </div>

                                    <div class="form-group col-lg-12">
                                        <label for="image">{{__('Image')}}</label>
                                        <div class="media-upload-btn-wrapper">
                                            <div class="img-wrap"></div>
                                            <input type="hidden" name="image">
                                            <button type="button" class="btn btn-info media_upload_form_btn"
                                                    data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}"
                                                    data-toggle="modal" data-target="#media_upload_modal">
                                                {{__('Upload Image')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                                <button id="submit" type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('backend.partials.media-upload.media-upload-markup')
@endsection
@section('script')
    <script src="{{asset('assets/backend/js/dropzone.js')}}"></script>
    <script>
        //Date Picker
        flatpickr('.date-flat', {
            enableTime: false,
            dateFormat: "d-m-Y",
        });
        <x-btn.submit/>
        <x-btn.update/>

    </script>
    @include('backend.partials.datatable.script-enqueue')
    @include('backend.partials.media-upload.media-js')
    <x-repeater/>
@endsection
