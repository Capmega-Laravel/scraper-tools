@php
use Sdkconsultoria\Base\Widgets\Grid\Details;
use Sdkconsultoria\Base\Widgets\Information\BreadCrumb;
@endphp
@extends('base::layouts.main')

@section('title_tab', __('scraping::attributes.url.show'))

@section('breadcrumb')
    <?= BreadCrumb::generate([
        'url.index' => __('scraping::attributes.url.items'),
        'Active'    => __('scraping::attributes.url.spin'),
        ]) ?>
@endsection

@section('content')

    @card({{__('scraping::attributes.url.spin')}})
        <h2>Current spin LVL: {{$post->spin_lvl}}</h2>
        <hr>
        <h4>Rewrite process</h4>
        <ul>
            <li>low: largest number of synonyms for various words and phrases, least readable unique variations of text</li>
            <li>medium: relatively reliable synonyms, usually well readable unique variations of text (default setting)</li>
            <li>high: only the most reliable synonyms, perfectly readable unique variations of text</li>
        </ul>
        <div class="form-group">
            <a href="{{route('url.spin', ['id'=> $model->seoname, 'lvl' => 'hight'])}}" class="btn btn-primary"> hight </a>
            <a href="{{route('url.spin', ['id'=> $model->seoname, 'lvl' => 'medium'])}}" class="btn btn-primary"> medium </a>
            <a href="{{route('url.spin', ['id'=> $model->seoname, 'lvl' => 'low'])}}" class="btn btn-primary"> low </a>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h1> @lang('scraping::attributes.url.content_original')</h1>
                {!!$post->description!!}
            </div>
            <div class="col-md-6">
                <h1> @lang('scraping::attributes.url.content_spined')</h1>
                {!!$post->spin!!}
            </div>
        </div>
    @endcard
@endsection
