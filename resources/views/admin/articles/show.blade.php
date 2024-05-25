@extends('layouts.appAdmin')
@section('style')
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/assets/custom_css/sweetalert2.min.css') }}">
    <style>
        .app-show{
            padding-bottom: 5px;
        }
    </style>
@endsection
@section('content')
    <div id="section_one">
        <div class="heading-title">
            <h3> Article Details </h3>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-sm-12 col-md-12 col-12">
            <div id="section_userlist" class="common-section">
                <div class="row">
                    <div class="col-xl-12 col-sm-12 col-md-12 col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="has-bottom-line title"><span>Article Details</span></h4>
                        </div>
                    </div>
                    <div class="col-xl-12 col-sm-12 col-md-12 col-12 res-user-show">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%;">Title</th>
                                    <td>{{ $article->title }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 30%;">Category</th>
                                    <td>{{ $article->category->title ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 30%;">Tags</th>
                                    <td>{{ $article->tags }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 30%;">Description</th>
                                    <td>{!! $article->description !!}</td>
                                </tr>
                                <tr>
                                    <th style="width: 30%;">Meta Keyword</th>
                                    <td>{{ $article->meta_keyword }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 30%;">Meta Description</th>
                                    <td>{{ $article->meta_description }}</td>
                                </tr>
                                <tr>
                                    <th>Image</th>
                                    <td>
                                        <a href="{{ Config('app.aws_path').$article->image }}" target="_blank" class="btn btn-yellow">View</a>
                                        <a href="{{ route('downloadDocumentsUploadeAdmin',['file'=>$article->image]) }}" class="btn btn-blue">Download</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


@endsection

@section('script')
@endsection
