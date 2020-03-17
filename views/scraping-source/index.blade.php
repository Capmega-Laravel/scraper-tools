@php
use Sdkconsultoria\Base\Widgets\Grid\GridView;
use Sdkconsultoria\Base\Widgets\Information\{BreadCrumb};
use Sdkconsultoria\Base\Widgets\Messages\Alert;
@endphp
@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.source.items'))


@section('breadcrumb')
    <?= BreadCrumb::generate([
        'Active'    => __('scraping::attributes.source.items'),
        ]) ?>
@endsection

@section('content')

    @card({{__('scraping::attributes.source.items')}})
    <div class="form-group">
        <a href="{{route('source.create')}}" class="btn btn-primary"> @lang('scraping::attributes.source.create') </a>
    </div>
    <?= Alert::generate() ?>
    <?= GridView::generate([
        'model' => $model,
        'models' => $models,
        'route' => 'source',
        'key' => 'seoname',
        'attributes' => [
            'created_at',
            'name',
            'status',
        ]
    ])?>
    @endcard
@endsection
