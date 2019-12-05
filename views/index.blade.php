{{ app('xe.frontend')->js('assets/core/xe-ui-component/js/xe-page.js')->load() }}
{{ app('xe.frontend')->js('assets/vendor/jqueryui/jquery-ui.min.js')->load() }}
{{ app('xe.frontend')->css('assets/vendor/jqueryui/jquery-ui.min.css')->load() }}

<div class="panel-heading">
	<div class="pull-left">
		<h3 class="panel-title">{{ xe_trans('loginlog::loginlog') }}</h3>
		( {{xe_trans('loginlog::searchLogCount')}} : {{ $searchLogCount }} / {{xe_trans('loginlog::allLogCount')}} : {{ $totalCount }})
	</div>
</div>
<div class="panel-group">
	<div class="panel">
		<div class="panel-heading">
			<div class="pull-left">
				<div class="btn-group" role="group">
					<button type="button" class="btn btn-default __xe_remove">{{xe_trans('xe::deleteSelected')}}</button>
				</div>
			</div>
			<div class="pull-right">
				<div class="input-group search-group">
					<form method="get" action="{{ route('loginlog::list') }}" accept-charset="UTF-8" role="form" id="_search-form" class="form-inline">
						<div class="form-group">
							<div class="input-group-btn">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<span class="__xe_selectedKeyfield">
										@if(Request::get('keyfield')==='display_name')
											{{xe_trans('xe::name')}}
										@elseif(Request::get('keyfield')==='email')
											{{xe_trans('xe::email')}}
										@elseif(Request::get('keyfield')==='platform')
											OS
										@elseif(Request::get('keyfield')==='browser')
											{{xe_trans('loginlog::browser')}}
										@elseif(Request::get('keyfield')==='ipaddress')
											IP
										@else
											{{xe_trans('xe::select')}}
										@endif
									</span>
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="#" class="__xe_selectKeyfield" data-value="display_name">{{xe_trans('xe::displayName')}}</a></li>
                                            <li><a href="#" class="__xe_selectKeyfield" data-value="email">{{xe_trans('xe::email')}}</a></li>
                                            <li><a href="#" class="__xe_selectKeyfield" data-value="platform">OS</a></li>
                                            <li><a href="#" class="__xe_selectKeyfield" data-value="browser">{{xe_trans('loginlog::browser')}}</a></li>
                                            <li><a href="#" class="__xe_selectKeyfield" data-value="ipaddress">IP</a></li>
                                        </ul>
                                    </div>
                                    <div class="search-input-group">
                                        <input type="text" name="keyword" class="form-control" aria-label="Text input with dropdown button" placeholder="검색어를 입력하세요" value="{{ Request::get('keyword') }}">
                                        <button type="submit" class="btn-link">
                                            <i class="xi-search"></i><span class="sr-only">{{xe_trans('xe::search')}}</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group input-group-btn">
                                    <div class="input-group">
                                        <span class="input-group-addon">{{ xe_trans('loginlog::login_date') }}</span>
                                        <input type="text" id="startDatePicker" name="startDate" class="form-control" value="{{ Request::get('startDate') }}" autocomplete="off">
                                        <input type="text" id="endDatePicker" name="endDate" class="form-control" value="{{ Request::get('endDate') }}" autocomplete="off">
                                    </div>
                                </div>
                                @foreach(Request::except(['keyfield','keyword','page','startDate', 'endDate']) as $name => $value)
                                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                                @endforeach
                                <input type="hidden" class="__xe_keyfield" name="keyfield" value="{{ Request::get('keyfield') }}">
                            </form>
                        </div>
                    </div>
		</div>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>
						<input type="checkbox" class="__xe_check-all">
					</th>
					<th>
						{{ xe_trans('xe::category') }}
					</th>
					<th>
						{{ xe_trans('xe::displayName') }}
					</th>
					<th>
						{{ xe_trans('xe::email') }}
					</th>
					<th>OS</h>
					<th>{{ xe_trans('loginlog::browser')}}</h>
					<th>IP</h>
					<th>{{ xe_trans('loginlog::login_date')}}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($items as $item)
				<tr>
					<td>
						<input name="log_id[]" class="__xe_checkbox" type="checkbox" value="{{ $item->log_id }}">
					</td>
					<td>
						@if ($item->is_succeed == 'Y')
						<label class="label label-green">{{ xe_trans('loginlog::success') }}</label>
						@else
						<label class="label label-danger">{{ xe_trans('loginlog::failed') }}</label>
						@endif
					</td>
					<td>
						<span>
                            @if ($item->user !== null)
                            <img data-toggle="xe-page-toggle-menu"
                                 data-url="{{ route('toggleMenuPage') }}"
                                 data-data='{!! json_encode(['id'=>$item->user_id, 'type'=>'user']) !!}' src="{{ $item->user->getProfileImage() }}" width="30" height="30" alt="{{ xe_trans('xe::profileImage') }}" class="user-profile">
                            @endif

                            @if ($item->user === null)
                                {{ $item->display_name }}
                            @else
                            <a href="#"
								data-toggle="xe-page-toggle-menu"
								data-url="{{ route('toggleMenuPage') }}"
								data-data='{!! json_encode(['id'=> $item->user_id, 'type'=>'user']) !!}' data-text="{{ $item->display_name }}">{{ $item->display_name }}</a>
                            @endif
						</span>
					</td>
					<td>
						{{ $item->email }}
					</td>
					<td>
						{{ $item->platform }}
					</td>
					<td>
						{{ $item->browser }}
					</td>
					<td>
						{{ $item->ipaddress }}
					</td>
					<td>
						{{ $item->logined_at }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		@if($pagination = $items->render())
		<div class="panel-footer">
			<div class="pull-left">
				<nav>
					{!! $pagination !!}
				</nav>
			</div>
		</div>
		@endif
	</div>
</div>

<script>
    $(function () {
       $("#startDatePicker").datepicker({
            dateFormat: "yy-mm-dd",
            maxDate: 0,
        });

        $("#endDatePicker").datepicker({
            dateFormat: "yy-mm-dd",
        });

        $("#startDatePicker").change(function () {
            setEndDatePickerSetDate($(this).datepicker('getDate'));
            setEndDatePickerMinDate($(this).datepicker('getDate'));

            $(this).closest('form').submit();
        });

        $("#endDatePicker").change(function () {
            $(this).closest('form').submit();
        });

        initDatePicker();
    });

    function initDatePicker() {
        var startDate = $("#startDatePicker").val();

        if (startDate != '') {
            setEndDatePickerMinDate($("#startDatePicker").datepicker('getDate'));
        }
    }

    function setEndDatePickerSetDate(newDate) {
        newDate.setMonth(newDate.getMonth() + 1);
        $("#endDatePicker").datepicker("setDate", newDate);
    }

    function setEndDatePickerMinDate(minDate) {
        minDate.setDate(minDate.getDate());
        $("#endDatePicker").datepicker('option',{minDate:minDate});
    }

    var LogList = (function() {
        var self;

        return {
            init: function() {
                self = this;

                self.cache();
                self.bindEvents();

                return this;
            },
            cache: function() {
				self.$selectKeyfield = $('.__xe_selectKeyfield');
                self.$selectedKeyfield = $('.__xe_selectedKeyfield');
				self.$keyfield = $('.__xe_keyfield');
				self.$checkAll = $('.__xe_check-all');
                self.$remove = $('.__xe_remove');
				self.$dropdownToggle = $('.dropdown-toggle');
            },
            bindEvents: function() {
				self.$selectKeyfield.on('click', self.selectKeyfield);
				self.$checkAll.on('change', self.checkAll);
                self.$remove.on('click', self.remove);
				self.$dropdownToggle.dropdown();
            },
            selectKeyfield: function(e) {
                e.preventDefault();

                var $this = $(this),
                        val = $this.attr('data-value'),
                        name = $this.text();

                self.$selectedKeyfield.text(name);
                self.$keyfield.val(val);
            },
            checkAll: function(e) {
                if ($(this).is(':checked')) {
                    $('input.__xe_checkbox:not(disabled)').prop('checked', true);
                } else {
                    $('input.__xe_checkbox:not(disabled)').prop('checked', false);
                }
            },
            remove: function() {
                if (!$('input.__xe_checkbox:checked').is('input')) {
                    return false;
                }

                var logIds = $('input.__xe_checkbox:checked').map(function() {
                    return this.value;
                }).get().join();

                var options = {
                    'data' : {
                        'logIds': logIds
                    }
                };

                XE.pageModal('{{ route('loginlog::settings.loginlog.delete') }}', options);
            }
        }
    })().init();
</script>