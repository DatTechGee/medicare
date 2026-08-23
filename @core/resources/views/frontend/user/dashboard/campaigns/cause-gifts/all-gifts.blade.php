@extends('frontend.user.dashboard.user-master')
@section('site-title')
    {{__('Campaign GIft List')}}
@endsection
@section('style')
  <x-media.css/>
    @include('backend.partials.datatable.style-enqueue')

    <style>
        .gift_table .attachment-preview {
            width: 104px;
            height: 60px;
        }

        .gift_table .alert-success {
            padding: 6px 8px;
        }
    </style>
@endsection
@section('section')
    @php
        $colors = ['success','primary','warning','danger','info','dark'];
    @endphp
<div class="row">
    <div class="col-lg-12">
        <h5 class="modal-title margin-bottom-25">{{__('All Gifts')}}
            <a class="btn btn-primary pull-right btn-sm" href="{{route('user.campaign.gift.new')}}">{{__('Add New Gift')}}</a>
        </h5>
        <div class="table-wrap table-responsive gift_table">
            <table class="table table-default">
                <thead>
                <th>{{__('ID')}}</th>
                <th>{{__('Title')}}</th>
                <th>{{__('Image')}}</th>
                <th>{{__('Amount')}}</th>
                <th>{{__('Gifts')}}</th>
                <th>{{__('Status')}}</th>
                <th>{{__('Action')}}</th>
                </thead>
                <tbody>
                @foreach($all_gifts as $data)
                    <tr>
                        <td>{{$data->id}}</td>
                        <td>{{$data->title}}</td>
                        <td>{!! render_attachment_preview_for_admin($data->image) !!}</td>
                        <td>{{ amount_with_currency_symbol($data->amount) }}</td>
                        <td>
                            @php
                                $gifts_decoded = json_decode($data->gifts) ?? [];
                            @endphp

                            @foreach($gifts_decoded as $gift)
                                <span class="badge badge-{{$colors[$loop->index % count($colors)]}}">{{$gift ?? ''}}</span>
                            @endforeach
                        </td>
                        <td>
                            <x-status-span :status="$data->status"/>
                        </td>
                        <td>

                            <a tabindex="0" class="btn btn-danger btn-sm swal_delete_button text-light">
                                <i class="fa fa-trash"></i>
                            </a>
                            <form method='post' action='{{route('user.campaign.gift.delete',$data->id)}}' class="d-none">
                                <input type='hidden' name='_token' value='{{csrf_token()}}'>
                                <br>
                                <button type="submit" class="swal_form_submit_btn d-none"></button>
                            </form>

                                <a href="{{route('user.campaign.gift.edit',$data->id)}}" class="btn btn-info category_edit_btn btn-sm">
                                    <i class="fa fa-edit"></i>
                                </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<x-media.markup
        :userUpload="true"
        :imageUploadRoute="route('user.upload.media.file')">
</x-media.markup>
@endsection
@section('scripts')
    <script>
        <x-btn.submit/>
        <x-btn.update/>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                $(document).on('click','.mobile_nav',function(e){
                  e.preventDefault(); 
                   $(this).parent().toggleClass('show');
               });
               
              $(document).on('click', '.category_edit_btn', function () {
                  var el = $(this);
                  var id = el.data('id');
                  var title = el.data('title');
                  var modal = $('#category_edit_modal');
                  var image = el.data('image');
                  var imageUrl = el.data('imageurl');

                  modal.find('input[name="title"]').val(title);
                  $('#case_update_id').val(id);

                  modal.find('textarea[name="description"]').val(el.data('description'));
                  if (image !== '') {
                      modal.find('.media-upload-btn-wrapper .img-wrap').html('<div class="attachment-preview"><div class="thumbnail"><div class="centered"><img class="avatar user-thumb" src="' + imageUrl + '" > </div></div></div>');
                      modal.find('.media-upload-btn-wrapper input').val(image);
                      modal.find('.media-upload-btn-wrapper .media_upload_form_btn').text('Change Image');
                  }
              });

                $(document).on('click','.swal_delete_button',function(e){
                  e.preventDefault();
                    Swal.fire({
                      title: '{{__("Are you sure?")}}',
                      text: '{{__("You would not be able to revert this item!")}}',
                      icon: 'warning',
                      showCancelButton: true,
                      confirmButtonColor: '#3085d6',
                      cancelButtonColor: '#d33',
                      confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                      if (result.isConfirmed) {
                        $(this).next().find('.swal_form_submit_btn').trigger('click');
                      }
                    });
                });
            });
        })(jQuery)
    </script>
    <x-media.js
            :deleteRoute="route('user.upload.media.file.delete')"
            :imgAltChangeRoute="route('user.upload.media.file.alt.change')"
            :allImageLoadRoute="route('user.upload.media.file.all')">
    </x-media.js>
@endsection
