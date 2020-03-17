@php
use Sdkconsultoria\Base\Widgets\Information\BreadCrumb;
use Sdkconsultoria\Base\Widgets\Form\ActiveField;
use Sdkconsultoria\Base\Widgets\Messages\Error;
@endphp

@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.url.edit'))

@section('breadcrumb')
    <?= BreadCrumb::generate([
        'url.index' => __('scraping::attributes.url.items'),
        'Active'    => __('scraping::attributes.url.edit'),
        ]) ?>
@endsection

@section('content')
    @card({{__('scraping::attributes.url.edit')}})
        <?= Error::generate($errors) ?>
        <form action="{{route('url.update', $model->seoname)}}" method="post" novalidate>
            @csrf
            @method('PUT')
            @include('scraping::scraping-url._form')
        </form>
    @endcard
@endsection
