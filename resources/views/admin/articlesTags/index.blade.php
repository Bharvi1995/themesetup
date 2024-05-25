@extends('layouts.appAdmin')


@section('content')
    <div id="section_one">
        <div class="heading-title">
            <h3> Article Tags </h3>
        </div>
    </div>
    <div class="common-section mb-5">
        <div class="col-xl-12 col-sm-12 col-md-12 col-12">
            <div class="heading-title mb-4">
                <h3 class="mb-0">Article Tags </h3>
                <a href="{{ route('article-tags.create') }}" class="btn btn-yellow"> <i class="fa fa-plus-circle me-2"
                        aria-hidden="true"></i>New Tag</a>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <table id="user_role_list" class="table responsive nowrap custom-inner-tables" style="width:100%">
                        <thead>
                            <tr>
                                <th width="50px">Id</th>
                                <th>Title</th>
                                <th>Meta keyword</th>
                                <th>Meta description</th>
                                <th style="min-width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 0;
                            @endphp
                            @foreach ($data as $key => $tag)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $tag->title }}</td>
                                    <td>{{ $tag->meta_keyword }}</td>
                                    <td>{{ $tag->meta_description }}</td>
                                    <td>
                                        <a href="{{ route('article-tags.edit', $tag->id) }}" class="btn btn-yellow"><i
                                                class="fas fa-pen"></i></a>
                                        <button type="button" class="btn btn-yellow delete_modal"
                                            data-bs-target="#delete_modal" data-bs-toggle="modal"
                                            data-url="{!! URL::route('article-tags.destroy', $tag->id) !!}" data-id="{{ $tag->id }}"><i
                                                class="fa fa-trash"></i></button>
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
@endsection

@section('script')
@endsection
