@php
use Sdkconsultoria\Base\Widgets\Information\BreadCrumb;
use Sdkconsultoria\Base\Widgets\Form\ActiveField;
use Sdkconsultoria\Base\Widgets\Messages\Error;
@endphp

@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.source.edit'))

@section('breadcrumb')
    <?= BreadCrumb::generate([
        'source.index' => __('scraping::attributes.source.items'),
        'Active'    => __('scraping::attributes.source.edit'),
        ]) ?>
@endsection

@section('content')
    @card({{__('scraping::attributes.source.edit')}})
        <?= Error::generate($errors) ?>
        <form action="{{route('source.update', $model->seoname)}}" method="post" novalidate>
            @csrf
            @method('PUT')
            @include('scraping::scraping-source._form')
        </form>
    @endcard
@endsection
