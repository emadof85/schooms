@extends('layouts.master')
@section('page_title', __('msg.field_definitions'))

@section('content')
<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">{{ __('msg.field_definitions') }}</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <div class="text-right mb-3">
            <a href="{{ route('field-definitions.create') }}" class="btn btn-primary">
                <i class="icon-plus3 mr-2"></i>{{ __('msg.add_field') }}
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped datatable-button-html5-basic">
                <thead>
                    <tr>
                        <th>{{ __('msg.name') }}</th>
                        <th>{{ __('msg.label') }}</th>
                        <th>{{ __('msg.type') }}</th>
                        <th>{{ __('msg.required') }}</th>
                        <th>{{ __('msg.active') }}</th>
                        <th>{{ __('msg.sort_order') }}</th>
                        <th class="text-center">{{ __('msg.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($field_definitions as $field)
                    <tr>
                        <td>{{ $field->name }}</td>
                        <td>{{ $field->label }}</td>
                        <td>{{ ucfirst($field->type) }}</td>
                        <td>
                            @if($field->required)
                                <span class="badge badge-danger">{{ __('msg.yes') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ __('msg.no') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($field->active)
                                <span class="badge badge-success">{{ __('msg.active') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('msg.inactive') }}</span>
                            @endif
                        </td>
                        <td>{{ $field->sort_order }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('field-definitions.edit', $field->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="icon-pencil"></i>
                                </a>
                                <form action="{{ route('field-definitions.toggle', $field->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-warning">
                                        @if($field->active)
                                            <i class="icon-eye-blocked"></i>
                                        @else
                                            <i class="icon-eye"></i>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('field-definitions.destroy', $field->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('{{ __('msg.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="icon-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection