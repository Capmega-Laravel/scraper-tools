@php
use Sdkconsultoria\Base\Widgets\Grid\Details;
use Sdkconsultoria\Base\Widgets\Information\BreadCrumb;
@endphp
@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.source.show'))

@section('breadcrumb')
    <?= BreadCrumb::generate([
        'source.index' => __('scraping::attributes.source.items'),
        'Active'    => __('scraping::attributes.source.show'),
        ]) ?>
@endsection

@section('content')

    @card({{__('scraping::attributes.source.show')}})
        <div class="form-group">
            <a href="{{route('source.scan-url', $model->seoname)}}" class="btn btn-primary">@lang('scraping::attributes.source.load_menu')</a>
        </div>
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

        ])?>
    @endcard

@endsection
