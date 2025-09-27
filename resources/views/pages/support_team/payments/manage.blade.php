@extends('layouts.master')
@section('page_title',  __('msg.student_payments'))
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-cash2 mr-2"></i> {{ __('msg.student_payments') }}</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <form method="post" action="{{ route('payments.select_class') }}">
                @csrf
              <div class="row">
                  <div class="col-md-6 offset-md-3">
                      <div class="row">
                          <div class="col-md-10">
                              <div class="form-group">
                                  <label for="my_class_id" class="col-form-label font-weight-bold">{{ __('msg.class_6788') }}</label>
                                  <select required id="my_class_id" name="my_class_id" class="form-control select">
                                      <option value="">{{ __('msg.select_class') }}</option>
                                      @foreach($my_classes as $c)
                                          <option {{ ($selected && $my_class_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                      @endforeach
                                  </select>
                              </div>
                          </div>

                          <div class="col-md-2 mt-4">
                              <div class="text-right mt-1">
                                  <button type="submit" class="btn btn-primary">{{__('msg.submit')}} <i class="icon-paperplane ml-2"></i></button>
                              </div>
                          </div>

                      </div>
                  </div>
              </div>

            </form>
        </div>
    </div>
    @if($selected)
        <div class="card">
            <div class="card-body">
                <table class="table datatable-button-html5-columns">
                    <thead>
                    <tr>
                        <th>{{ __('msg.sn') }}</th>
                        <th>{{ __('msg.photo') }}</th>
                        <th>{{ __('msg.name_49ee') }}</th>
                        <th>{{ __('msg.adm_no_e965') }}</th>
                        <th>{{ __('msg.payments') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                            <td>{{ $s->user->name }}</td>
                            <td>{{ $s->adm_no }}</td>
                            <td>
                                <div class="dropdown">
                                    <a href="#" class=" btn btn-danger" data-toggle="dropdown"> {{ __('msg.manage_payments') }} <i class="icon-arrow-down5"></i>
                                    </a>
                            <div class="dropdown-menu dropdown-menu-left">
                                <a href="{{ route('payments.invoice', [Qs::hash($s->user_id)]) }}" class="dropdown-item">{{__('msg.all_payments')}}</a>
                                @foreach(Pay::getYears($s->user_id) as $py)
                                @if($py)
                                    <a href="{{ route('payments.invoice', [Qs::hash($s->user_id), $py]) }}" class="dropdown-item">{{ $py }}</a>
                                @endif
                                @endforeach
                            </div>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
