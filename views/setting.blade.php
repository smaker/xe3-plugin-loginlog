<div class="row">
    <div class="col-sm-12">
        <div class="panel-group">
            <div class="panel">
				<form role="form" action="{{ route('settings.loginlog.saveAdminConfig') }}" method="post" id="__xe_settingForm" accept-charset="UTF-8">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="panel-heading">
						<div class="pull-left">
							<h3 class="panel-title">{{xe_trans('loginlog::setting')}}</h3>
						</div>
					</div>
					<div id="collapseOne" class="panel-collapse collapse in">
						<div class="panel-body">
							<div class="form-group">
								<label for="item-url">최고 관리자 로그인 기록</label>
								<div class="list-group-item">
									<div class="radio">
										<label>
											<input type="radio" name="admin_user_log" value="Y" @if ($config->get('admin_user_log') == 'Y') checked="checked" @endif> 사용
										</label>
										<label>
											<input type="radio" name="admin_user_log" value="N" @if ($config->get('admin_user_log') == 'N') checked="checked" @endif> 사용 안함
										</label>
									</div>
								</div>
								<p class="help-block">최고 관리자의 로그인 기록을 남깁니다. 로그인 실패 시에는 설정값과 상관없이 기록됩니다.</p>
							</div>
						</div>
					</div>
					<div class="panel-footer">
						<div class="pull-right">
							<button type="submit" class="btn btn-primary btn-lg">{{xe_trans('xe::save')}}</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
