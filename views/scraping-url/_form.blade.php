@php
use Sdkconsultoria\Base\Widgets\Form\ActiveField;
use Sdkconsultoria\BlogScraping\Models\ScrapingSource;
@endphp

<?= ActiveField::Input($model, 'scraping_source_id')->select(ScrapingSource::getSelect())?>
<?= ActiveField::Input($model, 'name')?>
<?= ActiveField::Input($model, 'url')?>
<?= ActiveField::Input($model, 'driver')->select(config('scraping.drivers'))?>

<div class="form-group">
    <button type="submit" class="btn btn-primary">@lang('base::messages.save')</button>
</div>
