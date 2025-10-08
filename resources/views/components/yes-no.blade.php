@props(['value'])

@if($value)
    <span class="badge badge-success">{{ __('msg.yes') }}</span>
@else
    <span class="badge badge-secondary">{{ __('msg.no') }}</span>
@endif
