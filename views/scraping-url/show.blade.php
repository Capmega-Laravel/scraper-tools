@php
use Sdkconsultoria\Base\Widgets\Grid\Details;
use Sdkconsultoria\Base\Widgets\Information\BreadCrumb;
@endphp
@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.url.show'))

@section('breadcrumb')
    <?= BreadCrumb::generate([
        'url.index' => __('scraping::attributes.url.items'),
        'Active'    => __('scraping::attributes.url.show'),
        ]) ?>
@endsection

@section('content')

    @card({{__('scraping::attributes.url.show')}})
        <?= Details::generate($model, [
            'id',
            'created_at',
            'updated_at',
            'status',
            'updated_by',
            'deleted_by',
            'deleted_reason',
            'deleted_at',
            'scraping_source_id',
            'name',
            'seoname',
            'url',
            'driver',
            
        ])?>
    @endcard
@endsection
