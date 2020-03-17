@php
use Sdkconsultoria\Base\Widgets\Grid\GridView;
use Sdkconsultoria\Base\Widgets\Information\{BreadCrumb};
use Sdkconsultoria\Base\Widgets\Messages\Alert;
@endphp
@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.url.items'))


@section('breadcrumb')
    <?= BreadCrumb::generate([
        'Active'    => __('scraping::attributes.url.items'),
        ]) ?>
@endsection

@section('content')

    @card({{__('scraping::attributes.url.items')}})
    <div class="form-group">
        <a href="{{route('url.create')}}" class="btn btn-primary"> @lang('scraping::attributes.url.create') </a>
    </div>
    <?= Alert::generate() ?>
    <?= GridView::generate([
        'model' => $model,
        'models' => $models,
        'route' => 'url',
        'key' => 'seoname',
        'attributes' => [
            'created_at',
            'scraping_source_id',
            'name',
            'url',
            'driver',
            'status',
        ]
    ])?>
    @endcard
@endsection
