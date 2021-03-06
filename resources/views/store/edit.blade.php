@extends('layouts.adminlte.master')

@section('title')
    @lang('store.edit.title')
@endsection

@section('page_title')
    <span class="fa fa-umbrella fa-fw"></span>&nbsp;@lang('store.edit.page_title')
@endsection

@section('page_title_desc')
    @lang('store.edit.page_title_desc')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('admin_store_edit', $store->hId()) !!}
@endsection

@section('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('adminlte/fileinput/fileinput.css') }}">

    <style>
        .pac-container {
            background-color: #FFF;
            z-index: 2000;
            position: fixed;
            display: inline-block;
            float: left;
        }
        .modal{
            z-index: 2000;
        }
        .modal-backdrop{
            z-index: 1000;
        }​
    </style>
@endsection

@section('content')
    <div id="storeVue">
        <div v-show="errors.count() > 0" v-cloak>
            <div class="alert alert-danger">
                <strong>@lang('labels.GENERAL_ERROR_TITLE')</strong> @lang('labels.GENERAL_ERROR_DESC')<br><br>
                <ul v-for="(e, eIdx) in errors.all()">
                    <li>@{{ e }}</li>
                </ul>
            </div>
        </div>

        <form id="storeForm" class="form-horizontal" v-on:submit.prevent="validateBeforeSubmit()">
            {{ csrf_field() }}
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('store.edit.header.title')</h3>
                </div>
                <div class="box-body">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_store" data-toggle="tab">@lang('store.create.tab.store')&nbsp;<span id="storeDataTabError" class="parsley-asterisk hidden">*</span></a></li>
                            <li><a href="#tab_bank_account" data-toggle="tab">@lang('store.create.tab.bank_account')&nbsp;<span id="bankAccountTabError" class="parsley-asterisk hidden">*</span></a></li>
                            <li><a href="#tab_currencies" data-toggle="tab">@lang('store.create.tab.currencies')&nbsp;<span id="currenciesTabError" class="parsley-asterisk hidden">*</span></a></li>
                            <li><a href="#tab_settings" data-toggle="tab">@lang('store.create.tab.settings')&nbsp;<span id="settingsTabError" class="parsley-asterisk hidden">*</span></a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_store">
                                <div v-bind:class="{ 'form-group':true, 'has-error':errors.has('name') }">
                                    <label for="inputStoreName" class="col-sm-2 control-label">@lang('store.field.name')</label>
                                    <div class="col-sm-10">
                                        <input id="inputStoreName" name="name" type="text" class="form-control" value="{{ $store->name }}" placeholder="Name"
                                               v-validate="'required'" data-vv-as="{{ trans('store.field.name') }}">
                                        <span v-show="errors.has('name')" class="help-block" v-cloak>@{{ errors.first('name') }}</span>
                                    </div>
                                </div>
                                <div class="form-group {{ $errors->has('image_path') ? 'has-error' : '' }}">
                                    <label for="inputStoreImage" class="col-sm-2 control-label">&nbsp;</label>
                                    <div class="col-sm-10">
                                        @if(!empty($store->image_filename))
                                            <img src="{{ asset('images/'.$store->image_filename) }}" class="img-responsive img-circle" style="max-width: 150px; max-height: 150px;"/>
                                        @endif
                                        <input id="inputStoreImage" name="image_path" type="file" class="form-control" value="{{ old('image_path') }}">
                                        <span class="help-block">{{ $errors->has('image_path') ? $errors->first('image_path') : '' }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputAddress" class="col-sm-2 control-label">@lang('store.field.address')</label>
                                    <div class="col-sm-9">
                                        <textarea id="inputAddress" class="form-control" rows="5" name="address">{{ $store->address }}</textarea>
                                    </div>
                                    <div class="col-sm-1">
                                        <button id="btnChooseLocation" type="button" class="btn btn-default btn-mini" data-toggle="modal" data-target="#myModal"><i class="fa fa-location-arrow"></i></button>
                                        <input id="inputLatitude" type="hidden" name="latitude" value="{{ $store->latitude }}">
                                        <input id="inputLongitude" type="hidden" name="longitude" value="{{ $store->longitude }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPhone" class="col-sm-2 control-label">@lang('store.field.phone')</label>
                                    <div class="col-sm-10">
                                        <input id="inputPhone" name="phone_num" type="text" class="form-control" value="{{ $store->phone_num }}" placeholder="Phone">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputFax" class="col-sm-2 control-label">@lang('store.field.fax')</label>
                                    <div class="col-sm-10">
                                        <input id="inputFax" name="fax_num" type="text" class="form-control" value="{{ $store->fax_num }}" placeholder="Fax">
                                    </div>
                                </div>
                                <div v-bind:class="{ 'form-group':true, 'has-error':errors.has('tax_id') }">
                                    <label for="inputTax" class="col-sm-2 control-label">@lang('store.field.tax_id')</label>
                                    <div class="col-sm-10">
                                        <input id="inputTax" name="tax_id" type="text" class="form-control" value="{{ $store->tax_id }}" placeholder="Tax ID"/>
                                        <span v-show="errors.has('tax_id')" class="help-block" v-cloak>@{{ errors.first('tax_id') }}</span>
                                    </div>
                                </div>
                                <div v-bind:class="{ 'form-group':true, 'has-error':errors.has('status') }">
                                    <label for="inputStatus" class="col-sm-2 control-label">@lang('warehouse.field.status')</label>
                                    <div class="col-sm-10">
                                        <select class="form-control"
                                                name="status"
                                                v-model="status"
                                                v-validate="'required|checkactive'"
                                                data-vv-as="{{ trans('store.field.status') }}">
                                            <option v-bind:value="defaultStatus">@lang('labels.PLEASE_SELECT')</option>
                                            <option v-for="(value, key) in statusDDL" v-bind:value="key">@{{ value }}</option>
                                        </select>
                                        <span v-show="errors.has('status')" class="help-block" v-cloak>@{{ errors.first('status') }}</span>
                                    </div>
                                </div>
                                <div v-bind:class="{ 'form-group':true, 'has-error':errors.has('is_default') }">
                                    <label for="inputIsDefault" class="col-sm-2 control-label">@lang('store.field.default')</label>
                                    <div class="col-sm-10">
                                        <select class="form-control"
                                                name="is_default"
                                                v-model="is_default"
                                                v-validate="'required'"
                                                data-vv-as="{{ trans('store.field.default') }}">
                                            <option v-bind:value="defaultYesNo">@lang('labels.PLEASE_SELECT')</option>
                                            <option v-for="(value, key) in yesnoDDL" v-bind:value="key">@{{ value }}</option>
                                        </select>
                                        <span v-show="errors.has('is_default')" class="help-block" v-cloak>@{{ errors.first('is_default') }}</span>
                                    </div>
                                </div>
                                <div v-bind:class="{ 'form-group':true, 'has-error':errors.has('frontweb') }">
                                    <label for="inputFrontWeb" class="col-sm-2 control-label">@lang('store.field.frontweb')</label>
                                    <div class="col-sm-10">
                                        <select class="form-control"
                                                name="frontweb"
                                                v-validate="'required'"
                                                data-vv-as="{{ trans('store.field.frontweb') }}">
                                            <option v-bind:value="defaultYesNo">@lang('labels.PLEASE_SELECT')</option>
                                            <option v-for="(value, key) in yesnoDDL" v-bind:value="key">@{{ value }}</option>
                                        </select>
                                        <span v-show="errors.has('frontweb')" class="help-block" v-cloak>@{{ errors.first('frontweb') }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputRemarks" class="col-sm-2 control-label">@lang('store.field.remarks')</label>
                                    <div class="col-sm-10">
                                        <input id="inputRemarks" name="remarks" type="text" class="form-control" value="{{ $store->remarks }}" placeholder="Remarks">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_bank_account">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">@lang('store.create.table_bank.header.bank')</th>
                                            <th class="text-center">@lang('store.create.table_bank.header.account_name')</th>
                                            <th class="text-center">@lang('store.create.table_bank.header.account_number')</th>
                                            <th class="text-center">@lang('store.create.table_bank.header.remarks')</th>
                                            <th class="text-center">@lang('labels.ACTION')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(bank, bankIdx) in banks">
                                            <td v-bind:class="{ 'has-error':errors.has('bank_' + bankIdx) }">
                                                <select class="form-control"
                                                        name="bank[]"
                                                        v-model="bank.bank_id"
                                                        v-validate="'required'"
                                                        v-bind:data-vv-as="'{{ trans('store.create.table_bank.header.bank') }} ' + (bankIdx + 1)"
                                                        v-bind:data-vv-name="'bank_' + bankIdx">
                                                    <option value="">@lang('labels.PLEASE_SELECT')</option>
                                                    <option v-for="b in bankDDL" v-bind:value="b.id">@{{ b.name }} (@{{ b.short_name }})</option>
                                                </select>
                                            </td>
                                            <td v-bind:class="{ 'has-error':errors.has('account_name_' + bankIdx) }">
                                                <input type="text" class="form-control" name="account_name[]" v-model="bank.account_name"
                                                       v-validate="'required'" v-bind:data-vv-as="'{{ trans('store.create.table_bank.header.account_name') }} ' + (bankIdx + 1)"
                                                       v-bind:data-vv-name="'account_name_' + bankIdx">
                                            </td>
                                            <td v-bind:class="{ 'has-error':errors.has('account_number_' + bankIdx) }">
                                                <input type="text" class="form-control" name="account_number[]" v-model="bank.account_number"
                                                       v-validate="'required'" v-bind:data-vv-as="'{{ trans('store.create.table_bank.header.account_number') }} ' + (bankIdx + 1)"
                                                       v-bind:data-vv-name="'account_number_' + bankIdx">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="bank_remarks[]" v-model="bank.remarks">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-xs btn-danger" data="@{{ $index }}" v-on:click="removeSelectedBank($index)"><span class="fa fa-close fa-fw"></span></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button class="btn btn-xs btn-default" type="button" v-on:click="addNewBank()">@lang('buttons.create_new_button')</button>
                            </div>
                            <div class="tab-pane" id="tab_currencies">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">@lang('store.create.table_currencies.header.currencies')</th>
                                            <th class="text-center">@lang('store.create.table_currencies.header.base_currencies')</th>
                                            <th class="text-center">@lang('store.create.table_currencies.header.conversion_value')</th>
                                            <th class="text-center">@lang('store.create.table_currencies.header.remarks')</th>
                                            <th class="text-center">@lang('labels.ACTION')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item,idx) in currencies">
                                            <td v-bind:class="{ 'has-error':errors.has('currencies_' + idx) }">
                                                <select class="form-control"
                                                        name="currencies[]"
                                                        v-model="item.currencies_id"
                                                        v-validate="'required'" v-bind:data-vv-as="'{{ trans('store.create.table_currencies.header.currencies') }} ' + (idx + 1)"
                                                        v-bind:data-vv-name="'currencies_' + idx">
                                                    <option value="">@lang('labels.PLEASE_SELECT')</option>
                                                    <option v-for="c in currenciesDDL" v-bind:value="c.id">@{{ c.name }} (@{{ c.symbol }})</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <vue-icheck v-bind:id="'check_' + idx" name="base_currencies[]" v-model="item.is_base" v-on:click="selectedBaseCurrencies(idx)"></vue-icheck>
                                            </td>
                                            <td v-bind:class="{ 'has-error':errors.has('currencies_conv_val_' + idx) }">
                                                <input type="text" class="form-control" name="currencies_conversion_value[]" v-model="item.conversion_value" v-bind:readonly="(item.is_base != 0)"
                                                       v-validate="'required'" v-bind:data-vv-as="'{{ trans('store.create.table_currencies.header.conversion_value') }} ' + (idx + 1)"
                                                       v-bind:data-vv-name="'currencies_conv_val_' + idx">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="currencies_remarks[]" v-model="item.remarks">
                                            </td>
                                            <td class="text-center valign-middle">
                                                <button type="button" class="btn btn-xs btn-danger" v-on:click="removeSelectedCurencies(idx)"><span class="fa fa-close fa-fw"></span></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button class="btn btn-xs btn-default" type="button" v-on:click="addNewCurrencies()">@lang('buttons.create_new_button')</button>
                            </div>
                            <div class="tab-pane" id="tab_settings">
                                <div class="form-group {{ $errors->has('date_format') ? 'has-error' : '' }}">
                                    <label for="inputDateFormat" class="col-sm-2 control-label">@lang('store.field.date_format')</label>
                                    <div class="col-sm-10">
                                        <select name="date_format" class="form-control">
                                            <option value="DD-MM-YYYY" {{ $store->date_format == 'DD-MM-YYYY' ? 'selected':'' }}>{{ date('d-m-Y') }} (default)</option>
                                            <option value="DD MMM YYYY" {{ $store->date_format == 'DD MMM YYYY' ? 'selected':'' }}>{{ date('d M Y') }}</option>
                                            <option value="DD/MM/YYYY" {{ $store->date_format == 'DD/MM/YYYY' ? 'selected':'' }}>{{ date('d/m/Y') }}</option>
                                        </select>
                                        <span class="help-block">{{ $errors->has('date_format') ? $errors->first('date_format') : '' }}</span>
                                    </div>
                                </div>
                                <div class="form-group {{ $errors->has('time_format') ? 'has-error' : '' }}">
                                    <label for="inputTimeFormat" class="col-sm-2 control-label">@lang('store.field.time_format')</label>
                                    <div class="col-sm-4">
                                        <select name="date_format" class="form-control">
                                            <option value="hh:mm A" {{ $store->time_format == 'hh:mm A' ? 'selected':'' }}>{{ date('h:m a') }} (default)</option>
                                            <option value="hh:mm:ss" {{ $store->time_format == 'hh:mm:ss' ? 'selected':'' }}>{{ date('h:m:s') }}</option>
                                        </select>
                                    </div>
                                    <span class="help-block">{{ $errors->has('time_format') ? $errors->first('time_format') : '' }}</span>
                                </div>
                                <div class="form-group {{ $errors->has('thousand_separator') ? 'has-error' : '' }}">
                                    <label for="inputThousandSeparator" class="col-sm-2 control-label">@lang('store.field.thousand_separator')</label>
                                    <div class="col-sm-10">
                                        <select name="thousand_separator" class="form-control">
                                            <option value="," {{ $store->thousand_separator == ',' ? 'selected':'' }}>@lang('store.field.comma')&nbsp;-&nbsp;1,000,000 (Default)</option>
                                            <option value="." {{ $store->thousand_separator == '.' ? 'selected':'' }}>@lang('store.field.dot')&nbsp;-&nbsp;1.000.000</option>
                                            <option value=" " {{ $store->thousand_separator == ' ' ? 'selected':'' }}>@lang('store.field.space')&nbsp;-&nbsp;1 000 000</option>
                                        </select>
                                        <span class="help-block">{{ $errors->has('thousand_separator') ? $errors->first('thousand_separator') : '' }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputDecimalSeparator" class="col-sm-2 control-label">@lang('store.field.decimal_separator')</label>
                                    <div class="col-sm-10">
                                        <select name="decimal_separator" class="form-control">
                                            <option value="," {{ $store->decimal_separator == ',' ? 'selected':'' }}>@lang('store.field.comma')&nbsp;-&nbsp;0,00 (Default)</option>
                                            <option value="." {{ $store->decimal_separator == '.' ? 'selected':'' }}>@lang('store.field.dot')&nbsp;-&nbsp;0.00</option>
                                            <option value=" " {{ $store->decimal_separator == ' ' ? 'selected':'' }}>@lang('store.field.space')&nbsp;-&nbsp;0 00</option>
                                        </select>
                                        <span class="help-block">{{ $errors->has('decimal_separator') ? $errors->first('decimal_separator') : '' }}</span>
                                    </div>
                                </div>
                                <div v-bind:class="{ 'form-group':true, 'has-error':errors.has('decimal_digit') }">
                                    <label for="inputDecimalDigit" class="col-sm-2 control-label">@lang('store.field.decimal_digit')</label>
                                    <div class="col-sm-10">
                                        <input id="inputDecimalDigit" name="decimal_digit" type="text" class="form-control" value="{{ $store->decimal_digit }}" placeholder="Decimal Digit"
                                               v-validate="'required|max_value:4|min_value:0|numeric'" data-vv-as="{{ trans('store.field.decimal_digit') }}">
                                        <span v-show="errors.has('decimal_digit')" class="help-block" v-cloak>@{{ errors.first('decimal_digit') }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputRibbon" class="col-sm-2 control-label">@lang('store.field.ribbon')</label>
                                    <div class="col-sm-3">
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-none" {{ $store->ribbon == 'store-ribbon-none' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.none')</label>
                                        </div>
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-blue" {{ $store->ribbon == 'store-ribbon-blue' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.blue')</label>
                                        </div>
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-black" {{ $store->ribbon == 'store-ribbon-black' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.black')</label>
                                        </div>
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-red" {{ $store->ribbon == 'store-ribbon-red' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.red')</label>
                                        </div>
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-yellow" {{ $store->ribbon == 'store-ribbon-yellow' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.yellow')</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-purple" {{ $store->ribbon == 'store-ribbon-purple' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.purple')</label>
                                        </div>
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-green"  {{ $store->ribbon == 'store-ribbon-green' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.green')</label>
                                        </div>
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-blue-light" {{ $store->ribbon == 'store-ribbon-blue-light' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.blue-light')</label>
                                        </div>
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-black-light" {{ $store->ribbon == 'store-ribbon-black-light' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.black-light')</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-red-light" {{ $store->ribbon == 'store-ribbon-red-light' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.red-light')</label>
                                        </div>
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-yellow-light" {{ $store->ribbon == 'store-ribbon-yellow-light' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.yellow-light')</label>
                                        </div>
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-purple-light" {{ $store->ribbon == 'store-ribbon-purple-light' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.purple-light')</label>
                                        </div>
                                        <div class="checkbox icheck">
                                            <input type="radio" name="ribbon" value="store-ribbon-green-light" {{ $store->ribbon == 'store-ribbon-green-light' ? 'checked':'' }} class="is_icheck">
                                            <label>&nbsp;@lang('store.field.green-light')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputButton" class="col-sm-2 control-label"></label>
                        <div class="col-sm-10">
                            <a href="{{ route('db.admin.store') }}" class="btn btn-default">@lang('buttons.cancel_button')</a>
                            <button class="btn btn-default" type="submit">@lang('buttons.submit_button')</button>
                        </div>
                    </div>
                </div>
                <div class="box-footer"></div>
            </div>

            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span class="sr-only">Close</span></button>
                            <h4 class="modal-title">Choose Location</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="inputModalAddress">Address:</label>
                                <input type="text" class="form-control" id="inputModalAddress" name="inputModalAddress">
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="inputModalLat">Latitude:</label>
                                            <input type="text" class="form-control col-sm-6" id="inputModalLat" name="inputModalLat">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="inputModalLng">Longitude:</label>
                                            <input type="text" class="form-control col-sm-6" id="inputModalLng" name="inputModalLng">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="map" style="width: 870px; height: 400px;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" id="location-ok-btn">OK</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('custom_js')
    <script async defer src="https://maps.googleapis.com/maps/api/js?callback=mapsCallback&libraries=places&key={{ $mapsAPIKey }}"></script>
    <script type="application/javascript">
        Vue.use(VeeValidate, { locale: '{!! LaravelLocalization::getCurrentLocale() !!}' });

        Vue.component('vue-icheck', {
            template: "<input v-bind:id='id' v-bind:name='name' type='checkbox' v-bind:disabled='disabled' v-model='value'>",
            props: ['id', 'name', 'disabled', 'value'],
            model: {
                event: 'click'
            },
            mounted: function() {
                var vm = this;

                $(this.$el).iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue'
                }).on('ifChecked', function(event) {
                    vm.onChecked();
                }).on('ifUnchecked', function(event) {
                    vm.onUnchecked();
                });

                if (this.value) { $(this.$el).iCheck('check'); }
                if (this.disabled == 'true') { $(this.$el).iCheck('disable'); }
            },
            methods: {
                onChecked: function() {
                    var vm = this;
                    if (!isNaN(parseFloat(this.value)) && isFinite(this.value)) {
                        this.value = 1;
                    } else {
                        this.value = true;
                    }
                    vm.$emit('click', this.value);
                },
                onUnchecked: function() {
                    var vm = this;
                    if (!isNaN(parseFloat(this.value)) && isFinite(this.value)) {
                        this.value = 0;
                    } else {
                        this.value = false;
                    }
                    vm.$emit('click', this.value);
                }
            },
            watch: {
                value: function(newVal, oldVal) {
                    var vm = this;
                    if (this.value) { $(this.$el).prop('checked', true).iCheck('update'); }
                    else { $(this.$el).prop('checked', false).iCheck('update'); }
                }
            },
            destroyed: function() {
                $(this.$el).iCheck('destroy');
            }
        });

        var app = new Vue({
            el: '#storeVue',
            data: {
                banks: JSON.parse('{!! empty(htmlspecialchars_decode($store->bankAccounts)) ? '[]':htmlspecialchars_decode($store->bankAccounts) !!}'),
                currencies: JSON.parse('{!! empty(htmlspecialchars_decode($store->currenciesConversions)) ? '[]':htmlspecialchars_decode($store->currenciesConversions) !!}'),
                bankDDL: JSON.parse('{!! htmlspecialchars_decode($bankDDL) !!}'),
                currenciesDDL: JSON.parse('{!! htmlspecialchars_decode($currenciesDDL) !!}')
            },
            methods: {
                validateBeforeSubmit: function() {
                    this.$validator.validateAll().then(function(result) {
                        $('#loader-container').fadeIn('fast');
                        axios.post('{{ route('api.post.db.admin.store.edit', $store->hId()) }}' + '?api_token=' + $('#secapi').val()
                            , new FormData($('#storeForm')[0])
                            , { headers: { 'content-type': 'multipart/form-data' } })
                            .then(function(response) {
                                if (response.data.result == 'success') { window.location.href = '{{ route('db.admin.store') }}'; }
                            });
                    });
                },
                addNewBank: function() {
                    this.banks.push({
                        'bank_id': '',
                        'account_name': '',
                        'account_number': '',
                        'remarks': ''
                    });
                },
                removeSelectedBank: function(idx) {
                    this.banks.splice(idx, 1);
                },
                addNewCurrencies: function(){
                    this.currencies.push({
                        'currencies_id': '',
                        'is_base': 0,
                        'conversion_value': '',
                        'remarks': ''
                    });
                },
                selectedBaseCurrencies: function(idx){
                    var vm = this;

                    for (var i = 0; i < vm.currencies.length; i++) {
                        if (idx == i) {
                            if (vm.currencies[i].is_base) {
                                vm.currencies[i].conversion_value = 1;
                            }
                        } else {
                            vm.currencies[i].is_base = 0;
                        }
                    }
                },
                removeSelectedCurencies: function(idx){
                    this.currencies.splice(idx, 1);
                }
            },
            mounted: function() {
                this.$validator.extend('checkactive', {
                    messages: {
                        en: function(field, args) { return 'Default Store cannot be inactived' },
                        id: function(field, args) { return 'Toko utama tidak bisa dinonaktifkan' }
                    },
                    validate: function(value, args) {
                        var result = false;

                        if (storeApp.is_default == 'YESNOSELECT.YES') {
                            if (storeApp.status == 'STATUS.ACTIVE') {
                                result = true;
                            } else {
                                result = false;
                            }
                        } else {
                            result = true;
                        }

                        return result;
                    }
                });

                this.$validator.extend('isdefault_switch_no', {
                    messages: {
                        en: function(field, args) { return 'Default Store cannot be switched off, replace other Store as YES instead.' },
                        id: function(field, args) { return 'Toko utama tidak bisa dinonaktifkan, pilih Toko lain sebagai pengganti terlebih dahulu' }
                    },
                    validate: function(value, args) {
                        var result = false;


                        return result;
                    }
                });

                this.$validator.extend('frontweb_switch_no', {
                    messages: {
                        en: function(field, args) { return 'Front Web cannot be inactived, replace other Store as YES instead' },
                        id: function(field, args) { return 'Website tidak bisa dinonaktifkan, pilih Toko lain sebagai pengganti terlebih dahulu' }
                    },
                    validate: function(value, args) {
                        var result = false;


                        return result;
                    }
                });

            },
            computed: {
                defaultStatus: function() {
                    return '';
                },
                defaultYesNo: function() {
                    return '';
                }
            }
        });

        function mapsCallback() {
            $('#btnChooseLocation').show();
        }

        $(document).ready(function() {
            $('input.is_icheck').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });

            var location;
            var map;
            var markers = [];

            function init() {
                map = new google.maps.Map(document.getElementById('map'), { zoom: 16 });

                var input = document.getElementById('inputModalAddress');
                var address = input.value;
                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo("bounds", map);

                deleteMarkers();

                var marker = new google.maps.Marker({map: map});

                google.maps.event.addListener(autocomplete, "place_changed", function() {
                    var place = autocomplete.getPlace();
                    location = place;

                    if(place.geometry != undefined) {
                        if (place.geometry.viewport) {
                            map.fitBounds(place.geometry.viewport);
                        } else {
                            map.setCenter(place.geometry.location);
                            map.setZoom(16);
                        }

                        $('#inputModalAddress').val(place.formatted_address);
                        $('#inputModalLat').val(place.geometry.location.lat());
                        $('#inputModalLng').val(place.geometry.location.lng());

                        marker.setPosition(place.geometry.location);
                        markers.push(marker);
                    }
                });

                if (address.length === 0) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        // Do stuff with the geo data...
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;
                        var latLong = new google.maps.LatLng(lat, lng);

                        deleteMarkers();

                        marker = new google.maps.Marker({
                            position: latLong
                        });
                        marker.setMap(map);

                        map.setZoom(16);
                        map.setCenter(marker.getPosition());
                        markers.push(marker);

                        var geocoder = new google.maps.Geocoder();
                        geocoder.geocode({ 'location': latLong }, function(results, status) {
                            if(status === 'OK') {
                                if(results[0]) {
                                    location = results[0];
                                    $('#inputModalAddress').val(location.formatted_address);
                                    $('#inputModalLat').val(location.geometry.location.lat());
                                    $('#inputModalLng').val(location.geometry.location.lng());
                                }
                            }
                        });
                    }, function(error) {
                        alert(error.code + ": " + error.message);
                    });
                }
                else {
                    locateByAddress(address);
                }
            }

            $('#myModal').on('shown.bs.modal', function() {
                if ($('#inputAddress').val() === '') {
                    $('#inputModalLat').val($('#inputLatitude').val());
                    $('#inputModalLng').val($('#inputLongitude').val());
                } else {
                    $('#inputModalAddress').val($('#inputAddress').val());
                }

                init();
            });

            $('#location-ok-btn').click(function() {
                if(location != undefined) {
                    $('#inputLatitude').val(location.geometry.location.lat());
                    $('#inputLongitude').val(location.geometry.location.lng());
                }
            });

            $('#inputModalAddress').focus(function() {
                $(this).select();
            });

            function locateByAddress(address) {
                var geocoder = new google.maps.Geocoder();

                geocoder.geocode({
                        'address': address
                    },
                    function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            location = results[0];

                            $('#inputModalAddress').val(location.formatted_address);
                            $('#inputModalLat').val(location.geometry.location.lat());
                            $('#inputModalLng').val(location.geometry.location.lng());

                            deleteMarkers();

                            var marker = new google.maps.Marker({
                                position: results[0].geometry.location,
                                map: map
                            });
                            markers.push(marker);

                            google.maps.event.trigger(map, 'resize');
                            map.setCenter(results[0].geometry.location);
                        }
                    }
                );
            }

            function locateByCoordinate(lat, lng) {
                deleteMarkers();

                var latLong = new google.maps.LatLng(lat, lng);
                var marker = new google.maps.Marker({
                    position: latLong,
                    map: map
                });
                markers.push(marker);

                google.maps.event.trigger(map, 'resize');
                map.setCenter(latLong);
            }

            $('#inputModalAddress').keypress(function(event) {
                if (event.keyCode == 13) {
                    locateByAddress($('#inputModalAddress').val());
                }
            });

            $('#inputModalLat').keypress(function(event) {
                if (event.keyCode == 13) {
                    locateByCoordinate($('#inputModalLat').val(), $('#inputModalLng').val());
                }
            });

            $('#inputModalLng').keypress(function(event) {
                if (event.keyCode == 13) {
                    locateByCoordinate($('#inputModalLat').val(), $('#inputModalLng').val());
                }
            });

            // Deletes all markers in the array by removing references to them.
            function deleteMarkers() {
                for (var i = 0; i < markers.length; i++) {
                    markers[i].setMap(null);
                }

                markers = [];
            }
        });
    </script>
@endsection