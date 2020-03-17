@php
use Sdkconsultoria\Base\Widgets\Grid\GridView;
use Sdkconsultoria\Base\Widgets\Information\{BreadCrumb};
use Sdkconsultoria\Base\Widgets\Messages\Alert;
@endphp
@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.target.items'))


@section('breadcrumb')
    <?= BreadCrumb::generate([
        'Active'    => __('scraping::attributes.target.items'),
        ]) ?>
@endsection

@section('content')

    @card({{__('scraping::attributes.target.items')}})
    <div class="form-group">
        <a href="{{route('target.create')}}" class="btn btn-primary"> @lang('scraping::attributes.target.create') </a>
    </div>
    <?= Alert::generate() ?>
    <?= GridView::generate([
        'model' => $model,
        'models' => $models,
        'route' => 'target',
        'key' => 'seoname',
        'attributes' => [
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
            
        ]
    ])?>
    @endcard
@endsection
