@php
use Sdkconsultoria\Base\Widgets\Information\{BreadCrumb};
use Sdkconsultoria\Base\Widgets\Messages\Error;
@endphp

@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.url.create'))

@section('breadcrumb')
    <?= BreadCrumb::generate([
        'url.index' => __('scraping::attributes.url.items'),
        'Active'    => __('scraping::attributes.url.create'),
        ]) ?>
@endsection

@section('content')
    @card({{__('scraping::attributes.url.create')}})

    <?= Error::generate($errors) ?>
    <form action="{{route('url.store')}}" method="post" novalidate>
        @csrf
        @include('scraping::scraping-url._form')
    </form>
    @endcard
@endsection
