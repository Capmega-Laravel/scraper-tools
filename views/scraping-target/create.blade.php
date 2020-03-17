@php
use Sdkconsultoria\Base\Widgets\Information\{BreadCrumb};
use Sdkconsultoria\Base\Widgets\Messages\Error;
@endphp

@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.target.create'))

@section('breadcrumb')
    <?= BreadCrumb::generate([
        'target.index' => __('scraping::attributes.target.items'),
        'Active'    => __('scraping::attributes.target.create'),
        ]) ?>
@endsection

@section('content')
    @card({{__('scraping::attributes.target.create')}})

    <?= Error::generate($errors) ?>
    <form action="{{route('target.store')}}" method="post" novalidate>
        @csrf
        @include('scraping::scraping-target._form')
    </form>
    @endcard
@endsection
