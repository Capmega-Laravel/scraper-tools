@php
use Sdkconsultoria\Base\Widgets\Information\BreadCrumb;
use Sdkconsultoria\Base\Widgets\Form\ActiveField;
use Sdkconsultoria\Base\Widgets\Messages\Error;
@endphp

@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.target.edit'))

@section('breadcrumb')
    <?= BreadCrumb::generate([
        'target.index' => __('scraping::attributes.target.items'),
        'Active'    => __('scraping::attributes.target.edit'),
        ]) ?>
@endsection

@section('content')
    @card({{__('scraping::attributes.target.edit')}})
        <?= Error::generate($errors) ?>
        <form action="{{route('target.update', $model->seoname)}}" method="post" novalidate>
            @csrf
            @method('PUT')
            @include('scraping::scraping-target._form')
        </form>
    @endcard
@endsection
