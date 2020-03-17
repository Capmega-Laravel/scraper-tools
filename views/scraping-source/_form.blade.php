@php
use Sdkconsultoria\Base\Widgets\Form\ActiveField;
@endphp

<?= ActiveField::Input($model, 'name')?>
<?= ActiveField::Input($model, 'domain')?>

<div class="form-group">
    <button type="submit" class="btn btn-primary">@lang('base::messages.save')</button>
</div>
