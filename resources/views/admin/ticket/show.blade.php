@extends('layouts.admin.default')

@section('title')
Admin Ticket Show
@endsection
@section('breadcrumbTitle')
<a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.ticket') }}">Ticket</a> / Show
@endsection

@section('customeStyle')
<style type="text/css">
    .desc {
        background-color: #262626;
        padding: 10px 15px;
        border-radius: 15px;
        position: relative;
        min-height: 120px;
        float: right;
        width: calc(100% - 70px);
    }

    .desc:after {
        position: absolute;
        top: 10px;
        left: -29px;
        content: "";
        border: 15px solid red;
        border-color: transparent #262626 transparent transparent;
        border-width: 15px;
    }

    .media-info {
        float: left;
        background: #262626;
        color: #34383E;
        height: 50px;
        width: 50px;
        border-radius: 1.25rem;
        font-size: 20px;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }
</style>
@endsection

@section('content')
<div class="row">
    @include('partials.ticket.item' , ['backUrl' => route('admin.ticket') ])
</div>

@endsection