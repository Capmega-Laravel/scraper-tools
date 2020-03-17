@php
use Sdkconsultoria\Base\Widgets\Information\{BreadCrumb};
use Sdkconsultoria\Base\Widgets\Messages\Error;
@endphp

@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.source.create'))

@section('breadcrumb')
    <?= BreadCrumb::generate([
        'source.index' => __('scraping::attributes.source.items'),
        'Active'    => __('scraping::attributes.source.create'),
        ]) ?>
@endsection

@section('content')
    @card({{__('scraping::attributes.source.create')}})

    <?= Error::generate($errors) ?>
    <form action="{{route('source.store')}}" method="post" novalidate>
        @csrf
        @include('scraping::scraping-source._form')
    </form>
    @endcard
@endsection
