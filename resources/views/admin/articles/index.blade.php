@extends('layouts.appAdmin')
@section('style')
    <style>
        table.dataTable.nowrap td {
            white-space: unset;
        }
    </style>
@endsection
@section('content')
    <div id="section_one">
        <div class="heading-title">
            <h3> Articles </h3>
        </div>
    </div>
    <div class="common-section mb-5">
        <div class="col-xl-12 col-sm-12 col-md-12 col-12">
            <div class="heading-title mb-4">
                <h3 class="mb-0">Articles </h3>
                <a href="{{ route('article.create') }}" class="btn btn-yellow"> <i class="fa fa-plus-circle me-2"
                        aria-hidden="true"></i>New Article</a>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <table id="user_role_list" class="table responsive nowrap custom-inner-tables" style="width:100%">
                        <thead>
                            <tr>
                                <th width="50px">Id</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th style="min-width: 170px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 0;
                            @endphp
                            @foreach ($data as $key => $article)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $article->title }}</td>
                                    <td>{{ $article->category->title ?? '' }}</td>
                                    <td>
                                        {!! strlen($article->description) > 300 ? substr($article->description, 0, 300) . '...' : $article->description !!}
                                    </td>
                                    <td>
                                        <a href="{{ route('article.edit', $article->id) }}" class="btn btn-yellow"><i
                                                class="fas fa-pen"></i></a>
                                        <button type="button" class="btn btn-yellow delete_modal"
                                            data-bs-target="#delete_modal" data-bs-toggle="modal"
                                            data-url="{!! URL::route('article.destroy', $article->id) !!}" data-id="{{ $article->id }}"><i
                                                class="fa fa-trash"></i></button>
                                        <a href="{{ url('admin/article/view/' . $article->slug) }}"
                                            class="btn btn-yellow"><i class="fa fa-eye" aria-hidden="true"></i></a>
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
