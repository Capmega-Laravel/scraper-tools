@php
use Sdkconsultoria\Base\Widgets\Grid\Details;
use Sdkconsultoria\Base\Widgets\Information\BreadCrumb;
@endphp
@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.target.show'))

@section('breadcrumb')
    <?= BreadCrumb::generate([
        'target.index' => __('scraping::attributes.target.items'),
        'Active'    => __('scraping::attributes.target.show'),
        ]) ?>
@endsection

@section('content')

    @card({{__('scraping::attributes.target.show')}})
        <?= Details::generate($model, [
            'id',
            'created_at',
            'updated_at',
            'status',
            'updated_by',
            'deleted_by',
            'deleted_reason',
            'deleted_at',
            'name',
            'seoname',
            'api_key',
            'domain',
            'entry',
            
        ])?>
    @endcard
@endsection
