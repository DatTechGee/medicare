@extends('backend.admin-master')
@section('site-title')
    {{__('Country List')}}
@endsection
@section('style')
    <x-media.css/>
    <x-datatable.css/>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-msg.error/>
                <x-msg.success/>
            </div>
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('Country Items')}}</h4>
                        <div class="bulk-delete-wrapper">
                                <div class="select-box-wrap">
                                    <x-bulk-action/>
                                </div>

                                <div class="btn-wrapper">
                                    <a href="" data-toggle="modal" data-target="#new_testimonial"
                                       class="btn btn-info pull-right mb-4">{{__('Add New')}}</a>
                                </div>

                        </div>

                        <div class="table-wrap table-responsive">
                            <table class="table table-default">
                                <thead>
                                <x-bulk-th/>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Action')}}</th>
                                </thead>
                                <tbody>
                                @foreach($all_countries as $data)
                                    @php $img_url =''; @endphp
                                    <tr>
                                        <td>
                                            <x-bulk-delete-checkbox :id="$data->id"/>
                                        </td>
                                        <td>{{$data->id}}</td>

                                        <td>{{$data->name}}</td>
                                        <td>
                                            <x-status-span :status="$data->status"/>
                                        </td>
                                        <td>
                                                <x-delete-popover :url="route('admin.country.delete',$data->id)"/>

                                                <a href="#"
                                                   data-toggle="modal"
                                                   data-target="#testimonial_item_edit_modal"
                                                   class="btn btn-primary btn-xs mb-3 mr-1 testimonial_edit_btn"
                                                   data-id="{{$data->id}}"
                                                   data-action="{{route('admin.country.update')}}"
                                                   data-name="{{$data->name}}"
                                                   data-status="{{$data->status}}"
                                                >
                                                    <i class="ti-pencil"></i>
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

                <div class="modal fade" id="new_testimonial" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{__('New Country Item')}}</h5>
                                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                            </div>
                            <form action="{{route('admin.country')}}" method="post" enctype="multipart/form-data">
                                <div class="modal-body">
                                    @csrf

                                    <div class="form-group">
                                        <label for="edit_name">{{__('Name')}}</label>
                                        <input type="text" class="form-control" name="name"
                                               placeholder="{{__('Name')}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_status">{{__('Status')}}</label>
                                        <select name="status" class="form-control">
                                            <option value="publish">{{__('Publish')}}</option>
                                            <option value="draft">{{__('Draft')}}</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">{{__('Close')}}</button>
                                    <button type="submit" class="btn btn-primary">{{__('Save Changes')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="testimonial_item_edit_modal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{__('Edit Testimonial Item')}}</h5>
                                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                            </div>
                            <form action="#" id="testimonial_edit_modal_form" method="post"
                                  enctype="multipart/form-data">
                                <div class="modal-body">
                                    @csrf
                                    <input type="hidden" name="id" id="testimonial_id" value="">
                                    <div class="form-group">
                                        <label for="edit_name">{{__('Name')}}</label>
                                        <input type="text" class="form-control" id="edit_name" name="name"
                                               placeholder="{{__('Name')}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_status">{{__('Status')}}</label>
                                        <select name="status" class="form-control" id="edit_status">
                                            <option value="publish">{{__('Publish')}}</option>
                                            <option value="draft">{{__('Draft')}}</option>
                                        </select>
                                    </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">{{__('Close')}}</button>
                                    <button type="submit" class="btn btn-primary">{{__('Save Changes')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            <x-media.markup/>
            @endsection
            @section('script')

        <script>
            (function ($) {
                "use strict";

                $(document).ready(function () {
                    <x-bulk-action-js :url="route('admin.country.bulk.action')" />
                    <x-btn.submit/>
                    <x-btn.update/>

                    $(document).on('click', '.testimonial_edit_btn', function () {
                        var el = $(this);
                        var id = el.data('id');
                        var name = el.data('name');
                        var action = el.data('action');


                        var form = $('#testimonial_edit_modal_form');
                        form.attr('action', action);
                        form.find('#testimonial_id').val(id);
                        form.find('#edit_name').val(name);
                        form.find('#edit_status option[value="' + el.data('status') + '"]').attr('selected', true);
                        if (imageid != '') {
                            form.find('.media-upload-btn-wrapper .img-wrap').html('<div class="attachment-preview"><div class="thumbnail"><div class="centered"><img class="avatar user-thumb" src="' + image + '" > </div></div></div>');
                            form.find('.media-upload-btn-wrapper input').val(imageid);
                            form.find('.media-upload-btn-wrapper .media_upload_form_btn').text('Change Image');
                        }
                    });
                });
            })(jQuery)
        </script>

        <x-datatable.js/>
        <script src="{{asset('assets/backend/js/dropzone.js')}}"></script>
        <x-media.js/>
@endsection
