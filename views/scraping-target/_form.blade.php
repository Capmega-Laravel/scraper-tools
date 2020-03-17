@php
use Sdkconsultoria\Base\Widgets\Form\ActiveField;
@endphp

<?= ActiveField::Input($model, 'name')?>
<?= ActiveField::Input($model, 'api_key')?>
<?= ActiveField::Input($model, 'domain')?>
<?= ActiveField::Input($model, 'entry')?>

<div class="form-group">
    <button type="submit" class="btn btn-primary">@lang('base::messages.save')</button>
</div>
