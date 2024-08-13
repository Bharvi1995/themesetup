@extends('layouts.admin.default')

@section('title')
Admin Ticket Show
@endsection
@section('breadcrumbTitle')
<nav aria-label="breadcrumb">
   <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.ticket') }}">Tickets</a></li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Show</li>
   </ol>
   <h6 class="font-weight-bolder mb-0">Show</h6>
</nav>
@endsection

@section('customeStyle')
<style type="text/css">
    .desc {
        background-color: #eaeaea;
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
        border-color: transparent #eaeaea transparent transparent;
        border-width: 15px;
    }

    .media-info {
        float: left;
        background: #eaeaea;
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